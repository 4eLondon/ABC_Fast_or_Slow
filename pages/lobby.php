<?php
require __DIR__ . '/../includes/components.php';
$pageTitle = "Local Lobby";
$username = $_SESSION['username'] ?? "Player 1";
$players = $_SESSION['players'] ?? [];
$total = count($players) + 1;
$letter = $_SESSION['letter'] ?? "A";
$rerollSpeed = $_SESSION['speed'] ?? "fast";
?>

<!DOCTYPE HTML>
<html>

<head>
    <title><?= $pageTitle ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../src/css/classes.css">
    <link rel="stylesheet" href="../src/css/master.css">
    <link rel="stylesheet" href="../src/css/lobby.css">
    <?php drawFavicon() ?>
</head>

<body>


    <div id="master" class="fullscreen flex-col items-center">

        <!-------------------------------------------------------|
             x x x x x x x x [ Main Section ] x x x x x x x x x 
        |-------------------------------------------------------->

        <section id="lobbyMenu" class="fill flex-row items-center">
            <div id="hero" class="txt-condition flex-col gap-1 items-center">
                <h1 class="hero-text" style="margin: 0;">Local Lobby</h1>
                <form method="post" action="../includes/game.php">
                    <button type="submit" name="start_game" class="button-start-game flex-row items-center gap-1" <?= $total < 2 ? "disabled" : "" ?>>
                        <img src="../assets/icons/icon-play-yellow.svg" class="button-start-game-text icon">
                        <h2 class="txt-regular-4 button-start-game-text accent-2-txt" style="color:var(--a2);"><?= $total < 2 ? "Need at least 2 players" : "Start game" ?></h2>
                    </button>
                </form>
            </div>

            <div id="card" class="flex-col gap-1">
                <span class="flex-row items-center gap-1">
                    <h2 class="txt-condition" id="cardGuide">Click to reroll</h2>
                    <button id="speedButton" class="button-reroll-small flex-row items-center txt-regular-4" style="gap:0.3rem">
                        <img id="speedIcon" src="../assets/icons/icon-<?= $rerollSpeed ?>-yellow.svg" class="icon">
                        <h3 id="speedText" class="accent-2-txt" style="margin: 0;"><?= $rerollSpeed ?></h3>
                    </button>
                </span>
                <button id="cardButton" class="card-display txt-condition accent-2-txt flex-col items-center">
                    <h1 id="letter"><?= $letter ?></h1>
                </button>
            </div>



            <div id="players">

                <ul class="txt-regular-4 flex-col gap-2">

                    <li>
                        <button class="button-new-player flex-row gap-1 items-center">
                            <img src="../assets/icons/icon-crown-yellow.svg" class="icon">
                            <h2 class="txt-regular-4" id="hostPlayerID"><?= htmlspecialchars($username) ?></h2>
                        </button>
                    </li>


                    <?php foreach ($players as $i => $player): ?>
                        <li>
                            <button class="player-button button-new-player flex-row gap-1 items-center" data-index="<?= $i ?>">
                                <img src="../assets/icons/icon-player-yellow.svg" class="icon">
                                <h2 class="txt-regular-4"><?= htmlspecialchars($player) ?></h2>
                            </button>
                        </li>
                    <?php endforeach; ?>



                    <?php if ($total >= 6): ?>
                        <li>
                            <button class="button-new-player flex-row gap-1 items-center" disabled>
                                <h2 class="txt-regular-4">Lobby is full</h2>
                            </button>
                        </li>
                    <?php else: ?>

                        <li>
                            <button id="addButton" class="button-new-player flex-row gap-1 items-center">
                                <img src="../assets/icons/icon-add-light.svg" id="addIcon">
                                <h2 class="txt-regular-4">New local player</h2>
                            </button>
                        </li>

                        <li id="addNewPlayer" class="hidden">
                            <form method="post" action="../includes/game.php">
                                <ul>
                                    <li class="flex-row gap-1">
                                        <input type="text" class="input-text-new-player txt-regular-4" name="field_username" maxlength="20" placeholder="New player name" required>
                                        <button type="submit" name="add_player" class="button-new-player-alt">OK</button>
                                    </li>
                                </ul>
                            </form>
                        </li>
                    <?php endif; ?>

                </ul>
            </div>



        </section>



        <!-------------------------------------------------------|
             x x x x x x x x [ Decals Section ] x x x x x x x x x 
        |-------------------------------------------------------->
        <?php drawDecals(2); ?>



    </div>

    <script src="../src/js/lobby.js"></script>
</body>

</html>
