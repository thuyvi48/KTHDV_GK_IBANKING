<?php
// backend/transaction_service/list_transaction.php
header('Content-Type: application/json');
require_once '../config.php';

// Lấy USER_ID từ GET hoặc POST
$userId = $_GET['user_id'] ?? '';

if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'Missing user_id']);
    exit;
}

// Truy vấn dữ liệu
$sql = "SELECT t.TRANSACTION_ID, t.PAYMENT_ID, t.TYPE, t.CHANGE_AMOUNT, t.BALANCE_AFTER, t.DESCRIPTION, t.CREATED_AT, t.STATUS,
               p.AMOUNT AS PAYMENT_AMOUNT, p.STATUS AS PAYMENT_STATUS
        FROM TRANSACTIONS t
        LEFT JOIN PAYMENTS p ON t.PAYMENT_ID = p.PAYMENT_ID
        WHERE t.USER_ID = :user_id
        ORDER BY t.CREATED_AT DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $userId]);
$transactions = $stmt->fetchAll();

echo json_encode(['success' => true, 'data' => $transactions]);
