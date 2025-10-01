<?php
$userId = $_SESSION['USER_ID'] ?? '';
if (!$userId) {
    header("Location: login.php");
    exit;
}

// Gọi API Gateway
function callApi($service, $action, $payload) {
    $url = "http://localhost/KTHDV_GK_IBANKING/backend/api_gateway/index.php?service=$service&action=$action";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

// Lấy thông tin user
$user = callApi('user', 'get_info', ['user_id' => $userId]);

?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="page-header mb-4">
                <h2><i class="fas fa-user-circle me-2"></i>Thông tin khách hàng</h2>
                <p class="text-muted">Quản lý thông tin cá nhân và tài khoản</p>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Thông tin cá nhân -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Thông tin cá nhân</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Họ và tên</label>
                            <p class="fw-bold fs-6"><?php echo htmlspecialchars($user['FULL_NAME'] ?? 'Chưa cập nhật'); ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Email</label>
                            <p class="fw-bold fs-6"><?php echo htmlspecialchars($user['EMAIL'] ?? 'Chưa cập nhật'); ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Số điện thoại</label>
                            <p class="fw-bold fs-6"><?php echo htmlspecialchars($user['PHONE'] ?? 'Chưa cập nhật'); ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Tên đăng nhập</label>
                            <p class="fw-bold fs-6"><?php echo htmlspecialchars($user['USENAME'] ?? 'Chưa cập nhật'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thông tin tài khoản -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-wallet me-2"></i>Tài khoản</h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <label class="form-label text-muted">Số dư khả dụng</label>
                        <h3 class="text-success fw-bold">
                            <?php echo number_format($user['BALANCE'] ?? 0, 0, ',', '.'); ?> đ
                        </h3>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Trạng thái tài khoản</label>
                        <p class="mb-0">
                            <span class="badge bg-success">Hoạt động</span>
                        </p>
                    </div>
                    <button class="btn btn-primary btn-sm" onclick="showRechargeModal()">
                        <i class="fas fa-plus-circle me-1"></i>Nạp tiền
                    </button>
                </div>
            </div>

            <!-- Thống kê nhanh -->
            <div class="card shadow-sm mt-3">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Thống kê</h6>
                </div>
                <div class="card-body">
                    <?php
                    // Lấy số lượng giao dịch từ bảng transactions
                    $transactionCountSql = "SELECT COUNT(*) as total FROM transactions WHERE USER_ID = ?";
                    $transactionStmt = $conn->prepare($transactionCountSql);
                    $transactionStmt->bind_param("s", $userId);
                    $transactionStmt->execute();
                    $transactionResult = $transactionStmt->get_result();
                    $transactionCount = $transactionResult->fetch_assoc()['total'] ?? 0;
                    
                    // Lấy tổng số tiền giao dịch thành công
                    $totalAmountSql = "SELECT SUM(AMOUNT) as total FROM transactions WHERE USER_ID = ? AND STATUS = 'SUCCESS'";
                    $totalStmt = $conn->prepare($totalAmountSql);
                    $totalStmt->bind_param("s", $userId);
                    $totalStmt->execute();
                    $totalResult = $totalStmt->get_result();
                    $totalAmount = $totalResult->fetch_assoc()['total'] ?? 0;
                    ?>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <small class="text-muted">Tổng giao dịch:</small>
                        <span class="fw-bold"><?php echo $transactionCount; ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <small class="text-muted">Đã thanh toán:</small>
                        <span class="fw-bold"><?php echo number_format($totalAmount, 0, ',', '.'); ?> đ</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">ID người dùng:</small>
                        <span class="fw-bold"><?php echo $userId; ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Nút hành động -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="card-title mb-3">Hành động</h6>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary" onclick="editProfile()">
                            <i class="fas fa-edit me-1"></i>Chỉnh sửa thông tin
                        </button>
                        <button type="button" class="btn btn-outline-warning" onclick="changePassword()">
                            <i class="fas fa-key me-1"></i>Đổi mật khẩu
                        </button>
                        <button type="button" class="btn btn-outline-info" onclick="viewHistory()">
                            <i class="fas fa-history me-1"></i>Lịch sử giao dịch
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nạp tiền -->
<div class="modal fade" id="rechargeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nạp tiền vào tài khoản</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="rechargeAmount" class="form-label">Số tiền nạp</label>
                        <input type="number" class="form-control" id="rechargeAmount" min="10000" step="1000">
                        <div class="form-text">Số tiền tối thiểu: 10,000 đ</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phương thức thanh toán</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="paymentMethod" value="bank" checked>
                            <label class="form-check-label">Chuyển khoản ngân hàng</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="paymentMethod" value="momo">
                            <label class="form-check-label">Ví MoMo</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-success">Nạp tiền</button>
            </div>
        </div>
    </div>
</div>

<style>
.page-header {
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 15px;
}

.card {
    border: none;
    border-radius: 10px;
}

.card-header {
    border-radius: 10px 10px 0 0 !important;
    border: none;
}

.btn-group .btn {
    margin-right: 5px;
}

.badge {
    font-size: 0.8rem;
}
</style>

<script>
function showRechargeModal() {
    const modal = new bootstrap.Modal(document.getElementById('rechargeModal'));
    modal.show();
}

function editProfile() {
    // Chuyển đến trang chỉnh sửa thông tin
    window.location.href = '?page=edit-profile';
}

function changePassword() {
    // Chuyển đến trang đổi mật khẩu
    window.location.href = '?page=change-password';
}

function viewHistory() {
    // Chuyển đến trang lịch sử giao dịch
    window.location.href = '?page=transaction-history';
}

// Auto refresh balance every 30 seconds
setInterval(function() {
    // Có thể thêm AJAX call để cập nhật số dư
}, 30000);
</script>