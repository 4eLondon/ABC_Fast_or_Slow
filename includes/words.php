<?php

//check if word exists
function realWord($word)
{
    // Keep the dictionary in memory so it does not
    // have to be loaded again every time this function runs.
    static $words = null;

    // Load the dictionary the first time the function is called.
    if ($words === null) {

        // Read each word from dictionary.txt.
        $wordList = file(
            __DIR__ . '/dictionary.txt',
            FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
        );

        // Use the words as array keys to make searching easier.
        $words = array_flip($wordList);
    }

    // Check whether the complete word exists in the dictionary.
    if (isset($words[$word])) {
        return true;
    }

    // Also check the word without its last letter.
    // This allows some plural words ending in "s" to pass.
    $shortWord = substr($word, 0, -1);

    if (isset($words[$shortWord])) {
        return true;
    }

    return false;
}

//check if id is valid 
function isWord($word, $letter, $category)
{
    // Remove extra spaces and convert the answer to lowercase.
    $word = strtolower(trim($word));

    // A valid answer must contain at least 3 letters.
    if (strlen($word) < 3) {
        return false;
    }

    if ($word[0] !== strtolower($letter)) {
        return false;
    }

    // Food, Thing and Animal categories must contain real words.
    // Category numbers 3, 4 and 5 represent these categories.
    if ($category >= 3) {

        // Check whether the answer exists in the dictionary.
        if (!realWord($word)) {
            return false;
        }
    }

    return true;
}

//calculate each players score 
function scoreRound($answers, $letter)
{
    // Create an empty array to store each player's points.
    $scores = [];

    // Give every player an initial score of zero.
    foreach ($answers as $player => $list) {
        $scores[$player] = 0;
    }

    for ($category = 0; $category < 6; $category++) {

        // Store how many times each valid word was entered.
        $counts = [];

        // Go through every player's answer in this category.
        foreach ($answers as $player => $list) {

            // Get the answer, remove extra spaces and use lowercase.
            // If the player left it blank, use an empty string.
            $word = strtolower(trim($list[$category] ?? ""));

            // Only count answers that are valid.
            if (isWord($word, $letter, $category)) {

                // Check whether the word already has a count.
                if (isset($counts[$word])) {
                    $counts[$word] = $counts[$word] + 1;
                } else {
                    $counts[$word] = 1;
                }
            }
        }

        // Go through the players again to award their points.
        foreach ($answers as $player => $list) {

            // Get the player's answer for this category.
            $word = strtolower(trim($list[$category] ?? ""));

            // Only award points for valid answers.
            if (isWord($word, $letter, $category)) {

                $points = 100 / $counts[$word];

                // Add the points to the player's total.
                $scores[$player] += (int) $points;
            }
        }
    }

    // Return the final scores for all players.
    return $scores;
}

//check results before each player answers
function wordResults($answers, $letter)
{
    // Store the result of each player's answer.
    $results = [];

    // Check all 6 game categories.
    for ($category = 0; $category < 6; $category++) {

        // Count how many players entered each valid word.
        $counts = [];

        foreach ($answers as $player => $list) {

            // Get the answer and convert it to lowercase.
            $word = strtolower(trim($list[$category] ?? ""));

            // Only count valid words.
            if (isWord($word, $letter, $category)) {

                // Increase the count if the word already exists.
                if (isset($counts[$word])) {
                    $counts[$word] = $counts[$word] + 1;
                } else {
                    $counts[$word] = 1;
                }
            }
        }

        // Check each player's answer and assign a result.
        foreach ($answers as $player => $list) {

            $word = strtolower(trim($list[$category] ?? ""));

            // If the answer is invalid, mark it as wrong.
            if (!isWord($word, $letter, $category)) {

                $results[$player][] = "wrong";

            // If more than one player entered the same word,
            // mark the answer as shared.
            } elseif ($counts[$word] > 1) {

                $results[$player][] = "shared";

            // Otherwise, the answer is correct and unique.
            } else {

                $results[$player][] = "right";
            }
        }
    }

    // Return all the players' answer results.
    return $results;
}

function removeDisqualified($answers, $disqualified)
{
    // Go through every answer that has been disqualified.
    foreach ($disqualified as $key) {

        // Separate the player number and category number.
        $parts = explode("-", $key);

        $player = $parts[0];
        $category = $parts[1];

        // Replace the disqualified answer with an empty value.
        $answers[$player][$category] = "";
    }

    // Return the updated answers without the disqualified words.
    return $answers;
}

//Apendix
// AI helped explain unfamiliar PHP functions and techniques, including array_flip()
//Additionally, assistance was provided in simplifying the conditional statements used to check answers 
// and assign the results "right", "shared" or "wrong".

?>
