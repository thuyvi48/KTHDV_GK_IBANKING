<?php
header('Content-Type: application/json');
require_once "db.php"; 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

<<<<<<< HEAD
echo json_encode([
    "transactions" => $transactions
]);
$conn->close();
=======
$user_id = $_SESSION['User_ID'] ?? $_GET['user_id'] ?? '';
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
            WHERE t.USER_ID = :user_id
            ORDER BY t.CREATED_AT DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_id' => $user_id]);
    $transactions = $stmt->fetchAll();

    echo json_encode(['success'=>true,'data'=>$transactions]);

} catch (PDOException $e) {
    echo json_encode(['success'=>false,'message'=>'Query failed: '.$e->getMessage()]);
}
?>
>>>>>>> 1b3cfeec964d30c13d9b396ad10f84b85e9dc211
