<?php

function drawFavicon()
{
    echo "
    <link rel='icon' type='image/x-icon' href='../assets/icons/favicon.svg'>
";
}

function drawDecals(int $type)
{
    $userAccountStatus = "Not logged in";

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

                <a href='../pages/auth.php' class='navi'>
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
    }
}




function drawCard() {}
