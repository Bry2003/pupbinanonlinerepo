<?php
// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'test');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create archive_list table
$conn->query("CREATE TABLE IF NOT EXISTS `archive_list` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `archive_code` varchar(100) NOT NULL,       
  `curriculum_id` int(30) NOT NULL,
  `year` year(4) NOT NULL,
  `title` text NOT NULL,
  `abstract` text NOT NULL,
  `members` text NOT NULL,
  `banner_path` text NOT NULL,
  `document_path` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `student_id` int(30) DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

echo "Archive list table created successfully!";
?>