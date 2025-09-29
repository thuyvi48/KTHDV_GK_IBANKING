<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'create':
        require_once 'create_otp.php';
        break;

    case 'verify':
        require_once 'verify_otp.php';
        break;

    default:
        echo json_encode(["error" => "Action OTP không hợp lệ"]);
}
