<?php
require_once __DIR__ . '/account.php';
require_once __DIR__ . '/db.php';

$error = "";

// Log out
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = $_POST['field_username'];
    $password = $_POST['field_password'];

    // Sign up
    if (isset($_POST['signup'])) {
        $email = $_POST['field_email'];

        // Does this username or email already exist?
        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ? OR email = ?");
        mysqli_stmt_bind_param($stmt, "ss", $username, $email);
        mysqli_stmt_execute($stmt);
        $exists = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        if ($password !== $_POST['field_confirm_password']) {
            $error = "Passwords do not match.";
        } elseif ($exists) {
            $error = "That user already exists in the system. Please log in instead.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, "INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sss", $username, $email, $hash);
            mysqli_stmt_execute($stmt);

            $_SESSION['username'] = $username;
            header("Location: ../index.php");
            exit;
        }
    }

    // Log in
    if (isset($_POST['login'])) {
        $stmt = mysqli_prepare($conn, "SELECT password_hash FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['username'] = $username;
            header("Location: ../index.php");
            exit;
        } else {
            $error = "Wrong username or password.";
        }
    }
}
