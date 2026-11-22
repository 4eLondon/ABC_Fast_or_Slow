<?php

// Connects to database
$conn = mysqli_connect("localhost", "root", "123", "abc");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
