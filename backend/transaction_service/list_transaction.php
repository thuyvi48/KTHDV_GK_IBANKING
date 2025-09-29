<?php
require_once 'db.php';
header('Content-Type: application/json');

// Lấy user_id từ query string
if (!isset($_GET['user_id'])) {
    echo json_encode(["error" => "Thiếu tham số user_id"]);
    exit;
}

$user_id = $_GET['user_id'];

try {
    $stmt = $conn->prepare("SELECT TRANSACTION_ID, PAYMENT_ID, USER_ID, CHANGE_AMOUNT, BALANCE_AFTER, TYPE, DESCRIPTION, CREATED_AT 
                            FROM TRANSACTIONS 
                            WHERE USER_ID = ?
                            ORDER BY CREATED_AT DESC");
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $transactions = [];
    while ($row = $result->fetch_assoc()) {
        $transactions[] = $row;
    }

    echo json_encode($transactions);

    $stmt->close();
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}

$conn->close();
?>
