<?php
$host = "localhost";
$dbname = "self_leveling";
$username = "root";
$password = "";  // XAMPP default MySQL has no password

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>