<?php
header('Content-Type: application/json');
require __DIR__ . '/db.php';

$input = json_decode(file_get_contents("php://input"), true);

$payment_id = trim($input['payment_id'] ?? '');
$user_id = trim($input['user_id'] ?? '');
$otp_code = trim($input['otp_code'] ?? '');

if (!$payment_id || !$user_id || !$otp_code) {
    echo json_encode(["error" => "Thiếu dữ liệu"]);
    exit;
}

// Tìm OTP khớp giao dịch
$sql = "SELECT OTP_ID, EXPIRES_AT, IS_USED 
        FROM OTPS 
        WHERE PAYMENT_ID = ? AND USER_ID = ? AND CODE = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $payment_id, $user_id, $otp_code);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["error" => "OTP không hợp lệ hoặc không khớp giao dịch"]);
    exit;
}

$row = $result->fetch_assoc();

// Kiểm tra hết hạn
if (strtotime($row['EXPIRES_AT']) < time()) {
    echo json_encode(["error" => "OTP đã hết hạn"]);
    exit;
}

// Kiểm tra đã dùng chưa
if ($row['IS_USED']) {
    echo json_encode(["error" => "OTP đã được sử dụng"]);
    exit;
}

// Cập nhật trạng thái đã dùng
$update = $conn->prepare("UPDATE OTPS SET IS_USED = 1 WHERE OTP_ID = ?");
$update->bind_param("s", $row['OTP_ID']);
$update->execute();
$update->close();

echo json_encode(["success" => "Xác thực OTP thành công"]);

$stmt->close();
$conn->close();
?>
