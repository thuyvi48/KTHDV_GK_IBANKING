<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/db.php'; // kết nối otpdb
require __DIR__ . '/../../vendor/PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/../../vendor/PHPMailer-master/src/SMTP.php';
require __DIR__ . '/../../vendor/PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

$input = json_decode(file_get_contents("php://input"), true);
$email = trim($input['email'] ?? '');

if (!$email) {
    echo json_encode(["error" => "Thiếu email"]);
    exit;
}

// --- gọi User Service để lấy USER_ID theo email ---
$userApiUrl = "http://localhost/KTHDV_GK_IBANKING/backend/user_service/get_user.php?email=" . urlencode($email);
$userResponse = @file_get_contents($userApiUrl);
if ($userResponse === false) {
    echo json_encode(["error" => "Không kết nối được User Service"]);
    exit;
}

$userData = json_decode($userResponse, true);
if (!isset($userData['USER_ID'])) {
    echo json_encode(["error" => "Không tìm thấy user với email này"]);
    exit;
}
$user_id = $userData['USER_ID'];

// --- sinh OTP ---
$otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
$expires_seconds = 60; // = 60s, đổi nếu muốn 300 (5 phút)
$expires_at = date("Y-m-d H:i:s", time() + $expires_seconds);

// --- lưu OTP vào otpdb.OTPS ---
$otp_id = uniqid("OTP");
$stmt = $conn->prepare("INSERT INTO OTPS (OTP_ID, USER_ID, CODE, STATUS, IS_USED, CREATED_AT, EXPIRES_AT) VALUES (?, ?, ?, 'ACTIVE', 0, NOW(), ?)");
$stmt->bind_param("ssss", $otp_id, $user_id, $otp, $expires_at);
if (!$stmt->execute()) {
    echo json_encode(["error" => "Lưu OTP thất bại"]);
    exit;
}
$stmt->close();
$conn->close();

// --- gửi mail bằng PHPMailer ---
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'minhthuhuynh23@gmail.com';      // thay bằng email bạn
    $mail->Password   = 'kapendjgusnxwczc';   // đặt App Password ở Gmail
    $mail->SMTPSecure = 'ssl';
    $mail->Port       = 465;
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom('your@gmail.com', 'iMAGINE App'); // thay
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = 'Mã OTP xác thực';
    $mail->Body    = "<h3>Mã OTP của bạn là: <b>$otp</b></h3><p>OTP sẽ hết hạn sau {$expires_seconds} giây.</p>";

    $mail->send();

    // trả về success + thời điểm gửi để frontend set countdown
    echo json_encode([
        "success" => "OTP đã gửi đến email",
        "otp_sent_time" => time(),
        "expires_in" => $expires_seconds
    ]);
} catch (Exception $e) {
    echo json_encode(["error" => "Không gửi được OTP: {$mail->ErrorInfo}"]);
}
