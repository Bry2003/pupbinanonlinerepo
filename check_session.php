<?php
session_start();
echo '<pre>';
if(isset($_SESSION['userdata'])) {
    print_r($_SESSION['userdata']);
    echo "\nLogin Type: " . $_SESSION['userdata']['login_type'];
} else {
    echo "No session data found. You are not logged in.";
}
echo '</pre>';
?>