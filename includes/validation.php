<?php

    require_once __DIR__ . '/game.php';

    //checks whether a player's answer is valid 
    function validate_answer(string $answer, string $roundLetter ): bool {

        // Remove unnecessary spaces
            $answer = trim($answer);

            // Check whether the answer is empty.
            if ($answer === "") {
                return false;
            }

        // Check whether the answer is longer than 40 characters.
            if (mb_strlen($answer, 'UTF-8') > 40) {
                return false;
            }

        // Check whether the round letter contains exactly one character.
        if (mb_strlen($roundLetter, 'UTF-8') !== 1) {
            return false;
        }

        // Convert both values to lowercase for comparison.
        $answer = strtolower($answer);
        $roundLetter = strtolower($roundLetter);

        // Check whether the answer starts with the round letter.
        if (substr($answer, 0, 1) !== $roundLetter) {
            return false;
        }

        return true; 
    }

    // checks whether a player has answered every category before stopping.
    function can_player_stop(array $answers, array $categories, string $roundLetter): bool {

        foreach ($categories as $category){

            //checks if the answer exists for that category 
            if (!isset($answers[$category]) || trim($answers[$category]) === "") {
                return false;
                }
            }

            //checks if the answer starts with the correct letter 
            if (!validate_answer($answers[$category], $roundLetter)) {
                return false;
            }

            return true;
    }

    //records who ended the round
    function stop_round(mysqli $conn, int $roundId, ?int $playerId, string $reason): bool
{
    // List the valid reasons for ending a round.
        $allowedReasons = ["first_finished", "timer", "host"];

    // Check whether the reason is valid
        if (!in_array($reason, $allowedReasons, true)) {
            return false;
    }

    // Check whether the player ID matches the end reason.
    if ($reason === "timer" && $playerId !== null) {
        return false;
    }

    if ($reason !== "timer" && $playerId === null) {
        return false;
    }

    // update the round information.
    $sql = "UPDATE rounds 
        SET stopped_at = NOW(), stopped_by = ?, end_reason = ?
        WHERE id = ? AND stopped_at IS NULL";

    // prepare the SQL statement
    $stmt = mysqli_prepare($conn, $sql);

    // Check whether the statement was prepared successfully.
    if (!$stmt) {
        return false;
    }

    // Connect the PHP variables to the SQL placeholders.
    mysqli_stmt_bind_param($stmt, "isi", $playerId, $reason, $roundId);

    // Execute the prepared statement.
    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return false;
    }

    // Check whether a round was actually updated.
    $updated = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);

    // Return true if one round was updated.
    return $updated === 1;  
}

    // saves a player's answer.
    function save_answer(
        mysqli $conn,
        int $roundId,
        int $playerId,
        int $categoryId,
        string $answer
    ): bool {

        // Get the round letter from the database.
        $sql = "SELECT letter FROM rounds WHERE id = ?";

        $stmt = mysqli_prepare($conn, $sql);

        // Check whether the query was prepared successfully.
        if (!$stmt) {
            return false;
        }

        // Connect the round ID to the SQL placeholder.
        mysqli_stmt_bind_param($stmt, "i", $roundId);
        mysqli_stmt_execute($stmt);

        // Get the result from the database.
        $result = mysqli_stmt_get_result($stmt);

        // Fetch the round information.
        $round = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        // Check whether the round exists.
        if (!$round) {
            return false;
        }

        $roundLetter = $round['letter'];

        // Check whether the answer is valid.
        if (!validate_answer($answer, $roundLetter)) {
            return false;
     
        }
}



?>
