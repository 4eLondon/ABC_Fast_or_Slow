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

    

    // This function checks whether a player has permission to stop the current round.
    function can_stop_round(
        int $playerId,
        int $callerId,
        int $hostId
    ): bool {

        // Compare the player's ID with the caller's ID and the host's ID.
        if ($playerId === $callerId || $playerId === $hostId) {
            return true;
        }

        // If the player is neither the caller nor the host, deny permission.
        return false;
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
?>
