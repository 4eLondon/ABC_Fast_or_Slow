<?php
require __DIR__ . '/../includes/components.php';
$pageTitle = "Game";
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

            <div class="card-display-alt flex-row items-center">
                <h1 class="txt-condition" style="font-size: 6rem;">A</h1>
            </div>


            <div id="cards" class="fullscreen flex-row gap-2 items-center">

                <div class="flex-col items-center gap-2">
                    <span class="flex-col items-center card-small card-small-back-1">
                        <img src="../assets/icons/icon-male-white.svg">
                        <p class="txt-regular-4" style="font-size: 1.5rem; margin: 0; color: white;">Boy</p>
                    </span>
                    <input type="text" maxlength="50" minlength="3" class="txt-regular-4 input-text-card accent-1-border accent-1-txt" style="font-size:1rem;">
                </div>

                <div class="flex-col items-center gap-2">
                    <span class="flex-col items-center card-small card-small-back-2">
                        <img src="../assets/icons/icon-female-white.svg">
                        <p class="txt-regular-4" style="font-size: 1.5rem; margin: 0; color: white;">Girl</p>
                    </span>
                    <input type="text" maxlength="50" minlength="3" class="txt-regular-4 input-text-card accent-2-border accent-2-txt" style="font-size:1rem;">
                </div>

                <div class="flex-col items-center gap-2">
                    <span class="flex-col items-center card-small card-small-back-3">
                        <img src="../assets/icons/icon-place-white.svg">
                        <p class="txt-regular-4" style="font-size: 1.5rem; margin: 0; color: white;">Place</p>
                    </span>
                    <input type="text" maxlength="50" minlength="3" class="txt-regular-4 input-text-card accent-3-border accent-3-txt" style="font-size:1rem;">
                </div>

                <div class="flex-col items-center gap-2">
                    <span class="flex-col items-center card-small card-small-back-4">
                        <img src="../assets/icons/icon-food-white.svg">
                        <p class="txt-regular-4" style="font-size: 1.5rem; margin: 0; color: white;">Food</p>
                    </span>
                    <input type="text" maxlength="50" minlength="3" class="txt-regular-4 input-text-card accent-4-border accent-4-txt" style="font-size:1rem;">
                </div>

                <div class="flex-col items-center gap-2">
                    <span class="flex-col items-center card-small card-small-back-5">
                        <img src="../assets/icons/icon-thing-white.svg">
                        <p class="txt-regular-4" style="font-size: 1.5rem; margin: 0; color: white;">Thing</p>
                    </span>
                    <input type="text" maxlength="50" minlength="3" class="txt-regular-4 input-text-card accent-5-border accent-5-txt" style="font-size:1rem;">
                </div>

                <div class="flex-col items-center gap-2">
                    <span class="flex-col items-center card-small card-small-back-6">
                        <img src="../assets/icons/icon-animal-white.svg">
                        <p class="txt-regular-4" style="font-size: 1.5rem; margin: 0; color: white;">Animal</p>
                    </span>
                    <input type="text" maxlength="50" minlength="3" class="txt-regular-4 input-text-card accent-6-border accent-6-txt" style="font-size:1rem;">
                </div>



            </div>

            <h3 id="tip" class="txt-regular-2">Tip: You can skip to the next card by pressing enter</h3>/
        </section>



        <!-------------------------------------------------------|
             x x x x x x x x [ Decals Section ] x x x x x x x x x 
        |-------------------------------------------------------->
        <?php drawDecals(3); ?>


    </div>

</body>

</html>
