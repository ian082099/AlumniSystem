<?php
$servername = "127.0.0.1";
$username = "root"; // Change this to your MySQL username
$password = ""; // Change this to your MySQL password if set
$database = "dbjointable"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
