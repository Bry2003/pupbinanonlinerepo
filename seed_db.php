<?php
require_once('config.php');
// $conn is available from config.php

$files = [
    'DATAANALYTICS/pupbcadmin_schema.sql',
    'public_html/database/events_table.sql',
    'public_html/database/add_created_by_to_events.sql',
    'public_html/database/news_table.sql',
    'public_html/database/page_views_table.sql'
];

foreach ($files as $file) {
    if (!file_exists($file)) {
        // Try relative path from root
        $file = __DIR__ . '/' . $file;
        if (!file_exists($file)) {
            echo "File not found: $file\n";
            continue;
        }
    }
    
    echo "Processing $file...\n";
    $sql_content = file_get_contents($file);
    
    // Remove CREATE DATABASE and USE statements to use the current connection's DB
    $sql_content = preg_replace('/^CREATE DATABASE.*;/mi', '', $sql_content);
    $sql_content = preg_replace('/^USE.*;/mi', '', $sql_content);
    
    // Split by semicolon, but be careful about semicolons in strings (basic split for now)
    // A more robust way is to just run the whole thing if the driver supports multi-query, 
    // but mysqli::multi_query is needed.
    
    if ($conn->multi_query($sql_content)) {
        do {
            if ($result = $conn->store_result()) {
                $result->free();
            }
        } while ($conn->more_results() && $conn->next_result());
        echo "Successfully executed $file\n";
    } else {
        echo "Error executing $file: " . $conn->error . "\n";
    }
}

echo "Database seeding completed.\n";
?>