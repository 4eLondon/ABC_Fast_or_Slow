<?php
session_start();
require_once '../includes/app.php';
require_once '../includes/game_repository.php';
require_once '../includes/rooms.php';

$db = database_connection();
$roomCode = strtoupper(trim((string) ($_GET['room'] ?? '')));
if (!preg_match('/^[A-Z]{4}$/', $roomCode)) {
    header('Location: lobby.php?m=multi');
    exit;
}

$user = current_user($db);
if (!$user) {
    header('Location: auth.php?next=' . rawurlencode('game.php?room=' . $roomCode));
    exit;
}

$room = find_room($db, $roomCode);
$player = $room ? find_room_player($db, (int) $room['id'], (int) $user['id']) : null;
if (!$room || !$player || $room['status'] !== 'playing' || !$room['round']) {
    header('Location: lobby.php?m=multi');
    exit;
}
// Mark the room finished before showing the page if its last round already ended.
if ($room['round']['stopped_at'] !== null && (int) $room['round']['number'] >= (int) $room['total_rounds']) {
    $finish = $db->prepare(
        "UPDATE rooms SET status = 'finished', phase = 'finished', finished_at = UTC_TIMESTAMP()
         WHERE id = :id AND status = 'playing'"
    );
    $finish->execute(['id' => $room['id']]);
    $room['status'] = 'finished';
}
if ($room['status'] === 'finished') {
    header('Location: ../index.php');
    exit;
}

$round = $room['round'];
// AI assisted 41 - 54
// The database stores UTC times, so calculate the timer using the same timezone.
$roundDeadline = strtotime($round['deadline_at'] . ' UTC');
$secondsRemaining = max(0, $roundDeadline - time());
$isRoundEnded = $round['stopped_at'] !== null || $secondsRemaining === 0;
$submissionLookup = $db->prepare('SELECT 1 FROM submissions WHERE round_id = :round_id AND room_player_id = :player_id');
$submissionLookup->execute(['round_id' => $round['id'], 'player_id' => $player['id']]);
$hasSubmittedAnswers = (bool) $submissionLookup->fetchColumn();
$savedAnswerLookup = $db->prepare('SELECT category_id, value FROM answers WHERE round_id = :round_id AND room_player_id = :player_id');
$savedAnswerLookup->execute(['round_id' => $round['id'], 'player_id' => $player['id']]);
$savedAnswersByCategory = [];
foreach ($savedAnswerLookup->fetchAll() as $savedAnswer) {
    $savedAnswersByCategory[(int) $savedAnswer['category_id']] = $savedAnswer['value'];
}

$categoryColors = ['Boy' => 'blue', 'Girl' => 'red', 'Thing' => 'yel', 'Food' => 'grn', 'Place' => 'plum'];
$isFinalRound = (int) $round['number'] >= (int) $room['total_rounds'];
$csrfToken = csrf_token();
?>
<!DOCTYPE html>
<html>

<head>
    <title>Game</title>
    <link rel="stylesheet" href="../src/css/classes.css">
    <link rel="stylesheet" href="../src/css/style.css">
</head>

<body>
    <div id="master" class="flex-col screen-full"
        data-room-code="<?= escape_html($roomCode) ?>"
        data-csrf-token="<?= escape_html($csrfToken) ?>"
        data-rounds="<?= (int) $room['total_rounds'] ?>"
        data-round-id="<?= (int) $round['id'] ?>"
        data-round-number="<?= (int) $round['number'] ?>"
        data-letter="<?= escape_html($round['letter']) ?>"
        data-remaining="<?= $secondsRemaining ?>"
        data-ended="<?= $isRoundEnded ? 'true' : 'false' ?>"
        data-round-stopped="<?= $round['stopped_at'] !== null ? 'true' : 'false' ?>"
        data-submitted="<?= $hasSubmittedAnswers ? 'true' : 'false' ?>"
        data-final-round="<?= $isFinalRound ? 'true' : 'false' ?>">

        <div class="band"></div>

        <section class="flex-row" id="game_top">
            <span id="round-count">Round <?= (int) $round['number'] ?> of <?= (int) $room['total_rounds'] ?></span>
            <span id="game-timer" role="timer" aria-label="Time remaining"><?= intdiv($secondsRemaining, 60) ?>:<?= str_pad((string) ($secondsRemaining % 60), 2, '0', STR_PAD_LEFT) ?></span>
        </section>

        <section class="flex-row ele-center g-2" id="game_main">
            <?php $cardWidth = '14rem'; $cardLetter = $round['letter']; include '../includes/lcard.php'; ?>
            <div id="answers">
                <?php foreach ($room['categories'] as $category) {
                    $categoryLabel = $category['label'];
                    if (!isset($categoryColors[$categoryLabel])) {
                        continue;
                    }
                    $categoryId = (int) $category['id'];
                    ?>
                    <label class="card c-<?= $categoryColors[$categoryLabel] ?>">
                        <span class="flex-col ele-center-hori card-strip"><?= escape_html($categoryLabel[0]) ?></span>
                        <div class="flex-col ele-center g-1 card-body">
                            <span class="card-name"><?= escape_html($categoryLabel) ?></span>
                            <input type="text" name="answers[<?= $categoryId ?>]" maxlength="40" autocomplete="off" spellcheck="false"
                                aria-label="<?= escape_html($categoryLabel) ?> answer"
                                aria-describedby="answer-error-<?= $categoryId ?>"
                                value="<?= escape_html($savedAnswersByCategory[$categoryId] ?? '') ?>"
                                <?= $hasSubmittedAnswers || $isRoundEnded ? 'disabled' : '' ?>>
                            <small class="answer-error" id="answer-error-<?= $categoryId ?>" aria-live="polite"></small>
                        </div>
                        <span class="card-corner"><?= escape_html($categoryLabel[0]) ?></span>
                    </label>
                <?php } ?>
            </div>
        </section>

        <p id="game-status" class="form-error" role="status" aria-live="polite" hidden></p>

        <section class="flex-row" id="game_bottom">
            <div class="flex-row g-1">
                <?php foreach ($room['players'] as $roomPlayer) { ?>
                    <span class="chip"><?= escape_html($roomPlayer['username']) ?> <?= (int) $roomPlayer['total_units'] ?></span>
                <?php } ?>
            </div>
            <div class="flex-row g-1">
                <form method="post" action="lobby.php">
                    <input type="hidden" name="csrf_token" value="<?= escape_html($csrfToken) ?>">
                    <input type="hidden" name="room" value="<?= escape_html($roomCode) ?>">
                    <button class="btn-classic hover-red" type="submit" name="action" value="leave">Leave</button>
                </form>
                <button class="btn-classic hover-grn" id="submit-answers" type="button" <?= $hasSubmittedAnswers || $isRoundEnded ? 'disabled' : '' ?>>Submit answers</button>
            </div>
        </section>

        <div class="band band-flip"></div>
    </div>

    <section class="over" id="spin">
        <h2><?= $isFinalRound ? 'Game complete' : ($hasSubmittedAnswers ? 'Waiting for the next round' : 'Round complete') ?></h2>
        <?php if (!$isFinalRound) { ?>
            <?php include '../includes/wheel.php'; ?>
        <?php } ?>
        <button class="btn-classic hover-grn" id="next-round" type="button" <?= $isFinalRound ? '' : 'disabled' ?>>
            <?= $isFinalRound ? 'Return home' : 'Next round' ?>
        </button>
    </section>

    <script src="../src/js/spin.js"></script>
    <script src="../src/js/game.js"></script>
</body>

</html>
