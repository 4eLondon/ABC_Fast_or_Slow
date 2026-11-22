<?php
// True if the word is in the word list (also tries it without a trailing "s")
function realWord($word)
{
    static $words = null;

    if ($words === null) {
        $words = array_flip(file(__DIR__ . '/dictionary.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
    }

    return isset($words[$word]) || isset($words[substr($word, 0, -1)]);
}

// A word counts if it is at least 3 letters, starts with the round letter,
// and (for Food, Thing and Animal) is a real word
function isWord($word, $letter, $category)
{
    $word = strtolower(trim($word));

    if (strlen($word) < 3) {
        return false;
    }

    if ($word[0] !== strtolower($letter)) {
        return false;
    }

    // Categories 3, 4 and 5 are Food, Thing and Animal
    if ($category >= 3 && !realWord($word)) {
        return false;
    }

    return true;
}

// 100 points per valid word, split between players who wrote the same word
function scoreRound($answers, $letter)
{
    $scores = [];

    foreach ($answers as $player => $list) {
        $scores[$player] = 0;
    }

    // 6 categories
    for ($category = 0; $category < 6; $category++) {

        // Count how many players used each word
        $counts = [];
        foreach ($answers as $player => $list) {
            $word = strtolower(trim($list[$category] ?? ""));
            if (isWord($word, $letter, $category)) {
                $counts[$word] = ($counts[$word] ?? 0) + 1;
            }
        }

        // Give out the points
        foreach ($answers as $player => $list) {
            $word = strtolower(trim($list[$category] ?? ""));
            if (isWord($word, $letter, $category)) {
                $scores[$player] += (int) (100 / $counts[$word]);
            }
        }
    }

    return $scores;
}

// For each player: "right", "shared" or "wrong" for each of the 6 categories
function wordResults($answers, $letter)
{
    $results = [];

    for ($category = 0; $category < 6; $category++) {

        // Count how many players used each word
        $counts = [];
        foreach ($answers as $player => $list) {
            $word = strtolower(trim($list[$category] ?? ""));
            if (isWord($word, $letter, $category)) {
                $counts[$word] = ($counts[$word] ?? 0) + 1;
            }
        }

        // Mark each player's word
        foreach ($answers as $player => $list) {
            $word = strtolower(trim($list[$category] ?? ""));

            if (!isWord($word, $letter, $category)) {
                $results[$player][] = "wrong";
            } elseif ($counts[$word] > 1) {
                $results[$player][] = "shared";
            } else {
                $results[$player][] = "right";
            }
        }
    }

    return $results;
}


// Blank out disqualified words. Keys look like "player-category", e.g. "1-3"
function removeDisqualified($answers, $disqualified)
{
    foreach ($disqualified as $key) {
        [$player, $category] = explode("-", $key);
        $answers[$player][$category] = "";
    }

    return $answers;
}
