<?php
require_once 'db.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents("php://input"), true);
$username = trim($input['username'] ?? '');
$password = trim($input['password'] ?? '');

if (empty($username) && empty($password)) {
    echo json_encode(["success" => false, "message" => "Vui lòng nhập tên đăng nhập và mật khẩu"]);
    exit;
}
if (empty($username)) {
    echo json_encode(["success" => false, "message" => "Vui lòng nhập tên đăng nhập hoặc email"]);
    exit;
}
if (empty($password)) {
    echo json_encode(["success" => false, "message" => "Vui lòng nhập mật khẩu"]);
    exit;
}

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
        echo json_encode(["success" => false, "message" => "Sai mật khẩu"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Tên đăng nhập không tồn tại"]);
}

$stmt->close();
$conn->close();
