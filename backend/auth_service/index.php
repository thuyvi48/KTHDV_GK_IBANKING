<?php
header('Content-Type: application/json');
require_once 'db.php';

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'login':
        // Chỉ chấp nhận POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(["error" => "Phương thức không hợp lệ, phải dùng POST"]);
            exit;
        }

        $input = json_decode(file_get_contents("php://input"), true);
        $username = $input['username'] ?? '';
        $password = $input['password'] ?? '';

        if (!$username || !$password) {
            echo json_encode(["error" => "Thiếu username hoặc password"]);
            exit;
        }

        $sql = "SELECT AUTH_ID, USERNAME, PASSWORD, USER_ID 
                FROM USERS_AUTH 
                WHERE USERNAME = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if ($row['PASSWORD'] === $password) {
                echo json_encode([
                    "success" => true,
                    "auth_id" => $row['AUTH_ID'],
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
        break;

    case 'logout':
        // Ví dụ logout đơn giản (thực tế cần token/session)
        echo json_encode(["success" => true, "message" => "Đăng xuất thành công"]);
        break;

    default:
        echo json_encode(["error" => "Hành động không hợp lệ"]);
}

$conn->close();
