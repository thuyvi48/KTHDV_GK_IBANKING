<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'create':
        require_once 'create_payment.php';
        break;

    case 'status':
        require_once 'get_payment_status.php';
        break;

    case 'confirm': 
    $raw = file_get_contents("php://input");
    $input = json_decode($raw, true);

    // Hỗ trợ cả hai dạng key: snake_case và camelCase
    $payment_id = $input['payment_id'] ?? $input['paymentId'] ?? '';
    $user_id    = $input['user_id'] ?? $input['userId'] ?? '';
    $otpCode    = $input['otpCode'] ?? $input['code'] ?? $input['otp'] ?? '';


    if (!$payment_id || !$user_id || !$otpCode) {
        echo json_encode(["success" => false, "message" => "Thiếu dữ liệu xác thực OTP"]);
        exit;
    }

    // Gọi file xử lý chính
    require_once("confirm_payment.php");
    break;

    case 'transactions': 
        require_once 'get_transactions.php';  
        break;

    default:
        echo json_encode(["error" => "Action Payment không hợp lệ"]);
}
