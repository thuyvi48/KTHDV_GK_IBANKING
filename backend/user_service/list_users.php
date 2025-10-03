<?php
header("Content-Type: application/json");
require_once "db.php";

$sql = "SELECT USER_ID, FULL_NAME, EMAIL, PHONE, BALANCE FROM USERS";
$result = $conn->query($sql);

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

echo json_encode($users);
