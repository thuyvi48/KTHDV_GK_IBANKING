<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get':
        require_once 'get_user.php';
        break;

    default:
        echo json_encode(["error" => "Action không hợp lệ"]);
}
