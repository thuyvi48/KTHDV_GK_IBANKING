<?php
require_once 'db.php';
header('Content-Type: application/json');

// Lấy transaction_id từ query string
if (!isset($_GET['id'])) {
    echo json_encode(["error" => "Thiếu tham số id"]);
    exit;
}

$transaction_id = $_GET['id'];

try {
    $stmt = $conn->prepare("SELECT TRANSACTION_ID, PAYMENT_ID, USER_ID, CHANGE_AMOUNT, BALANCE_AFTER, TYPE, DESCRIPTION, CREATED_AT 
                            FROM TRANSACTIONS 
                            WHERE TRANSACTION_ID = ?");
    $stmt->bind_param("s", $transaction_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode($row);
    } else {
        echo json_encode(["error" => "Không tìm thấy giao dịch"]);
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}

$conn->close();
?>
