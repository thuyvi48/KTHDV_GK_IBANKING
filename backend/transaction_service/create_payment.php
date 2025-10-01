<?php
require_once 'db.php';
header('Content-Type: application/json');

// Đọc dữ liệu JSON từ request body
$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['user_id']) || !isset($data['student_id']) || !isset($data['invoice_id']) || !isset($data['amount'])) {
    echo json_encode(["error" => "Thiếu tham số"]);
    exit;
}

// Sinh PAYMENT_ID (ví dụ P001, P002...)
$payment_id = 'P' . rand(100, 999);

$user_id    = $data['user_id'];
$student_id = $data['student_id'];
$invoice_id = $data['invoice_id'];
$amount     = $data['amount'];
$status     = "PENDING";
$idepotency = uniqid("key_"); // tạo idempotency key ngẫu nhiên
$created_at = date("Y-m-d H:i:s");

try {
    $stmt = $conn->prepare("INSERT INTO PAYMENTS (PAYMENT_ID, USER_ID, STUDENT_ID, INVOICE_ID, AMOUNT, STATUS, IDEPOTENCY, CREATED_AT) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $payment_id, $user_id, $student_id, $invoice_id, $amount, $status, $idepotency, $created_at);

    if ($stmt->execute()) {
        echo json_encode([
            "success" => true,
            "payment_id" => $payment_id,
            "status" => $status
        ]);
    } else {
        echo json_encode(["error" => "Không thể tạo payment"]);
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}

$conn->close();
?>
