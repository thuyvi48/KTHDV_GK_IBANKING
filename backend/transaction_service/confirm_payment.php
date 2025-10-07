<?php
header('Content-Type: application/json');
require_once 'db.php';

$input = json_decode(file_get_contents("php://input"), true);
$payment_id = $input['payment_id'] ?? null;
$user_id    = $input['user_id'] ?? null;
$code       = $input['code'] ?? null;

if (!$payment_id || !$user_id || !$code) {
    echo json_encode(['success' => false, 'message' => 'Thiếu dữ liệu xác thực OTP']);
    exit;
}

/* 1️⃣ Xác thực OTP */
$otpUrl = "http://localhost/KTHDV_GK_IBANKING/backend/otp_service/verify_otp.php";
$ch = curl_init($otpUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    "payment_id" => $payment_id,
    "user_id"    => $user_id,
    "code"       => $code
]));
$otpRes = curl_exec($ch);
curl_close($ch);

$otpJson = json_decode($otpRes, true);
if (!$otpJson || !$otpJson['success']) {
    echo json_encode(['success' => false, 'message' => $otpJson['message'] ?? 'OTP không hợp lệ']);
    exit;
}

/* 2️⃣ Lấy dữ liệu payment */
$stmt = $conn->prepare("SELECT STUDENT_ID, INVOICE_ID, AMOUNT FROM PAYMENTS WHERE PAYMENT_ID=? AND USER_ID=? LIMIT 1");
$stmt->bind_param("ss", $payment_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$row = $result->fetch_assoc()) {
    echo json_encode(["success" => false, "message" => "Không tìm thấy giao dịch"]);
    exit;
}
$stmt->close();

/* 3️⃣ Cập nhật trạng thái payment */
$stmt = $conn->prepare("UPDATE PAYMENTS SET STATUS='done', CONFIRM_AT=NOW() WHERE PAYMENT_ID=?");
$stmt->bind_param("s", $payment_id);
$stmt->execute();
$stmt->close();

/* 4️⃣ Trả về cho frontend (để gọi transaction_service) */
echo json_encode([
    "success"      => true,
    "message"      => "Xác thực OTP và cập nhật giao dịch thành công",
    "payment_id"   => $payment_id,
    "user_id"      => $user_id,
    "student_id"   => $row['STUDENT_ID'],
    "invoice_id"   => $row['INVOICE_ID'],
    "amount"       => (float)$row['AMOUNT']
]);

$conn->close();
