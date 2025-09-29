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

    default:
        echo json_encode(["error" => "Action Payment không hợp lệ"]);
}
