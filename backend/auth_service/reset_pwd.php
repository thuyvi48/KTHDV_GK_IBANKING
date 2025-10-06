<?php
header('Content-Type: application/json');
require __DIR__ . '/db.php'; // db.php chỉ kết nối authdb

// Tạo thêm kết nối tới userdb
$userConn = new mysqli('localhost', 'root', '', 'userdb');

if ($conn->connect_errno) {
    echo json_encode(["error" => "Lỗi kết nối authdb: " . $conn->connect_error]);
    exit;
}

if ($userConn->connect_errno) {
    echo json_encode(["error" => "Lỗi kết nối userdb: " . $userConn->connect_error]);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);
$email = trim($input['email'] ?? '');
$password = trim($input['password'] ?? '');

if (!$email || !$password) {
    echo json_encode(["error" => "Thiếu dữ liệu"]);
    exit;
}

// Hash password mới
$newHash = $password;

// Lấy USER_ID từ userdb.USERS theo EMAIL
$sqlGetUser = "SELECT USER_ID FROM USERS WHERE EMAIL = ?";
$stmt = $userConn->prepare($sqlGetUser);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["error" => "Không tìm thấy người dùng có email này"]);
    exit;
}

$row = $result->fetch_assoc();
$userId = $row['USER_ID'];
$stmt->close();

// Cập nhật PASSWORD trong authdb.USERS_AUTH
$sqlUpdate = "UPDATE USERS_AUTH SET PASSWORD = ? WHERE USER_ID = ?";
$stmt = $conn->prepare($sqlUpdate);
$stmt->bind_param("ss", $newHash, $userId);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(["success" => "Đặt lại mật khẩu thành công!"]);
    } else {
        echo json_encode(["error" => "Người dùng chưa có tài khoản trong USERS_AUTH"]);
    }
} else {
    echo json_encode(["error" => "Không thể cập nhật mật khẩu"]);
}

$stmt->close();
$conn->close();
$userConn->close();
?>
