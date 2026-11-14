<?php
require_once 'validation.php';
require_once 'db.php';


var_dump(validate_answer("Jamaica", "J"));
var_dump(validate_answer("Kingston", "J"));
var_dump(validate_answer("", "J"));
var_dump(validate_answer("Jamaica", "AB"));

echo "<br><br>";
    $categories = ["Country", "City", "Food"];

    $answers = [
        "Country" => "Jamaica",
        "City" => "Jerusalem",
        "Food" => "Jerk Chicken"
    ];

    var_dump(can_player_stop($answers, $categories, "J"));

    $answers["Food"] = "";
    var_dump(can_player_stop($answers, $categories, "J"));

    $answers["Food"] = "Jerk Chicken";
    $answers["City"] = "Kingston";
    var_dump(can_player_stop($answers, $categories, "J"));


    echo "<br><br>";
    var_dump(stop_round($conn, 1, 1, "invalid"));

?>