<?php
session_start();
require_once __DIR__ . '/db.php'; // Retrives data from database

// if logged in use user name else dont
if (isset($_SESSION['username'])) {
    $userAccountStatus = $_SESSION['username'];
    $userAccount = "Log out";
    $userAccountLink = "pages/auth.php?logout=1";
} else {
    $userAccountStatus = "Not logged in";
    $userAccount = "Sign up / Log in";
    $userAccountLink = "pages/auth.php";
}
