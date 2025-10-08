<?php
header('Content-Type: application/json');
require __DIR__ . '/db.php';
file_put_contents(__DIR__ . "/debug_confirm_log.txt", date('Y-m-d H:i:s') . " - confirm_payment called\n", FILE_APPEND);

$input = json_decode(file_get_contents("php://input"), true) ?? [];
file_put_contents(__DIR__ . "/debug_confirm_input.txt", file_get_contents("php://input"));
    
$payment_id = trim($input['payment_id'] ?? $input['paymentId'] ?? '');
$user_id    = trim($input['user_id'] ?? $input['userId'] ?? '');
$otpCode    = trim($input['otpCode'] ?? $input['code'] ?? $input['otp'] ?? $input['otp_code'] ?? '');

if ($payment_id === '' || $user_id === '' || $otpCode === '') {
    echo json_encode(["success" => false, "message" => "Thiếu dữ liệu xác thực OTP"]);
    exit;
}

// Ghi log payload để kiểm tra
file_put_contents(__DIR__ . "/debug_confirm_payload.txt", json_encode($input, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

/* 1️⃣ GỌI OTP SERVICE ĐỂ XÁC THỰC OTP */
$otpUrl = "http://localhost/KTHDV_GK_IBANKING/backend/otp_service/verify_otp_pay.php";

$ch = curl_init($otpUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    "payment_id" => $payment_id,
    "user_id"    => $user_id,
    "otpCode"    => $otpCode
]));
$otpRes = curl_exec($ch);
$curl_err = curl_error($ch);
curl_close($ch);

if ($curl_err) {
    echo json_encode(["success" => false, "message" => "Không thể kết nối tới OTP service: $curl_err"]);
    exit;
}

$otpJson = json_decode($otpRes, true);
if (!$otpJson || empty($otpJson['success'])) {
    echo json_encode(['success' => false, 'message' => $otpJson['message'] ?? 'OTP không hợp lệ']);
    exit;
}

/* 2️⃣ Nếu OTP hợp lệ thì tiếp tục xử lý giao dịch */
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

/* 4️⃣ (Chuẩn bị gọi các service khác để xử lý hậu giao dịch) */
echo json_encode([
    "success"      => true,
    "message"      => "Xác thực OTP và cập nhật giao dịch thành công",
    "payment_id"   => $payment_id,
    "user_id"      => $user_id,
    "student_id"   => $row['STUDENT_ID'],
    "invoice_id"   => $row['INVOICE_ID'],
    "amount"       => (float)$row['AMOUNT']
]);
/* 5️⃣ Trừ tiền người gửi */
$accountUrl = "http://localhost/KTHDV_GK_IBANKING/backend/account_service/update_balance.php";
$payload = [
    "user_id" => $user_id,
    "amount"  => $row['AMOUNT']
];
file_get_contents($accountUrl . '?' . http_build_query($payload));

/* 6️⃣ Gạch nợ học phí */
$invoiceUrl = "http://localhost/KTHDV_GK_IBANKING/backend/student_service/update_invoice.php";
$payload2 = [
    "invoice_id" => $row['INVOICE_ID'],
    "status"     => "PAID"
];
file_get_contents($invoiceUrl . '?' . http_build_query($payload2));
$conn->close();
