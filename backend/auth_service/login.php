<?php
require_once 'db.php'; // kết nối DB

header('Content-Type: application/json');

// Chỉ cho phép POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["error" => "Phương thức không hợp lệ"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$username = trim($data['username'] ?? '');
$password = trim($data['password'] ?? '');

if (empty($username) || empty($password)) {
    echo json_encode(["error" => "Thiếu username hoặc password"]);
    exit;
}

$sql = "SELECT USER_ID, FULLNAME, PASSWORD 
        FROM USERS 
        WHERE USERNAME = ? OR EMAIL = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $username);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    // Nếu trong DB đang dùng plain text thì so sánh trực tiếp
    // Nếu đã dùng password_hash() thì thay bằng password_verify()
    if ($password === $row['PASSWORD']) {
        echo json_encode([
            "success" => true,
            "user" => [
                "id" => $row['USER_ID'],
                "fullname" => $row['FULLNAME']
            ],
            "token" => bin2hex(random_bytes(16)) // giả lập JWT/Session Token
        ]);
    } else {
        echo json_encode(["error" => "Sai mật khẩu"]);
    }
} else {
    echo json_encode(["error" => "Không tìm thấy tài khoản"]);
}

$stmt->close();
$conn->close();
