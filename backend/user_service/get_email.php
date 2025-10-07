<?php
header("Content-Type: application/json");
require_once("db.php"); // kết nối tới userdb

$user_id = $_GET['user_id'] ?? '';

if (empty($user_id)) {
    echo json_encode(["success" => false, "message" => "Thiếu user_id"]);
    exit;
}

$stmt = $conn->prepare("SELECT EMAIL FROM USERS WHERE USER_ID = ?");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode(["success" => true, "email" => $row['EMAIL']]);
} else {
    echo json_encode(["success" => false, "message" => "Không tìm thấy người dùng"]);
}

$stmt->close();
$conn->close();
?>
