<?php
header('Content-Type: application/json');
require_once 'db.php'; // DB userdb

// Chỉ POST JSON
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "message" => "Chỉ hỗ trợ POST"]);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);
$user_id = $input['user_id'] ?? null;

if (!$user_id) {
    echo json_encode(["success" => false, "message" => "Thiếu user_id"]);
    exit;
}

$stmt = $conn->prepare("SELECT BALANCE FROM USERS WHERE USER_ID = ?");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode(["success" => true, "balance" => (float)$row['BALANCE']]);
} else {
    echo json_encode(["success" => false, "message" => "Không tìm thấy user"]);
}
?>
