<?php
require_once 'db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

$otp_id = $data['otp_id'] ?? '';
$code   = $data['code'] ?? '';

if (!$otp_id || !$code) {
    echo json_encode(["error" => "Thiếu otp_id hoặc code"]);
    exit;
}

$sql = "SELECT * FROM OTPS WHERE OTP_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $otp_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $now = date("Y-m-d H:i:s");

    if ($row['STATUS'] == "SUCCESS") {
        echo json_encode(["error" => "OTP đã được sử dụng"]);
    } elseif ($now > $row['EXPIRES_AT']) {
        echo json_encode(["error" => "OTP đã hết hạn"]);
    } elseif ($row['CODE'] !== $code) {
        echo json_encode(["error" => "OTP không chính xác"]);
    } else {
        // Update trạng thái
        $update = $conn->prepare("UPDATE OTPS SET STATUS = 'SUCCESS' WHERE OTP_ID = ?");
        $update->bind_param("s", $otp_id);
        $update->execute();
        echo json_encode(["success" => true, "message" => "Xác thực thành công"]);
    }
} else {
    echo json_encode(["error" => "Không tìm thấy OTP"]);
}

$stmt->close();
$conn->close();
?>
