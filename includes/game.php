<?php
function normalize_answer(string $playerAnswer): string
{
    // AI assisted only for line 7 the inconv function, the rest is original code
    $normalizedAnswer = trim($playerAnswer);
    $normalizedAnswer = preg_replace('/\s+/u', ' ', $normalizedAnswer);
    $asciiAnswer = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $normalizedAnswer);

    if ($asciiAnswer !== false) {
        $normalizedAnswer = $asciiAnswer;
    }

    return strtolower($normalizedAnswer);
}
