<?php
$title = "Game";
$let = $_GET['l'] ?? 'A';
$let = (strlen($let) == 1 && ctype_upper($let)) ? $let : 'A';
$cats = ['Boy' => 'blue', 'Girl' => 'red', 'Thing' => 'yel', 'Food' => 'grn', 'Place' => 'plum'];
?>

<!DOCTYPE html>
<html>

<head>
    <title><?= $title ?></title>
    <link rel="stylesheet" href="../src/css/classes.css">
    <link rel="stylesheet" href="../src/css/style.css">
</head>

<body>
    <div id="master" class="flex-col screen-full">

        <div class="band"></div>


        <!-- [ Round and timer ] -->
        <section class="flex-row" id="game_top">
            <span>Round 1 of 5</span>
            <span>0:30</span>
        </section>



        <!-- [ Chosen letter and answer cards ] -->
        <section class="flex-row ele-center g-2" id="game_main">
            <?php $lw = '14rem'; include '../includes/lcard.php'; ?>
            <div id="answers">
                <?php foreach ($cats as $n => $c) { ?>
                    <label class="card c-<?= $c ?>">
                        <span class="flex-col ele-center-hori card-strip"><?= $n[0] ?></span>
                        <div class="flex-col ele-center g-1 card-body">
                            <span class="card-name"><?= $n ?></span>
                            <input>
                        </div>
                        <span class="card-corner"><?= $n[0] ?></span>
                    </label>
                <?php } ?>
            </div>
        </section>



        <!-- [ Scores and navigation ] -->
        <section class="flex-row" id="game_bottom">
            <div class="flex-row g-1">
                <span class="chip">You 0</span>
                <span class="chip">Player 2 0</span>
                <span class="chip">Player 3 0</span>
            </div>
            <div class="flex-row g-1">
                <a class="btn-classic hover-red" href="../index.php">Leave</a>
                <a class="btn-classic hover-grn" href="#spin">Submit answers</a>
            </div>
        </section>

        <div class="band band-flip"></div>

    </div>



    <!-- [ Round end: spin again for the next letter ] -->
    <section class="over" id="spin">
        <h2>Spin for the next letter</h2>
        <?php include '../includes/wheel.php'; ?>
        <a class="btn-classic hover-grn" href="#">Next round</a>
    </section>

    <script src="../src/js/spin.js"></script>
</body>

</html>
