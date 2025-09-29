<?php
require_once 'db.php'; // $conn

header('Content-Type: application/json');

// Chỉ cho phép POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["error" => "Phương thức không hợp lệ"]);
    exit;
}

// Nhận JSON
$data = json_decode(file_get_contents("php://input"), true);

$username = trim($data['username'] ?? '');
$password = trim($data['password'] ?? '');

if (empty($username) || empty($password)) {
    echo json_encode(["error" => "Thiếu username hoặc password"]);
    exit;
}

// Truy vấn bảng USERS_AUTH
$sql = "SELECT AUTH_ID, USERNAME, PASSWORD, USER_ID 
        FROM USERS_AUTH
        WHERE USERNAME = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    // Nếu DB đang lưu plain text thì so sánh trực tiếp
    // Nếu dùng password_hash() thì thay bằng password_verify()
    if ($password === $row['PASSWORD']) {
        echo json_encode([
            "success" => true,
            "auth" => [
                "auth_id" => $row['AUTH_ID'],
                "user_id" => $row['USER_ID'],
                "username" => $row['USERNAME']
            ],
            "token" => bin2hex(random_bytes(16)) // giả lập token
        ]);
    } else {
        echo json_encode(["error" => "Sai mật khẩu"]);
    }
} else {
    echo json_encode(["error" => "Không tìm thấy tài khoản"]);
}

$stmt->close();
$conn->close();
