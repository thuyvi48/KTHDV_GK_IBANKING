<?php
header('Content-Type: application/json');
$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input['email']) || !isset($input['password'])) {
    echo json_encode(["error" => "Thiếu dữ liệu"]);
    exit;
}

$email = $input['email'];
$password = password_hash($input['password'], PASSWORD_BCRYPT);

$conn = new mysqli("localhost", "root", "", "ibanking");
if ($conn->connect_error) {
    echo json_encode(["error" => "DB lỗi"]);
    exit;
}

$stmt = $conn->prepare("UPDATE USERS SET PASSWORD = ? WHERE EMAIL = ?");
$stmt->bind_param("ss", $password, $email);
if ($stmt->execute()) {
    echo json_encode(["success" => "Cập nhật mật khẩu thành công"]);
} else {
    echo json_encode(["error" => "Không thể cập nhật mật khẩu"]);
}
?>
