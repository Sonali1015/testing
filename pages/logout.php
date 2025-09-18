<?php
// Start output buffering to prevent header issues
ob_start();

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Clear all session variables
$_SESSION = [];

// Destroy session
session_destroy();

// Redirect to homepage (relative path)
header("Location: ../index.php"); // go up one level from pages/
exit();

ob_end_flush(); // End output buffering
