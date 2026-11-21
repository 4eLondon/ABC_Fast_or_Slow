<?php
    // This function finds all letters from A to Z that have not been used yet.
    function available_letters(array $usedLetters): array //tells php that the function must return an array 
    {
        // array containing every letter 
        $alphabet = range('A', 'Z');

        // Convert all previously used letters to uppercase 
        $usedLetters = array_map('strtoupper', $usedLetters);

        // Compare the alphabet with the used letters and remove any letters already used
        $available = array_diff($alphabet, $usedLetters);

        return array_values($available);
    }

    // This function selects a letter at random from the avalible letters
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

    // This function checks whether a player can manually stop a round.
    function can_stop_round(
        mysqli $conn,
        int $playerId,
        int $callerId,
        int $roomId
    ): bool {

        // Check whether the player is the caller or the room host.
        $sql = "SELECT rp.id
                FROM room_players rp
                JOIN rooms rm ON rp.room_id = rm.id
                WHERE rp.id = ?
                AND rm.id = ?
                AND (rp.id = ? OR rp.user_id = rm.host_id)";

        // Prepare the SQL statement.
        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            return false;
        }

        // Connect the player's ID, room ID and caller ID.
        mysqli_stmt_bind_param($stmt, "iii", $playerId, $roomId, $callerId);

        // Execute the query.
        if (!mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return false;
        }

        // Get the result.
        $result = mysqli_stmt_get_result($stmt);

        // Check whether the player has permission.
        $allowed = mysqli_fetch_assoc($result) !== null;

        // Close the statement.
        mysqli_stmt_close($stmt);

        return $allowed;
    }


    // This function checks whether the round's time limit has expired.
        function should_auto_stop_round(string $deadlineAt): bool
        {
            // Convert the deadline into a timestamp.
            $deadline = strtotime($deadlineAt);

            // Check whether the deadline is valid.
            if ($deadline === false) {
                return false;
            }

            // Compare the deadline with the current server time.
            // Return true if the deadline has been reached.
            if (time() >= $deadline) {
                return true;
            }

            return false;
        }

    // This function stops a round and records why it stopped.
    function stop_round(
        mysqli $conn,
        int $roundId,
        ?int $playerId,
        string $reason
    ): bool {

        // Check whether the stopping reason is valid.
        $validReasons = ["first_finished", "timer", "host"];

        if (!in_array($reason, $validReasons, true)) {
            return false;
        }

        // Timer stops must not have a player ID.
        if ($reason === "timer" && $playerId !== null) {
            return false;
        }

        // Manual stops must identify the player who stopped the round.
        if ($reason !== "timer" && $playerId === null) {
            return false;
        }

        // Update the round only if it has not already stopped.
        $sql = "UPDATE rounds
                SET stopped_at = NOW(),
                    stopped_by = ?,
                    end_reason = ?
                WHERE id = ?
                AND stopped_at IS NULL";

        // Prepare the SQL statement.
        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            return false;
        }

        // Connect the values to the query.
        mysqli_stmt_bind_param(
            $stmt,
            "isi",
            $playerId,
            $reason,
            $roundId
        );

        // Execute the update.
        if (!mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return false;
        }

        // Check whether a round was updated.
        $success = mysqli_stmt_affected_rows($stmt) === 1;

        mysqli_stmt_close($stmt);

        return $success;
    }

    // This function automatically stops a round when its timer expires.
    function auto_stop_round(mysqli $conn, int $roundId): bool
    {
        // Retrieve the round's deadline and stopping status.
        $sql = "SELECT deadline_at, stopped_at
                FROM rounds
                WHERE id = ?";

        // Prepare the SQL statement.
        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            return false;
        }

        // Connect the round ID to the query.
        mysqli_stmt_bind_param($stmt, "i", $roundId);

        // Execute the query.
        if (!mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return false;
        }

        // Get the round information.
        $result = mysqli_stmt_get_result($stmt);
        $round = mysqli_fetch_assoc($result);

        // Close the statement.
        mysqli_stmt_close($stmt);

        // Check whether the round exists.
        if (!$round) {
            return false;
        }

        // Do not stop a round that has already stopped.
        if ($round['stopped_at'] !== null) {
            return false;
        }

        // Check whether the round has a deadline.
        if ($round['deadline_at'] === null) {
            return false;
        }

        // Check whether the timer has expired.
        if (should_auto_stop_round($round['deadline_at'])) {

            // Stop the round automatically because time has expired.
            return stop_round($conn, $roundId, null, "timer");
        }

        // The timer has not expired yet.
        return false;
    }

    // This function creates a new game round.
    function create_round(
        mysqli $conn,
        int $roomId,
        int $callerId,
        string $speed
    ): bool {

    // Check whether the selected speed is valid.
    if ($speed != "fast" && $speed != "slow") {
        return false;
    }

    // Retrieve the room settings.
    $sql = "SELECT fast_seconds, slow_seconds, total_rounds
            FROM rooms
            WHERE id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "i", $roomId);

    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return false;
    }

    $result = mysqli_stmt_get_result($stmt);

    // Check whether the query returned a result.
    if (!$result) {
        mysqli_stmt_close($stmt);
        return false;
    }

    $room = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    // Check whether the room exists.
    if (!$room) {
        return false;
    }

    // Check whether the room already has an active round.
    $sql = "SELECT id
            FROM rounds
            WHERE room_id = ?
            AND stopped_at IS NULL
            LIMIT 1";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "i", $roomId);

    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return false;
    }

    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        mysqli_stmt_close($stmt);
        return false;
    }

    $activeRound = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    // Prevent another round from being created if one is active.
    if ($activeRound) {
        return false;
    }

    // Count how many rounds already exist in this room.
    $sql = "SELECT COUNT(*) AS round_count
            FROM rounds
            WHERE room_id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "i", $roomId);

    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return false;
    }

    $result = mysqli_stmt_get_result($stmt);

    // Check whether the query returned a result.
    if (!$result) {
        mysqli_stmt_close($stmt);
        return false;
    }

    $roundData = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    // Check whether the room has reached its round limit.
    if (!$roundData || $roundData['round_count'] >= $room['total_rounds']) {
        return false;
    }

    // Select the correct time limit based on the speed.
    if ($speed === "fast") {
        $duration = $room['fast_seconds'];
    } else {
        $duration = $room['slow_seconds'];
    }

    // Calculate the round's starting time and deadline.
    $startedAt = date("Y-m-d H:i:s");
    $deadlineAt = date("Y-m-d H:i:s", strtotime($startedAt) + $duration);

    // Retrieve the letters used in the most recent 26 rounds.
    $sql = "SELECT letter
            FROM rounds
            WHERE room_id = ?
            AND letter IS NOT NULL
            ORDER BY id DESC
            LIMIT 26";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "i", $roomId);

    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return false;
    }

    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        mysqli_stmt_close($stmt);
        return false;
    }

    // Store the previously used letters.
    $usedLetters = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $usedLetters[] = $row['letter'];
    }

    mysqli_stmt_close($stmt);

    // Reset the letters after all 26 have been used.
    if (count($usedLetters) === 26) {
        $usedLetters = [];
    }

    // Find the letters that are still available.
    $availableLetters = available_letters($usedLetters);

    // Select a random available letter.
    $letter = select_random_letter($availableLetters);

    if ($letter === null) {
        return false;
    }

    // Calculate the next round number.
    $roundNumber = $roundData['round_count'] + 1;

    // Check whether the caller belongs to this room.
    $sql = "SELECT id
            FROM room_players
            WHERE id = ?
            AND room_id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "ii", $callerId, $roomId);

    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return false;
    }

    $result = mysqli_stmt_get_result($stmt);

    // Check whether the query returned a result.
    if (!$result) {
        mysqli_stmt_close($stmt);
        return false;
    }

    $caller = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    // Stop if the caller does not belong to this room.
    if (!$caller) {
        return false;
    }

    // Insert the new round into the database.
    $sql = "INSERT INTO rounds
            (room_id, number, caller_id, speed, letter, started_at, deadline_at)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

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

    // Execute the query to create the round.
    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return false;
    }

    mysqli_stmt_close($stmt);

    // Return true when the round is successfully created.
    return true;
}

    

    // This function saves a player's answer for a category.
    function save_answer(
        mysqli $conn,
        int $roundId,
        int $playerId,
        int $categoryId,
        string $answer
    ): bool {

        // Get the round letter and check whether the round is still open.
        $sql = "SELECT letter, stopped_at, deadline_at
                FROM rounds
                WHERE id = ?";

        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            return false;
        }

        mysqli_stmt_bind_param($stmt, "i", $roundId);

        if (!mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return false;
        }

        $result = mysqli_stmt_get_result($stmt);
        $round = mysqli_fetch_assoc($result);

        mysqli_stmt_close($stmt);

        // Check whether the round exists.
        if (!$round) {
            return false;
        }

        // Do not save answers if the round has already stopped.
        if ($round['stopped_at'] !== null) {
            return false;
        }

        // Check whether the round has a deadline.
        if ($round['deadline_at'] === null) {
            return false;
        }

        // Check whether the deadline has passed.
        if (strtotime($round['deadline_at']) <= time()) {
            return false;
        }

        // Check whether the player belongs to the round's room.
        $sql = "SELECT rp.id
                FROM room_players rp
                JOIN rounds r ON rp.room_id = r.room_id
                WHERE r.id = ?
                AND rp.id = ?";

        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            return false;
        }

        mysqli_stmt_bind_param($stmt, "ii", $roundId, $playerId);

        if (!mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return false;
        }

        $result = mysqli_stmt_get_result($stmt);
        $player = mysqli_fetch_assoc($result);

        mysqli_stmt_close($stmt);

        if (!$player) {
            return false;
        }

        // Check whether the category exists and is active.
        $sql = "SELECT id
                FROM categories
                WHERE id = ?
                AND is_active = 1";

        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            return false;
        }

        mysqli_stmt_bind_param($stmt, "i", $categoryId);

        if (!mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return false;
        }

        $result = mysqli_stmt_get_result($stmt);
        $category = mysqli_fetch_assoc($result);

        mysqli_stmt_close($stmt);

        if (!$category) {
            return false;
        }

        // Validate the answer using the round's letter.
        if (!validate_answer($answer, $round['letter'])) {
            return false;
        }

        // Save the answer or update it if the player already entered one.
        $sql = "INSERT INTO answers (round_id, room_player_id, category_id, value)
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE value = VALUES(value)";

        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            return false;
        }

        mysqli_stmt_bind_param(
            $stmt,
            "iiis",
            $roundId,
            $playerId,
            $categoryId,
            $answer
        );

        if (!mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return false;
        }

        mysqli_stmt_close($stmt);

        return true;
    }

// This function records when a player submits their answers.
function record_submission(mysqli $conn, int $roundId, int $playerId): bool
{
    // Get the round status, room ID and round letter.
    $sql = "SELECT r.stopped_at, r.deadline_at, r.room_id, r.letter
            FROM rounds r
            WHERE r.id = ?";

    // Prepare the SQL statement.
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    // Connect the round ID to the SQL query.
    mysqli_stmt_bind_param($stmt, "i", $roundId);

    // Execute the query.
    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return false;
    }

    // Get the round information.
    $result = mysqli_stmt_get_result($stmt);
    $round = mysqli_fetch_assoc($result);

    // Close the statement.
    mysqli_stmt_close($stmt);

    // Check whether the round exists.
    if (!$round) {
        return false;
    }

    // Get all active categories.
    $sql = "SELECT id FROM categories WHERE is_active = 1";

    // Prepare the SQL statement.
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    // Execute the query.
    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return false;
    }

    // Store the active categories.
    $result = mysqli_stmt_get_result($stmt);
    $categories = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row['id'];
    }

    // Close the statement.
    mysqli_stmt_close($stmt);

    // Make sure there are active categories to complete.
    if (empty($categories)) {
        return false;
    }

    // Get the player's answers for this round.
    $sql = "SELECT category_id, value
            FROM answers
            WHERE round_id = ?
            AND room_player_id = ?";

    // Prepare the SQL statement.
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    // Connect the round ID and player ID.
    mysqli_stmt_bind_param($stmt, "ii", $roundId, $playerId);

    // Execute the query.
    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return false;
    }

    // Store the player's answers in an array.
    $result = mysqli_stmt_get_result($stmt);
    $answers = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $answers[$row['category_id']] = $row['value'];
    }

    // Close the statement.
    mysqli_stmt_close($stmt);

    // Check that every active category has a valid answer.
    foreach ($categories as $categoryId) {
        if (
            !isset($answers[$categoryId]) ||
            !validate_answer($answers[$categoryId], $round['letter'])
        ) {
            return false;
        }
    }

    // Check whether the player belongs to the room.
    $sql = "SELECT id
            FROM room_players
            WHERE id = ?
            AND room_id = ?";

    // Prepare the SQL statement.
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    // Connect the player ID and room ID.
    mysqli_stmt_bind_param($stmt, "ii", $playerId, $round['room_id']);

    // Execute the query.
    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return false;
    }

    // Get the player information.
    $result = mysqli_stmt_get_result($stmt);
    $player = mysqli_fetch_assoc($result);

    // Close the statement.
    mysqli_stmt_close($stmt);

    // Reject the submission if the player is not in the room.
    if (!$player) {
        return false;
    }

    // Check whether the round has already stopped.
    if ($round['stopped_at'] !== null) {
        return false;
    }

    // Check whether the round has a deadline.
    if ($round['deadline_at'] === null) {
        return false;
    }

    // Check whether the deadline has passed.
    if (strtotime($round['deadline_at']) <= time()) {
        return false;
    }

    // Save the player's submission.
    $sql = "INSERT INTO submissions (round_id, room_player_id)
            VALUES (?, ?)";

    // Prepare the SQL statement.
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    // Connect the round ID and player ID.
    mysqli_stmt_bind_param($stmt, "ii", $roundId, $playerId);

    // Execute the query.
    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return false;
    }

    // Close the statement.
    mysqli_stmt_close($stmt);

    // Stop the round because this player finished first.
    stop_round($conn, $roundId, $playerId, "first_finished");

    // Confirm that the submission was saved.
    return true;
}

    // This function retrieves all players' answers for review.
    function get_review_answers(
        mysqli $conn,
        int $roundId
    ): array {

        // Retrieve the players' answers and category names.
        $sql = "SELECT
                    a.id AS answer_id,
                    a.room_player_id,
                    a.category_id,
                    c.label AS category_name,
                    a.value AS answer
                FROM answers a
                JOIN categories c ON a.category_id = c.id
                WHERE a.round_id = ?
                ORDER BY a.room_player_id, c.label";

        // Prepare the SQL statement.
        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            return [];
        }

        // Connect the round ID.
        mysqli_stmt_bind_param($stmt, "i", $roundId);

        // Execute the query.
        if (!mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return [];
        }

        // Retrieve the results.
        $result = mysqli_stmt_get_result($stmt);

        $answers = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $answers[] = $row;
        }

        // Close the statement.
        mysqli_stmt_close($stmt);

        return $answers;
    }

    // Save a player's vote on an answer.
    function save_vote(
        mysqli $conn,
        int $answerId,
        int $voterId,
        string $vote
    ): bool {

        // Check that the vote is valid.
        if ($vote !== "accept" && $vote !== "reject") {
            return false;
        }

        // Check that the player is not voting on their own answer.
        $sql = "SELECT room_player_id
                FROM answers
                WHERE id = ?";

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $answerId);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $answer = mysqli_fetch_assoc($result);

        mysqli_stmt_close($stmt);

        if (!$answer || $answer['room_player_id'] == $voterId) {
            return false;
        }

       // Check if this player has already voted on this answer.
        $sql = "SELECT id FROM votes
                WHERE answer_id = ? AND voter_id = ?";

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $answerId, $voterId);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $existingVote = mysqli_fetch_assoc($result);

        mysqli_stmt_close($stmt);

        // Do not allow another vote.
        if ($existingVote) {
            return false;
        }

        // Save the vote.
        $sql = "INSERT INTO votes (answer_id, voter_id, vote)
                VALUES (?, ?, ?)";


        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iis", $answerId, $voterId, $vote);

        $success = mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);

        return $success;
    }

    // Retrieve the votes for an answer.
    function get_answer_votes(
        mysqli $conn,
        int $answerId
    ): array {

        $sql = "SELECT
                    vote,
                    COUNT(*) AS total
                FROM votes
                WHERE answer_id = ?
                GROUP BY vote";

        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            return [];
        }

        mysqli_stmt_bind_param($stmt, "i", $answerId);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        $votes = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $votes[$row['vote']] = (int) $row['total'];
        }

        mysqli_stmt_close($stmt);

        return $votes;
    }    
?>
