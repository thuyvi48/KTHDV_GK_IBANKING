<?php
header('Content-Type: application/json');
require __DIR__ . '/../db.php';

$input = json_decode(file_get_contents("php://input"), true);
$email = isset($input['email']) ? trim($input['email']) : '';
$otp   = isset($input['otp'])   ? trim((string)$input['otp']) : '';

if (!$email || !$otp) {
    echo json_encode(["error" => "Thiếu email hoặc OTP"]);
    exit;
}

// Lấy USER_ID
$stmt = $conn->prepare("SELECT USER_ID FROM USERS WHERE EMAIL=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo json_encode(["error" => "Không tìm thấy user"]);
    exit;
}
$user_id = $result->fetch_assoc()['USER_ID'];
$stmt->close();

// Lấy OTP mới nhất còn pending và chưa dùng
$stmt = $conn->prepare("SELECT OTP_ID, CODE, STATUS, IS_USED, EXPIRES_AT FROM OTPS WHERE USER_ID=? AND STATUS='pending' AND IS_USED=0 ORDER BY CREATED_AT DESC LIMIT 1");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    echo json_encode(["error" => "Không tìm thấy OTP hợp lệ"]);
    exit;
}

$otp_row = $res->fetch_assoc();
$stmt->close();

// Kiểm tra hết hạn
date_default_timezone_set('Asia/Ho_Chi_Minh');
$current_time = new DateTime();
$expires_at   = new DateTime($otp_row['EXPIRES_AT']);
if ($expires_at < $current_time) {
    echo json_encode(["error" => "OTP đã hết hạn"]);
    exit;
}

// So sánh OTP
if (trim((string)$otp_row['CODE']) !== trim((string)$otp)) {
    echo json_encode(["error" => "OTP không hợp lệ"]);
    exit;
}

// Cập nhật OTP đã dùng
$stmt = $conn->prepare("UPDATE OTPS SET IS_USED=1, STATUS='used' WHERE OTP_ID=?");
$stmt->bind_param("s", $otp_row['OTP_ID']);
$stmt->execute();
$stmt->close();
$conn->close();

echo json_encode(["success" => "OTP hợp lệ"]);
