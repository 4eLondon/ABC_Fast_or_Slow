<?php

// Connect to the database
require_once __DIR__ . '/db.php';

// Retrieve all categories from the categories table
$sql = "SELECT * FROM categories";

$result = mysqli_query($conn, $sql);

// Check whether the query worked
if ($result) {

    echo "<h2>Database Test Successful!</h2>";

    // Display each category
    while ($row = mysqli_fetch_assoc($result)) {
        echo htmlspecialchars($row['label']) . "<br>";
    }

} else {
    echo "Query failed: " . mysqli_error($conn);
}

?>