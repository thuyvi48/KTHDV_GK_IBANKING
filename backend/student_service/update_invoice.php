<?php
header("Content-Type: application/json");
require_once __DIR__ . "/db.php";

// Đọc dữ liệu JSON
$input = json_decode(file_get_contents("php://input"), true) ?? [];
if (empty($input)) {
    $input = $_GET;
}

$invoice_id  = trim($input['invoice_id'] ?? '');
$amount_paid = isset($input['amount_paid']) ? floatval($input['amount_paid']) : null;

if ($invoice_id === '') {
    echo json_encode(["success" => false, "message" => "Thiếu mã hóa đơn (invoice_id)"]);
    exit;
}

// Lấy thông tin hóa đơn
$stmt = $conn->prepare("SELECT AMOUNT_DUE, AMOUNT_PAID FROM TUITION_INVOICES WHERE INVOICE_ID = ?");
$stmt->bind_param("s", $invoice_id);
$stmt->execute();
$res = $stmt->get_result();

if (!$row = $res->fetch_assoc()) {
    echo json_encode(["success" => false, "message" => "Không tìm thấy hóa đơn"]);
    $stmt->close();
    $conn->close();
    exit;
}
$stmt->close();

$current_paid = (float)$row['AMOUNT_PAID'];
$amount_due   = (float)$row['AMOUNT_DUE'];

// Chỉ cập nhật thanh toán một phần duy nhất
if ($amount_paid === null || $amount_paid <= 0) {
    echo json_encode(["success" => false, "message" => "Thiếu dữ liệu amount_paid hợp lệ"]);
    $conn->close();
    exit;
}

$new_paid = $amount_paid; 
if ($new_paid >= $amount_due) {
    $new_paid = $amount_due;
    $new_status = 'PAID';
} else {
    $new_status = 'UNPAID';
}

// Cập nhật vào DB
$update = $conn->prepare("UPDATE TUITION_INVOICES 
                          SET AMOUNT_PAID = ?, STATUS = ?, UPDATED_AT = NOW() 
                          WHERE INVOICE_ID = ?");
$update->bind_param("dss", $new_paid, $new_status, $invoice_id);

if ($update->execute()) {
    echo json_encode([
        "success"      => true,
        "message"      => "Cập nhật hóa đơn thành công",
        "invoice_id"   => $invoice_id,
        "amount_due"   => $amount_due,
        "amount_paid"  => $new_paid,
        "status"       => $new_status
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Lỗi khi cập nhật hóa đơn"]);
}

$update->close();
$conn->close();
?>
