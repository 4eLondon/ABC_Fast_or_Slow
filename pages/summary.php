<?php
require __DIR__ . '/../includes/game.php';
require __DIR__ . '/../includes/components.php';
$pageTitle = "Summary";
?>
<!DOCTYPE HTML>
<html>

<head>
    <title><?= $pageTitle ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../src/css/master.css">
    <link rel="stylesheet" href="../src/css/classes.css">
    <link rel="stylesheet" href="../src/css/game.css">
    <?php drawFavicon() ?>
</head>

<body>


    <div id="master" class="fullscreen flex-col items-center" style="background-image: url(../assets/backgrounds/screen1.svg);">

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
                    <?php foreach ($names as $i => $name): ?>
                        <li class="flex-row items-center txt-regular-4">
                            <div class="flex-row gap-4 items-center">
                                <img src="../assets/icons/icon-<?= $i === $leader ? "crown-green" : "player-grey" ?>.svg" class="icon">
                                <h2><?= htmlspecialchars($name) ?></h2>
                                <h2>Score <?= $scores[$i] ?? 0 ?></h2>
                                <div class="flex-row gap-1">
                                    <?php foreach ($results[$i] ?? [] as $c => $result): ?>
                                        <span class="result flex-col items-center" data-key="<?= $i ?>-<?= $c ?>" style="color: var(--a<?= $c + 1 ?>);">
                                            <img class="icon" src="../assets/icons/icon-<?= $resultIcons[$result] ?>.svg">
                                            <h4><?= htmlspecialchars($answers[$i][$c] ?: "-") ?></h4>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <?php drawHint("shared"); ?>
                <?php drawHint("disqualify"); ?>
            </div>


            <div id="hero" class="txt-condition flex-col gap-4 items-center">

                <div id="card" class="flex-col gap-1">
                    <span class="flex-row items-center gap-1">
                        <h2 class="txt-condition" id="cardGuide">Reroll</h2>
                        <button id="speedButton" class="button-reroll-small flex-row items-center txt-regular-4" style="gap:0.3rem">
                            <img id="speedIcon" src="../assets/icons/icon-<?= $speed ?>-red.svg" class="icon">
                            <h3 id="speedText" class="accent-1-txt" style="margin: 0;"><?= $speed ?></h3>
                        </button>
                    </span>
                    <button id="cardButton" class="card-display accent-1-txt txt-condition flex-col items-center">
                        <h1 id="letter"><?= $letter ?></h1>
                    </button>
                </div>

                <div>
                    <h1 class="hero-text" style="margin: 0;">Next Round</h1>

                    <form id="nextForm" method="post" action="../includes/game.php">
                        <input type="hidden" name="next_round" value="1">
                        <button type="submit" class="button-start-game flex-row items-center gap-1">
                            <img src="../assets/icons/icon-play-red.svg" class="button-start-game-text icon">
                            <h2 class="txt-regular-4 button-start-game-text accent-1-txt" style="color:var(--a1);">Start (<span id="countdown"><?= $remaining ?></span>)</h2>
                        </button>
                    </form>
                </div>
            </div>

        </section>



        <!-------------------------------------------------------|
             x x x x x x x x [ Decals Section ] x x x x x x x x x 
        |-------------------------------------------------------->
        <?php drawDecals(4); ?>


    </div>
    <script src="../src/js/summary.js"></script>
</body>

</html>
