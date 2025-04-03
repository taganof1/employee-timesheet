<?php
// db_connection.php
$servername = "localhost";
$username = "clockadmin";
$password = "[7]7JCweetDb9o)X";
$database = "clocking_system";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>