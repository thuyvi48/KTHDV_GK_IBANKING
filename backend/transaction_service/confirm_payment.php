<?php
header("Content-Type: application/json");
require_once("db.php");

$input = json_decode(file_get_contents("php://input"), true);

$payment_id = $input['payment_id'] ?? '';
$user_id    = $input['user_id'] ?? '';
$otp_code   = $input['otp_code'] ?? '';

if (!$payment_id || !$user_id || !$otp_code) {
    echo json_encode(["success" => false, "message" => "Thiếu ddữ liệu"]);
    exit;
}

// 1️⃣ Xác minh OTP trước
$otp_service_url = "http://localhost/KTHDV_GK_IBANKING/backend/otp_service/verify_otp.php";
$data = json_encode([
    "user_id" => $user_id,
    "otp_code" => $otp_code,
    "type" => "tuition"
]);

$ch = curl_init($otp_service_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
$otp_response = curl_exec($ch);
curl_close($ch);

$otp_result = json_decode($otp_response, true);

if (empty($otp_result['success'])) {
    echo json_encode(["success" => false, "message" => "OTP không hợp lệ hoặc đã hết hạn"]);
    exit;
}

// 2️⃣ Lấy thông tin payment
$stmt = $conn->prepare("SELECT USER_ID, STUDENT_ID, AMOUNT, INVOICE_ID, STATUS FROM PAYMENTS WHERE PAYMENT_ID = ?");
$stmt->bind_param("s", $payment_id);
$stmt->execute();
$result = $stmt->get_result();
$payment = $result->fetch_assoc();
$stmt->close();

if (!$payment) {
    echo json_encode(["success" => false, "message" => "Không tìm thấy payment"]);
    exit;
}

if ($payment['STATUS'] !== 'pending') {
    echo json_encode(["success" => false, "message" => "Giao dịch đã được xử lý"]);
    exit;
}

// 3️⃣ Kiểm tra số dư của người thanh toán
$stmt = $conn->prepare("SELECT BALANCE FROM USERS WHERE USER_ID = ?");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user || $user['BALANCE'] < $payment['AMOUNT']) {
    echo json_encode(["success" => false, "message" => "Số dư không đủ"]);
    exit;
}

// 4️⃣ Thực hiện giao dịch: trừ tiền user, gạch nợ student
$conn->begin_transaction();

try {
    // Trừ tiền user
    $stmt = $conn->prepare("UPDATE USERS SET BALANCE = BALANCE - ? WHERE USER_ID = ?");
    $stmt->bind_param("ds", $payment['AMOUNT'], $user_id);
    $stmt->execute();
    $stmt->close();

    // Gạch nợ học phí (update invoice)
    $stmt = $conn->prepare("UPDATE INVOICES SET STATUS = 'paid' WHERE INVOICE_ID = ?");
    $stmt->bind_param("s", $payment['INVOICE_ID']);
    $stmt->execute();
    $stmt->close();

    // Cập nhật trạng thái payment
    $stmt = $conn->prepare("UPDATE PAYMENTS SET STATUS = 'completed', COMPLETED_AT = NOW() WHERE PAYMENT_ID = ?");
    $stmt->bind_param("s", $payment_id);
    $stmt->execute();
    $stmt->close();

    $conn->commit();

    echo json_encode(["success" => true, "message" => "Thanh toán thành công"]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(["success" => false, "message" => "Lỗi khi xử lý thanh toán"]);
}

$conn->close();
?>
