<?php
header("Content-Type: application/json");
require_once "db.php"; // file kết nối DB

if (!isset($_GET['user_id'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing user_id"]);
    exit;
}

$userId = $_GET['user_id'];

// Lấy 5 giao dịch gần nhất theo USER_ID
$sql = "SELECT TRANSACTION_ID, USER_ID, PAYMENT_ID, BALANCE_AFTER, TYPE, CHANGE_AMOUNT, DESCRIPTION, CREATED_AT
        FROM TRANSACTIONS
        WHERE USER_ID = ?
        ORDER BY CREATED_AT DESC
        LIMIT 5";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $userId);
$stmt->execute();
$result = $stmt->get_result();

$transactions = [];
while ($row = $result->fetch_assoc()) {
    $transactions[] = [
        "transaction_id" => $row['TRANSACTION_ID'],
        "payment_id"     => $row['PAYMENT_ID'],
        "user_id"        => $row['USER_ID'],
        "balance_after"  => (float)$row['BALANCE_AFTER'],
        "type"           => $row['TYPE'],
        "change_amount"  => (float)$row['CHANGE_AMOUNT'],
        "description"    => $row['DESCRIPTION'],
        "created_at"     => $row['CREATED_AT']
    ];
}

echo json_encode($transactions);
?>
