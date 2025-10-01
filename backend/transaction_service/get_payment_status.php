<?php
require_once 'db.php';
header('Content-Type: application/json');

// Lấy payment_id từ query string
if (!isset($_GET['payment_id'])) {
    echo json_encode(["error" => "Thiếu tham số payment_id"]);
    exit;
}

$payment_id = $_GET['payment_id'];

try {
    $stmt = $conn->prepare("SELECT PAYMENT_ID, USER_ID, STUDENT_ID, INVOICE_ID, AMOUNT, STATUS, CREATED_AT, CONFIRM_AT 
                            FROM PAYMENTS 
                            WHERE PAYMENT_ID = ?");
    $stmt->bind_param("s", $payment_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode($row);
    } else {
        echo json_encode(["error" => "Không tìm thấy payment"]);
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}

$conn->close();
?>
