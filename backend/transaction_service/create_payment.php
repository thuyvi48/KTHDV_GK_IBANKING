<?php
header("Content-Type: application/json");
require_once("db.php"); // connection to paymentdb

$input = json_decode(file_get_contents("php://input"), true);

$student_id = $input['student_id'] ?? '';
$user_id    = $input['user_id'] ?? '';
$invoice_id = $input['invoice_id'] ?? '';
$amount     = floatval($input['amount'] ?? 0);

if (!$student_id || !$user_id || !$invoice_id || $amount <= 0) {
    echo json_encode(["success" => false, "message" => "Thiếu dữ liệu hợp lệ"]);
    exit;
}

// Tạo payment id
$payment_id = "PAY" . time() . rand(100,999);

// Insert payment (status pending)
$sql = "INSERT INTO PAYMENTS (PAYMENT_ID, STUDENT_ID, USER_ID, INVOICE_ID, AMOUNT, STATUS, CREATED_AT) 
        VALUES (?, ?, ?, ?, ?, 'pending', NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssd", $payment_id, $student_id, $user_id, $invoice_id, $amount);

if (!$stmt->execute()) {
    echo json_encode(["success" => false, "message" => "Lỗi khi tạo payment: " . $stmt->error]);
    exit;
}
$stmt->close();

// Lấy email người nộp tiền từ user_service
$userApiUrl = "http://localhost/KTHDV_GK_IBANKING/backend/user_service/get_user.php?user_id=" . urlencode($user_id);
$userResponse = @file_get_contents($userApiUrl);
if ($userResponse === false) {
    // không gây lỗi nhiệt đột, nhưng trả về failure để frontend biết
    echo json_encode(["success" => false, "message" => "Không kết nối được User Service"]);
    exit;
}
$userData = json_decode($userResponse, true);
$email = $userData['EMAIL'] ?? '';
$fullname = $userData['FULL_NAME'] ?? '';

if (!$email) {
    echo json_encode(["success" => false, "message" => "Không tìm thấy email người dùng"]);
    exit;
}

// Gọi OTP service để tạo + gửi OTP cho giao dịch (khoá payment_id)
$otp_service_url = "http://localhost/KTHDV_GK_IBANKING/backend/otp_service/create_otp.php";
// payload: payment_id + user_id + email + ttl (giây)
$otp_payload = json_encode([
    "payment_id" => $payment_id,
    "user_id" => $user_id,
    "email" => $email,
    "ttl_seconds" => 300 // 5 phút
]);

$ch = curl_init($otp_service_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $otp_payload);
$otp_response = curl_exec($ch);
$curl_err = curl_error($ch);
curl_close($ch);

if ($otp_response === false || $otp_response === null) {
    echo json_encode(["success" => false, "message" => "Không thể kết nối tới OTP service: $curl_err"]);
    exit;
}

$otp_result = json_decode($otp_response, true);
if (!$otp_result || empty($otp_result['success'])) {
    $msg = $otp_result['error'] ?? ($otp_result['message'] ?? 'Gửi OTP thất bại');
    echo json_encode(["success" => false, "message" => "Gửi OTP thất bại: $msg"]);
    exit;
}

// Thành công: trả về payment_id và trạng thái pending
echo json_encode([
    "success" => true,
    "message" => "Payment created. OTP đã gửi đến email.",
    "payment_id" => $payment_id,
    "status" => "pending"
]);
$conn->close();
?>
