<?php
require_once("../db.php");

$input = json_decode(file_get_contents("php://input"), true);
$payment_id = $input['payment_id'];
$amount     = $input['amount'];
$type       = "tuition";

$stmt = $conn->prepare("INSERT INTO transactions (payment_id, type, amount, created_at) VALUES (?, ?, ?, NOW())");
$stmt->bind_param("isi", $payment_id, $type, $amount);
$stmt->execute();
echo json_encode(["success" => true, "message" => "Ghi nhận giao dịch thành công"]);
