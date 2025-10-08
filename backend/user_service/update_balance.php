<?php
header('Content-Type: application/json');
require_once 'db.php'; // DB userdb

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);
    $user_id = $input['user_id'] ?? null;
    $balance_after = $input['balance_after'] ?? null;
} elseif ($method === 'GET') {
    $user_id = $_GET['user_id'] ?? null;
    $balance_after = $_GET['balance_after'] ?? null;
} else {
    echo json_encode(["success" => false, "message" => "Chỉ hỗ trợ GET hoặc POST"]);
    exit;
}

if (!$user_id || $balance_after === null) {
    echo json_encode(["success" => false, "message" => "Thiếu dữ liệu"]);
    exit;
}

$stmt = $conn->prepare("UPDATE USERS SET BALANCE = ?, UPDATED_AT = NOW() WHERE USER_ID = ?");
$stmt->bind_param("ds", $balance_after, $user_id);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Cập nhật số dư thành công",
        "new_balance" => (float)$balance_after
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Cập nhật số dư thất bại"]);
}
?>
