<?php
require __DIR__ . '/../includes/game.php';
require __DIR__ . '/../includes/components.php';
$pageTitle = "Game";
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

    <div id="master" class="fullscreen flex-col items-center" style="background-image: url(../assets/backgrounds/bg<?= $background ?>.svg);">

        <!-------------------------------------------------------|
             x x x x x x x x [ Main Section ] x x x x x x x x x 
        |-------------------------------------------------------->

        <section id="turnScreen" class="fullscreen flex-col gap-2 items-center">
            <h2 class="txt-condition" style="font-size: 2rem; margin: 0.5rem;">Round <?= $round ?></h2>
            <h1 class="txt-color" style="font-size: 5rem; margin: 0;"><?= htmlspecialchars($player) ?></h1>
            <h3 class="txt-regular-4" style="font-size: 3rem; margin: 0;">Get ready in... <span id="countdown">3</span></h3>
        </section>



        <section id="gameScreen" class="fullscreen flex-col items-center hidden" data-letter="<?= $letter ?>" data-seconds="<?= $seconds ?>">

            <form id="answerForm" method="post" autocomplete="off" action="../includes/game.php" class="fullscreen flex-col items-center">
                <input type="hidden" name="submit_turn" value="1">

                <div class="card-display-alt flex-row items-center">
                    <h1 class="txt-condition" style="font-size: 6rem;"><?= $letter ?></h1>
                </div>

                <div id="cards" class="fullscreen flex-row gap-2 items-center">
                    <?php foreach ($categories as $i => $card): ?>
                        <div class="flex-col items-center gap-2">
                            <span class="flex-col items-center card-small card-small-back-<?= $i + 1 ?>">
                                <img src="../assets/icons/icon-<?= $card[1] ?>-white.svg">
                                <p class="txt-regular-4" style="font-size: 1.5rem; margin: 0; color: white;"><?= $card[0] ?></p>
                            </span>
                            <input type="text" autocomplete="off" name="answer[]" maxlength="40" class="answer-input txt-regular-4 input-text-card accent-<?= $i + 1 ?>-border accent-<?= $i + 1 ?>-txt" style="font-size:1rem;">
                            <small class="answer-error txt-regular-4"></small>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="flex-col items-center gap-2" id="tip">
                    <button type="submit" class="button-new-player-alt" id="button_submit_key">Lock in</button>
                    <?php drawHint("enter"); ?>
                </div>
            </form>

        </section>

        <!-------------------------------------------------------|
             x x x x x x x x [ Decals Section ] x x x x x x x x x 
        |-------------------------------------------------------->
        <?php drawDecals(3); ?>


    </div>
    <script src="../src/js/game.js"></script>
</body>

</html>
