<?php
// Simple MySQL connectivity test for pupbinanonlinerepo
// Usage: C:\xampp\php\php.exe tools\test_db.php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__.'/../config.php';

if(!$conn){
    fwrite(STDERR, "No mysqli connection available.\n");
    exit(2);
}

$res = $conn->query('SELECT 1');
if($res){
    echo "DB OK\n";
    $row = $res->fetch_row();
    echo "SELECT 1 returned: ".$row[0]."\n";
    exit(0);
}
echo "DB FAIL: ".$conn->error."\n";
exit(1);
?>