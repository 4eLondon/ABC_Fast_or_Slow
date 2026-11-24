<?php

// Store all the hints that can be displayed during the game.
$hints = [

    "shared" => "{shared-yellow} means points are split between players because they entered similar words.",
    "enter" => "Tip: You can skip to the next card by pressing Enter.",
    "letter" => "{ready-green} Words must have at least 3 letters and start with the round letter.",
    "wrong" => "{not-ready-red} A red cross means the word was incorrect or too short.",
    "disqualify" => "{disqualified-grey} Click a word to disqualify it. Click again to undo."
];

function drawHint($name)
{
    // Allow the function to access the hints array
    global $hints;

    // Find the hint using the name provided.
    if (isset($hints[$name])) {
        $text = $hints[$name];
    } else {
        $text = "";
    }

    // Find words inside curly brackets.
    // Replace each one with its matching icon image.
    $text = preg_replace(
        '/\{([a-z-]+)\}/',
        "<img class='icon' src='../assets/icons/icon-$1.svg' alt=''>",
        $text
    );

    echo "<h3 class='hint txt-regular-2'>$text</h3>";
}

//Apendix
//AI assistance was used to understand how icons could be integrated into the game using PHP. 
//Since I was unfamiliar with this technique, AI helped explain how icon placeholders,like {shared-yellow}, 
//could be used within the hint text and replaced with the corresponding SVG icon images.
//AI also assisted in understanding the preg_replace() function and how it uses regular expressions 
//to identify text inside curly brackets and replace it with the correct HTML image tag. 