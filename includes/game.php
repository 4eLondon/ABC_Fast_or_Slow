<?php

// Include the files that contain account information and word-checking functions.
require_once __DIR__ . '/account.php';
require_once __DIR__ . '/words.php';

// Letters that appear more often are repeated in the pool.
// This gives common letters a better chance of being selected.
$pool = "AAAABCCDDEEEEFGHIIIJKLLMNNOOOPQRRSSSTTTUUVWXYYZ";


//updates the players scores 
function updateScores()
{
    // Get the letter being used for the current round.
    $letter = $_SESSION['round_letter'];

    // Remove answers belonging to players who were disqualified.
    $answers = removeDisqualified(
        $_SESSION['answers'],
        $_SESSION['disqualified']
    );

    // Start with the scores players had before this round.
    $_SESSION['scores'] = $_SESSION['base_scores'];

    // Calculate the points each player earned this round.
    $roundPoints = scoreRound($answers, $letter);

    foreach ($roundPoints as $player => $points) {

        // Add this round's points to the player's previous score.
        if (isset($_SESSION['scores'][$player])) {
            $_SESSION['scores'][$player] += $points;
        } else {
            $_SESSION['scores'][$player] = $points;
        }
    }

    // Check each answer and determine whether it is right, shared or wrong.
    $_SESSION['results'] = wordResults($answers, $letter);

    // Mark answers belonging to disqualified players as "out".
    foreach ($_SESSION['disqualified'] as $key) {

        // Separate the player number and category number.
        $parts = explode("-", $key);

        $player = $parts[0];
        $category = $parts[1];

        $_SESSION['results'][$player][$category] = "out";
    }
}

//game speed and letter
//runs when the game requests the selected speed.
if (isset($_GET['speed'])) {

    // Save the selected game speed.
    if ($_GET['speed'] === "slow") {
        $_SESSION['speed'] = "slow";
    } else {
        $_SESSION['speed'] = "fast";
    }

    // Only select a new letter if the player requests a reroll.
    if (isset($_GET['reroll'])) {

        // Generate a random position in the letter pool.
        $randomPosition = random_int(0, strlen($pool) - 1);

        // Select the letter at that position.
        $_SESSION['letter'] = $pool[$randomPosition];
    }

    // Send the selected letter back to the game page.
    echo $_SESSION['letter'] ?? "A";
    exit;
}

//add a player 
// Check whether the add player button was clicked.
if (isset($_POST['add_player'])) {

    // Get the player's name and remove unnecessary spaces.
    $name = trim($_POST['field_username']);

    // Check how many temporary players are currently in the lobby.
    $players = $_SESSION['players'] ?? [];

    //name cannot be empty and the lobby allows a maximum of 5 players.
    if ($name !== "" && count($players) <= 5) {

        // Add the new player to the session.
        $_SESSION['players'][] = $name;
    }

    // Return to the lobby after adding the player.
    header("Location: ../pages/lobby.php");
    exit;
}

//remove a player 

// Check whether a remove player request was sent.
if (isset($_GET['remove'])) {

    // Get the current list of temporary players.
    $players = $_SESSION['players'] ?? [];

    //convert the player number from the URL into an integer.
    $playerIndex = (int) $_GET['remove'];

    unset($players[$playerIndex]);

    // Reset the array numbers so they start from zero again.
    $_SESSION['players'] = array_values($players);

    header("Location: ../pages/lobby.php");
    exit;
}


//starts the game
// Check whether the host clicked the start game button.
if (isset($_POST['start_game'])) {

    // The host always takes the first turn.
    $_SESSION['turn'] = 0;

    // Start at round one.
    $_SESSION['round'] = 1;

    // Clear the answers from previous games.
    $_SESSION['answers'] = [];

    // Clear the previous scores.
    $_SESSION['scores'] = [];
    $_SESSION['base_scores'] = [];

    // Clear previous answer results.
    $_SESSION['results'] = [];

    // Clear the list of disqualified answers.
    $_SESSION['disqualified'] = [];

    header("Location: ../pages/game.php");
    exit;
}

//submits answers  
// This runs when a player submits their answers or their time runs out.
if (isset($_POST['submit_turn'])) {

    // Find out which player's turn it is.
    $turn = $_SESSION['turn'] ?? 0;

    // Save the answers submitted by this player.
    $_SESSION['answers'][$turn] = $_POST['answer'] ?? [];

    // Move to the next player's turn.
    $_SESSION['turn'] = $turn + 1;

    // Count the host and all temporary players.
    $totalPlayers = 1 + count($_SESSION['players'] ?? []);

    // Check whether every player has completed their turn.
    if ($_SESSION['turn'] >= $totalPlayers) {

        // Save the letter used for this round.
        $_SESSION['round_letter'] = $_SESSION['letter'] ?? "A";

        // Save the scores before calculating this round's points.
        $_SESSION['base_scores'] = $_SESSION['scores'] ?? [];

        // Clear any disqualifications from the previous round.
        $_SESSION['disqualified'] = [];

        // Record the time the summary page begins.
        $_SESSION['summary_start'] = time();

        updateScores();

        // Select a new letter for the next round.
        $randomPosition = random_int(0, strlen($pool) - 1);
        $_SESSION['letter'] = $pool[$randomPosition];

        header("Location: ../pages/summary.php");

    } else {

        header("Location: ../pages/game.php");
    }

    exit;
}


//starts next round

// Check whether the next round button was clicked.
if (isset($_POST['next_round'])) {

    //increase the round number
    $_SESSION['round'] = ($_SESSION['round'] ?? 1) + 1;

    //the host goes first again.
    $_SESSION['turn'] = 0;

    $_SESSION['answers'] = [];

    $_SESSION['results'] = [];

    $_SESSION['disqualified'] = [];

    //return to the game page.
    header("Location: ../pages/game.php");
    exit;
}


//disqualify or restore an aswer

//runs when someone clicks an answer on the summary page.
if (isset($_GET['disqualify'])) {

    // Get the player number and category number from the URL.
    $key = $_GET['disqualify'];

    //separate the two numbers using the hyphen.
    $parts = explode("-", $key);

    // Make sure the key contains exactly two whole numbers.
    if (
        count($parts) === 2 &&
        ctype_digit($parts[0]) &&
        ctype_digit($parts[1])
    ) {

        //get the current list of disqualified answers.
        $list = $_SESSION['disqualified'] ?? [];

        //check whether this answer has already been disqualified.
        if (in_array($key, $list)) {

            //create a new list without the selected answer.
            $updatedList = [];

            foreach ($list as $item) {

                //keep every answer except the selected one.
                if ($item !== $key) {
                    $updatedList[] = $item;
                }
            }

            $list = $updatedList;

        } else {

            //if the answer is not disqualified, add it to the list.
            $list[] = $key;
        }

        //save the updated disqualification list.
        $_SESSION['disqualified'] = $list;

        updateScores();
    }

    exit;
}

//get the current round number.
$round = $_SESSION['round'] ?? 1;

//get the current letter.
$letter = $_SESSION['letter'] ?? "A";

// Decide how many seconds players get to answer.
if (($_SESSION['speed'] ?? "fast") === "slow") {
    $seconds = 120;
} else {
    $seconds = 60;
}

$categories = [
    ["Boy", "male"],
    ["Girl", "female"],
    ["Place", "place"],
    ["Food", "food"],
    ["Thing", "thing"],
    ["Animal", "animal"],
];

//players info

//get the host's username.
$host = $_SESSION['username'] ?? "Player 1";

//get the temporary players.
$otherPlayers = $_SESSION['players'] ?? [];

//combine the two into one list 
$names = array_merge([$host], $otherPlayers);

//get the current player's turn number.
$turn = $_SESSION['turn'] ?? 0;

//find the name of the player whose turn it is.
if (isset($names[$turn])) {
    $player = $names[$turn];
} else {
    $player = $names[0];
}

// Give each player a different background number.
// The background numbers go from 1 to 6.
$background = ($turn % 6) + 1;


$speed = $_SESSION['speed'] ?? "fast";

$scores = $_SESSION['scores'] ?? [];

$results = $_SESSION['results'] ?? [];

$answers = $_SESSION['answers'] ?? [];


// Find the player with the highest score.
$leader = 0;

if (!empty($scores)) {

    // Find the highest score.
    $highestScore = max($scores);

    // Find the player position with that score.
    $leader = array_search($highestScore, $scores);
}

// The summary page automatically moves to the next round after 20 seconds

// Find out how much time has passed since the summary started.
$elapsedTime = time() - ($_SESSION['summary_start'] ?? time());

// Calculate the remaining time.
$remaining = 20 - $elapsedTime;

// Make sure the remaining time never goes below one second.
if ($remaining < 1) {
    $remaining = 1;
}

// Connect each answer result to its matching icon.
$resultIcons = [
    "right" => "ready-green",
    "shared" => "shared-yellow",
    "wrong" => "not-ready-red",
    "out" => "disqualified-grey",
];


//Apendix 
//The areas where AI assisted: 
//Assistance was used to understand how to calculate each player's points, 
//combine current-round points with previous scores and update the leaderboard.
// AI helped explain PHP array functions such as array_merge(), array_values() and array_search() 
//Assistance was used to improve the logic for adding and removing players, managing player turns
//and keeping track of player information using PHP sessions.
//AI helped simplify the process of disqualifying and restoring answers

?>
