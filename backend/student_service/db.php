<?php
$host = "localhost";
$user = "root";   // hoặc user MySQL bạn đã tạo
$pass = "";       // password MySQL
$db   = "studentdb"; // database của user_service

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>
