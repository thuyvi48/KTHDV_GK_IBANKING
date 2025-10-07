<?php
header('Content-Type: application/json');
require __DIR__ . '/../db.php'; // kết nối tới userdb

$input = json_decode(file_get_contents("php://input"), true);
$userId = $input['user_id'] ?? null;
$email  = trim($input['email'] ?? '');
$phone  = trim($input['phone'] ?? '');

if (!$userId || !$email || !$phone) {
    echo json_encode(["success" => false, "message" => "Thiếu thông tin cần thiết"]);
    exit;
}

// Kiểm tra định dạng email và số điện thoại
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["success" => false, "message" => "Email không hợp lệ"]);
    exit;
}
if (!preg_match('/^[0-9]{9,11}$/', $phone)) {
    echo json_encode(["success" => false, "message" => "Số điện thoại không hợp lệ"]);
    exit;
}

$stmt = $conn->prepare("UPDATE USERS SET EMAIL=?, PHONE=? WHERE USER_ID=?");
$stmt->bind_param("sss", $email, $phone, $userId); // 🔧 đã sửa 'i' thành 's'

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(["success" => true, "message" => "Cập nhật thành công"]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Không có dữ liệu nào được thay đổi (USER_ID không tồn tại hoặc giá trị giống hệt cũ)",
            "debug_user_id" => $userId
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "Lỗi khi thực thi câu lệnh SQL",
        "error" => $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>
