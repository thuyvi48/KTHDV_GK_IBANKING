<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../../vendor/PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/../../vendor/PHPMailer-master/src/SMTP.php';
require __DIR__ . '/../../vendor/PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

// Đọc dữ liệu từ frontend
$input = json_decode(file_get_contents("php://input"), true);
if (!isset($input['email']) || empty($input['email'])) {
    echo json_encode(["error" => "Thiếu email"]);
    exit;
}

$email = $input['email'];

// 🔹 Gọi user_service để lấy USER_ID theo email
$userApiUrl = "http://localhost/KTHDV_GK_IBANKING/backend/user_service/get_user.php?email=" . urlencode($email);
$userResponse = file_get_contents($userApiUrl);

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

// Sinh OTP 6 số ngẫu nhiên
$otp = rand(100000, 999999);
$expires_at = date("Y-m-d H:i:s", time() + 60); // hết hạn sau 60 giây

// 🔹 Kết nối DB OTP
$conn = new mysqli("localhost", "root", "", "otpdb"); 
if ($conn->connect_error) {
    echo json_encode(["error" => "DB lỗi: " . $conn->connect_error]);
    exit;
}

// Lưu OTP vào bảng OTPS
// Lưu OTP vào bảng OTPS
$otp_id = uniqid("OTP");
$stmt = $conn->prepare("INSERT INTO OTPS (OTP_ID, USER_ID, CODE, STATUS, CREATED_AT, EXPIRES_AT) 
                        VALUES (?, ?, ?, 'pending', NOW(), ?)");
$stmt->bind_param("ssss", $otp_id, $user_id, $otp, $expires_at);


if (!$stmt->execute()) {
    echo json_encode(["error" => "Lưu OTP thất bại"]);
    exit;
}
$stmt->close();
$conn->close();

// 🔹 Gửi mail bằng PHPMailer
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'minhthuhuynh23@gmail.com';   // Gmail của bạn
    $mail->Password   = 'kapendjgusnxwczc';           // App Password Gmail
    $mail->SMTPSecure = 'ssl';                        // Dùng SSL
    $mail->Port       = 465;                          // Port SSL
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom('minhthuhuynh23@gmail.com', 'iMAGINE App');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = 'Mã OTP xác thực';
    $mail->Body    = "<h3>Mã OTP của bạn là: <b>$otp</b></h3><p>OTP sẽ hết hạn sau 60 giây.</p>";

    $mail->send();
    echo json_encode(["success" => "OTP đã gửi đến email"]);
} catch (Exception $e) {
    echo json_encode(["error" => "Không gửi được OTP: {$mail->ErrorInfo}"]);
}
