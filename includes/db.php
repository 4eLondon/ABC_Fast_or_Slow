<?php

// Set the application's time zone to Jamaica.
date_default_timezone_set('America/Jamaica');

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


//Apendix
//Prompt - Help me troubleshoot why my PHP function `save_answer()` keeps returning false even though the answer is valid and the round is still active. 
// identifying a time-zone mismatch between PHP and MySQL that caused the application to incorrectly treat an active round as expired. The issue was resolved by setting PHP's default time zone to `America/Jamaica` in the database connection file.
?>