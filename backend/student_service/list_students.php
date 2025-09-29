<?php
require_once 'db.php';
header('Content-Type: application/json');

$sql = "SELECT ID, MSSV, FULLNAME, EMAIL, CREATED_AT FROM STUDENTS";
$result = $conn->query($sql);

$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

echo json_encode($students);

$conn->close();
?>
