<?php

// Connects to database
$conn = mysqli_connect("localhost", "root", "", "abc_fast_or_slow");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
