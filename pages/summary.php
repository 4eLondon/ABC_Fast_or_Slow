<?php
require __DIR__ . '/../includes/components.php';
$pageTitle = "Summary";
$username = "Player";
$score = 1000;
?>

<!DOCTYPE HTML>
<html>

<head>
    <title><?= $pageTitle ?></title>
    <link rel="stylesheet" href="../src/css/master.css">
    <link rel="stylesheet" href="../src/css/classes.css">
    <link rel="stylesheet" href="../src/css/game.css">
    <?php drawFavicon() ?>
</head>

<body>


    <div id="master" class="fullscreen flex-col items-center">

        <!-------------------------------------------------------|
             x x x x x x x x [ Main Section ] x x x x x x x x x 
        |-------------------------------------------------------->

        <section id="" class="fullscreen flex-col items-center">

            <div id="scoreCard" class="flex-row items-center gap-2">
                <span class="flex-col items-center card-large card-large-back-1">
                    <img src="../assets/icons/icon-score-colors.svg">
                </span>
            </div>

            <div id="scores" class="fullscreen flex-col gap-2 items-center">
                <h1 class="txt-color" style="font-size: 4rem; margin: 0;">Summary</h1>
                <ul>
                    <li class="flex-row items-center txt-regular-4">
                        <div class="flex-row gap-4 items-center">
                            <img src="../assets/icons/icon-crown-green.svg" class="icon">
                            <h2><?= $username ?></h2>
                            <h2>Score <?= $score ?></h2>
                            <ul class="flex-row items-center">
                                <li><img class="icon" src="../assets/icons/icon-ready-green.svg"></li>
                                <li><img class="icon" src="../assets/icons/icon-ready-green.svg"></li>
                                <li><img class="icon" src="../assets/icons/icon-ready-green.svg"></li>
                                <li><img class="icon" src="../assets/icons/icon-ready-green.svg"></li>
                                <li><img class="icon" src="../assets/icons/icon-ready-green.svg"></li>
                                <li><img class="icon" src="../assets/icons/icon-ready-green.svg"></li>
                            </ul>
                        </div>
                    </li>

                    <li class="flex-row items-center txt-regular-4">
                        <div class="flex-row gap-4 items-center">
                            <img src="../assets/icons/icon-player-grey.svg" class="icon">
                            <h2><?= $username ?></h2>
                            <h2>Score <?= $score ?></h2>
                            <ul class="flex-row items-center">
                                <li><img class="icon" src="../assets/icons/icon-not-ready-red.svg"></li>
                                <li><img class="icon" src="../assets/icons/icon-ready-green.svg"></li>
                                <li><img class="icon" src="../assets/icons/icon-ready-green.svg"></li>
                                <li><img class="icon" src="../assets/icons/icon-not-ready-red.svg"></li>
                                <li><img class="icon" src="../assets/icons/icon-ready-green.svg"></li>
                                <li><img class="icon" src="../assets/icons/icon-not-ready-red.svg"></li>
                            </ul>
                        </div>
                    </li>

                    <li class="flex-row items-center txt-regular-4">
                        <div class="flex-row gap-4 items-center">
                            <img src="../assets/icons/icon-player-grey.svg" class="icon">
                            <h2><?= $username ?></h2>
                            <h2>Score <?= $score ?></h2>
                            <ul class="flex-row items-center">
                                <li><img class="icon" src="../assets/icons/icon-not-ready-red.svg"></li>
                                <li><img class="icon" src="../assets/icons/icon-ready-green.svg"></li>
                                <li><img class="icon" src="../assets/icons/icon-ready-green.svg"></li>
                                <li><img class="icon" src="../assets/icons/icon-not-ready-red.svg"></li>
                                <li><img class="icon" src="../assets/icons/icon-ready-green.svg"></li>
                                <li><img class="icon" src="../assets/icons/icon-not-ready-red.svg"></li>
                            </ul>
                        </div>
                    </li>

                    <li class="flex-row items-center txt-regular-4">
                        <div class="flex-row gap-4 items-center">
                            <img src="../assets/icons/icon-player-grey.svg" class="icon">
                            <h2><?= $username ?></h2>
                            <h2>Score <?= $score ?></h2>
                            <ul class="flex-row items-center">
                                <li><img class="icon" src="../assets/icons/icon-not-ready-red.svg"></li>
                                <li><img class="icon" src="../assets/icons/icon-ready-green.svg"></li>
                                <li><img class="icon" src="../assets/icons/icon-ready-green.svg"></li>
                                <li><img class="icon" src="../assets/icons/icon-not-ready-red.svg"></li>
                                <li><img class="icon" src="../assets/icons/icon-ready-green.svg"></li>
                                <li><img class="icon" src="../assets/icons/icon-not-ready-red.svg"></li>
                            </ul>
                        </div>
                    </li>

                    <li class="flex-row items-center txt-regular-4">
                        <div class="flex-row gap-4 items-center">
                            <img src="../assets/icons/icon-player-grey.svg" class="icon">
                            <h2><?= $username ?></h2>
                            <h2>Score <?= $score ?></h2>
                            <ul class="flex-row items-center">
                                <li><img class="icon" src="../assets/icons/icon-not-ready-red.svg"></li>
                                <li><img class="icon" src="../assets/icons/icon-ready-green.svg"></li>
                                <li><img class="icon" src="../assets/icons/icon-ready-green.svg"></li>
                                <li><img class="icon" src="../assets/icons/icon-not-ready-red.svg"></li>
                                <li><img class="icon" src="../assets/icons/icon-ready-green.svg"></li>
                                <li><img class="icon" src="../assets/icons/icon-not-ready-red.svg"></li>
                            </ul>
                        </div>
                    </li>
                </ul>
            </div>



            <div id="hero" class="txt-condition flex-col gap-1 items-center">
                <h1 class="hero-text" style="margin: 0;">Next Round</h1>

                <button class="button-start-game flex-row items-center gap-1">
                    <img src="../assets/icons/icon-play-red.svg" class="button-start-game-text icon">
                    <h2 class="txt-regular-4 button-start-game-text accent-1-txt" style="color:var(--a1);">Start</h2>
                </button>
            </div>

        </section>



        <!-------------------------------------------------------|
             x x x x x x x x [ Decals Section ] x x x x x x x x x 
        |-------------------------------------------------------->
        <?php drawDecals(3); ?>


    </div>

</body>

</html>
