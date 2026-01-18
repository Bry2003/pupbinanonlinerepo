<?php
// Migration script to ensure `student_list` table exists and matches app expectations
// Usage: C:\xampp\php\php.exe tools\migrate_student_list.php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__.'/../config.php';

function execQuery($conn, $sql, $label){
    if($conn->query($sql)){
        echo "[OK] $label\n";
        return true;
    }
    echo "[FAIL] $label: ".$conn->error."\n";
    return false;
}

$hasTable = false;
$rs = $conn->query("SHOW TABLES LIKE 'student_list'");
if($rs && $rs->num_rows > 0){
    $hasTable = true;
}

if(!$hasTable){
    echo "Creating table student_list...\n";
    $sql = "CREATE TABLE `student_list` (
      `id` int(30) NOT NULL AUTO_INCREMENT,
      `firstname` text NOT NULL,
      `middlename` text NOT NULL,
      `lastname` text NOT NULL,
      `department_id` int(30) NULL,
      `curriculum_id` int(30) NULL,
      `email` text NOT NULL,
      `password` text NOT NULL,
      `gender` varchar(50) NOT NULL,
      `status` tinyint(4) NOT NULL DEFAULT 0,
      `avatar` text NOT NULL DEFAULT '',
      `date_created` datetime NOT NULL DEFAULT current_timestamp(),
      `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp(),
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    execQuery($conn, $sql, 'Create student_list');
} else {
    echo "Table student_list already exists. Applying schema updates...\n";
}

// Ensure NULL allowed for department_id / curriculum_id (visitor accounts)
execQuery($conn, "ALTER TABLE `student_list` MODIFY `department_id` int(30) NULL", 'Allow NULL department_id');
execQuery($conn, "ALTER TABLE `student_list` MODIFY `curriculum_id` int(30) NULL", 'Allow NULL curriculum_id');

// Ensure avatar default empty string to avoid NOT NULL insert failures
execQuery($conn, "ALTER TABLE `student_list` MODIFY `avatar` text NOT NULL", 'Ensure avatar NOT NULL');

// Add helpful columns if missing
$cols = ['account_type' => "ALTER TABLE `student_list` ADD COLUMN `account_type` varchar(20) NULL AFTER `gender`",
          'id_doc_url'   => "ALTER TABLE `student_list` ADD COLUMN `id_doc_url` text NULL AFTER `avatar`",
          'live_face_url'=> "ALTER TABLE `student_list` ADD COLUMN `live_face_url` text NULL AFTER `id_doc_url`" ];
foreach($cols as $name => $sql){
    $chk = $conn->query("SHOW COLUMNS FROM `student_list` LIKE '{$name}'");
    if(!$chk || $chk->num_rows == 0) execQuery($conn, $sql, "Add column {$name}");
}

// Ensure indexes
// Unique email index (use BTREE; some MySQL versions don’t support HASH here)
$idxEmail = $conn->query("SHOW INDEX FROM `student_list` WHERE Key_name='email'");
if(!$idxEmail || $idxEmail->num_rows == 0){
    execQuery($conn, "ALTER TABLE `student_list` ADD UNIQUE KEY `email` (`email`(255))", 'Add unique index on email');
}

// Foreign keys to department_list and curriculum_list; set NULL on delete for optional references
// Drop conflicting FKs first if present
function dropFkIfExists($conn, $table, $fkName){
    $res = $conn->query("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='{$table}' AND CONSTRAINT_NAME='{$fkName}'");
    if($res && $res->num_rows){
        execQuery($conn, "ALTER TABLE `{$table}` DROP FOREIGN KEY `{$fkName}`", "Drop FK {$fkName}");
    }
}
dropFkIfExists($conn, 'student_list', 'student_list_ibfk_1');
dropFkIfExists($conn, 'student_list', 'student_list_ibfk_2');

execQuery($conn, "ALTER TABLE `student_list` ADD CONSTRAINT `student_list_ibfk_1` FOREIGN KEY (`curriculum_id`) REFERENCES `curriculum_list` (`id`) ON DELETE SET NULL ON UPDATE CASCADE", 'Add FK curriculum_id');
execQuery($conn, "ALTER TABLE `student_list` ADD CONSTRAINT `student_list_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `department_list` (`id`) ON DELETE SET NULL ON UPDATE CASCADE", 'Add FK department_id');

echo "Migration finished.\n";
?>