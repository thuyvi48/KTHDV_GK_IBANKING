<?php
require_once 'db.php';
header('Content-Type: application/json');

// Nhận transaction_id từ request
$transaction_id = $_GET['transaction_id'] ?? '';
$user_id = $_GET['user_id'] ?? '';

if (!$transaction_id || !$user_id) {
    echo json_encode(["error" => "Thiếu tham số transaction_id hoặc user_id"]);
    exit;
}

// Sinh OTP 6 số
$otp_code = rand(100000, 999999);
$otp_id = uniqid("OTP");
$status = "PENDING";
$created_at = date("Y-m-d H:i:s");
$expires_at = date("Y-m-d H:i:s", strtotime("+60 seconds"));
$attempts = 0;

// Lưu vào DB
$sql = "INSERT INTO OTPS (OTP_ID, TRANSACTION_ID, USER_ID, CODE, STATUS, CREATED_AT, EXPIRES_AT, ATTEMPTS)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssssi", $otp_id, $transaction_id, $user_id, $otp_code, $status, $created_at, $expires_at, $attempts);

if ($stmt->execute()) {
    // 🔹 Lấy email user từ bảng USERS
    $sql_user = "SELECT EMAIL FROM USERS WHERE USER_ID = ?";
    $stmt_user = $conn->prepare($sql_user);
    $stmt_user->bind_param("s", $user_id);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();

    if ($row = $result_user->fetch_assoc()) {
        $to = $row['EMAIL'];
        $subject = "Mã OTP xác nhận giao dịch";
        $message = "Mã OTP của bạn là: $otp_code. Hết hạn sau 60 giây.";
        $headers = "From: no-reply@ibanking.com";

        // Gửi mail (PHP mail function, cần cấu hình sendmail/SMTP trong XAMPP)
        mail($to, $subject, $message, $headers);
    }

    echo json_encode(["success" => true, "otp_id" => $otp_id]);
} else {
    echo json_encode(["error" => "Không thể tạo OTP"]);
}

$stmt->close();
$conn->close();
?>
