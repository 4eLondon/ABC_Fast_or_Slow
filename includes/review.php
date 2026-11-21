<?php 

    session_start();

    require_once __DIR__. '/db.php';
    require_once __DIR__ .'/game.php';

    // Get the round ID from the URL
    $round_id = isset($_GET['round_id']) ? (int) $_GET['round_id'] : 0;

    // Check that a valid round ID was provided
    if ($round_id <= 0) {
        die("Invalid round ID.");
    }

    // Get the answers for this round
    $answers = get_review_answers($conn, $round_id);

    // TEMPORARY: Use Player 2 for testing
    $voter_id = 2;

    // Handle voting
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $answer_id = (int) ($_POST['answer_id'] ?? 0);
        $vote = $_POST['vote'] ?? '';

        if ($answer_id > 0) {

            if (save_vote($conn, $answer_id, $voter_id, $vote)) {
                $message = "Your vote was saved!";
            } else {
                $message = "Vote could not be saved. You may have already voted or selected your own answer.";
            }

        }

        header("Location: " . $_SERVER['PHP_SELF'] . "?round_id=" . $round_id . "&message=" . urlencode($message));
        exit;
    }

    
//Appendix 
//ChatGPT assisted with understanding SQL queries, database relationships, and troubleshooting errors. 
?>

    <!DOCTYPE html>

    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Review Answers</title>
    </head>
    <body>

        <h1>Review Answers</h1>

        <?php if (isset($_GET['message'])): ?>

        <p>
            <?php echo htmlspecialchars($_GET['message']); ?>
        </p>

    <?php endif; ?>

        <h2>Round <?php echo $round_id; ?></h2>

        <?php if (empty($answers)): ?>

            <p>No answers have been submitted for this round.</p>

        <?php else: ?>

            <?php foreach ($answers as $answer): ?>

                <div>
                    <h3><?php echo htmlspecialchars($answer['category_name']); ?></h3>

                    <p>
                        Answer:
                        <?php echo htmlspecialchars($answer['answer']); ?>
                    </p>

                    <form method="POST">

                <input
                    type="hidden"
                    name="answer_id"
                    value="<?php echo $answer['answer_id']; ?>"
                >

                <button type="submit" name="vote" value="accept">
                    Accept
                </button>

                <button type="submit" name="vote" value="reject">
                    Reject
                </button>

            </form>

            <?php
            $vote_counts = get_answer_votes($conn, (int) $answer['answer_id']);

            $accept_count = $vote_counts['accept'] ?? 0;
            $reject_count = $vote_counts['reject'] ?? 0;
            ?>

            <p>
                Accept: <?php echo $accept_count; ?>
            </p>

            <p>
                Reject: <?php echo $reject_count; ?>
            </p>
                </div>

                <hr>

            <?php endforeach; ?>

        <?php endif; ?>
    </body>
    </html>






