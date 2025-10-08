<?php
header('Content-Type: application/json');
require_once 'db.php';

$raw = file_get_contents("php://input");
$input = json_decode($raw, true);

// Nhận input
$student_id    = $input['student_id'] ?? null;
$invoice_id    = $input['invoice_id'] ?? null;
$amount_to_pay = $input['amount'] ?? null;
$userId        = $input['userId'] ?? null;

if (!$student_id || !$invoice_id || !$amount_to_pay || !$userId) {
    echo json_encode(['success' => false, 'message' => 'Thiếu dữ liệu bắt buộc']);
    exit;
}

// --- Tạo payment ID ---
$paymentId = "PAY_" . substr(uniqid(), -6); // Ví dụ: PAY_ab12cd

// --- Chuẩn bị INSERT ---
$stmt = $conn->prepare("
    INSERT INTO PAYMENTS
    (PAYMENT_ID, STUDENT_ID, USER_ID, INVOICE_ID, AMOUNT, IDEMPOTENCY, STATUS, CREATED_AT)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");

$idempotency = null;
$status = 'pending';
$createdAt = date("Y-m-d H:i:s");

$stmt->bind_param(
    "ssssdsss",
    $paymentId,
    $student_id,
    $userId,
    $invoice_id,
    $amount_to_pay,
    $idempotency,
    $status,
    $createdAt
);

// --- GỌI USER SERVICE LẤY EMAIL ---
$user_api_url = "http://localhost/KTHDV_GK_IBANKING/backend/user_service/get_email.php?user_id=" . urlencode($userId);

$ch = curl_init($user_api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$user_response = curl_exec($ch);
$curl_err = curl_error($ch);
curl_close($ch);

if ($curl_err) {
    echo json_encode(['success' => false, 'message' => "Không thể kết nối user service: $curl_err"]);
    exit;
}

$user_data = json_decode($user_response, true);
if (!$user_data || empty($user_data['success']) || !$user_data['success']) {
    echo json_encode(['success' => false, 'message' => 'Không thể lấy email người dùng']);
    exit;
}

$email = $user_data['email'] ?? null;
if (!$email) {
    echo json_encode(['success' => false, 'message' => 'Không tìm thấy email của người dùng']);
    exit;
}

// --- Ghi vào bảng PAYMENTS ---
if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Không thể tạo giao dịch']);
    exit;
}
$stmt->close();

// --- GỌI OTP SERVICE ---
$otpUrl = "http://localhost/KTHDV_GK_IBANKING/backend/otp_service/create_otp.php";

$payload = [
    "payment_id" => $paymentId,
    "user_id"    => $userId,
    "email"      => $email,
    "ttlSeconds" => 90
];

$ch = curl_init($otpUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
$otpRes = curl_exec($ch);
$curl_err = curl_error($ch);
curl_close($ch);

// Debug: lưu response
file_put_contents("debug_otp.txt", $otpRes ?: $curl_err);

if ($curl_err) {
    echo json_encode(['success' => false, 'message' => "Không thể kết nối OTP service: $curl_err"]);
    exit;
}

// Parse phản hồi từ OTP service
$otpJson = json_decode($otpRes, true);
if (!$otpJson || empty($otpJson['success']) || !$otpJson['success']) {
    echo json_encode(['success' => false, 'message' => $otpJson['message'] ?? 'Lỗi OTP']);
    exit;
}

// --- Trả về frontend ---
echo json_encode([
    'success' => true,
    'message' => 'Giao dịch tạo thành công. OTP đã gửi.',
    'payment_id' => $paymentId,
    'otpExpiresIn' => $otpJson['expiresIn'] ?? 300
]);
