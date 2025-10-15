<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require __DIR__ . '/db.php'; // kết nối tới otpdb
require __DIR__ . '/../../vendor/PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/../../vendor/PHPMailer-master/src/SMTP.php';
require __DIR__ . '/../../vendor/PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// --- Nhận dữ liệu từ frontend ---
$input = json_decode(file_get_contents("php://input"), true);
$payment_id = trim($input['payment_id'] ?? '');
$user_id    = trim($input['user_id'] ?? '');

if (!$payment_id || !$user_id) {
    echo json_encode(["success" => false, "message" => "Thiếu thông tin cần thiết"]);
    exit;
}

// --- Gọi user_service để lấy email ---
$userApiUrl = "http://localhost/KTHDV_GK_IBANKING/backend/user_service/get_email.php?user_id=" . urlencode($user_id);
$userResponse = @file_get_contents($userApiUrl);

if ($userResponse === false) {
    echo json_encode(["success" => false, "message" => "Không thể kết nối User Service"]);
    exit;
}

$userData = json_decode($userResponse, true);
if (empty($userData['success']) || empty($userData['email'])) {
    echo json_encode(["success" => false, "message" => "Không tìm thấy email người dùng"]);
    exit;
}

$email = $userData['email'];

// --- Sinh OTP mới ---
$otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
$expires_seconds = 30;
$expires_at = date("Y-m-d H:i:s", time() + $expires_seconds);

// --- Lưu OTP mới vào DB ---
$otp_id = uniqid("OTP");
$stmt = $conn->prepare("INSERT INTO OTPS (OTP_ID, PAYMENT_ID, USER_ID, CODE, STATUS, IS_USED, CREATED_AT, EXPIRES_AT) 
                        VALUES (?, ?, ?, ?, 'ACTIVE', 0, NOW(), ?)");
$stmt->bind_param("sssss", $otp_id, $payment_id, $user_id, $otp, $expires_at);

if (!$stmt->execute()) {
    echo json_encode(["success" => false, "message" => "Lưu OTP thất bại"]);
    exit;
}
$stmt->close();

// --- Gửi email ---
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'minhthuhuynh23@gmail.com';      // email của bạn
    $mail->Password   = 'kapendjgusnxwczc';              // app password
    $mail->SMTPSecure = 'ssl';
    $mail->Port       = 465;
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom('minhthuhuynh23@gmail.com', 'IBanking System');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = 'Mã OTP mới để xác thực giao dịch';
    $mail->Body    = "
        <h3>Mã OTP mới của bạn là: <b>$otp</b></h3>
        <p>OTP có hiệu lực trong <b>{$expires_seconds}</b> giây.</p>
        <p>Giao dịch: $payment_id</p>
    ";

    $mail->send();

    echo json_encode([
        "success" => true,
        "message" => "OTP mới đã được gửi tới email.",
        "otpExpiresIn" => $expires_seconds
    ]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Không gửi được OTP: {$mail->ErrorInfo}"]);
}
