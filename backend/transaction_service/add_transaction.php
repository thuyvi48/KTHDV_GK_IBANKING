<?php
header('Content-Type: application/json');

// DB transactions
$host = "localhost";
$user = "root";
$pass = "";
$db   = "paymentdb";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die(json_encode(["success"=>false,"message"=>"Kết nối thất bại: ".$conn->connect_error]));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success"=>false,"message"=>"Chỉ hỗ trợ POST"]);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);
$payment_id    = $input['payment_id'] ?? null;
$user_id       = $input['user_id'] ?? null;
$type          = $input['type'] ?? null; // deposit / withdraw
$change_amount = $input['change_amount'] ?? null;
$description   = $input['description'] ?? "";

if (!$user_id || !$type || !$change_amount) {
    echo json_encode(["success"=>false,"message"=>"Thiếu dữ liệu đầu vào"]);
    exit;
}

//  Lấy balance hiện tại từ user_service
$get_balance_url = "http://localhost/KTHDV_GK_IBANKING/backend/user_service/get_balance.php";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $get_balance_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(["user_id"=>$user_id]));
$balance_response = curl_exec($ch);
curl_close($ch);

$balance_data = json_decode($balance_response, true);
if (!$balance_data || !$balance_data['success']) {
    echo json_encode(["success"=>false,"message"=>"Không tìm thấy user"]);
    exit;
}

$current_balance = (float)$balance_data['balance'];

// 2Tính balance_after
if ($type === "CREDIT") {
    $balance_after = $current_balance + $change_amount;
} elseif ($type === "DEBIT") {
    if ($current_balance < $change_amount) {
        echo json_encode(["success"=>false,"message"=>"Số dư không đủ"]);
        exit;
    }
    $balance_after = $current_balance - $change_amount;
} else {
    echo json_encode(["success"=>false,"message"=>"Loại giao dịch không hợp lệ"]);
    exit;
}

// 3Tạo transaction_id
$res = $conn->query("SELECT TRANSACTION_ID FROM transactions ORDER BY TRANSACTION_ID DESC LIMIT 1");
if ($res && $row = $res->fetch_assoc()) {
    $num = (int)substr($row['TRANSACTION_ID'], 2)+1;
    $transaction_id = "TX".str_pad($num,4,"0",STR_PAD_LEFT);
} else {
    $transaction_id = "TX001";
}

// 4️⃣ Thêm vào bảng transactions
$balance_after = (float)$balance_after; // đảm bảo là double
$change_amount = (int)$change_amount;   // đảm bảo là int
$description   = (string)$description;

$stmt = $conn->prepare("INSERT INTO transactions 
    (TRANSACTION_ID, PAYMENT_ID, USER_ID, BALANCE_AFTER, TYPE, CHANGE_AMOUNT, DESCRIPTION, CREATED_AT, STATUS) 
    VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), 'DONE')");

// bind_param theo kiểu chính xác: sssdsis
$stmt->bind_param(
    "sssdsis", 
    $transaction_id,  // s
    $payment_id,      // s
    $user_id,         // s
    $balance_after,   // d (decimal)
    $type,            // s
    $change_amount,   // i
    $description      // s
);

if (!$stmt->execute()) {
    echo json_encode(["success"=>false,"message"=>"Lỗi khi thêm transaction"]);
    exit;
}

// 5️⃣ Gọi update_balance
$update_url = "http://localhost/KTHDV_GK_IBANKING/backend/user_service/update_balance.php";
$update_payload = json_encode([
    "user_id" => $user_id,
    "balance_after" => $balance_after
]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $update_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $update_payload);
$update_response = curl_exec($ch);
curl_close($ch);

$update_data = json_decode($update_response, true);
if (!$update_data || !$update_data['success']) {
    echo json_encode(["success"=>false,"message"=>"Cập nhật số dư thất bại"]);
    exit;
}

// 6️⃣ Trả về JSON
echo json_encode([
    "success" => true,
    "transaction_id" => $transaction_id,
    "user_id" => $user_id,
    "balance_before" => $current_balance,
    "balance_after" => $balance_after,
    "type" => $type,
    "change_amount" => $change_amount,
    "description" => $description
]);

?>
