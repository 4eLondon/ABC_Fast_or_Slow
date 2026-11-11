<?php
session_start();
require_once '../includes/app.php';
require_once '../includes/game_repository.php';
require_once '../includes/rooms.php';

$db = database_connection();
$multi = ($_GET['m'] ?? 'local') === 'multi' || isset($_GET['room']);
$mode = $multi ? 'multi' : 'local';
$user = current_user($db);
if (!$user) {
    header('Location: auth.php?next=' . rawurlencode('lobby.php?m=' . $mode));
    exit;
}

$title = 'Lobby';
$playerColors = ['red', 'yel', 'grn', 'blue'];
$error = '';
$roomCode = strtoupper(trim((string) ($_GET['room'] ?? '')));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token()) {
        $error = 'Your session expired. Refresh the page and try again.';
    } else {
        $action = (string) ($_POST['action'] ?? '');
        try {
            if ($action === 'create') {
                $rounds = (int) ($_POST['rounds'] ?? 5);
                $speed = (string) ($_POST['speed'] ?? 'slow');
                if (!in_array($rounds, [5, 10], true) || !in_array($speed, ['fast', 'slow'], true)) {
                    throw new DomainException('Choose valid game settings.');
                }
                $roomCode = create_room($db, (int) $user['id'], false, $rounds, $speed, 'A');
                header('Location: lobby.php?room=' . $roomCode);
                exit;
            }

            if ($action === 'join') {
                $roomCode = strtoupper(trim((string) ($_POST['code'] ?? '')));
                if (!preg_match('/^[A-Z]{4}$/', $roomCode)) {
                    throw new DomainException('Enter a valid 4-letter room code.');
                }
                $room = find_room($db, $roomCode);
                if (!$room) {
                    throw new DomainException('That room was not found.');
                }
                join_room($db, (int) $room['id'], (int) $user['id']);
                header('Location: lobby.php?room=' . $roomCode);
                exit;
            }

            if ($action === 'leave') {
                $roomCode = strtoupper(trim((string) ($_POST['room'] ?? '')));
                $leavingRoom = find_room($db, $roomCode);
                leave_room($db, $roomCode, (int) $user['id']);
                if ($leavingRoom && $leavingRoom['mode'] === 'local') {
                    header('Location: ../index.php');
                    exit;
                }
                header('Location: lobby.php?m=multi');
                exit;
            }

            if ($action === 'start-local') {
                $letter = strtoupper(trim((string) ($_POST['letter'] ?? 'A')));
                $rounds = (int) ($_POST['rounds'] ?? 5);
                $speed = (string) ($_POST['speed'] ?? 'slow');
                if (!preg_match('/^[A-Z]$/', $letter)
                    || !in_array($rounds, [5, 10], true)
                    || !in_array($speed, ['fast', 'slow'], true)) {
                    throw new DomainException('Choose a valid letter and game settings.');
                }
                $roomCode = create_room($db, (int) $user['id'], true, $rounds, $speed, $letter);
                header('Location: game.php?room=' . $roomCode);
                exit;
            }

            $roomCode = strtoupper(trim((string) ($_POST['room'] ?? '')));
            $room = find_room($db, $roomCode);
            if (!$room || !find_room_player($db, (int) $room['id'], (int) $user['id'])) {
                throw new DomainException('You are not a member of that room.');
            }

            if ($action === 'settings') {
                $rounds = (int) ($_POST['rounds'] ?? 0);
                $speed = (string) ($_POST['speed'] ?? '');
                if (!in_array($rounds, [5, 10], true) || !in_array($speed, ['fast', 'slow'], true)) {
                    throw new DomainException('Choose valid game settings.');
                }
                update_room_settings($db, $room, (int) $user['id'], $rounds, $speed);
            } elseif ($action === 'start') {
                start_room($db, $room, (int) $user['id'], 'A');
                header('Location: game.php?room=' . $roomCode);
                exit;
            } else {
                throw new DomainException('That lobby action is not available.');
            }
        } catch (DomainException $exception) {
            $error = $exception->getMessage();
        }
    }
}

$room = $multi && preg_match('/^[A-Z]{4}$/', $roomCode) ? find_room($db, $roomCode) : null;
$isPlayer = $room && find_room_player($db, (int) $room['id'], (int) $user['id']);
$isHost = $room && (int) $room['host_id'] === (int) $user['id'];
if ($room && !$isPlayer) {
    $room = null;
    $isHost = false;
    $error = 'Join this room using its code before opening the lobby.';
}
if ($room && $room['status'] === 'playing') {
    header('Location: game.php?room=' . $roomCode);
    exit;
}
if ($room && $room['status'] !== 'lobby') {
    $room = null;
    $error = 'That room is no longer available.';
}

$csrfToken = csrf_token();
?>
<!DOCTYPE html>
<html>

<head>
    <title><?= $title ?></title>
    <link rel="stylesheet" href="../src/css/classes.css">
    <link rel="stylesheet" href="../src/css/style.css">
</head>

<body>
    <div id="master" class="flex-col screen-full g-2 p-2">
        <?php if ($multi && !$room) { ?>
            <section class="flex-row" id="lobby_head">
                <h1 id="lobby_title">Multiplayer game</h1>
                <a class="btn-classic hover-red" href="../index.php">Back</a>
            </section>

            <?php if ($error !== '') { ?>
                <p class="form-error" role="alert"><?= escape_html($error) ?></p>
            <?php } ?>

            <section class="room-entry-grid">
                <form class="room-entry bg-blue" method="post">
                    <h2>Create a room</h2>
                    <input type="hidden" name="csrf_token" value="<?= escape_html($csrfToken) ?>">
                    <label for="create-rounds">Rounds</label>
                    <select class="input-classic" id="create-rounds" name="rounds">
                        <option value="5">5 rounds</option>
                        <option value="10">10 rounds</option>
                    </select>
                    <label for="create-speed">Letter speed</label>
                    <select class="input-classic" id="create-speed" name="speed">
                        <option value="slow">Slow letters</option>
                        <option value="fast">Fast letters</option>
                    </select>
                    <button class="btn-classic hover-grn" type="submit" name="action" value="create">Create room</button>
                </form>

                <form class="room-entry bg-teal" method="post">
                    <h2>Join a room</h2>
                    <input type="hidden" name="csrf_token" value="<?= escape_html($csrfToken) ?>">
                    <label for="room-code-input">4-letter room code</label>
                    <input class="input-classic" id="room-code-input" name="code" maxlength="4" pattern="[A-Za-z]{4}" required>
                    <button class="btn-classic hover-yel" type="submit" name="action" value="join">Join room</button>
                </form>
            </section>
        <?php } elseif ($multi) { ?>
            <section class="flex-row" id="lobby_head">
                <h1 id="lobby_title">Multiplayer lobby</h1>
                <span id="room_code">Room <?= escape_html($roomCode) ?></span>
            </section>

            <?php if ($error !== '') { ?>
                <p class="form-error" role="alert"><?= escape_html($error) ?></p>
            <?php } ?>

            <section class="flex-row ele-center g-3" id="lobby_main">
                <div id="players">
                    <?php foreach ($room['players'] as $i => $player) { ?>
                        <div class="card c-<?= $playerColors[$i] ?>">
                            <span class="flex-col ele-center-hori card-strip"><?= $i + 1 ?></span>
                            <div class="flex-col ele-center g-1 card-body">
                                <div class="avatar"></div>
                                <span class="card-name"><?= escape_html($player['username']) ?></span>
                                <span class="tag"><?= (int) $player['user_id'] === (int) $room['host_id'] ? 'Host' : 'Ready' ?></span>
                            </div>
                            <span class="card-corner"><?= $i + 1 ?></span>
                        </div>
                    <?php } ?>
                </div>

                <div class="flex-col ele-center-hori g-1" id="side">
                    <h2>Host settings</h2>
                    <form class="flex-col g-1" method="post">
                        <input type="hidden" name="csrf_token" value="<?= escape_html($csrfToken) ?>">
                        <input type="hidden" name="room" value="<?= escape_html($roomCode) ?>">
                        <label for="rounds">Rounds</label>
                        <select class="input-classic" id="rounds" name="rounds" <?= $isHost ? '' : 'disabled' ?>>
                            <option value="5" <?= (int) $room['total_rounds'] === 5 ? 'selected' : '' ?>>5 rounds</option>
                            <option value="10" <?= (int) $room['total_rounds'] === 10 ? 'selected' : '' ?>>10 rounds</option>
                        </select>
                        <label for="speed">Letter speed</label>
                        <select class="input-classic" id="speed" name="speed" <?= $isHost ? '' : 'disabled' ?>>
                            <option value="slow" <?= $room['default_speed'] === 'slow' ? 'selected' : '' ?>>Slow letters</option>
                            <option value="fast" <?= $room['default_speed'] === 'fast' ? 'selected' : '' ?>>Fast letters</option>
                        </select>
                        <?php if ($isHost) { ?>
                            <button class="btn-classic hover-blue" type="submit" name="action" value="settings">Save settings</button>
                        <?php } ?>
                    </form>
                </div>
            </section>

            <section class="flex-row g-1" id="lobby_buttons">
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?= escape_html($csrfToken) ?>">
                    <input type="hidden" name="room" value="<?= escape_html($roomCode) ?>">
                    <button class="btn-classic hover-red" type="submit" name="action" value="leave">Leave room</button>
                </form>
                <?php if ($isHost) { ?>
                    <form method="post">
                        <input type="hidden" name="csrf_token" value="<?= escape_html($csrfToken) ?>">
                        <input type="hidden" name="room" value="<?= escape_html($roomCode) ?>">
                        <button class="btn-classic hover-grn" type="submit" name="action" value="start">Start game</button>
                    </form>
                <?php } else { ?>
                    <span class="waiting-note">Waiting for the host to start</span>
                <?php } ?>
                <a class="btn-classic hover-blue" href="lobby.php?room=<?= escape_html($roomCode) ?>">Refresh lobby</a>
            </section>
        <?php } else { ?>
            <section class="flex-row" id="lobby_head">
                <h1 id="lobby_title">Local game</h1>
                <span>Signed in as <?= escape_html($user['username']) ?></span>
            </section>

            <?php if ($error !== '') { ?>
                <p class="form-error" role="alert"><?= escape_html($error) ?></p>
            <?php } ?>

            <section class="flex-row ele-center g-3" id="lobby_main">
                <div id="players">
                    <div class="card c-blue">
                        <span class="flex-col ele-center-hori card-strip">1</span>
                        <div class="flex-col ele-center g-1 card-body">
                            <div class="avatar"></div>
                            <span class="card-name"><?= escape_html($user['username']) ?></span>
                        </div>
                        <span class="card-corner">1</span>
                    </div>
                </div>
                <?php include '../includes/wheel.php'; ?>
                <div class="flex-col ele-center-hori g-1" id="side">
                    <div class="lcard" style="--lw: 10rem">
                        <span class="corner pick">A</span>
                        <div class="lband"><span class="letter pick">A</span></div>
                        <span class="corner corner-b pick">A</span>
                    </div>
                    <label for="local-rounds">Rounds</label>
                    <select class="input-classic" id="local-rounds" name="rounds" form="local-start">
                        <option value="5">5 rounds</option>
                        <option value="10">10 rounds</option>
                    </select>
                    <label for="local-speed">Letter speed</label>
                    <select class="input-classic" id="local-speed" name="speed" form="local-start">
                        <option value="slow">Slow letters</option>
                        <option value="fast">Fast letters</option>
                    </select>
                </div>
            </section>

            <section class="flex-row g-1" id="lobby_buttons">
                <a class="btn-classic hover-red" href="../index.php">Back</a>
                <form id="local-start" method="post">
                    <input type="hidden" name="csrf_token" value="<?= escape_html($csrfToken) ?>">
                    <input type="hidden" id="start-letter" name="letter" value="A">
                    <button class="btn-classic hover-grn" id="go" type="submit" name="action" value="start-local">Start game</button>
                </form>
            </section>
        <?php } ?>
    </div>
    <script src="../src/js/spin.js"></script>
</body>

</html>
