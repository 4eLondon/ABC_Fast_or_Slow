<?php
session_start();
require_once '../includes/rooms.php';

$title = "Lobby";
$m = ($_GET['m'] ?? 'local') == 'multi' || isset($_GET['room']) ? 'multi' : 'local';
$multi = $m == 'multi';
$names = ['You', 'Player 2', 'Player 3', 'Player 4'];
$pc = ['red', 'yel', 'grn', 'blue'];
$error = '';
$rooms = $multi ? load_rooms() : [];
$player_id = session_id();

if ($multi && isset($_GET['leave'])) {
    $leave_code = strtoupper(trim($_GET['leave']));
    if (isset($rooms[$leave_code])) {
        foreach ($rooms[$leave_code]['players'] as $i => $player) {
            if ($player['id'] == $player_id) {
                array_splice($rooms[$leave_code]['players'], $i, 1);
                if ($rooms[$leave_code]['host'] == $player_id) {
                    if (count($rooms[$leave_code]['players']) == 0) {
                        unset($rooms[$leave_code]);
                    } else {
                        $rooms[$leave_code]['host'] = $rooms[$leave_code]['players'][0]['id'];
                    }
                }
                save_rooms($rooms);
                break;
            }
        }
    }

    unset($_SESSION['room_code']);
    header('Location: lobby.php?m=multi');
    exit;
}

if ($multi && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action == 'create' || $action == 'join') {
        $name = trim($_POST['name'] ?? '');
        if ($name == '' || strlen($name) > 20) {
            $error = 'Enter a player name up to 20 characters.';
        } elseif ($action == 'create') {
            $code = new_room_code($rooms);
            $rooms[$code] = [
                'host' => $player_id,
                'status' => 'lobby',
                'settings' => ['rounds' => 5, 'speed' => 'slow'],
                'players' => [['id' => $player_id, 'name' => $name]]
            ];
            save_rooms($rooms);
            header('Location: lobby.php?room=' . $code);
            exit;
        } else {
            $code = strtoupper(trim($_POST['code'] ?? ''));
            if (!preg_match('/^[A-Z]{4}$/', $code)) {
                $error = 'Enter a valid 4-letter room code.';
            } elseif (!isset($rooms[$code])) {
                $error = 'That room was not found.';
            } elseif ($rooms[$code]['status'] != 'lobby') {
                $error = 'That game has already started.';
            } else {
                $already_joined = false;
                foreach ($rooms[$code]['players'] as $player) {
                    if ($player['id'] == $player_id) {
                        $already_joined = true;
                    }
                }

                if (!$already_joined && count($rooms[$code]['players']) >= 4) {
                    $error = 'That room is full.';
                } else {
                    if (!$already_joined) {
                        $rooms[$code]['players'][] = ['id' => $player_id, 'name' => $name];
                        save_rooms($rooms);
                    }
                    header('Location: lobby.php?room=' . $code);
                    exit;
                }
            }
        }
    } else {
        $code = strtoupper(trim($_POST['room'] ?? ''));
        if (isset($rooms[$code]) && $rooms[$code]['host'] == $player_id) {
            if ($action == 'settings') {
                $rounds = $_POST['rounds'] ?? '';
                $speed = $_POST['speed'] ?? '';
                if (!in_array($rounds, ['5', '10'], true) || !in_array($speed, ['slow', 'fast'], true)) {
                    $error = 'Choose valid game settings.';
                } else {
                    $rooms[$code]['settings'] = ['rounds' => (int) $rounds, 'speed' => $speed];
                    save_rooms($rooms);
                    header('Location: lobby.php?room=' . $code);
                    exit;
                }
            } elseif ($action == 'start') {
                if (count($rooms[$code]['players']) < 1) {
                    $error = 'At least one player is needed to start.';
                } else {
                    $rooms[$code]['status'] = 'started';
                    save_rooms($rooms);
                    header('Location: game.php?room=' . $code . '&l=A');
                    exit;
                }
            }
        } else {
            $error = 'Only the room host can change settings or start the game.';
        }
    }
}

$room_code = strtoupper(trim($_GET['room'] ?? ''));
$room = isset($rooms[$room_code]) ? $rooms[$room_code] : null;
$is_host = $room && $room['host'] == $player_id;
$is_player = false;
if ($room) {
    foreach ($room['players'] as $player) {
        if ($player['id'] == $player_id) {
            $is_player = true;
            break;
        }
    }
}
if ($multi && $room && !$is_player) {
    $room = null;
    $error = 'Join this room using its code before opening the lobby.';
}
if ($multi && $room && $room['status'] == 'started') {
    header('Location: game.php?room=' . $room_code . '&l=A');
    exit;
}
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

            <?php if ($error != '') { ?>
                <p class="form-error" role="alert"><?= escape_html($error) ?></p>
            <?php } ?>

            <section class="room-entry-grid">
                <form class="room-entry bg-blue" method="post">
                    <h2>Create a room</h2>
                    <label for="host-name">Your name</label>
                    <input class="input-classic" id="host-name" name="name" maxlength="20" required>
                    <button class="btn-classic hover-grn" type="submit" name="action" value="create">Create room</button>
                </form>

                <form class="room-entry bg-teal" method="post">
                    <h2>Join a room</h2>
                    <label for="join-name">Your name</label>
                    <input class="input-classic" id="join-name" name="name" maxlength="20" required>
                    <label for="room-code-input">4-letter room code</label>
                    <input class="input-classic" id="room-code-input" name="code" maxlength="4" pattern="[A-Za-z]{4}" required>
                    <button class="btn-classic hover-yel" type="submit" name="action" value="join">Join room</button>
                </form>
            </section>
        <?php } else { ?>
            <section class="flex-row" id="lobby_head">
                <h1 id="lobby_title"><?= $multi ? 'Multiplayer lobby' : 'Local lobby' ?></h1>
                <?php if ($multi) { ?>
                    <span id="room_code">Room <?= escape_html($room_code) ?></span>
                <?php } ?>
            </section>

            <?php if ($error != '') { ?>
                <p class="form-error" role="alert"><?= escape_html($error) ?></p>
            <?php } ?>

            <section class="flex-row ele-center g-3" id="lobby_main">
                <div id="players">
                    <?php if ($multi) { ?>
                        <?php foreach ($room['players'] as $i => $player) { ?>
                            <div class="card c-<?= $pc[$i] ?>">
                                <span class="flex-col ele-center-hori card-strip"><?= $i + 1 ?></span>
                                <div class="flex-col ele-center g-1 card-body">
                                    <div class="avatar"></div>
                                    <span class="card-name"><?= escape_html($player['name']) ?></span>
                                    <span class="tag"><?= $player['id'] == $room['host'] ? 'Host' : 'Ready' ?></span>
                                </div>
                                <span class="card-corner"><?= $i + 1 ?></span>
                            </div>
                        <?php } ?>
                    <?php } else { ?>
                        <?php foreach ($pc as $i => $c) { ?>
                            <div class="card c-<?= $c ?>">
                                <span class="flex-col ele-center-hori card-strip"><?= $i + 1 ?></span>
                                <div class="flex-col ele-center g-1 card-body">
                                    <div class="avatar"></div>
                                    <input class="input-classic" placeholder="<?= $names[$i] ?>" aria-label="<?= $names[$i] ?>">
                                </div>
                                <span class="card-corner"><?= $i + 1 ?></span>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>

                <?php if (!$multi) { ?>
                    <?php include '../includes/wheel.php'; ?>
                <?php } ?>

                <div class="flex-col ele-center-hori g-1" id="side">
                    <?php if (!$multi) { ?>
                        <?php $lw = '10rem'; $let = '?'; include '../includes/lcard.php'; ?>
                        <select class="input-classic" aria-label="Game length">
                            <option>5 rounds</option>
                            <option>10 rounds</option>
                        </select>
                        <select class="input-classic" aria-label="Letter speed">
                            <option>Slow letters</option>
                            <option>Fast letters</option>
                        </select>
                    <?php } else { ?>
                        <h2>Host settings</h2>
                        <form class="flex-col g-1" method="post">
                            <input type="hidden" name="room" value="<?= escape_html($room_code) ?>">
                            <label for="rounds">Rounds</label>
                            <select class="input-classic" id="rounds" name="rounds" <?= $is_host ? '' : 'disabled' ?>>
                                <option value="5" <?= $room['settings']['rounds'] == 5 ? 'selected' : '' ?>>5 rounds</option>
                                <option value="10" <?= $room['settings']['rounds'] == 10 ? 'selected' : '' ?>>10 rounds</option>
                            </select>
                            <label for="speed">Letter speed</label>
                            <select class="input-classic" id="speed" name="speed" <?= $is_host ? '' : 'disabled' ?>>
                                <option value="slow" <?= $room['settings']['speed'] == 'slow' ? 'selected' : '' ?>>Slow letters</option>
                                <option value="fast" <?= $room['settings']['speed'] == 'fast' ? 'selected' : '' ?>>Fast letters</option>
                            </select>
                            <?php if ($is_host) { ?>
                                <button class="btn-classic hover-blue" type="submit" name="action" value="settings">Save settings</button>
                            <?php } ?>
                        </form>
                    <?php } ?>
                </div>
            </section>

            <section class="flex-row g-1" id="lobby_buttons">
                <?php if ($multi) { ?>
                    <a class="btn-classic hover-red" href="lobby.php?m=multi&amp;leave=<?= escape_html($room_code) ?>">Leave room</a>
                    <?php if ($is_host) { ?>
                        <form method="post">
                            <input type="hidden" name="room" value="<?= escape_html($room_code) ?>">
                            <button class="btn-classic hover-grn" type="submit" name="action" value="start">Start game</button>
                        </form>
                    <?php } else { ?>
                        <span class="waiting-note">Waiting for the host to start</span>
                    <?php } ?>
                    <a class="btn-classic hover-blue" href="lobby.php?room=<?= escape_html($room_code) ?>">Refresh lobby</a>
                <?php } else { ?>
                    <a class="btn-classic hover-red" href="../index.php">Back</a>
                    <a class="btn-classic hover-grn" id="go" href="game.php?m=local&amp;l=A">Start game</a>
                <?php } ?>
            </section>
        <?php } ?>
    </div>
    <script src="../src/js/spin.js"></script>
</body>

</html>
