<?php
header("Content-Type: application/json");
require_once("db.php");

$input = json_decode(file_get_contents("php://input"), true);
$payment_id = $input['payment_id'] ?? '';

if (!$payment_id) {
    echo json_encode(["success" => false, "message" => "Thiếu payment_id"]);
    exit;
}

// Lấy thông tin payment
$otp_service_url = "http://localhost/backend/otp_service/create_otp.php"; 

$response = file_get_contents($otp_service_url . "?payment_id=" . urlencode($payment_id));
$otp_result = json_decode($response, true);

if (!$otp_result['success']) {
    echo json_encode(["success" => false, "message" => "Không thể tạo OTP"]);
    exit;
}

echo json_encode([
    "success" => true,
    "message" => "OTP đã được gửi, vui lòng xác nhận",
    "payment_id" => $payment_id
]);