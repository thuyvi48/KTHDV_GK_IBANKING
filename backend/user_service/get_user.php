<?php
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(["error" => "Thiếu tham số id"]);
    exit;
}

$id = $_GET['id'];

$sql = "SELECT USER_ID, FULLNAME, EMAIL, AVAILABLE__BALANCE 
        FROM USERS WHERE USER_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode($row);
} else {
    echo json_encode(["error" => "Không tìm thấy user"]);
}

$stmt->close();
$conn->close();
?>
