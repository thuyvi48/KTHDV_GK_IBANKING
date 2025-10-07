<?php
header("Content-Type: application/json");
require_once("db.php");

// 1. Nhận payload từ gateway
$input = json_decode(file_get_contents("php://input"), true);

// 2. Chuyển key frontend sang backend chuẩn
$student_id = $input['student_id'] ?? '';
$user_id    = $input['user_id'] ?? '';
$invoice_id = $input['invoice_id'] ?? '';
$amount     = $input['amount'] ?? 0;

// 3. Lấy invoice_id từ student_service
if ($student_id) {
    $student_service_url = "http://localhost/KTHDV_GK_IBANKING/api_gateway/index.php?service=student&action=get_invoice";
    $invoice_payload = json_encode(["student_id" => $student_id]);

    $ch = curl_init($student_service_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $invoice_payload);
    $invoice_response = curl_exec($ch);
    curl_close($ch);

    $invoice_data = json_decode($invoice_response, true);
    $invoice_id = $invoice_data['invoice_id'] ?? '';
}

// 4. Kiểm tra dữ liệu bắt buộc
error_log("==== DEBUG create_payment ====");
error_log("student_id: " . $student_id);
error_log("user_id: " . $user_id);
error_log("invoice_id: " . $invoice_id);
error_log("amount: " . $amount);
error_log("================================");
if (!$student_id || !$user_id || !$invoice_id || $amount <= 0) {
    echo json_encode(["success" => false, "message" => "Thiếu dữ liệu"]);
    exit;
}

// 5. Tạo payment mới
$payment_id = "PAY" . time();

$sql = "INSERT INTO PAYMENTS (PAYMENT_ID, STUDENT_ID, USER_ID, INVOICE_ID, AMOUNT, STATUS, CREATED_AT) 
        VALUES (?, ?, ?, ?, ?, 'pending', NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssd", $payment_id, $student_id, $user_id, $invoice_id, $amount);

if ($stmt->execute()) {
    // 6. Gọi OTP service
    $otp_service_url = "http://localhost/KTHDV_GK_IBANKING/backend/otp_service/create_otp.php";
    $otp_payload = json_encode(["payment_id" => $payment_id, "user_id" => $user_id]);

    $ch = curl_init($otp_service_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $otp_payload);
    $otp_response = curl_exec($ch);
    curl_close($ch);

    $otp_result = json_decode($otp_response, true);

    if (!$otp_result || !$otp_result['success']) {
        echo json_encode(["success" => false, "message" => "Không thể gửi OTP"]);
        exit;
    }

    echo json_encode([
        "success" => true,
        "message" => "OTP đã được gửi, vui lòng xác nhận để hoàn tất thanh toán",
        "payment_id" => $payment_id,
        "status" => "pending"
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Lỗi khi tạo payment"]);
}

$stmt->close();
$conn->close();
?>
