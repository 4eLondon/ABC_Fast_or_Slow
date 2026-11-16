<?php
$title = "Page";
?>

<!DOCTYPE HTML>
<html>

<head>
    <title><?= $title ?></title>
    <link rel="stylesheet" href="src/css/master.css">
    <link rel="stylesheet" href="src/css/index.css">
    <link rel="stylesheet" href="src/css/classes.css">
</head>

<body>
    <div id="master" class="flex flex-col fullscreen items-center-y">

        <!-- [ Navigation Bar ]  -->
        <nav id="nav" class="flex flex-row items-center-x gap-1">
            <button id="signup" class="button-classic"><a href="pages/auth.php">Sign up</a></button>
            <button id="login" class="button-classic"><a href="pages/auth.php">Log in</a></button>
        </nav>

        <!-- [ Main page content ]  -->
        <section id="hero" class="flex flex-row gap-4">

            <h1 id="header_a"><span id="header_b">ABC</span><br>Fast or Slow</h1>
            <div class="card-large flex items-center">


                <div class="flex items-center card-large-inner">
                    <img style="transform: rotateZ(45deg); width: 7rem; height: 5rem; border-radius: 0.2rem; background-color: black;">
                </div>


            </div>

        </section>




        <section id="gamemode" class="flex flex-col gap-2 items-center">

            <h1 id="gamemode_title">Gamemodes</h1>


            <section id="gamemode_selector" class="flex flex-row gap-4 items-center">

                <div class="flex flex-col items-center-y gap-1 card-mode">
                    <a href="pages/lobby.php"><img src=""></a>
                    <span class="card-mode-banner">Classic</span>
                </div>

                <div class="flex flex-col items-center-y gap-1 card-mode">
                    <a href="pages/lobby.php"><img src=""></a>
                    <span class="card-mode-banner">King of the hill</span>
                </div>

                <div class="flex flex-col items-center-y gap-1 card-mode">
                    <a href="pages/lobby.php"><img src="" style="height: 15rem; width: 15rem; background-color: white;"></a>
                    <span class="card-mode-banner">Blitz</span>
                </div>

                <div class="flex flex-col items-center-y gap-1 card-mode">
                    <a href="pages/lobby.php"><img src=""></a>
                    <span class="card-mode-banner">Team deathmatch</span>
                </div>

                <div class="flex flex-col items-center-y gap-1 card-mode">
                    <a href="pages/lobby.php"><img src=""></a>
                    <span class="card-mode-banner">Power-up Mode</span>
                </div>

            </section>

            <section id="gamemode_description" class="flex flex-col card-mode-long items-center-y hidden">
                <p>Game Mode can refer to different things depending on the platform: on iPhone/iPad/Mac it prioritizes your game’s performance</p>
            </section>

        </section>





        <section id="guide" class="flex flex-row gap-4">
            <div id="guide_text" class="flex flex-col">
                <h1>How to play</h1>
                <p>Name a boy, girl, thing, food and place that start with it.</p>
            </div>

            <section class="flex flex-row gap-1">
                <div class="card-small"></div>
                <div class="card-small"></div>
                <div class="card-small"></div>
                <div class="card-small"></div>
                <div class="card-small"></div>
                <div class="card-small"></div>
            </section>

        </section>


    </div>
</body>

</html>
