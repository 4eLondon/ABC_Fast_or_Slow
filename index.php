<?php
$pageTitle = "Page";
?>

<!DOCTYPE HTML>
<html>

<head>
    <title><?= $pageTitle ?></title>
    <link rel="stylesheet" href="src/css/master.css">
    <link rel="stylesheet" href="src/css/classes.css">
</head>

<body>

    <div id="master" class="fullscreen flex-col">

        <section id="menu">

            <h1 id="menuText_A">
                <span id="MenuText_b">ABC</span>
                Fast or slow
            </h1>

            <div id="menuOptions">
                <ul class="flex-col gap-1">
                    <li><button class="button-classic">New local game</button></li>
                    <li><button class="button-classic">Join online game</button></li>
                    <li><button class="button-classic">Host online room</button></li>
                </ul>
            </div>

        </section>

    </div>
</body>

</html>
