<?php
header("Content-Type: application/json");
require_once("db.php");

$input = json_decode(file_get_contents("php://input"), true);
$payment_id = $input['payment_id'] ?? '';

if (!$payment_id) {
    echo json_encode(["success" => false, "message" => "Thiếu payment_id"]);
    exit;
}

// Update PAYMENTS thành success
$update = $conn->prepare("UPDATE PAYMENTS SET STATUS = 'success', CONFIRM_AT = NOW() WHERE PAYMENT_ID = ?");
$update->bind_param("s", $payment_id);
$update->execute();

// Thêm record vào TRANSACTIONS
$transaction_id = "TRANS" . time();
$type = "tuition_payment";
$desc = "Thanh toán học phí";

$sql = "INSERT INTO TRANSACTIONS (TRANSACTION_ID, PAYMENT_ID, USER_ID, BALANCE_AFTER, TYPE, CHANGE_AMOUNT, DESCRIPTION, CREATED_AT) 
        VALUES (?, ?, '', 0, ?, 0, ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $transaction_id, $payment_id, $type, $desc);
$stmt->execute();

echo json_encode([
    "success" => true,
    "message" => "Xác nhận thanh toán thành công",
    "transaction_id" => $transaction_id
]);

$stmt->close();
$conn->close();
