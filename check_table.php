<?php
require_once('config.php');

$db = new DBConnection;
$conn = $db->conn;

if(!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$result = mysqli_query($conn, 'SHOW TABLES');
echo "Tables in database:\n";
if ($result) {
    while ($row = mysqli_fetch_row($result)) {
        echo $row[0] . "\n";
    }
} else {
    echo 'Error: ' . mysqli_error($conn);
}

// Check if system_info table exists
$result = mysqli_query($conn, 'SHOW TABLES LIKE "system_info"');
if (mysqli_num_rows($result) > 0) {
    echo "\nsystem_info table exists\n";
    
    // Check table structure
    $result = mysqli_query($conn, 'DESCRIBE system_info');
    echo "\nTable structure:\n";
    while ($row = mysqli_fetch_assoc($result)) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
    
    // Check if table has data
    $result = mysqli_query($conn, 'SELECT COUNT(*) FROM system_info');
    $count = mysqli_fetch_row($result)[0];
    echo "\nNumber of rows: " . $count . "\n";
} else {
    echo "\nsystem_info table does not exist\n";
    
    // Create the table
    echo "\nCreating system_info table...\n";
    $sql = "CREATE TABLE IF NOT EXISTS `system_info` (
        `id` int(30) NOT NULL AUTO_INCREMENT,
        `meta_field` text NOT NULL,
        `meta_value` text NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
    
    if (mysqli_query($conn, $sql)) {
        echo "Table created successfully\n";
        
        // Insert default values
        $sql = "INSERT INTO `system_info` (`meta_field`, `meta_value`) VALUES
            ('name', 'PUP Binan Online Repository System'),
            ('short_name', 'IntelHub');
        ";
        
        if (mysqli_query($conn, $sql)) {
            echo "Default values inserted successfully\n";
        } else {
            echo "Error inserting default values: " . mysqli_error($conn) . "\n";
        }
    } else {
        echo "Error creating table: " . mysqli_error($conn) . "\n";
    }
}
?>