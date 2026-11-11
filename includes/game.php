<?php

    //removes unnecessary spaces, converts accented characters and changes the text to lowercase.
    function normalize_answer(string $answer): string
    {
        // Remove extra spaces from the beginning and end of the answer.
        $answer = trim($answer);

        // Replace multiple spaces between words with a single space.
        $answer = preg_replace('/\s+/u', ' ', $answer);

        // Convert accented characters into their closest regular English characters.
        // eg. Café" becomes "Cafe 
        $convertedAnswer = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $answer);

        // Check whether the conversion was successful. if successful, replace the original answer with the converted version.
        if ($convertedAnswer !== false) {
            $answer = $convertedAnswer;
        }

        // Convert all letters to lowercase so answers are not treated differently
        $answer = strtolower($answer);

        // Return the cleaned and standardized answer.
        return $answer;
    }

    // This function finds all letters from A to Z that have not yet been used.
    // It receives an array containing the letters already used in the game.
    function available_letters(array $usedLetters): array
    {
        // Create an array containing every letter from A to Z.
        $alphabet = range('A', 'Z');

        // Convert all previously used letters to uppercase ensuring that lowercase and uppercase letters are treated equally.
        $usedLetters = array_map('strtoupper', $usedLetters);

        // Compare the alphabet with the used letters and remove any letters already used
        $available = array_diff($alphabet, $usedLetters);

        // Return the remaining letters as a normal indexed array.
        return array_values($available);
    }

    // This function randomly selects one letter from the letters that are available.
    // It returns the selected letter or null if there are no letters left.
    function select_random_letter(array $availableLetters): ?string
    {
        // Check whether the available letters array is empty. if none are left it returns null
        if (empty($availableLetters)) {
            return null;
        }

        // Generate a random number between 0 and the last position in the array.
        // This number represents the position of the letter that will be selected.
        $randomIndex = random_int(0, count($availableLetters) - 1);

        // Use the random position to retrieve and return the selected letter.
        return $availableLetters[$randomIndex];
    }

    // This function checks whether a player has permission to stop the current round.
    function can_stop_round(
        int $playerId,
        int $callerId,
        int $hostId
    ): bool {

        // Compare the player's ID with the caller's ID and the host's ID.
        // The round can only be stopped by the player who called STOP or the room host.
        if ($playerId === $callerId || $playerId === $hostId) {
            return true;
        }

        // If the player is neither the caller nor the host, deny permission.
        return false;
    }

    // This function checks whether the round should automatically stop
    // based on how many seconds have passed.
    function should_auto_stop_round(int $elapsedSeconds): bool
    {
        // Check whether 20 seconds or more have passed.
        // If the time limit has been reached, return true to indicate that the round should stop.
        if ($elapsedSeconds >= 20) {
            return true;
        }

        // If less than 20 seconds have passed the round continues 
        return false;
    }

    function create_round(
    mysqli $conn,
    int $roomId,
    int $callerId,
    string $speed
): bool {

    // Check whether the selected speed is valid.
    if ($speed !== 'fast' && $speed !== 'slow') {
        return false;
    }

    // Set the time limit based on the selected speed.
    $timeLimit = ($speed === 'fast') ? 20 : 60;

    // Get the letters already used in this room.
    $sql = "SELECT letter FROM rounds WHERE room_id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param($stmt, "i", $roomId);

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    $usedLetters = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $usedLetters[] = $row['letter'];
    }

    mysqli_stmt_close($stmt);

    // Find the letters that are still available.
    $letters = available_letters($usedLetters);

    // Randomly select a letter.
    $letter = select_random_letter($letters);

    // Stop if there are no letters remaining.
    if ($letter === null) {
        return false;
    }

    // Get the next round number.
    $sql = "SELECT COALESCE(MAX(number), 0) + 1 AS next_round
            FROM rounds WHERE room_id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param($stmt, "i", $roomId);

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    $row = mysqli_fetch_assoc($result);

    $roundNumber = $row['next_round'];

    mysqli_stmt_close($stmt);

    // Calculate the starting time and deadline.
    $startedAt = date('Y-m-d H:i:s');
    $deadlineAt = date('Y-m-d H:i:s', time() + $timeLimit);

    // Save the new round in the database.
    $sql = "INSERT INTO rounds
            (room_id, number, caller_id, speed, letter, started_at, deadline_at)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "iiissss",
        $roomId,
        $roundNumber,
        $callerId,
        $speed,
        $letter,
        $startedAt,
        $deadlineAt
    );

    $success = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);

    return $success;
}
?>
