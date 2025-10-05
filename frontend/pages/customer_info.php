    <?php
    if (session_status() === PHP_SESSION_NONE) session_start();

    $userId = $_SESSION['USER_ID'] ?? null;

    // Giả lập dữ liệu nếu chưa có API
    $user = [
        "FULL_NAME" => "Nguyễn Văn A",
        "EMAIL" => "nguyenvana@example.com",
        "PHONE" => "0912345678",
        "BALANCE" => 2500000,
        "PAYMENT_STATUS" => "completed" // pending | completed | failed
    ];
    ?>

    <style>
    .customer-info-page.container {
        max-width: 100%;
        padding: 0px;
        margin-top: -5px; 
    }

    .customer-info-page, 
    .customer-info-page * {
        font-family: 'Roboto', sans-serif ;
    }
    .cust-form-card, .cust-security-card {
        background-color: #ffffff;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        margin-bottom: 20px;
    }
    .cust-security-card .form-control {
    flex: 1;
}

.cust-security-card button.btn-sm {
    white-space: nowrap;
    height: 38px;
    padding: 0 12px;
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
                                    echo match($user['PAYMENT_STATUS']) {
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

    <!-- Mật khẩu -->
    <div class="mb-3">
        <label for="currentPassword" class="form-label">Mật khẩu</label>
        <div class="input-group" style="max-width: 400px;">
            <input type="password" id="currentPassword" class="form-control" value="12345678" readonly>
            <button class="btn btn-outline-warning btn-sm" type="button" id="toggleChangePassword" onclick="toggleChangePassword()">
                Đổi
            </button>
        </div>
    </div>

    <!-- Form đổi mật khẩu mới (ẩn mặc định) -->
    <form id="changePasswordForm" onsubmit="return submitNewPassword(event)" style="display: none; max-width: 400px;">
<!-- Mật khẩu -->
<div class="mb-3" style="max-width: 400px;">
    <label for="currentPassword" class="form-label">Mật khẩu</label>
    <div class="d-flex align-items-center gap-2">
        <input type="password" id="currentPassword" class="form-control" value="12345678" readonly>
        <button class="btn btn-outline-warning btn-sm flex-shrink-0" 
                type="button" 
                id="toggleChangePassword" 
                onclick="toggleChangePassword()">
            Đổi
        </button>
    </div>
</div>
    </form>

</div>


<script>
function toggleChangePassword() {
    const form = document.getElementById('changePasswordForm');
    form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'block' : 'none';
}

function submitNewPassword(e) {
    e.preventDefault();
    const newPass = document.getElementById('newPassword').value.trim();

    if (newPass.length < 6) {
        alert("Mật khẩu phải có ít nhất 6 ký tự!");
        return false;
    }

    // Giả lập gọi API đổi mật khẩu
    alert("Đổi mật khẩu thành công!");
    document.getElementById('changePasswordForm').reset();
    document.getElementById('changePasswordForm').style.display = 'none';
    return false;

}
</script>
