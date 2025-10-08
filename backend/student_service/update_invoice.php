<?php
header("Content-Type: application/json");
require_once("db.php");

// Lấy dữ liệu JSON từ body
$input = json_decode(file_get_contents("php://input"), true);

$invoice_id = $input['invoice_id'] ?? '';
$amount_paid = $input['amount_paid'] ?? 0;

if (!$invoice_id || $amount_paid <= 0) {
    echo json_encode(["success" => false, "message" => "Thiếu tham số invoice_id hoặc amount_paid không hợp lệ"]);
    exit;
}

// Lấy thông tin hóa đơn
$sql = "SELECT AMOUNT_DUE, AMOUNT_PAID FROM TUITION_INVOICES WHERE INVOICE_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $invoice_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$row = $result->fetch_assoc()) {
    echo json_encode(["success" => false, "message" => "Không tìm thấy hóa đơn"]);
    $stmt->close();
    $conn->close();
    exit;
}

$current_paid = $row['AMOUNT_PAID'] ?? 0;

$due = $row['AMOUNT_DUE'];

// Cộng thêm số tiền vừa thanh toán
$new_paid = $current_paid + $amount_paid;

// Xác định trạng thái mới
$status = "partial";
if ($new_paid >= $due) {
    $new_paid = $due;
    $status = "paid";
}

// Cập nhật DB
$update_sql = "UPDATE TUITION_INVOICES 
               SET AMOUNT_PAID = ?, STATUS = ?, UPDATED_AT = NOW() 
               WHERE INVOICE_ID = ?";
$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param("dss", $new_paid, $status, $invoice_id);

if ($update_stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Cập nhật hóa đơn thành công",
        "invoice_id" => $invoice_id,
        "amount_paid" => $new_paid,
        "amount_due" => $due,
        "status" => $status
    ]);

} else {
    echo json_encode(["success" => false, "message" => "Lỗi khi cập nhật hóa đơn"]);
}

$stmt->close();
$update_stmt->close();
$conn->close();
