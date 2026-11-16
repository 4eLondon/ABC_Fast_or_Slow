<?php
$title = "Page";
?>

<!DOCTYPE HTML>
<html>

<head>
    <title><?= $title ?></title>
    <link rel="stylesheet" href="../src/css/master.css">
    <link rel="stylesheet" href="../src/css/lobby.css">
    <link rel="stylesheet" href="../src/css/classes.css">
</head>

<body>



    <div id="master" class="flex flex-row fullscreen items-center-y">

        <section id="" class="">


            <section class="flex flex-row items-center-y" style="height:fit-content ; width: fit-content; position: absolute; bottom: 0%">


                <div style="height: 100dvh; width: 30rem; background-color: orange; transform: skewX(10deg) translateX(-40%);"></div>



                <ul class="flex flex-col gap-2" style="transform: rotateZ(-10deg) translateX(-40%);">

                    <li>
                        <button onclick="" class="flex flex-row gap-1 button-lobby-menu" style="font-size: 4rem;">
                            <img src="" class="icon-lobby-menu" style="scale: 160%;">
                            <span>Game Mode</span>
                        </button>
                    </li>

                    <li>
                        <button onclick="" class="flex flex-row gap-1 button-lobby-menu">
                            <img src="" class="icon-lobby-menu">
                            <span>Join online room</span>
                        </button>
                    </li>

                    <li>
                        <button onclick="" class="flex flex-row gap-1 button-lobby-menu">
                            <img src="" class="icon-lobby-menu">
                            <span>Host local game</span>
                        </button>
                    </li>

                    <li>
                        <button onclick="" class="flex flex-row gap-1 button-lobby-menu">
                            <img src="" class="icon-lobby-menu">
                            <span>Host multiplayer game</span>
                        </button>
                    </li>

                    <li>
                        <button onclick="" class="flex flex-row gap-1 button-lobby-menu">
                            <img src="" class="icon-lobby-menu">
                            <span>Join online room</span>
                        </button>
                    </li>

                    <li>
                        <button onclick="" class="flex flex-row gap-1 button-lobby-menu">
                            <img src="" class="icon-lobby-menu">
                            <span>Leaderboard</span>
                        </button>
                    </li>
                    <ul>
            </section>


            <section class="flex flex-col">
                <div class="flex flex-col items-center-y" style="width: 10rem; height: 10rem; background-color: black; border-radius: 0.2rem;"></div>

            </section>





        </section>

    </div>
</body>

</html>
