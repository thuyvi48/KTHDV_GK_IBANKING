<?php
require_once 'db.php';
header('Content-Type: application/json');

$sql = "SELECT USER_ID, FULLNAME, EMAIL, AVAILABLE__BALANCE 
        FROM USERS";
$result = $conn->query($sql);

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

echo json_encode($users);

$conn->close();
?>
