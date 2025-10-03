<?php
header("Content-Type: application/json");
require_once("db.php");

$input = json_decode(file_get_contents("php://input"), true);

$student_id = $input['student_id'] ?? '';
$user_id    = $input['user_id'] ?? '';
$invoice_id = $input['invoice_id'] ?? '';
$amount     = $input['amount'] ?? 0;

if (!$student_id || !$user_id || !$invoice_id || $amount <= 0) {
    echo json_encode(["success" => false, "message" => "Thiếu dữ liệu"]);
    exit;
}

// Tạo payment_id
$payment_id = "PAY" . time();

// Insert vào PAYMENTS
$sql = "INSERT INTO PAYMENTS (PAYMENT_ID, STUDENT_ID, USER_ID, INVOICE_ID, AMOUNT, STATUS, CREATED_AT) 
        VALUES (?, ?, ?, ?, ?, 'pending', NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssd", $payment_id, $student_id, $user_id, $invoice_id, $amount);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Khởi tạo thanh toán thành công",
        "payment_id" => $payment_id,
        "status" => "pending"
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Lỗi khi tạo payment"]);
}
$stmt->close();
$conn->close();
