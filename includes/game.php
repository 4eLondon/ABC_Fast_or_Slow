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
