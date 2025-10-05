<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: pages/login.php");
    exit();
}
$userId = $_SESSION['user_id'];

$recent_transactions = [];

$apiUrl = "http://localhost/KTHDV_GK_IBANKING/api_gateway/index.php?service=transaction&action=recent&user_id=" . urlencode($userId);
$response = @file_get_contents($apiUrl);
$data = json_decode($response, true);

if ($data['success'] ?? false) {
    $recent_transactions = $data['data'];
}

$status_class = [
    'Đang chờ xử lý' => 'pending',
    'Hoàn tất'       => 'success',
    'Thất bại'       => 'failed'
];
// Gọi API user_service
$apiUrl = "http://localhost/KTHDV_GK_IBANKING/backend/user_service/get_user.php?user_id=" . urlencode($userId);
$response = file_get_contents($apiUrl);
$userData = json_decode($response, true);

$payer_name       = $userData['FULL_NAME'] ?? '';
$payer_phone      = $userData['PHONE'] ?? '';
$payer_email      = $userData['EMAIL'] ?? '';
$account_balance  = $userData['BALANCE'] ?? 0;

// Gọi API transaction_service qua API Gateway
$transApi = "http://localhost/KTHDV_GK_IBANKING/api_gateway/index.php?service=transaction&action=get_transaction&user_id=" . urlencode($userId) . "&limit=4";
$transResponse = file_get_contents($transApi);
$recent_transactions = json_decode($transResponse, true) ?? [];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Thanh toán học phí</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../frontend/assets/css/dashboard.css">
</head>
<body>
<div class="dashboard">
    <div class="dashboard-header">
        <h1><strong>Thanh toán</strong></h1>
    </div>
    
    <!-- Account Info -->
   <div class="account-cards">
    <div class="account-card primary">
        <div class="card-header">
            <h3>Số dư khả dụng</h3>
        </div>
        <div class="card-balance">
            <span class="balance-amount">
                <?php echo number_format($account_balance, 0, ',', '.'); ?> đ
            </span>
        </div>
    </div>
</div>
    <!-- Payment Form -->
    <div class="payment-form">
        <form id="paymentForm">
            <h2>Người nộp tiền</h2>
            <label>Họ tên:</label>
            <input type="text" name="payer_name" value="<?php echo $payer_name; ?>" readonly>
            
            <label>Số điện thoại:</label>
            <input type="text" name="payer_phone" value="<?php echo $payer_phone; ?>" readonly>
            
            <label>Email:</label>
            <input type="email" name="payer_email" value="<?php echo $payer_email; ?>" readonly>

            <h2 style="grid-column:1 / -1">Thông tin học phí</h2>

                <label>MSSV:</label>
                <input type="text" id="mssv" name="mssv" placeholder="Nhập MSSV">

                <label>Họ tên sinh viên:</label>
                <input type="text" id="student_name" name="student_name" readonly>

                <label>Số tiền cần nộp:</label>
                <input type="text" id="amount" name="amount" readonly>

            <h2>Thông tin thanh toán</h2>
            <label>Số dư khả dụng:</label>
            <input type="text" name="balance" value="<?php echo number_format($account_balance, 0, ',', '.'); ?> đ" readonly>
            
            <label>Số tiền học phí cần thanh toán:</label>
            <input type="text" name="amount_to_pay" readonly>
            
            <div class="agree-submit">
                <label>
                    <input type="checkbox" name="agree"> Tôi đồng ý với các 
                    <span style="color:blue; margin:0 4px;">thỏa thuận và điều khoản</span> 
                    của hệ thống iMAGINE
                </label>
                <button type="submit" disabled>Xác nhận giao dịch</button>
            </div>
            <div id="message" style="margin-top: 15px;"></div>
        </form>
    </div>
    <!-- Recent Transactions -->
   <div class="recent-transactions">
        <div class="section-header">
            <h2>Giao dịch gần đây</h2>
            <p><?php echo count($recent_transactions); ?> giao dịch mới nhất</p>
            <button class="btn-view-all" onclick="window.location.href='invoice_history.php'">Xem tất cả giao dịch</button>
        </div>
        <div class="transactions-list">
            <?php if(!empty($recent_transactions)): ?>
                <?php foreach($recent_transactions as $transaction): ?>
                    <div class="transaction-item">
                        <div class="transaction-icon <?php echo $transaction['type']; ?>">
                            <?php if($transaction['type'] === 'online_shopping'): ?>
                                <i class="fas fa-shopping-cart"></i>
                            <?php else: ?>
                                <i class="fas fa-exchange-alt"></i>
                            <?php endif; ?>
                        </div>
                        <div class="transaction-details">
                            <h4><?php echo htmlspecialchars($transaction['description']); ?></h4>
                            <p><?php echo $transaction['date']; ?></p>
                            <span class="transaction-status <?php echo $status_class[$transaction['status']] ?? ''; ?>">
                                <?php echo $transaction['status']; ?>
                            </span>
                        </div>
                        <div class="transaction-amount <?php echo $transaction['amount'] > 0 ? 'positive' : 'negative'; ?>">
                            <?php echo $transaction['amount'] > 0 ? '+' : ''; ?><?php echo number_format($transaction['amount'], 0, ',', '.'); ?> đ
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Chưa có giao dịch nào</p>
            <?php endif; ?>
        </div>
    </div>

</div>

<script>
function formatCurrency(amount) {
    return new Intl.NumberFormat("vi-VN").format(amount) + " đ";
}

// Format lại số dư khả dụng khi load trang
document.addEventListener("DOMContentLoaded", () => {
    let balanceField = document.querySelector("[name='balance']");
    if (balanceField && balanceField.value) {
        let raw = balanceField.value.replace(/[^\d]/g, "");
        balanceField.value = formatCurrency(parseInt(raw));
    }
});

// Khi nhập MSSV -> gọi API và hiển thị thông tin học phí
document.querySelector("[name='mssv']").addEventListener("blur", function() {
    let mssv = this.value.trim();
    if (!mssv) return;

   fetch("http://localhost/KTHDV_GK_IBANKING/api_gateway/index.php?service=student&action=get_invoice", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ mssv: mssv })
    })
    .then(res => res.json())
    .then(res => {
        if (res.success) {
            document.querySelector("[name='student_name']").value = res.student_name;
            document.querySelector("[name='amount']").value = formatCurrency(res.amount_due);
            document.querySelector("[name='amount_to_pay']").value = formatCurrency(res.amount_due);
            document.querySelector("[name='invoice_id']").value = res.invoice_id;
        } else {
            document.querySelector("#message").innerText = res.message;
            document.querySelector("[name='student_name']").value = "";
            document.querySelector("[name='amount']").value = "";
            document.querySelector("[name='amount_to_pay']").value = "";
            document.querySelector("[name='invoice_id']").value = "";
        }
    });
});

function showMessage(text, type = "error") {
    const msg = document.getElementById("message");
    msg.textContent = text;
    msg.style.color = (type === "success") ? "green" : "red";
}

document.getElementById("paymentForm").addEventListener("submit", function(e) {
    e.preventDefault();

    let balance = parseInt(
        document.querySelector("[name='balance']").value.replace(/[^\d]/g, "")
    );
    let amountToPay = parseInt(
        document.querySelector("[name='amount_to_pay']").value.replace(/[^\d]/g, "")
    );

    if (isNaN(amountToPay) || amountToPay <= 0) {
        showMessage("Chưa có thông tin học phí cần thanh toán.");
        return;
    }
    if (amountToPay > balance) {
        showMessage("Số dư khả dụng không đủ để thanh toán học phí.");
        return;
    }

    let data = {
        payer_name: document.querySelector("[name='payer_name']").value,
        payer_phone: document.querySelector("[name='payer_phone']").value,
        payer_email: document.querySelector("[name='payer_email']").value,
        mssv: document.querySelector("[name='mssv']").value,
        student_name: document.querySelector("[name='student_name']").value,
        amount_to_pay: amountToPay
    };

    fetch("http://localhost/KTHDV_GK_IBANKING/api_gateway/index.php?service=payment&action=create", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(res => {
        if (res.success) {
            showMessage("Thanh toán thành công. Mã giao dịch: " + res.payment_id, "success");
            setTimeout(() => {
                window.location.href = "transaction.php?payment_id=" + res.payment_id;
            }, 2000);
        } else {
            showMessage("Thanh toán thất bại: " + res.message);
        }
    })
    .catch(() => {
        showMessage("Có lỗi xảy ra khi kết nối hệ thống.");
    });
});

// Bắt sự kiện tick vào "Tôi đồng ý"
document.querySelector("[name='agree']").addEventListener("change", function() {
    const submitBtn = document.querySelector(".agree-submit button");
    if (this.checked) {
        submitBtn.disabled = false;
        submitBtn.style.cursor = "pointer"; 
    } else {
        submitBtn.disabled = true;
        submitBtn.style.cursor = "not-allowed";
    }
});
</script>

</body>
</html>

