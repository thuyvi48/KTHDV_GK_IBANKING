<?php
header('Content-Type: application/json');
require __DIR__ . '/db.php'; // kết nối otpdb

$input = json_decode(file_get_contents("php://input"), true);
$email = isset($input['email']) ? trim($input['email']) : '';
$otp   = isset($input['otp'])   ? trim((string)$input['otp']) : '';

if (!$email || !$otp) {
    echo json_encode(["error" => "Thiếu email hoặc OTP"]);
    exit;
}

// Lấy USER_ID từ user_service
$userApiUrl = "http://localhost/KTHDV_GK_IBANKING/backend/user_service/get_user.php?email=" . urlencode($email);
$userResponse = @file_get_contents($userApiUrl);
if ($userResponse === false) {
    echo json_encode(["error" => "Không kết nối user_service"]);
    exit;
}
$userData = json_decode($userResponse, true);
if (!isset($userData['USER_ID'])) {
    echo json_encode(["error" => "Không tìm thấy user"]);
    exit;
}
$user_id = $userData['USER_ID'];

// Lấy OTP mới nhất còn ACTIVE và chưa dùng
$stmt = $conn->prepare("
    SELECT OTP_ID, CODE, STATUS, IS_USED, EXPIRES_AT 
    FROM OTPS 
    WHERE USER_ID=? AND STATUS='ACTIVE' AND IS_USED=0 
    ORDER BY CREATED_AT DESC 
    LIMIT 1
");
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
$current_time = new DateTime();
$expires_at   = new DateTime($otp_row['EXPIRES_AT']);
if ($expires_at < $current_time) {
    echo json_encode(["error" => "OTP đã hết hạn"]);
    exit;
}

// So sánh OTP (so sánh string, giữ leading zero)
if (strcmp(trim($otp_row['CODE']), trim((string)$otp)) !== 0) {
    echo json_encode(["error" => "OTP không hợp lệ"]);
    exit;
}

// Cập nhật OTP đã dùng
$upd = $conn->prepare("UPDATE OTPS SET IS_USED=1, STATUS='USED' WHERE OTP_ID=?");
$upd->bind_param("s", $otp_row['OTP_ID']);
$upd->execute();
$upd->close();
$conn->close();

echo json_encode(["success" => "OTP hợp lệ"]);
