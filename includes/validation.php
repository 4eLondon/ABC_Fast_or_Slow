<?php
require_once __DIR__ . '/game.php';

function validate_answer(string $playerAnswer, string $requiredStartingLetter): bool
{
    
    // Normalize both values so case, accents, and extra spaces don't affect the check.
    $normalizedAnswer = normalize_answer($playerAnswer);
    $normalizedStartingLetter = normalize_answer($requiredStartingLetter);

    return $normalizedAnswer !== ''
        && strlen($normalizedAnswer) <= 40
        && strlen($normalizedStartingLetter) === 1
        && substr($normalizedAnswer, 0, 1) === $normalizedStartingLetter;
}
