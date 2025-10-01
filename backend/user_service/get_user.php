<?php
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_GET['id']) && !isset($_GET['email'])) {
    echo json_encode(["error" => "Thiếu tham số id hoặc email"]);
    exit;
}

if (isset($_GET['id'])) {
    $sql = "SELECT USER_ID, FULL_NAME, EMAIL, BALANCE 
            FROM USERS WHERE USER_ID = ?";
    $param = $_GET['id'];
} else {
    $sql = "SELECT USER_ID, FULL_NAME, EMAIL, BALANCE 
            FROM USERS WHERE EMAIL = ?";
    $param = $_GET['email'];
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $param);
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
