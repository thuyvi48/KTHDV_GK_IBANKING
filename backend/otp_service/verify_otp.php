<?php
header('Content-Type: application/json');
require __DIR__ . '/../db.php'; // file kết nối DB

// Đọc payload JSON từ API Gateway
$input = json_decode(file_get_contents("php://input"), true);

// Debug nhanh
file_put_contents(__DIR__ . '/debug.log', print_r($input, true) . "\n", FILE_APPEND);

$email = trim($input['email'] ?? '');
$otp   = trim($input['otp'] ?? '');

// Kiểm tra dữ liệu đầu vào
if (!$email || !$otp) {
    echo json_encode(["error" => "Thiếu email hoặc OTP"]);
    exit;
}

// Lấy USER_ID từ bảng Users
$stmt = $conn->prepare("SELECT USER_ID FROM Users WHERE EMAIL = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["error" => "Không tìm thấy user"]);
    exit;
}

$row = $result->fetch_assoc();
$user_id = $row['USER_ID'];
$stmt->close();

// Lấy OTP mới nhất của user
$stmt = $conn->prepare("SELECT CODE, STATUS, EXPIRES_AT FROM OTPS 
                        WHERE USER_ID = ? 
                        ORDER BY CREATED_AT DESC 
                        LIMIT 1");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode(["error" => "Không tìm thấy OTP"]);
    exit;
}

$otp_row = $res->fetch_assoc();
$stmt->close();

// Kiểm tra trạng thái OTP
if ($otp_row['STATUS'] !== 'pending') {
    echo json_encode(["error" => "OTP đã sử dụng hoặc hết hạn"]);
    exit;
}

// Kiểm tra thời gian hết hạn
date_default_timezone_set('Asia/Ho_Chi_Minh');
$current_time = new DateTime();
$expires_at   = new DateTime($otp_row['EXPIRES_AT']);

if ($expires_at < $current_time) {
    echo json_encode(["error" => "OTP đã hết hạn"]);
    exit;
}

// So sánh OTP
if ((string)$otp_row['CODE'] !== (string)$otp) {
    echo json_encode(["error" => "OTP không hợp lệ"]);
    exit;
}

// Cập nhật trạng thái OTP thành 'used'
$stmt = $conn->prepare("UPDATE OTPS SET STATUS='used' WHERE USER_ID=? AND CODE=?");
$stmt->bind_param("ss", $user_id, $otp);
$stmt->execute();
$stmt->close();
$conn->close();

// Trả kết quả thành công
echo json_encode(["success" => "OTP hợp lệ"]);
