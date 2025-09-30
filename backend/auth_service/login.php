<?php
require_once 'db.php';
header('Content-Type: application/json');

// Lấy dữ liệu JSON từ frontend
$input = json_decode(file_get_contents("php://input"), true);
$username = $input['username'] ?? '';
$password = $input['password'] ?? '';

if (!$username || !$password) {
    echo json_encode(["error" => "Thiếu username hoặc password"]);
    exit;
}

// Kiểm tra trong bảng USERS_AUTH
$sql = "SELECT USER_ID, USERNAME, PASSWORD FROM USERS_AUTH WHERE USERNAME = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    if ($password === $row['PASSWORD']) {
        echo json_encode([
            "success" => true,
            "user_id" => $row['USER_ID'],
            "username" => $row['USERNAME']
        ]);
    } else {
        echo json_encode(["error" => "Sai mật khẩu"]);
    }
} else {
    echo json_encode(["error" => "Không tìm thấy user"]);
}

$stmt->close();
$conn->close();
?>
