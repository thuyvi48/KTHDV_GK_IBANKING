<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../../vendor/PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/../../vendor/PHPMailer-master/src/SMTP.php';
require __DIR__ . '/../../vendor/PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

// --- Nhận dữ liệu từ confirm_payment ---
$input = json_decode(file_get_contents("php://input"), true);

$email = trim($input['email'] ?? '');
$amount = $input['amount'] ?? 0;
$student_id = $input['student_id'] ?? '';
$invoice_id = $input['invoice_id'] ?? '';
$payment_id = $input['payment_id'] ?? '';

if (!$email || !$amount || !$student_id || !$invoice_id || !$payment_id) {
    echo json_encode(["success" => false, "message" => "Thiếu dữ liệu gửi email"]);
    exit;
}

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'minhthuhuynh23@gmail.com';      // 🔹 Gmail gửi đi
    $mail->Password   = 'kapendjgusnxwczc';              // 🔹 App Password Gmail
    $mail->SMTPSecure = 'ssl';
    $mail->Port       = 465;
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom('minhthuhuynh23@gmail.com', 'iMAGINE Banking');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = 'Xác nhận giao dịch thành công';
    $mail->Body = "
        <h3>Giao dịch thanh toán học phí thành công!</h3>
        <p><b>Mã giao dịch:</b> {$payment_id}</p>
        <p><b>Mã hóa đơn:</b> {$invoice_id}</p>
        <p><b>Mã sinh viên:</b> {$student_id}</p>
        <p><b>Số tiền:</b> " . number_format($amount, 0, ',', '.') . " VND</p>
        <br/>
        <p>Cảm ơn bạn đã sử dụng dịch vụ của iMAGINE.</p>
    ";

    $mail->send();
    echo json_encode(["success" => true, "message" => "Đã gửi email xác nhận giao dịch thành công"]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Gửi email thất bại: {$mail->ErrorInfo}"]);
}
?>
