
<?php
require_once __DIR__ . '/validation.php';


echo "<hr><h3>STOP Validation Tests</h3>";

$categories = [
    "Boy", "Girl", "Animal", "Place", "Food",
    "Thing", "Brand", "Job", "Movie"
];

$completeAnswers = [
    "Boy" => "Yohan",
    "Girl" => "Yvonne",
    "Animal" => "Yak",
    "Place" => "Yemen",
    "Food" => "Yam",
    "Thing" => "Yo-yo",
    "Brand" => "Yamaha",
    "Job" => "YouTuber",
    "Movie" => "Yesterday"
];

$missingAnswers = $completeAnswers;
unset($missingAnswers["Food"]);

$invalidAnswers = $completeAnswers;
$invalidAnswers["Food"] = "Apple";

echo "All valid answers: ";
echo can_player_stop($completeAnswers, $categories, "Y")
    ? "PASS" : "FAIL";

echo "<br>Missing answer: ";
echo !can_player_stop($missingAnswers, $categories, "Y")
    ? "PASS" : "FAIL";

echo "<br>Incorrect starting letter: ";
echo !can_player_stop($invalidAnswers, $categories, "Y")
    ? "PASS" : "FAIL";

?>
