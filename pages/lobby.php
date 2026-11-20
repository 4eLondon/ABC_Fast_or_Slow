<?php
$pageTitle = "Local Lobby";
$userAccountStatus = "Not Logged in";
$username = "Player 1";
$letter = "A";
?>

<!DOCTYPE HTML>
<html>

<head>
    <title><?= $pageTitle ?></title>
    <link rel="stylesheet" href="../src/css/classes.css">
    <link rel="stylesheet" href="../src/css/master.css">
    <link rel="stylesheet" href="../src/css/lobby.css">
</head>

<body>


    <div id="master" class="fullscreen flex-col items-center">

        <!-------------------------------------------------------|
             x x x x x x x x [ Main Section ] x x x x x x x x x 
        |-------------------------------------------------------->

        <section id="lobbyMenu" class="fill flex-row items-center">
            <div id="hero" class="txt-condition">
                <h1 class="hero-text">Local Lobby</h1>
            </div>

            <div id="card">
                <h2 class="txt-condition" id="cardGuide">Click to reroll</h2>
                <button class="card-display txt-condition accent-2-txt flex-col items-center">
                    <h1 id="letter"><?= $letter ?></h1>
                </button>
            </div>



            <div id="players">

                <ul class="txt-regular-4 flex-col gap-2">

                    <li>
                        <button class="button-new-player flex-row gap-1 items-center">
                            <img src="../assets/icons/icon-crown-yellow.svg" class="icon">
                            <h2 class="txt-regular-4" id="hostPlayerID"><?= $username ?></h2>
                        </button>
                    </li>


                    <li>
                        <button class="button-new-player flex-row gap-1 items-center">
                            <img src="../assets/icons/icon-player-yellow.svg" class="icon">
                            <h2 class="txt-regular-4" id="hostPlayerID"><?= $username ?></h2>
                        </button>
                    </li>

                    <li>
                        <button class="button-new-player flex-row gap-1 items-center">
                            <img src="../assets/icons/icon-player-yellow.svg" class="icon">
                            <h2 class="txt-regular-4" id="hostPlayerID"><?= $username ?></h2>
                        </button>
                    </li>

                    <li>
                        <button class="button-new-player flex-row gap-1 items-center">
                            <img src="../assets/icons/icon-player-yellow.svg" class="icon">
                            <h2 class="txt-regular-4" id="hostPlayerID"><?= $username ?></h2>
                        </button>
                    </li>

                    <li>
                        <button class="button-new-player flex-row gap-1 items-center">
                            <img src="../assets/icons/icon-player-yellow.svg" class="icon">
                            <h2 class="txt-regular-4" id="hostPlayerID"><?= $username ?></h2>
                        </button>
                    </li>

                    <li>
                        <button class="button-new-player flex-row gap-1 items-center">
                            <img src="../assets/icons/icon-add-light.svg" id="addIcon">
                            <h2 class="txt-regular-4">New local player</h2>
                        </button>
                    </li>

                    <li id="addNewPlayer" class="hidden">
                        <form>
                            <ul>
                                <li class="flex-row gap-1">
                                    <input type="text" class="input-text-new-player txt-regular-4" name="field_username" maxlength="20" placeholder="New player name">
                                    <button type="submit" class="button-new-player-alt">OK</button>
                                </li>
                            </ul>
                        </form>
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
