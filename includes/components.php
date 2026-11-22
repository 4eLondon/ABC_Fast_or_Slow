<?php
require_once __DIR__ . '/account.php';
require_once __DIR__ . '/hints.php';

function drawFavicon()
{
    echo "
    <link rel='icon' type='image/x-icon' href='../assets/icons/favicon.svg'>
";
}

function drawDecals(int $type)
{
    // Username and link for the account button (paths are relative to /pages)
    if (isset($_SESSION['username'])) {
        $userAccountStatus = htmlspecialchars($_SESSION['username']);
        $userAccountLink = "../pages/auth.php?logout=1";
    } else {
        $userAccountStatus = "Not logged in";
        $userAccountLink = "../pages/auth.php";
    }

    $turn = $_SESSION['turn'] ?? 0;
    $score = $_SESSION['scores'][$turn] ?? 0;
    $rounds = $_SESSION['round'] ?? 1;
    $timer = ($_SESSION['speed'] ?? "fast") === "slow" ? 120 : 60;
    $names = array_merge([$_SESSION['username'] ?? "Player 1"], $_SESSION['players'] ?? []);
    $activePlayer = htmlspecialchars($names[$turn] ?? $names[0]);

    if ($type == 1) {
        echo "
        <section id='decals' class='fill flex-col items-center-horizontal'>
            <nav class='fullscreen-width film-bar lock-top flex-row items-center'>

                <a href='../index.php' class='navi'>
                    <div class='naviInner flex-row txt-regular-4 gap-1 items-center-vertical'>
                        <img class='icon' src='../assets/icons/icon-home-dark.svg'>
                        <h2>Home</h2>
                    </div>
                </a>

            </nav>

            <footer class='fullscreen-width film-bar lock-bot'>

            </footer>
        </section>
";
    } elseif ($type == 2) {
        echo "
        <section id='decals' class='fill flex-row gap-2 items-center-horizontal'>
            <nav class='fullscreen-width film-bar lock-top gap-2 flex-row items-center'>

                <a href='../index.php' class='navi'>
                    <div class='naviInner flex-row txt-regular-4 gap-1 items-center-vertical'>
                        <img class='icon' src='../assets/icons/icon-home-dark.svg'>
                        <h2>Home</h2>
                    </div>
                </a>

                <a href='$userAccountLink' class='navi'>
                    <div class='naviInner flex-row txt-regular-4 gap-1 items-center-vertical'>
                        <img class='icon' src='../assets/icons/icon-account-dark.svg'>
                        <h2>$userAccountStatus</h2>
                    </div>
                </a>
            </nav>

            <footer class='fullscreen-width film-bar lock-bot flex-row items-center'>
                <a href='../pages/guide.php' class='foot'>
                    <div class='footInner flex-row txt-regular-4 gap-1 items-center-vertical'>
                        <img class='icon' src='../assets/icons/icon-about-dark.svg'>
                        <h2>How to play</h2>
                    </div>
                </a>
            </footer>
        </section>
";
    } elseif ($type == 3) {
        echo "
        <section id='decals' class='fill flex-col items-center-horizontal'>
            <nav class='fullscreen-width film-bar lock-top flex-row items-center gap-4'>

                   <div class='naviInner flex-row txt-regular-4 gap-1 items-center-vertical'>
                    <img class='icon' src='../assets/icons/icon-account-dark.svg'>
                    <h2>$activePlayer</h2>
                </div>

          
                <div class='naviInner flex-row txt-regular-4 gap-1 items-center-vertical'>
                    <img class='icon' src='../assets/icons/icon-score-colors.svg'>
                    <h2>Score $score</h2>
                </div>

                <div class='naviInner flex-row txt-regular-4 gap-1 items-center-vertical'>
                 <img id='roundIcon' class='icon' src='../assets/icons/icon-rounds-colors.svg'>
                 <h2>Round $rounds</h2>
                </div>

                <div class='naviInner flex-row txt-regular-4 gap-1 items-center-vertical'>
                    <img class='icon' src='../assets/icons/icon-timer-colors.svg'>
           <h2 id='timeLeft'>Time $timer</h2>
                </div>
            </nav>

            <footer class='fullscreen-width film-bar lock-bot flex-row items-center'>
                <a href='../index.php' class='foot'>
                    <div class='footInner flex-row txt-regular-4 gap-1 items-center-vertical'>
                        <img class='icon' src='../assets/icons/icon-not-ready-red.svg'>
                        <h2>Exit</h2>
                    </div>
                </a>
            </footer>
        </section>
";
    } elseif ($type == 4) {
        echo "
        <section id='decals' class='fill flex-col items-center-horizontal'>
            <nav class='fullscreen-width film-bar lock-top flex-row items-center'>

                <div class='naviInner flex-row txt-regular-4 gap-1 items-center-vertical'>
                    <img class='icon spin' src='../assets/icons/icon-rounds-colors.svg'>
                    <h2>Round $rounds</h2>
                </div>
            </nav>

            <footer class='fullscreen-width film-bar lock-bot flex-row items-center'>
                <a href='../index.php' class='foot'>
                    <div class='footInner flex-row txt-regular-4 gap-1 items-center-vertical'>
                        <img class='icon' src='../assets/icons/icon-not-ready-red.svg'>
                        <h2>Exit</h2>
                    </div>
                </a>
            </footer>
        </section>
";
    }
}
