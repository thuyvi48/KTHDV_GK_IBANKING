<?php
header("Content-Type: application/json");
require_once "db.php";

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'list':
        $user_id = $_GET['user_id'] ?? '';
        if (!$user_id) {
            echo json_encode(["error" => "Thiếu user_id"]);
            exit;
        }

        $stmt = $conn->prepare("SELECT * FROM TRANSACTIONS WHERE USER_ID = ? ORDER BY CREATED_AT DESC");
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $transactions = [];
        while ($row = $result->fetch_assoc()) {
            $transactions[] = $row;
        }

        echo json_encode($transactions);
        break;

    default:
        echo json_encode(["error" => "Action không hợp lệ"]);
}
