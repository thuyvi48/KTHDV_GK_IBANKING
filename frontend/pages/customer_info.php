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

// Nếu có user ID thì gọi API user_service
if ($userId) {
    // Lấy thông tin người dùng
    $apiUrl = "http://localhost/KTHDV_GK_IBANKING/api_gateway/index.php";
    $resp = callAPI("GET", $apiUrl, [
        "service" => "user",
        "action"  => "get_user",
        "user_id" => $userId
    ]);
    $resp = callAPI("GET", $apiUrl, ["user_id" => $userId]);

    if ($resp && isset($resp['FULL_NAME'])) {
        $user = $resp;
        $user['PASSWORD'] = "********";

        //  Gọi qua API Gateway để lấy trạng thái thanh toán
        $paymentApi = "http://localhost/KTHDV_GK_IBANKING/api_gateway/index.php";
        $payResp = callAPI("GET", $paymentApi, [
            "service" => "transaction",
            "action"  => "get_payment_status",
            "user_id" => $userId
        ]);


        if ($payResp && isset($payResp['STATUS'])) {
            $user['PAYMENT_STATUS'] = $payResp['STATUS'];
        } else {
            $user['PAYMENT_STATUS'] = "unknown";
        }
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

#changePasswordForm {
    margin-top: 10px;
    animation: fadeIn 0.3s ease-in-out;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-5px); }
    to { opacity: 1; transform: translateY(0); }
}
.password-input-group {
    display: flex;
    align-items: center;
    gap: 10px; 
    max-width: 300px; 
    margin-top: 6px;
}

.password-input-group input[type="password"] {
    flex-grow: 1;
    padding: 10px 14px; 
    border: 1px solid #434542ff; 
    background: white; 
}

.password-input-group .btn-change-password {
    flex-shrink: 0; 
    padding: 10px 15px; 
    font-size: 14px; 
    border-radius: 6px;
    background-color: #f0f0f0; 
    color: #333; 
    border: none;
    font-weight: 500;
    cursor: pointer;
    line-height: 1.2;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.new-password-input-group {
    display: flex;
    align-items: center;
    gap: 10px; 
    margin-top: 6px; 
}

.new-password-input-group input[type="password"] {
    flex-grow: 1; 
    padding: 10px 14px;
    border-color: #dee2e6;
    background: #f8f9fa;
}

.new-password-input-group input[type="password"]:focus {
    border-color: #65b806;
    background: white;
}

.new-password-input-group .btn-confirm {
    flex-shrink: 0;
    padding: 10px 15px; 
    font-size: 14px; 
    border-radius: 6px;
    background-color: #28a745; 
    color: white;
    border: none;
    font-weight: 500;
    cursor: pointer;
    line-height: 1.2;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
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

    <!-- Bảo mật -->
    <div class="cust-security-card p-4">
        <h3 class="mb-3">Tài khoản</h3>
            <div class="mb-3">
                <label for="currentPassword">Mật khẩu</label>
                
                <div class="password-input-group"> 
                    <input type="password" id="currentPassword" value="********" readonly>
                    <button class="btn-change-password" 
                            type="button" 
                            id="toggleChangePassword" 
                            onclick="toggleChangePassword()">
                        Đổi
                    </button>
                </div>
            </div>

        <!-- Form đổi mật khẩu (ẩn) -->
            <form id="changePasswordForm" onsubmit="return submitNewPassword(event)" style="display: none; max-width: 400px;">
                <div class="mb-3">
                    <label for="newPassword">Mật khẩu mới</label>
                    
                    <div class="new-password-input-group">
                        <input type="password" id="newPassword" placeholder="Nhập mật khẩu mới">
                        <button class="btn-sm btn-confirm" type="submit">Xác nhận</button>
                    </div>
                </div>
            </form>
    </div>
</div>
<script>
function toggleChangePassword() {
    const form = document.getElementById('changePasswordForm');
    form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'block' : 'none';
}

async function submitNewPassword(e) {
    e.preventDefault();
    const newPass = document.getElementById('newPassword').value.trim();

    if (newPass.length < 6) {
        alert("Mật khẩu phải có ít nhất 6 ký tự!");
        return false;
    }

    const userId = "<?php echo $userId; ?>";
    const apiUrl = "http://localhost/KTHDV_GK_IBANKING/api_gateway/index.php?service=user&action=change_password";

    try {
        const response = await fetch(apiUrl, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ user_id: userId, new_password: newPass })
        });

        const data = await response.json();

        if (data.status === "success") {
            alert("Đổi mật khẩu thành công!");
            document.getElementById('changePasswordForm').reset();
            document.getElementById('changePasswordForm').style.display = 'none';
        } else {
            alert(data.message || "Đổi mật khẩu thất bại!");
        }
    } catch (err) {
        console.error(err);
        alert("Lỗi khi kết nối API Gateway!");
    }

    return false;
}
</script>
