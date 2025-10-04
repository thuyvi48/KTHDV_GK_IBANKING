<?php
header("Content-Type: application/json");
require_once("db.php");

$sql = "SELECT t.TRANSACTION_ID, t.PAYMENT_ID, p.STUDENT_ID, p.INVOICE_ID, p.AMOUNT, p.STATUS, t.CREATED_AT 
        FROM TRANSACTIONS t 
        JOIN PAYMENTS p ON t.PAYMENT_ID = p.PAYMENT_ID
        ORDER BY t.CREATED_AT DESC";

$result = $conn->query($sql);

$transactions = [];
while ($row = $result->fetch_assoc()) {
    $transactions[] = $row;
}

echo json_encode([
    "transactions" => $transactions
]);
$conn->close();
