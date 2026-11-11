<?php

    // Includes the game.php file so this file can use the normalize_answer() function.
    // require_once stops the same file from being included more than once.
    require_once __DIR__ . '/game.php';


    // This function checks whether a player's answer follows the game rules.
    // It receives the player's answer and the letter selected for the current round.
    // It returns true if the answer is valid or false if it is invalid.
    function validate_answer(string $answer, string $roundLetter): bool
    {
        // Remove unnecessary spaces, convert the answer to lowercase
        // and standardize the text so it can be checked correctly.
        $answer = normalize_answer($answer);

        // Normalize the round letter as well so both values use the same format.
        $roundLetter = normalize_answer($roundLetter);

        // Check whether the player submitted an empty answer.
        // An empty answer is not allowed, so return false.
        if ($answer === "") {
            return false;
        }

        // Check whether the answer contains more than 40 characters.
        // Answers longer than 40 characters are not accepted.
        if (strlen($answer) > 40) {
            return false;
        }

        // Check whether the round letter contains exactly one character.
        // This ensures that the round has one valid starting letter.
        if (strlen($roundLetter) !== 1) {
            return false;
        }

        // Check whether the player's answer begins with the selected round letter.
        // If the first character does not match, the answer is invalid.
        if (substr($answer, 0, 1) !== $roundLetter) {
            return false;
        }

        // If the answer passes all the checks above, it is valid.
        return true;
    }

    // This function checks whether a player is allowed to vote on another player's answer.
    // It receives the voter's ID, the answer owner's ID, the answer,
    // the round letter and the current round phase.
    // It returns true if voting is allowed or false if it is not allowed.
    function can_vote_on_answer(
        int $voterId,
        int $answerOwnerId,
        string $answer,
        string $roundLetter,
        string $roundPhase
    ): bool {

        // Check whether the game is currently in the review phase.
        // Players are only allowed to vote during this phase.
        if ($roundPhase !== "review") {
            return false;
        }

        // Compare the voter's ID with the answer owner's ID.
        // A player cannot vote on their own answer.
        if ($voterId === $answerOwnerId) {
            return false;
        }

        // Check whether the answer follows the game rules.
        // Players cannot vote on answers that are empty, too long,
        // or do not begin with the selected round letter.
        if (!validate_answer($answer, $roundLetter)) {
            return false;
        }

        // If all voting conditions are satisfied, allow the player to vote.
        return true;
    }

    // Calculate the result of the votes submitted 
    // The function receives the number of yes votes and no votes.
    // It returns the result as a string.
    function calculate_vote_result(int $yesVotes, int $noVotes): string
    {
        // Check whether anyone has voted yet.
        // If there are no votes, the answer is still pending.
        if ($yesVotes === 0 && $noVotes === 0) {
            return "pending";
        }

        // Compare the yes votes with the no votes.
        // If yes votes are greater than or equal to no votes,
        // the answer is accepted. This also means a tie is accepted.
        if ($yesVotes >= $noVotes) {
            return "accepted";
        }

        // If no votes are greater than yes votes, the answer is rejected.
        return "rejected";
    }

?>
