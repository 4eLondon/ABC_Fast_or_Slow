<?php
require __DIR__ . '/includes/account.php';
$pageTitle = "ABC Fast or Slow";
?>

<!DOCTYPE HTML>
<html>

<head>
    <title><?= $pageTitle ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="src/css/master.css">
    <link rel="stylesheet" href="src/css/classes.css">
    <link rel="stylesheet" href="src/css/index.css">
    <link rel='icon' type='image/x-icon' href='assets/icons/favicon.svg'>
</head>

<body>

    <div id="master" class="fullscreen">


        <!-------------------------------------------------------
             x x x x x x x x [ Main Section ] x x x x x x x x x 
        -------------------------------------------------------->

        <section id="menu" class="fill flex-col items-center">

            <!--  [ Game title here ]   -->
            <h1 id="menuText" class="txt-regular-1 flex-col items-center-vertical">
                <span id="menuTextBold" class="txt-color">ABC</span><br>
                Fast or slow
            </h1>



            <!--  [ Game option Menu ]   -->
            <div id="menuOptions" class="txt-title items-center">
                <ul class="flex-col gap-1">
                    <li>
                        <a href="pages/lobby.php" class="button-classic flex-row items-center-vertical gap-1 txt-title accent-2"><img src="assets/icons/icon-new-local-light.svg" class="button-classic-icon"> New local game</a>
                    </li>

                    <li>
                        <a href="<?= $userAccountLink ?>" class="button-classic flex-row items-center-vertical gap-1 txt-title accent-7"><img src="assets/icons/icon-auth-light.svg" class="button-classic-icon"> <?= $userAccount ?></a>
                    </li>
                </ul>
            </div>

        </section>


        <!-------------------------------------------------------|
             x x x x x x x x [ Decals Section ] x x x x x x x x x 
        |-------------------------------------------------------->

        <section id="decals" class="fill flex-col items-center-horizontal">

            <nav class="fullscreen-width film-bar lock-top flex-row items-center">
                <a href="<?= $userAccountLink ?>" class="navi">
                    <div class="naviInner flex-row txt-regular-4 gap-1 items-center-vertical">
                        <img class="icon" src="assets/icons/icon-account-dark.svg">
                        <h2><?= htmlspecialchars($userAccountStatus) ?></h2>
                    </div>
                </a>

            </nav>

            <footer class="fullscreen-width film-bar lock-bot flex-row items-center gap-2">
                <a href="pages/guide.php" class="foot">
                    <div class="footInner flex-row txt-regular-4 gap-1 items-center-vertical">
                        <img class="icon" src="assets/icons/icon-about-dark.svg">
                        <h2>How to play</h2>
                    </div>
                </a>

            </footer>
        </section>


    </div>
</body>

</html>
