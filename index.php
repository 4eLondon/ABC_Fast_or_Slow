<?php
$title = "ABC Fast or Slow";
$cats = ['Boy' => 'blue', 'Girl' => 'red', 'Thing' => 'yel', 'Food' => 'grn', 'Place' => 'plum'];
$ex = ['Mike', 'Mia', 'Mug', 'Mango', 'Mexico'];
?>

<!DOCTYPE html>
<html>

<head>
    <title><?= $title ?></title>
    <link rel="stylesheet" href="src/css/classes.css">
    <link rel="stylesheet" href="src/css/style.css">
</head>

<body>
    <div id="master" class="flex-col g-1 w-page screen-full p-2">


        <!-- [ Hero section ] -->
        <section class="crate bg-wht" id="hero">
            <h1 id="hero_title">ABC<br>Fast or Slow</h1>
            <div class="stripes"></div>
        </section>



        <!-- [ Buttons for starting games ] -->
        <div class="flex-row g-1" id="hero_buttons">
            <a class="crate bg-red" href="pages/lobby.php?m=local">
                <span class="crate-code">L01</span>
                <span class="crate-name">New local game</span>
                <div class="diamond"></div>
            </a>
            <a class="crate bg-blue" href="pages/lobby.php?m=multi">
                <span class="crate-code">M02</span>
                <span class="crate-name">New multiplayer game</span>
                <div class="panels"><i></i><i></i><i></i><i></i><i></i><i></i></div>
            </a>
        </div>



        <!-- [ Guide on how to play, with example cards for the letter M ] -->
        <section class="crate bg-teal" id="guide">
            <div class="flex-col g-1">
                <h2>How to play</h2>
                <ul>
                    <li>Spin the wheel to pick a letter.</li>
                    <li>Name a boy, girl, thing, food and place that start with it.</li>
                    <li>Spin again every round.</li>
                </ul>
            </div>
            <div id="guide_cards">
                <?php $lw = '7rem';
                $let = 'M';
                include 'includes/lcard.php'; ?>
                <?php foreach ($cats as $n => $c) { ?>
                    <div class="card mini c-<?= $c ?>">
                        <span class="flex-col ele-center-hori card-strip"><?= $n[0] ?></span>
                        <div class="flex-col ele-center g-1 card-body">
                            <span class="card-name"><?= $n ?></span>
                            <span class="card-ans"><?= $ex[array_search($n, array_keys($cats))] ?></span>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </section>



        <!-- [ Misc footer details ] -->
        <section id="footer">
            ABC Fast or Slow
            <span> | </span>
            <a href="pages/auth.php?mode=login">Sign in</a>
            <span> | </span>
            <a href="pages/auth.php?mode=register">Create account</a>
        </section>

    </div>
</body>

</html>
