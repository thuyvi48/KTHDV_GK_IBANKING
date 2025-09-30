<?php
$host = "localhost";
$user = "root";   
$pass = "";       // password MySQL
$db   = "authdb"; 

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>
