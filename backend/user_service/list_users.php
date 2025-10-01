<?php
require_once 'db.php';

header('Content-Type: application/json');

$sql = "SELECT USER_ID, FULL_NAME, EMAIL, PHONE, BALANCE FROM USERS";
$result = $conn->query($sql);

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = [
        "user_id"   => $row['USER_ID'],
        "full_name" => $row['FULL_NAME'],
        "email"     => $row['EMAIL'],
        "phone"     => $row['PHONE'],
        "balance"   => $row['BALANCE']
    ];
}

echo json_encode($users);

$conn->close();
