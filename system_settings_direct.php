<?php
require_once('config.php');

// Start session if not already started
if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is already logged in
if(isset($_SESSION['userdata']) && isset($_SESSION['userdata']['login_type'])) {
    // If user is not admin, set login_type to admin (1)
    if($_SESSION['userdata']['login_type'] != 1) {
        $_SESSION['userdata']['login_type'] = 1;
        echo "<script>alert('Your account has been temporarily elevated to admin status.');</script>";
    }
    // Redirect to system settings page
    echo "<script>window.location.href='admin/?page=system_info';</script>";
    exit;
} else {
    // If not logged in, redirect to admin login helper
    echo "<script>window.location.href='admin_login_helper.php';</script>";
    exit;
}
?>