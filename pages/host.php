<?php
$pageTitle = "Local Lobby";
$userAccountStatus = "Not Logged in";
$startingLetter = "A";
$roomKey = "1234";
$username = "player";
?>

<!DOCTYPE HTML>
<html>

<head>
    <title><?= $pageTitle ?></title>
    <link rel="stylesheet" href="../src/css/classes.css">
    <link rel="stylesheet" href="../src/css/master.css">
    <link rel="stylesheet" href="../src/css/room.css">
</head>

<body>


    <div id="master" class="fullscreen flex-col items-center">

        <!-------------------------------------------------------|
             x x x x x x x x [ Main Section ] x x x x x x x x x 
        |-------------------------------------------------------->

        <section id="lobbyMenu" class="fill flex-row items-center">
            <div id="hero" class="txt-condition">
                <h1 class="hero-text">Hosting game</h1>
            </div>

            <div id="card" class="flex-col items-center">
                <h2 class="txt-condition" id="cardGuide">Starting letter</h2>
                <button class="card-display accent-4-txt txt-condition flex-col items-center">
                    <h1 id="letter"><?= $startingLetter ?></h1>
                </button>
            </div>


            <div id="roomKey" class="">
                <form>
                    <ul class="flex-col items-center-verticl">
                        <li class="flex-col gap-4">
                            <span>
                                <h1 class="txt-condition" style="font-size: 4rem; margin-bottom: 0;">New Room</h1>
                                <input type="text" class="input-text-new-player-alt txt-regular-4" name="field_roomkey" maxlength="8" placeholder="Create a room key ( 4-8 digits )">
                            </span>
                            <button type="submit" class="button-new-player-alt" id="button_submit_key">OK</button>
                        </li>

                    </ul>
                </form>
            </div>




            <div id="players" class="hidden">

                <ul class="txt-regular-4 flex-col gap-2">

                    <li id="lobbyTitle">
                        Room <?= $roomKey ?> Online players
                    </li>

                    <li>
                        <button class="button-new-player-static flex-row gap-1 items-center">
                            <img src="../assets/icons/icon-crown-green.svg" class="icon">
                            <h2 class="txt-regular-4" id="hostPlayerID"><?= $username ?></h2>
                            <img src="../assets/icons/icon-not-ready-red-label.svg" class="icon-label">
                        </button>
                    </li>

                    <li>
                        <button class="button-new-player-static flex-row gap-1 items-center">
                            <img src="../assets/icons/icon-player-black.svg" class="icon">
                            <h2 class="txt-regular-4" id="playerID"><?= $username ?></h2>
                            <img src="../assets/icons/icon-ready-green-label.svg" class="icon-label">
                        </button>
                    </li>

                    <li>
                        <button class="button-new-player-static flex-row gap-1 items-center">
                            <img src="../assets/icons/icon-player-black.svg" class="icon">
                            <h2 class="txt-regular-4" id="playerID"><?= $username ?></h2>
                            <img src="../assets/icons/icon-ready-green-label.svg" class="icon-label">
                        </button>
                    </li>

                    <li>
                        <button class="button-new-player-static flex-row gap-1 items-center">
                            <img src="../assets/icons/icon-player-black.svg" class="icon">
                            <h2 class="txt-regular-4" id="playerID"><?= $username ?></h2>
                            <img src="../assets/icons/icon-ready-green-label.svg" class="icon-label">
                        </button>
                    </li>


                    <li>
                        <button class="button-new-player-static flex-row gap-1 items-center">
                            <img src="../assets/icons/icon-player-black.svg" class="icon">
                            <h2 class="txt-regular-4" id="playerID"><?= $username ?></h2>
                            <img src="../assets/icons/icon-ready-green-label.svg" class="icon-label">
                        </button>
                    </li>

                    <li>
                        <button class="button-new-player-static flex-row gap-1 items-center">
                            <img src="../assets/icons/icon-player-black.svg" class="icon">
                            <h2 class="txt-regular-4" id="playerID"><?= $username ?></h2>
                            <img src="../assets/icons/icon-ready-green-label.svg" class="icon-label">
                        </button>
                    </li>


                </ul>
            </div>

        </section>



        <!-------------------------------------------------------|
             x x x x x x x x [ Decals Section ] x x x x x x x x x 
        |-------------------------------------------------------->

        <section id="decals" class="fill flex-row gap-2 items-center-horizontal">
            <nav class="fullscreen-width film-bar lock-top gap-2 flex-row items-center">

                <a href="../index.php" id="navi">
                    <div id="naviInner" class="flex-row txt-regular-4 gap-1 items-center-vertical">
                        <img src="../assets/icons/icon-home-dark.svg">
                        <h2>Home</h2>
                    </div>
                </a>

                <a href="../pages/auth.php" id="navi">
                    <div id="naviInner" class="flex-row txt-regular-4 gap-1 items-center-vertical">
                        <img src="../assets/icons/icon-account-dark.svg">
                        <h2><?= $userAccountStatus ?></h2>
                    </div>
                </a>
            </nav>

            <footer class="fullscreen-width film-bar lock-bot">

            </footer>
        </section>


    </div>

</body>

</html>
