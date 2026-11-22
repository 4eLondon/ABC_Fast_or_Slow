<?php
require_once __DIR__ . '/account.php';
require_once __DIR__ . '/words.php';

// Common letters appear more than once, so they are picked more often
$pool = "AAAABCCDDEEEEFGHIIIJKLLMNNOOOPQRRSSSTTTUUVWXYYZ";

// Work out this round's results and the running totals
function updateScores()
{
    $letter = $_SESSION['round_letter'];
    $answers = removeDisqualified($_SESSION['answers'], $_SESSION['disqualified']);

    // Totals before this round, plus this round's points
    $_SESSION['scores'] = $_SESSION['base_scores'];
    foreach (scoreRound($answers, $letter) as $index => $points) {
        $_SESSION['scores'][$index] = ($_SESSION['scores'][$index] ?? 0) + $points;
    }

    $_SESSION['results'] = wordResults($answers, $letter);

    // Mark disqualified words
    foreach ($_SESSION['disqualified'] as $key) {
        [$player, $category] = explode("-", $key);
        $_SESSION['results'][$player][$category] = "out";
    }
}

// Lobby and summary fetch calls: save the speed and reroll the letter
if (isset($_GET['speed'])) {
    if ($_GET['speed'] === "slow") {
        $_SESSION['speed'] = "slow";
    } else {
        $_SESSION['speed'] = "fast";
    }

    // Pick a new letter only when rerolling
    if (isset($_GET['reroll'])) {
        $_SESSION['letter'] = $pool[random_int(0, strlen($pool) - 1)];
    }

    echo $_SESSION['letter'] ?? "A";
    exit;
}

// Add a temporary player
if (isset($_POST['add_player'])) {
    $name = trim($_POST['field_username']);
    $players = $_SESSION['players'] ?? [];

    if ($name !== "" && count($players) < 5) {
        $_SESSION['players'][] = $name;
    }

    header("Location: ../pages/lobby.php");
    exit;
}

// Remove a temporary player
if (isset($_GET['remove'])) {
    $players = $_SESSION['players'] ?? [];
    unset($players[(int) $_GET['remove']]);
    $_SESSION['players'] = array_values($players);

    header("Location: ../pages/lobby.php");
    exit;
}

// Start the game, the host (turn 0) always goes first
if (isset($_POST['start_game'])) {
    $_SESSION['turn'] = 0;
    $_SESSION['round'] = 1;
    $_SESSION['answers'] = [];
    $_SESSION['scores'] = [];
    $_SESSION['base_scores'] = [];
    $_SESSION['results'] = [];
    $_SESSION['disqualified'] = [];

    header("Location: ../pages/game.php");
    exit;
}

// A player locked in or ran out of time: save their answers, then next turn
if (isset($_POST['submit_turn'])) {
    $turn = $_SESSION['turn'] ?? 0;
    $_SESSION['answers'][$turn] = $_POST['answer'] ?? [];
    $_SESSION['turn'] = $turn + 1;

    $total = 1 + count($_SESSION['players'] ?? []);

    if ($_SESSION['turn'] >= $total) {
        // Everyone has played: work out the points
        $_SESSION['round_letter'] = $_SESSION['letter'] ?? "A";
        $_SESSION['base_scores'] = $_SESSION['scores'] ?? [];
        $_SESSION['disqualified'] = [];
        $_SESSION['summary_start'] = time();
        updateScores();

        // New letter for the next round
        $_SESSION['letter'] = $pool[random_int(0, strlen($pool) - 1)];

        header("Location: ../pages/summary.php");
    } else {
        header("Location: ../pages/game.php");
    }
    exit;
}

// Start the next round, the host goes first again
if (isset($_POST['next_round'])) {
    $_SESSION['round'] = ($_SESSION['round'] ?? 1) + 1;
    $_SESSION['turn'] = 0;
    $_SESSION['answers'] = [];
    $_SESSION['results'] = [];
    $_SESSION['disqualified'] = [];

    header("Location: ../pages/game.php");
    exit;
}

// Click a word on the summary to disqualify it (click again to undo)
if (isset($_GET['disqualify'])) {
    $key = $_GET['disqualify'];

    if (preg_match('/^\d+-\d+$/', $key)) {
        $list = $_SESSION['disqualified'] ?? [];

        if (in_array($key, $list)) {
            $list = array_diff($list, [$key]);
        } else {
            $list[] = $key;
        }

        $_SESSION['disqualified'] = array_values($list);
        updateScores();
    }

    exit;
}

// Values used by the game page
$round = $_SESSION['round'] ?? 1;
$letter = $_SESSION['letter'] ?? "A";
$seconds = ($_SESSION['speed'] ?? "fast") === "slow" ? 120 : 60;

// The six cards: label and icon name
$categories = [
    ["Boy", "male"],
    ["Girl", "female"],
    ["Place", "place"],
    ["Food", "food"],
    ["Thing", "thing"],
    ["Animal", "animal"],
];

// The host is first in the list, then the temporary players
$names = array_merge([$_SESSION['username'] ?? "Player 1"], $_SESSION['players'] ?? []);
$turn = $_SESSION['turn'] ?? 0;
$player = $names[$turn] ?? $names[0];

// Each player gets a different background (bg1 to bg6)
$background = ($turn % 6) + 1;

// Values used by the summary page
$speed = $_SESSION['speed'] ?? "fast";
$scores = $_SESSION['scores'] ?? [];
$results = $_SESSION['results'] ?? [];
$answers = $_SESSION['answers'] ?? [];
$leader = $scores ? array_search(max($scores), $scores) : 0;

// Seconds left before the next round starts by itself
$remaining = max(1, 20 - (time() - ($_SESSION['summary_start'] ?? time())));

$resultIcons = [
    "right" => "ready-green",
    "shared" => "shared-yellow",
    "wrong" => "not-ready-red",
    "out" => "disqualified-grey",
];
