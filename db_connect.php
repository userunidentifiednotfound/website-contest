<?php
$servername = "sql109.infinityfree.com";
$username = "if0_38583332";
$password = "Devsprint";
$dbname = "if0_38583332_webcraft";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
