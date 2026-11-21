<?php
session_start();
require_once '../includes/app.php';
require_once '../includes/game_repository.php';
require_once '../includes/rooms.php';
require_once '../includes/validation.php';

header('Content-Type: application/json; charset=utf-8');

function game_action_response(int $httpStatus, array $responseData): void
{
    http_response_code($httpStatus);
    echo json_encode($responseData);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST'
    && !($_SERVER['REQUEST_METHOD'] === 'GET' && ($_GET['action'] ?? '') === 'state')) {
    game_action_response(405, ['error' => 'Use POST for game actions.']);
}

$db = database_connection();
$currentUser = current_user($db);
if (!$currentUser) {
    game_action_response(401, ['error' => 'Sign in again before continuing.']);
}

$roomCode = strtoupper(trim((string) ($_SERVER['REQUEST_METHOD'] === 'GET' ? ($_GET['room'] ?? '') : ($_POST['room'] ?? ''))));
if (!preg_match('/^[A-Z]{4}$/', $roomCode)) {
    game_action_response(400, ['error' => 'The room code is invalid.']);
}
$room = find_room($db, $roomCode);
$player = $room ? find_room_player($db, (int) $room['id'], (int) $currentUser['id']) : null;
if (!$room || !$player) {
    game_action_response(403, ['error' => 'You are not a member of this room.']);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // AI assisted lines 39-50
    // The browser polls this action for the latest round and submission status.
    $round = $room['round'];
    $roundDeadline = $round ? strtotime($round['deadline_at'] . ' UTC') : 0;
    $hasSubmittedAnswers = false;
    if ($round) {
        $checkSubmission = $db->prepare(
            'SELECT 1 FROM submissions WHERE round_id = :round_id AND room_player_id = :player_id'
        );
        $checkSubmission->execute(['round_id' => $round['id'], 'player_id' => $player['id']]);
        $hasSubmittedAnswers = (bool) $checkSubmission->fetchColumn();
    }

    game_action_response(200, [
        'status' => $room['status'],
        'roundId' => $round ? (int) $round['id'] : null,
        'roundEnded' => $round && ($round['stopped_at'] !== null || time() >= $roundDeadline),
        'submitted' => $hasSubmittedAnswers,
    ]);
}

if (!verify_csrf_token()) {
    game_action_response(403, ['error' => 'Your session expired. Refresh the game and try again.']);
}

$actionName = (string) ($_POST['action'] ?? '');
try {
    if ($actionName === 'submit') {
        // AI assisted lines 70-87
        $roundId = filter_var($_POST['round_id'] ?? null, FILTER_VALIDATE_INT);
        $timedOut = ($_POST['timed_out'] ?? '') === '1';
        $answers = $_POST['answers'] ?? [];
        if (!$roundId || !is_array($answers) || $room['status'] !== 'playing') {
            game_action_response(400, ['error' => 'The answer submission is invalid.']);
        }

        $categories = $room['categories'];
        $expectedRound = $room['round'];
        if (!$expectedRound || (int) $expectedRound['id'] !== $roundId) {
            game_action_response(409, ['error' => 'This round is no longer active. Refresh the game.']);
        }

        $roundDeadline = strtotime($expectedRound['deadline_at'] . ' UTC');
        if ($timedOut && time() < $roundDeadline) {
            game_action_response(409, ['error' => 'The round timer has not expired yet.']);
        }
        if (!$timedOut && time() >= $roundDeadline) {
            game_action_response(409, ['error' => 'Time is up. Try submitting again to save the answers before the timer closes.']);
        }

        // Validate every answer before opening the transaction so nothing is partly saved.
        $answersByCategoryId = [];
        foreach ($categories as $category) {
            $categoryId = (string) $category['id'];
            $value = $answers[$categoryId] ?? '';
            if (!is_string($value) || strlen($value) > 40) {
                game_action_response(422, ['error' => 'Each answer must be 40 characters or fewer.']);
            }
            $value = trim($value);
            if ($value !== '' && !validate_answer($value, (string) $expectedRound['letter'])) {
                game_action_response(422, [
                    'error' => 'Every answer must start with ' . $expectedRound['letter'] . '.',
                    'category' => $categoryId,
                ]);
            }
            $answersByCategoryId[(int) $category['id']] = $value;
        }

        // Lock the round while saving to prevent two requests from finishing it at once.
        $db->beginTransaction();
        $lock = $db->prepare('SELECT stopped_at FROM rounds WHERE id = :id AND room_id = :room_id FOR UPDATE');
        $lock->execute(['id' => $roundId, 'room_id' => $room['id']]);
        $lockedRound = $lock->fetch();
        if (!$lockedRound) {
            $db->rollBack();
            game_action_response(404, ['error' => 'The requested round could not be found.']);
        }

        $existing = $db->prepare(
            'SELECT 1 FROM submissions WHERE round_id = :round_id AND room_player_id = :player_id'
        );
        $existing->execute(['round_id' => $roundId, 'player_id' => $player['id']]);
        if ($existing->fetchColumn()) {
            $db->commit();
            game_action_response(200, ['saved' => true, 'alreadySubmitted' => true]);
        }
        if ($lockedRound['stopped_at'] !== null) {
            $db->rollBack();
            game_action_response(409, ['error' => 'The round has already ended.']);
        }

        $saveAnswer = $db->prepare(
            'INSERT INTO answers (round_id, room_player_id, category_id, value)
             VALUES (:round_id, :player_id, :category_id, :value)
             ON DUPLICATE KEY UPDATE value = VALUES(value)'
        );
        foreach ($answersByCategoryId as $categoryId => $value) {
            $saveAnswer->execute([
                'round_id' => $roundId,
                'player_id' => $player['id'],
                'category_id' => $categoryId,
                'value' => $value,
            ]);
        }

        $insertSubmission = $db->prepare(
            'INSERT INTO submissions (round_id, room_player_id) VALUES (:round_id, :player_id)'
        );
        $insertSubmission->execute(['round_id' => $roundId, 'player_id' => $player['id']]);
        $stop = $db->prepare(
            'UPDATE rounds SET stopped_at = UTC_TIMESTAMP(), end_reason = :reason WHERE id = :id AND stopped_at IS NULL'
        );
        $stop->execute(['reason' => $timedOut ? 'timer' : 'first_finished', 'id' => $roundId]);
        $setPhase = $db->prepare("UPDATE rooms SET phase = 'review' WHERE id = :id AND status = 'playing'");
        $setPhase->execute(['id' => $room['id']]);
        $db->commit();
        game_action_response(200, ['saved' => true]);
    }

    if ($actionName === 'next') {
        
        $letter = strtoupper(trim((string) ($_POST['letter'] ?? '')));
        if (!preg_match('/^[A-Z]$/', $letter) || $room['status'] !== 'playing' || !$room['round']) {
            game_action_response(400, ['error' => 'The next round request is invalid.']);
        }

        $db->beginTransaction();
        $lock = $db->prepare(
            'SELECT status, phase, total_rounds, default_speed, fast_seconds, slow_seconds
             FROM rooms WHERE id = :id FOR UPDATE'
        );
        $lock->execute(['id' => $room['id']]);
        $lockedRoom = $lock->fetch();
        if (!$lockedRoom || $lockedRoom['status'] !== 'playing') {
            $db->rollBack();
            game_action_response(409, ['error' => 'This game is no longer active.']);
        }

        $latest = $db->prepare(
            'SELECT id, number, deadline_at, stopped_at FROM rounds WHERE room_id = :room_id ORDER BY number DESC LIMIT 1 FOR UPDATE'
        );
        $latest->execute(['room_id' => $room['id']]);
        $currentRound = $latest->fetch();
        if (!$currentRound) {
            $db->rollBack();
            game_action_response(404, ['error' => 'No current round was found.']);
        }
        if ($currentRound['stopped_at'] === null && time() < strtotime($currentRound['deadline_at'] . ' UTC')) {
            $db->rollBack();
            game_action_response(409, ['error' => 'Finish the current round before starting another.']);
        }
        if ($currentRound['stopped_at'] === null) {
            $stop = $db->prepare(
                "UPDATE rounds SET stopped_at = UTC_TIMESTAMP(), end_reason = 'timer' WHERE id = :id"
            );
            $stop->execute(['id' => $currentRound['id']]);
        }

        if ((int) $currentRound['number'] >= (int) $lockedRoom['total_rounds']) {
            $finish = $db->prepare(
                "UPDATE rooms SET status = 'finished', phase = 'finished', finished_at = UTC_TIMESTAMP() WHERE id = :id"
            );
            $finish->execute(['id' => $room['id']]);
            $db->commit();
            game_action_response(200, ['finished' => true]);
        }

        $nextNumber = (int) $currentRound['number'] + 1;
        // A retry may arrive after another request has already created this round.
        $existingNext = $db->prepare('SELECT id, number, letter, deadline_at FROM rounds WHERE room_id = :room_id AND number = :number');
        $existingNext->execute(['room_id' => $room['id'], 'number' => $nextNumber]);
        $nextRound = $existingNext->fetch();
        if (!$nextRound) {
            $callerRoomPlayer = find_room_player($db, (int) $room['id'], (int) $currentUser['id']);
            $roundSpeed = $lockedRoom['default_speed'];
            $roundDurationSeconds = (int) $lockedRoom[$roundSpeed . '_seconds'];
            $nextRoundId = create_round(
                $db,
                (int) $room['id'],
                (int) $callerRoomPlayer['id'],
                $nextNumber,
                $roundSpeed,
                $letter,
                $roundDurationSeconds
            );
            $getNext = $db->prepare('SELECT id, number, letter, deadline_at FROM rounds WHERE id = :id');
            $getNext->execute(['id' => $nextRoundId]);
            $nextRound = $getNext->fetch();
        }
        $setPhase = $db->prepare("UPDATE rooms SET phase = 'answering' WHERE id = :id");
        $setPhase->execute(['id' => $room['id']]);
        $db->commit();

        game_action_response(200, [
            'finished' => false,
            'roundId' => (int) $nextRound['id'],
            'roundNumber' => (int) $nextRound['number'],
            'letter' => $nextRound['letter'],
            'remaining' => max(0, strtotime($nextRound['deadline_at'] . ' UTC') - time()),
        ]);
    }

    game_action_response(400, ['error' => 'That game action is not available.']);
} catch (DomainException $exception) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    game_action_response(422, ['error' => $exception->getMessage()]);
}
