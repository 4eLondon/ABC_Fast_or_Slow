<?php
$title = "Lobby";
$m = ($_GET['m'] ?? 'local') == 'multi' ? 'multi' : 'local';
$multi = $m == 'multi';
$names = ['You', 'Player 2', 'Player 3', 'Player 4'];
$pc = ['red', 'yel', 'grn', 'blue'];
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


        <!-- [ Title, plus room code for multiplayer ] -->
        <section class="flex-row" id="lobby_head">
            <h1 id="lobby_title"><?= $multi ? "Multiplayer lobby" : "Local lobby" ?></h1>
            <?php if ($multi) { ?>
                <span id="room_code">Room ABCD</span>
            <?php } ?>
        </section>



        <!-- [ Players, spin wheel and starting letter ] -->
        <section class="flex-row ele-center g-3" id="lobby_main">

            <div id="players">
                <?php foreach ($pc as $i => $c) { ?>
                    <div class="card c-<?= $c ?>">
                        <span class="flex-col ele-center-hori card-strip"><?= $i + 1 ?></span>
                        <div class="flex-col ele-center g-1 card-body">
                            <div class="avatar"></div>
                            <?php if ($multi) { ?>
                                <span class="card-name"><?= $names[$i] ?></span>
                                <span class="tag">Ready</span>
                            <?php } else { ?>
                                <input placeholder="<?= $names[$i] ?>">
                            <?php } ?>
                        </div>
                        <span class="card-corner"><?= $i + 1 ?></span>
                    </div>
                <?php } ?>
            </div>

            <?php include '../includes/wheel.php'; ?>

            <div class="flex-col ele-center-hori g-1" id="side">
                <?php $lw = '10rem'; $let = '?'; include '../includes/lcard.php'; ?>
                <select class="input-classic">
                    <option>5 rounds</option>
                    <option>10 rounds</option>
                </select>
                <select class="input-classic">
                    <option>Slow letters</option>
                    <option>Fast letters</option>
                </select>
            </div>

        </section>



        <!-- [ Navigation ] -->
        <section class="flex-row g-1" id="lobby_buttons">
            <a class="btn-classic hover-red" href="../index.php">Back</a>
            <a class="btn-classic hover-grn" id="go" href="game.php?m=<?= $m ?>&l=A">Start game</a>
        </section>

    </div>
    <script src="../src/js/spin.js"></script>
</body>

</html>
