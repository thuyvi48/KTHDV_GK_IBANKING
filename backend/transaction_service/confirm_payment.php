<?php
header('Content-Type: application/json');
require __DIR__ . '/db.php';

$input = json_decode(file_get_contents("php://input"), true) ?? [];

$payment_id = trim($input['payment_id'] ?? $input['paymentId'] ?? '');
$user_id    = trim($input['user_id'] ?? $input['userId'] ?? '');
$otpCode    = trim($input['otpCode'] ?? $input['code'] ?? $input['otp'] ?? $input['otp_code'] ?? '');

if ($payment_id === '' || $user_id === '' || $otpCode === '') {
    echo json_encode(["success" => false, "message" => "Thiếu dữ liệu xác thực OTP"]);
    exit;
}

/* 1 GỌI OTP SERVICE ĐỂ XÁC THỰC OTP */
$otpUrl = "http://localhost/KTHDV_GK_IBANKING/backend/otp_service/verify_otp_pay.php";

$ch = curl_init($otpUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    "payment_id" => $payment_id,
    "user_id"    => $user_id,
    "otpCode"    => $otpCode
]));
$otpRes = curl_exec($ch);
$curl_err = curl_error($ch);
curl_close($ch);

if ($curl_err) {
    echo json_encode(["success" => false, "message" => "Không thể kết nối tới OTP service: $curl_err"]);
    exit;
}

$otpJson = json_decode($otpRes, true);
if (!$otpJson || empty($otpJson['success'])) {
    echo json_encode(['success' => false, 'message' => $otpJson['message'] ?? 'OTP không hợp lệ']);
    exit;
}

/* 2 Nếu OTP hợp lệ thì tiếp tục xử lý giao dịch */
$stmt = $conn->prepare("SELECT STUDENT_ID, INVOICE_ID, AMOUNT FROM PAYMENTS WHERE PAYMENT_ID=? AND USER_ID=? LIMIT 1");
$stmt->bind_param("ss", $payment_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$row = $result->fetch_assoc()) {
    echo json_encode(["success" => false, "message" => "Không tìm thấy giao dịch"]);
    exit;
}
$stmt->close();

/*3 Cập nhật trạng thái payment */
$stmt = $conn->prepare("UPDATE PAYMENTS SET STATUS='done', CONFIRM_AT=NOW() WHERE PAYMENT_ID=?");
$stmt->bind_param("s", $payment_id);
$stmt->execute();
$stmt->close();

/* 4 Phản hồi thành công */
echo json_encode([
    "success"      => true,
    "message"      => "Xác thực OTP và cập nhật giao dịch thành công",
    "payment_id"   => $payment_id,
    "user_id"      => $user_id,
    "student_id"   => $row['STUDENT_ID'],
    "invoice_id"   => $row['INVOICE_ID'],
    "amount"       => (float)$row['AMOUNT']
]);

/* 5 Trừ tiền người gửi */
$getBalanceUrl = "http://localhost/KTHDV_GK_IBANKING/backend/user_service/get_balance.php";
$options = [
    'http' => [
        'header'  => "Content-Type: application/json\r\n",
        'method'  => 'POST',
        'content' => json_encode(['user_id' => $user_id]),
    ]
];
$context  = stream_context_create($options);
$response = file_get_contents($getBalanceUrl, false, $context);
$data = json_decode($response, true);

$current_balance = $data['balance'] ?? 0;
$balance_after   = $current_balance - $row['AMOUNT'];

// Gọi update_balance
$updateUrl = "http://localhost/KTHDV_GK_IBANKING/backend/user_service/update_balance.php";
$options = [
    'http' => [
        'header'  => "Content-Type: application/json\r\n",
        'method'  => 'POST',
        'content' => json_encode([
            'user_id'       => $user_id,
            'balance_after' => $balance_after
        ]),
    ]
];
$context  = stream_context_create($options);
$response = file_get_contents($updateUrl, false, $context);
$updateRes = json_decode($response, true);

/* 6 Gạch nợ học phí */
$invoiceUrl = "http://localhost/KTHDV_GK_IBANKING/backend/student_service/update_invoice.php";
$payload2 = [
    "invoice_id"  => $row['INVOICE_ID'],
    "amount_paid" => $row['AMOUNT']  // số tiền vừa thanh toán
];

$options = [
    "http" => [
        "header"  => "Content-Type: application/json\r\n",
        "method"  => "POST",
        "content" => json_encode($payload2),
    ]
];
$context = stream_context_create($options);
$response = file_get_contents($invoiceUrl, false, $context);

/* 7 Gửi email xác nhận giao dịch */
$userUrl = "http://localhost/KTHDV_GK_IBANKING/backend/user_service/get_user.php?user_id=" . urlencode($user_id);
$response = file_get_contents($userUrl);
$userData = json_decode($response, true);

$payer_email = $userData['EMAIL'] ?? '';


if ($payer_email) {
    $emailUrl = "http://localhost/KTHDV_GK_IBANKING/backend/otp_service/send_success.php";
    $emailPayload = [
        "email"      => $payer_email,
        "amount"     => $row['AMOUNT'],
        "student_id" => $row['STUDENT_ID'],
        "invoice_id" => $row['INVOICE_ID'],
        "payment_id" => $payment_id
    ];

    $opts = [
        "http" => [
            "method"  => "POST",
            "header"  => "Content-Type: application/json",
            "content" => json_encode($emailPayload)
        ]
    ];
    $context = stream_context_create($opts);
    $emailRes = file_get_contents($emailUrl, false, $context);
    $emailJson = json_decode($emailRes, true);

    if (!$emailJson || !$emailJson['success']) {
        error_log("Không gửi được email xác nhận: " . ($emailJson['message'] ?? 'Unknown error'));
    }
} else {
    error_log("Không tìm thấy email của user_id=$user_id để gửi xác nhận giao dịch");
}

/* 8 Ghi log giao dịch vào transaction_service */
$transactionUrl = "http://localhost/KTHDV_GK_IBANKING/backend/transaction_service/add_transaction.php";

$txnPayload = [
    "payment_id"    => $payment_id,
    "user_id"       => $user_id,
    "type"          => "DEBIT",  // vì là thanh toán học phí → trừ tiền
    "change_amount" => (float)$row['AMOUNT'],
    "description"   => "Thanh toán học phí - ".$row['INVOICE_ID']
];

$opts = [
    "http" => [
        "method"  => "POST",
        "header"  => "Content-Type: application/json",
        "content" => json_encode($txnPayload)
    ]
];
$context = stream_context_create($opts);
$txnRes = file_get_contents($transactionUrl, false, $context);
$txnJson = json_decode($txnRes, true);

if (!$txnJson || !$txnJson['success']) {
    error_log("Không ghi được transaction: ".($txnJson['message'] ?? 'Unknown error'));
}

exit;
