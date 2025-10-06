<?php
header('Content-Type: application/json');
require_once "db.php"; // file này tạo $conn

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_id = $_SESSION['USER_ID'] ?? $_GET['user_id'] ?? '';
if (!$user_id) {
    echo json_encode(['success'=>false,'message'=>'Missing user id']);
    exit;
}

try {
    $sql = "SELECT t.TRANSACTION_ID, t.PAYMENT_ID, t.USER_ID, t.BALANCE_AFTER, t.TYPE, t.CHANGE_AMOUNT,
                   t.DESCRIPTION, t.CREATED_AT, t.STATUS,
                   p.AMOUNT AS PAYMENT_AMOUNT, p.STATUS AS PAYMENT_STATUS
            FROM TRANSACTIONS t
            LEFT JOIN PAYMENTS p ON t.PAYMENT_ID = p.PAYMENT_ID
            WHERE t.USER_ID = ?
            ORDER BY t.CREATED_AT DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $transactions = $result->fetch_all(MYSQLI_ASSOC);

    echo json_encode(['success'=>true,'data'=>$transactions]);

} catch (Exception $e) {
    echo json_encode(['success'=>false,'message'=>'Query failed: '.$e->getMessage()]);
}
