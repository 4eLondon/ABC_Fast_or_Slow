<?php
    // Connect to the ABC Fast or Slow database
    $conn = mysqli_connect(
        "localhost",
        "root",
        "",
        "abc_fast_or_slow"
    );

    // Check whether the database connection was successful
    if ($conn === false) {

        // Display an error message if the connection fails
        die("Connection failed: " . mysqli_connect_error());
    }

    // Set the character encoding to support different characters
    mysqli_set_charset($conn, "utf8mb4");

?>
