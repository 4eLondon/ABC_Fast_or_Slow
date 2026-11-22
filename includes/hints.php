<?php
// All the hints. Use {icon-name} to show an icon inside a hint.
$hints = [
    "shared" => "{shared-yellow} means points are split between you due to typing a simular word.",
    "enter"  => "Tip: You can skip to the next card by pressing enter",
    "letter" => "{ready-green} Words must be 3+ letters and start with the round letter.",
    "wrong"  => "{not-ready-red} A red cross means the word was wrong or too short.",
    "disqualify" => "{disqualified-grey} Click a word to disqualify it. Click again to undo.",
];

// Show one hint by name
function drawHint($name)
{
    global $hints;

    $text = $hints[$name] ?? "";

    // Swap {icon-name} for the matching icon image
    $text = preg_replace(
        '/\{([a-z-]+)\}/',
        "<img class='icon' src='../assets/icons/icon-$1.svg'>",
        $text
    );

    echo "<h3 class='hint txt-regular-2'>$text</h3>";
}
