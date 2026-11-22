<?php

// This function checks whether a player's answer is valid.
function validate_answer(string $answer, string $roundLetter): bool
{
    // Remove unnecessary spaces.
    $answer = trim($answer);

    // Check whether the answer is empty.
    if ($answer === "") {
        return false;
    }

    // Check whether the answer is longer than 40 characters.
    if (mb_strlen($answer, 'UTF-8') > 40) {
        return false;
    }

    // Check whether the round letter contains exactly one character.
    if (mb_strlen($roundLetter, 'UTF-8') !== 1) {
        return false;
    }

    // Convert both values to lowercase for comparison.
    $answer = strtolower($answer);
    $roundLetter = strtolower($roundLetter);

    // Check whether the answer starts with the round letter.
    if (substr($answer, 0, 1) !== $roundLetter) {
        return false;
    }

    return true;
}

// This function checks whether a player has answered every category.
function can_player_stop(
    array $answers,
    array $categories,
    string $roundLetter
): bool {

    // Go through every category.
    foreach ($categories as $category) {

        // Check whether an answer exists for that category.
        if (!isset($answers[$category]) || trim($answers[$category]) === "") {
            return false;
        }

        // Check whether the answer starts with the correct letter.
        if (!validate_answer($answers[$category], $roundLetter)) {
            return false;
        }
    }

    // Return true if every category has a valid answer.
    return true;
}

// Apendix

//AI tools were used as a learning and development aid to understand MySQL database structures, SQL queries, unique constraints, prepared statements and PHP-MySQL integration.
//AI assistance was also used to troubleshoot XAMPP MySQL connection issues. 
//The generated suggestions were reviewed and implemented as part of the development process.

//Prompt - How do i save the answers users input. 
//Checking whether a round is active 
//(Prompt) How can I use MySQL to retrieve the round letter, stopped_at and deadline_at columns from a rounds table? 
//Explain how PHP can use these values to prevent players from submitting answers after a round ends.
//Saving and updating answers (prompt) Show me how to use MySQL INSERT INTO with ON DUPLICATE KEY UPDATE so that a player's answer can be updated instead of creating duplicate records.
//Troubleshooting MySQL - Help me troubleshoot the issue using the MySQL error log and Windows Command Prompt without risking my existing databases.
?>
