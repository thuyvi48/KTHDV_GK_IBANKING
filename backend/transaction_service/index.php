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

        $paymentId = $input['paymentId'] ?? '';
        $userId = $input['userId'] ?? '';
        $code = $input['code'] ?? '';

        if (!$paymentId || !$userId || !$code) {
            echo json_encode(["success" => false, "message" => "Thiếu dữ liệu xác thực OTP"]);
            exit;
        }

        // Xử lý xác thực OTP ở đây
        require_once("confirm_payment.php");
        break;

    case 'transactions': 
        require_once 'get_transactions.php';  
        break;

    default:
        echo json_encode(["error" => "Action Payment không hợp lệ"]);
}
