<?php
include 'config.php';
include 'classes/DBConnection.php';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$cover_path = 'uploads/pup.mp4';

// Update the cover in the database
$sql = "UPDATE system_info SET meta_value = '$cover_path' WHERE meta_field = 'cover'";
$result = $conn->query($sql);

if ($result) {
    echo "Cover updated successfully to: $cover_path";
    
    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Update session variable
    if(isset($_SESSION['system_info'])) {
        $_SESSION['system_info']['cover'] = $cover_path;
    }
} else {
    echo "Error updating cover: " . $conn->error;
}

echo "<br><a href='index.php'>Go to homepage</a> to see the changes";
$conn->close();
?>