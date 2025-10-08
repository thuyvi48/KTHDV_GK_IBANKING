<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/db.php';
require __DIR__ . '/../../vendor/PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/../../vendor/PHPMailer-master/src/SMTP.php';
require __DIR__ . '/../../vendor/PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

$input = json_decode(file_get_contents("php://input"), true);

$payment_id  = trim($input['payment_id'] ?? '');
$user_id     = trim($input['user_id'] ?? '');
$email       = trim($input['email'] ?? '');
$ttl_seconds = intval($input['ttlSeconds'] ?? 300);
$ttl_minutes = $ttl_seconds / 60; 
if (!$payment_id || !$user_id || !$email) {
    echo json_encode(["success" => false, "message" => "Thiếu dữ liệu (payment_id, user_id hoặc email)"]);
    exit;
}

// --- Sinh OTP ---
$code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
$expires_at = date("Y-m-d H:i:s", time() + $ttl_seconds);
$otp_id = uniqid("OTP");

// --- Lưu OTP ---
$stmt = $conn->prepare("
    INSERT INTO OTPS (OTP_ID, USER_ID, PAYMENT_ID, CODE, IS_USED, CREATED_AT, EXPIRES_AT)
    VALUES (?, ?, ?, ?, 0, NOW(), ?)
");
$stmt->bind_param("sssss", $otp_id, $user_id, $payment_id, $code, $expires_at);

if (!$stmt->execute()) {
    echo json_encode(["success" => false, "message" => "Không thể lưu OTP vào cơ sở dữ liệu"]);
    exit;
}
$stmt->close();
$conn->close();

// --- Gửi email OTP ---
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'minhthuhuynh23@gmail.com';
    $mail->Password   = 'kapendjgusnxwczc';
    $mail->SMTPSecure = 'ssl';
    $mail->Port       = 465;
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom('minhthuhuynh23@gmail.com', 'iMAGINE Banking');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = 'Mã OTP xác thực giao dịch';
    $mail->Body    = "
        <h2>Xác nhận giao dịch thanh toán</h2>
        <p>Mã OTP của bạn là: <b style='font-size:20px;color:#007bff;'>$code</b></p>
        <p>OTP này chỉ có hiệu lực trong <b>$ttl_minutes phút</b>.</p>
        <hr>
        <p style='font-size:13px;color:#666;'>Nếu bạn không thực hiện giao dịch này, vui lòng bỏ qua email.</p>
    ";

    $mail->send();

    echo json_encode([
        "success"     => true,
        "message"     => "OTP đã được gửi đến email $email",
        "otpSentTime" => time(),
        "expiresIn"   => $ttl_seconds,
        "payment_id"  => $payment_id
    ]);

} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Không thể gửi OTP qua email: {$mail->ErrorInfo}"]);
}
