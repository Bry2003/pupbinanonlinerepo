<?php
$conn = new mysqli('localhost', 'root', '', 'pupbinanonlinerepo_db');
if($conn->connect_error) die("Connect failed: " . $conn->connect_error);

$firstname = 'Test';
$lastname = 'Student';
$email = 'test'.time().'@example.com';
$password = md5('123456');
$gender = 'Male';
$status = 1;
$avatar = ''; // Explicitly set empty

$sql = "INSERT INTO student_list (firstname, lastname, email, password, gender, status, avatar) 
        VALUES ('$firstname', '$lastname', '$email', '$password', '$gender', '$status', '$avatar')";

if($conn->query($sql) === TRUE) {
    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}
$conn->close();
?>