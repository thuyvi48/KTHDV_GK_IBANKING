<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'list':
        require_once 'list_transactions.php';
        break;

    case 'get':
        require_once 'get_transaction.php';
        break;

    default:
        echo json_encode(["error" => "Action Transaction không hợp lệ"]);
}
