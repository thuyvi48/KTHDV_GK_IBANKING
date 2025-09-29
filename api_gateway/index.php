<?php
header('Content-Type: application/json');

$service = $_GET['service'] ?? '';
$action  = $_GET['action'] ?? '';

switch ($service) {
    case 'user':
        if ($action === 'get') {
            $id = $_GET['id'] ?? '';
            if (!$id) {
                echo json_encode(["error" => "Thiếu tham số id"]);
                exit;
            }
            $url = "http://localhost/KTHDV_GK_IBANKING/backend/user_service/get_user.php?id=" . urlencode($id);
            $response = @file_get_contents($url);

            if ($response === false) {
                echo json_encode(["error" => "Không thể kết nối user_service"]);
            } else {
                echo $response;
            }
        } else {
            echo json_encode(["error" => "Action user không hợp lệ"]);
        }
        break;

    case 'auth':
        if ($action === 'login') {
            $url = "http://localhost/KTHDV_GK_IBANKING/backend/auth_service/login.php";
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents("php://input"));
            $response = curl_exec($ch);
            
            if ($response === false) {
                echo json_encode(["error" => "Không thể kết nối auth_service"]);
            } else {
                echo $response;
            }

            curl_close($ch);
        } else {
            echo json_encode(["error" => "Action auth không hợp lệ"]);
        }
        break;

     case 'transaction':
        if ($action === 'list') {
            $user_id = $_GET['user_id'] ?? '';
            $url = "http://localhost/ibanking/backend/transaction_service/index.php?action=list&user_id=" . urlencode($user_id);
            $response = @file_get_contents($url);

            if ($response === false) {
                echo json_encode(["error" => "Không thể kết nối transaction_service"]);
            } else {
                echo $response;
            }
        } else {
            echo json_encode(["error" => "Action transaction không hợp lệ"]);
        }
        break;



    default:
        echo json_encode(["error" => "Service không hợp lệ"]);
}
