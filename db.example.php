<?php
$host = "your_host_here"; 
$username = "your_username_here";    
$password = "your_password_here";
$dbname = "your_db_name_here"; 

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn = connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>