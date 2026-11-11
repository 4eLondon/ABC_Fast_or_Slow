<?php
$conn = new mysqli("127.0.0.1", "root", "", "abc_fast_or_slow", 3307);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Connected!";   // <- add this line, test, then delete it
?>