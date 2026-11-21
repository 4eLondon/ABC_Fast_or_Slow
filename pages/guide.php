<?php
require __DIR__ . '/../includes/components.php';
$pageTitle = "How to play";
?>

<!DOCTYPE HTML>
<html>

<head>
    <title><?= $pageTitle ?></title>
    <link rel="stylesheet" href="../src/css/classes.css">
    <link rel="stylesheet" href="../src/css/master.css">
    <link rel="stylesheet" href="../src/css/index.css">
    <?php drawFavicon() ?>
</head>

<body>


    <div id="master" class="fullscreen flex-col items-center">

        <!-------------------------------------------------------|
             x x x x x x x x [ Main Section ] x x x x x x x x x 
        |-------------------------------------------------------->

        <section id="guide" class="fill flex-col items-center gap-4">


            <!--  [ Game title here ]   -->
            <h1 id="menuTextAlt" class="txt-regular-1 flex-col items-center-vertical">
                <span id="menuTextBoldAlt" class="txt-color">ABC</span><br>
                Fast or slow
            </h1>

            <div class="flex-col items-center gap-1">
                <h2 id="howtoBanner" class="txt-color">How to play the game</h2>


                <div class="flex-row gap-1">

                    <div class="flex-col items-center card-small card-small-back-1">
                        <img src="../assets/icons/icon-male-white.svg">
                        <p class="txt-regular-4" style="font-size: 1.5rem; margin: 0; color: white;">Boy</p>
                    </div>

                    <div class="flex-col items-center card-small card-small-back-2">
                        <img src="../assets/icons/icon-female-white.svg">
                        <p class="txt-regular-4" style="font-size: 1.5rem; margin: 0; color: white;">Girl</p>
                    </div>

                    <div class="flex-col items-center card-small card-small-back-3">
                        <img src="../assets/icons/icon-place-white.svg">
                        <p class="txt-regular-4" style="font-size: 1.5rem; margin: 0; color: white;">Place</p>
                    </div>

                    <div class="flex-col items-center card-small card-small-back-4">
                        <img src="../assets/icons/icon-food-white.svg">
                        <p class="txt-regular-4" style="font-size: 1.5rem; margin: 0; color: white;">Food</p>
                    </div>


                    <div class="flex-col items-center card-small card-small-back-5">
                        <img src="../assets/icons/icon-thing-white.svg">
                        <p class="txt-regular-4" style="font-size: 1.5rem; margin: 0; color: white;">Thing</p>
                    </div>

                    <div class="flex-col items-center card-small card-small-back-6">
                        <img src="../assets/icons/icon-animal-white.svg">
                        <p class="txt-regular-4" style="font-size: 1.5rem; margin: 0; color: white;">Animal</p>
                    </div>


                </div>
                <p class="txt-title" style="width: 60%; text-align: center;">The game ABC fast or slow is reletively simple. Each round someone will go through the entire alphabet either fast or slow as per the name before another players says when to stop. What ever letter the counter lands on will then be used to name a boy, girl, place, food, thing and animal that starts with the same letter. Points are scored based on the rarity of the word with duplicate words resulting in points being shared amoungst players</p>

            </div>

        </section>



        <!-------------------------------------------------------|
             x x x x x x x x [ Decals Section ] x x x x x x x x x 
        |-------------------------------------------------------->
        <?php drawDecals(1); ?>



    </div>

</body>

</html>
