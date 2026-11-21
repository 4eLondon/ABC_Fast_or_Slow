<?php
$pageTitle = 'ABC Fast or Slow';
$categoryColors = ['Boy' => 'blue', 'Girl' => 'red', 'Thing' => 'yel', 'Food' => 'grn', 'Place' => 'plum'];
$exampleAnswers = ['Boy' => 'Mike', 'Girl' => 'Mia', 'Thing' => 'Mug', 'Food' => 'Mango', 'Place' => 'Mexico'];
?>

<!DOCTYPE html>
<html>

<head>
    <title><?= $pageTitle ?></title>
    <link rel="stylesheet" href="src/css/classes.css">
    <link rel="stylesheet" href="src/css/style.css">
</head>

<body>
    <div id="master" class="flex-col g-1 w-page screen-full p-2">
        <section class="crate bg-wht" id="hero">
            <h1 id="hero_title">ABC<br>Fast or Slow</h1>
            <div class="stripes"></div>
        </section>

        <div class="flex-row g-1" id="hero_buttons">
            <a class="crate bg-red" href="pages/lobby.php?m=local">
                <span class="crate-code">L01</span>
                <span class="crate-name">New local game</span>
                <div class="diamond"></div>
            </a>
            <a class="crate bg-blue" href="pages/lobby.php?m=multi">
                <span class="crate-code">M02</span>
                <span class="crate-name">New multiplayer game</span>
                <div class="panels"><i></i><i></i><i></i><i></i><i></i><i></i></div>
            </a>
        </div>

        <section class="crate bg-teal" id="guide">
            <div class="flex-col g-1">
                <h2>How to play</h2>
                <ul>
                    <li>Spin the wheel to pick a letter.</li>
                    <li>Name a boy, girl, thing, food and place that start with it.</li>
                    <li>Spin again every round.</li>
                </ul>
            </div>
            <div id="guide_cards">
                <?php $cardWidth = '7rem';
                $cardLetter = 'M';
                include 'includes/lcard.php'; ?>
                <?php foreach ($categoryColors as $categoryName => $categoryColor) { ?>
                    <div class="card mini c-<?= $categoryColor ?>">
                        <span class="flex-col ele-center-hori card-strip"><?= $categoryName[0] ?></span>
                        <div class="flex-col ele-center g-1 card-body">
                            <span class="card-name"><?= $categoryName ?></span>
                            <span class="card-ans"><?= $exampleAnswers[$categoryName] ?></span>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </section>

        <section id="footer">
            ABC Fast or Slow
            <span> | </span>
            <a href="pages/auth.php?mode=login">Sign in</a>
            <span> | </span>
            <a href="pages/auth.php?mode=register">Create account</a>
        </section>

    </div>
</body>

</html>
