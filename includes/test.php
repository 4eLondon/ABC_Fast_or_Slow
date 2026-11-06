<?php

require_once 'includes/validation.php';

$roundLetter = "B";

$answers = [
    "Banana",
    "Blue Mountain",
    "Apple",
    "",
    "  Banana  "
];

foreach ($answers as $answer) {

    if (validate_answer($answer, $roundLetter)) {
        echo $answer . " - Valid<br>";
    } else {
        echo $answer . " - Invalid<br>";
    }

}

?>
