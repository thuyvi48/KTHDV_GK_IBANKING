<?php
file_put_contents(__DIR__ . "/debug_raw_input.txt", file_get_contents("php://input"));

header('Content-Type: application/json');
require __DIR__ . '/db.php';

// Đọc dữ liệu JSON đầu vào
$inputRaw = file_get_contents("php://input");
$input = json_decode($inputRaw, true);

// Ghi log input để debug (nếu cần)
file_put_contents(__DIR__ . "/debug_input_confirm.txt", json_encode($input, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// Kiểm tra nếu decode thất bại
if (!is_array($input)) {
    echo json_encode(["success" => false, "message" => "Dữ liệu gửi đến không hợp lệ (không phải JSON)."]);
    exit;
}

// Nhận các trường cần thiết
$payment_id = trim($input['payment_id'] ?? $input['paymentId'] ?? '');
$user_id    = trim($input['user_id'] ?? $input['userId'] ?? '');
$otpCode    = trim($input['otpCode'] ?? $input['code'] ?? $input['otp'] ?? $input['otp_code'] ?? '');

// Debug khi thiếu dữ liệu
if ($payment_id === '' || $user_id === '' || $otpCode === '') {
    $missing = [
        "payment_id" => empty($payment_id),
        "user_id" => empty($user_id),
        "otpCode" => empty($otpCode)
    ];
    file_put_contents(__DIR__ . "/debug_verify_missing.txt", json_encode($missing));
    echo json_encode(["success" => false, "message" => "Thiếu dữ liệu xác thực OTP"]);
    exit;
}

// --- Kiểm tra OTP trong DB ---
$sql = "SELECT OTP_ID, EXPIRES_AT, IS_USED 
        FROM OTPS 
        WHERE PAYMENT_ID = ? AND USER_ID = ? AND CODE = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $payment_id, $user_id, $otpCode);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "OTP không hợp lệ hoặc không khớp giao dịch"]);
    exit;
}

$row = $result->fetch_assoc();

// Kiểm tra hết hạn
if (strtotime($row['EXPIRES_AT']) < time()) {
    echo json_encode(["success" => false, "message" => "OTP đã hết hạn"]);
    exit;
}

// Kiểm tra đã sử dụng chưa
if ($row['IS_USED']) {
    echo json_encode(["success" => false, "message" => "OTP đã được sử dụng"]);
    exit;
}

// Cập nhật trạng thái IS_USED = 1
$update = $conn->prepare("UPDATE OTPS SET IS_USED = 1 WHERE OTP_ID = ?");
$update->bind_param("s", $row['OTP_ID']);
$update->execute();
$update->close();

// Thành công
echo json_encode(["success" => true, "message" => "Xác thực OTP thành công"]);

$stmt->close();
$conn->close();
?>
