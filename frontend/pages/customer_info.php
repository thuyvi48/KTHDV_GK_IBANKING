<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Gọi file config để dùng hàm callAPI() và BASE_URL
require_once __DIR__ . "/../config.php";

// Kiểm tra đăng nhập
$userId = $_SESSION['USER_ID'] ?? null;

$user = [
    "FULL_NAME" => "",
    "EMAIL" => "",
    "PHONE" => "",
    "BALANCE" => 0,
    "PAYMENT_STATUS" => ""
];

if ($userId) {
    // Gọi API Gateway để lấy thông tin người dùng
    $apiUrl = "http://localhost/KTHDV_GK_IBANKING/api_gateway/index.php";
    $resp = callAPI("GET", $apiUrl, [
        "service" => "user",
        "action"  => "get_user",
        "user_id" => $userId
    ]);

    if ($resp && isset($resp['FULL_NAME'])) {
        $user = $resp;

        // Gọi API Gateway để lấy trạng thái thanh toán
        $payResp = callAPI("GET", $apiUrl, [
            "service" => "transaction",
            "action"  => "get_payment_status",
            "user_id" => $userId
        ]);

        $user['PAYMENT_STATUS'] = $payResp['STATUS'] ?? "unknown";
    } else {
        $user['FULL_NAME'] = "Không tải được thông tin người dùng";
    }
} else {
    echo "<p style='color:red;'>Bạn chưa đăng nhập!</p>";
    exit;
}
?>



<style>
.customer-info-page.container {
    max-width: 100%;
    padding: 0px;
    margin-top: -5px; 
}

.customer-info-page, 
.customer-info-page * {
    font-family: 'Roboto', sans-serif;
}

.cust-form-card, .cust-security-card {
    background-color: #ffffff;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    margin-bottom: 20px;
}

.cust-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.cust-form-row {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}

.cust-form-group {
    display: flex;
    flex-direction: column;
    flex: 1;
    min-width: 200px;
}

.customer-info-page label {
    font-weight: 500;
    margin-bottom: 6px;
    color: #014d2e;
    font-size: 14px;
}

.customer-info-page input {
    padding: 10px 14px;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    font-size: 14px;
    background: #f8f9fa;
    color: #333;
}

.customer-info-page input:focus {
    outline: none;
    border-color: #65b806;
    background: white;
}

.btn-sm {
    padding: 4px 10px;
    font-size: 13px;
    border-radius: 6px;
}

</style>

<div class="customer-info-page container mt-4">
    <h1 class="mb-4">Thông tin tài khoản</h1>

    <!-- Thông tin cá nhân -->
    <div class="cust-form-card p-4">
        <form class="cust-form">
            <div class="cust-form-row">
                <div class="cust-form-group">
                    <label>Họ và tên</label>
                    <input type="text" value="<?php echo htmlspecialchars($user['FULL_NAME']); ?>" readonly>
                </div>
                <div class="cust-form-group">
                    <label>Email</label>
                    <input type="email" value="<?php echo htmlspecialchars($user['EMAIL']); ?>" readonly>
                </div>
                <div class="cust-form-group">
                    <label>Số điện thoại</label>
                    <input type="text" value="<?php echo htmlspecialchars($user['PHONE']); ?>" readonly>
                </div>
            </div>

            <div class="cust-form-row">
                <div class="cust-form-group">
                    <label>Số dư tài khoản</label>
                    <input type="text" value="<?php echo number_format($user['BALANCE'], 0, ',', '.'); ?> ₫" readonly>
                </div>

                <div class="cust-form-group">
                    <label>Trạng thái thanh toán</label>
                        <input type="text" 
                            value="<?php 
                                echo match(strtolower($user['PAYMENT_STATUS'] ?? '')) {
                                    'completed' => 'Hoàn tất',
                                    'pending'   => 'Đang chờ',
                                    'failed'    => 'Thất bại',
                                    default     => 'Không xác định'
                                }; 
                            ?>" 
                            readonly>
                </div>
            </div>
        </form>
    </div>
</div>

