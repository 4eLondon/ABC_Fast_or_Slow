<?php

// Load the normalize_answer() function
require_once __DIR__ . '/game.php';

// Check whether an answer follows the game rules
function validate_answer(string $answer, string $roundLetter): bool
{
    // Normalize the player's answer
    $answer = normalize_answer($answer);

    // Normalize the round letter
    $roundLetter = normalize_answer($roundLetter);

    // Check if the answer is empty
    if ($answer === "") {
        return false;
    }

    // Check if the answer is longer than 40 characters
    if (strlen($answer) > 40) {
        return false;
    }

    // Check if the round letter is exactly one character
    if (strlen($roundLetter) !== 1) {
        return false;
    }

    // Check if the answer starts with the round letter
    if (substr($answer, 0, 1) !== $roundLetter) {
        return false;
    }

    // All validation checks passed
    return true;
}

?>
