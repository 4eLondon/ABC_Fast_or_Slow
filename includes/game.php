<?php

// Normalize a player's answer
function normalize_answer(string $answer): string
{
    // Remove spaces at the beginning and end
    $answer = trim($answer);

    // Replace multiple spaces with one space
    $answer = preg_replace('/\s+/u', ' ', $answer);

    // Remove accents from characters
    $convertedAnswer = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $answer);

    if ($convertedAnswer !== false) {
        $answer = $convertedAnswer;
    }

    // Convert the answer to lowercase
    $answer = strtolower($answer);

    return $answer;
}


// Get all letters that have not been used
function available_letters(array $usedLetters): array
{
    // Create an alphabet from A to Z
    $alphabet = range('A', 'Z');

    // Convert used letters to uppercase
    $usedLetters = array_map('strtoupper', $usedLetters);

    // Remove used letters from the alphabet
    $available = array_diff($alphabet, $usedLetters);

    // Return the remaining letters as an indexed array
    return array_values($available);
}


// Select a random letter from the available letters
function select_random_letter(array $availableLetters): ?string
{
    // Check if there are any letters available
    if (empty($availableLetters)) {
        return null;
    }

    // Choose a random position in the array
    $randomIndex = random_int(0, count($availableLetters) - 1);

    // Return the selected letter
    return $availableLetters[$randomIndex];
}


// Check whether a player is allowed to stop the round
function can_stop_round(
    int $playerId,
    int $callerId,
    int $hostId
): bool {

    // Only the caller or host can stop the round
    if ($playerId === $callerId || $playerId === $hostId) {
        return true;
    }

    return false;
}


// Check whether the round should automatically stop
function should_auto_stop_round(int $elapsedSeconds): bool
{
    // Automatically stop after 15 seconds
    if ($elapsedSeconds >= 15) {
        return true;
    }

    return false;
}
