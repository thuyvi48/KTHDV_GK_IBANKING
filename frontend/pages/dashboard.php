<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['USER_ID'])) {
    header("Location: pages/login.php");
    exit();
}
$userId = $_SESSION['USER_ID'];

// ================= GỌI API USER =================
$apiUrl = "http://localhost/KTHDV_GK_IBANKING/backend/user_service/get_user.php?user_id=" . urlencode($userId);
$response = @file_get_contents($apiUrl);
$userData = json_decode($response, true);

$payer_name       = $userData['FULL_NAME'] ?? '';
$payer_phone      = $userData['PHONE'] ?? '';
$payer_email      = $userData['EMAIL'] ?? '';
$account_balance  = $userData['BALANCE'] ?? 0;

// ================= GỌI API TRANSACTION =================
// Lấy 4 giao dịch gần nhất qua API Gateway
$transApi = "http://localhost/KTHDV_GK_IBANKING/api_gateway/index.php?service=transaction&action=get_transaction&user_id=" . urlencode($userId) . "&limit=4";
$transResponse = @file_get_contents($transApi);
$transResult = json_decode($transResponse, true);

// Nếu API trả về thành công thì lấy data, ngược lại để mảng rỗng
$recent_transactions = [];
if ($transResult && isset($transResult['success']) && $transResult['success'] === true) {
    $recent_transactions = $transResult['data'];
}

// Map class cho trạng thái
$status_map = [
    'DONE'    => 'Hoàn tất',
    'PENDING' => 'Đang chờ xử lý',
    'FAILED'  => 'Thất bại'
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Thanh toán học phí</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../frontend/assets/css/dashboard.css">
        <style>
      /* nhỏ gọn style cho modal nếu css global ko cover */
      .modal-readonly { background:#f8f9fa; padding:12px; border-radius:6px; }
      .modal .modal-footer { border-top:0; }
    </style>
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
            <input type="hidden" name="invoice_id">
            <input type="hidden" name="student_id">
            <div class="agree-submit">
                <label>
                    <input type="checkbox" name="agree"> Tôi đồng ý với các 
                    <span style="color:blue; margin:0 4px; cursor:pointer;">thỏa thuận và điều khoản</span> 
                    của hệ thống iMAGINE
                </label>
                    <button type="submit">Xác nhận giao dịch</button>
            </div>
            <div id="message" style="margin-top: 15px;"></div>
        </form>
    </div>

    <!-- Recent Transactions -->
    <div class="recent-transactions">
        <div class="section-header">
            <h2>Giao dịch gần đây</h2>
            <p><?php echo count($recent_transactions); ?> giao dịch mới nhất</p>
            <button class="btn-view-all" onclick="window.location.href='/KTHDV_GK_IBANKING/frontend/index.php?page=transaction'">
                Xem tất cả giao dịch
            </button>
        </div>
        <div class="transactions-list">
            <?php if(!empty($recent_transactions)): ?>
                <?php foreach($recent_transactions as $transaction): ?>
                    <div class="transaction-item">
                        <div class="transaction-icon <?php echo $transaction['TYPE'] === 'CREDIT' ? 'CREDIT' : 'DEBIT'; ?>">
                            <?php if($transaction['TYPE'] === 'CREDIT'): ?>
                                <i class="fas fa-arrow-down"></i>
                            <?php else: ?>
                                <i class="fas fa-arrow-up"></i>
                            <?php endif; ?>
                        </div>
                        <div class="transaction-details">
                            <h4><?php echo htmlspecialchars($transaction['DESCRIPTION']); ?></h4>
                            <p class="date"><?php echo $transaction['CREATED_AT']; ?></p>
                            <span class="transaction-status <?php echo strtolower($transaction['STATUS']); ?>">
                                <?php echo $status_map[$transaction['STATUS']] ?? $transaction['STATUS']; ?>
                            </span>
                        </div>
                        <div class="transaction-amount <?php echo $transaction['TYPE'] === 'CREDIT' ? 'positive' : 'negative'; ?>">
                            <?php echo $transaction['TYPE'] === 'CREDIT' ? '+' : '-'; ?>
                            <?php echo number_format($transaction['CHANGE_AMOUNT'], 0, ',', '.'); ?> đ
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Chưa có giao dịch nào</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- MODAL: Confirm payment & nhập OTP -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Xác nhận giao dịch</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
      </div>
      <div class="modal-body">
        <div class="modal-readonly">
          <p><strong>MSSV:</strong> <span id="confirm_mssv"></span></p>
          <p><strong>Họ tên sinh viên:</strong> <span id="confirm_student_name"></span></p>
          <p><strong>Invoice ID:</strong> <span id="confirm_invoice_id"></span></p>
          <p><strong>Số tiền:</strong> <span id="confirm_amount_display"></span></p>
        </div>

        <div id="confirmMessage" class="mt-2"></div>

        <!-- Form nhập OTP (ẩn ban đầu, sẽ hiện sau khi gửi OTP) -->
        <div id="otpSection" style="display:none; margin-top:20px;">
          <hr>
          <p>Mã OTP đã được gửi đến email của bạn. Vui lòng nhập mã để xác nhận thanh toán.</p>
          <input type="text" id="otp_input" class="form-control" placeholder="Nhập mã OTP">
          <div id="otpMessage" class="mt-2" style="color:red;"></div>
          <button type="button" class="btn btn-success w-100 mt-3" id="verifyOtpBtn">Xác thực OTP</button>
        </div>
      </div>

      <div class="modal-footer">
        <button id="createPaymentBtn" type="button" class="btn btn-primary w-100">Tạo giao dịch & Gửi OTP</button>
      </div>
    </div>
  </div>
</div>


<!-- bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
function formatCurrency(amount) {
    return new Intl.NumberFormat("vi-VN").format(amount) + " đ";
}

document.addEventListener("DOMContentLoaded", () => {
    const balanceField = document.querySelector("[name='balance']");
    const submitBtn = document.querySelector(".agree-submit button");
    const agreeCheck = document.querySelector("[name='agree']");
    const mssvInput = document.querySelector("[name='mssv']");
    const messageBox = document.getElementById("message");

    // Modal
    const confirmModalEl = document.getElementById('confirmModal');
    const confirmModal = new bootstrap.Modal(confirmModalEl);
    const createPaymentBtn = document.getElementById('createPaymentBtn');
    const confirmMessage = document.getElementById('confirmMessage');
    const otpSection = document.getElementById('otpSection');
    const verifyOtpBtn = document.getElementById('verifyOtpBtn');
    const otpMessage = document.getElementById('otpMessage');

    let currentPaymentId = null;
    let isInvoiceValid = false;

    // Disable nút gửi ban đầu
    submitBtn.disabled = true;
    submitBtn.style.cursor = "not-allowed";
    submitBtn.style.opacity = "0.6";

    // Format tiền
    if (balanceField && balanceField.value) {
        let raw = balanceField.value.replace(/[^\d]/g, "");
        balanceField.value = formatCurrency(parseInt(raw));
    }

    function toggleSubmitButton() {
        if (agreeCheck.checked && isInvoiceValid) {
            submitBtn.disabled = false;
            submitBtn.style.cursor = "pointer";
            submitBtn.style.opacity = "1";
        } else {
            submitBtn.disabled = true;
            submitBtn.style.cursor = "not-allowed";
            submitBtn.style.opacity = "0.6";
        }
    }

    // Khi MSSV mất focus → gọi API học phí
    mssvInput.addEventListener("blur", function() {
        const mssv = this.value.trim();
        if (!mssv) {
            isInvoiceValid = false;
            toggleSubmitButton();
            return;
        }

        fetch("http://localhost/KTHDV_GK_IBANKING/api_gateway/index.php?service=student&action=get_invoice", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ mssv })
        })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                document.querySelector("[name='student_name']").value = res.student_name;
                document.querySelector("[name='amount']").value = formatCurrency(res.amount_due);
                document.querySelector("[name='amount_to_pay']").value = formatCurrency(res.amount_due);
                document.querySelector("[name='invoice_id']").value = res.invoice_id;
                document.querySelector("[name='student_id']").value = res.student_id;
                messageBox.textContent = "";
                isInvoiceValid = true;
            } else {
                messageBox.textContent = res.message;
                messageBox.style.color = "red";
                isInvoiceValid = false;
            }
            toggleSubmitButton();
        })
        .catch(() => {
            messageBox.textContent = "Không thể kết nối máy chủ.";
            messageBox.style.color = "red";
            isInvoiceValid = false;
            toggleSubmitButton();
        });
    });

    agreeCheck.addEventListener("change", toggleSubmitButton);

    function showMessage(text, type = "error") {
        messageBox.textContent = text;
        messageBox.style.color = (type === "success") ? "green" : "red";
        setTimeout(() => (messageBox.textContent = ""), 5000);
    }

    // Gửi form
    document.getElementById("paymentForm").addEventListener("submit", function(e) {
        e.preventDefault();
        if (submitBtn.disabled) return;

        const balance = parseInt(balanceField.value.replace(/[^\d]/g, ""));
        const amountToPay = parseInt(document.querySelector("[name='amount_to_pay']").value.replace(/[^\d]/g, ""));

        if (isNaN(amountToPay) || amountToPay <= 0) {
            showMessage("Chưa có thông tin học phí cần thanh toán.");
            return;
        }

        if (balance >= amountToPay) {
            document.getElementById('confirm_mssv').textContent = mssvInput.value;
            document.getElementById('confirm_student_name').textContent = document.querySelector("[name='student_name']").value;
            document.getElementById('confirm_invoice_id').textContent = document.querySelector("[name='invoice_id']").value;
            document.getElementById('confirm_amount_display').textContent = document.querySelector("[name='amount_to_pay']").value;
            confirmMessage.innerHTML = "";
            otpSection.style.display = "none";
            confirmModal.show();
        } else {
            showMessage("Số dư khả dụng không đủ để thanh toán học phí.");
        }
    });

    // Gửi OTP
    createPaymentBtn.addEventListener('click', function() {
        createPaymentBtn.disabled = true;
        confirmMessage.innerHTML = "⏳ Đang tạo giao dịch và gửi OTP...";

        const student_id = document.querySelector("[name='student_id']").value;
        const invoice_id = document.querySelector("[name='invoice_id']").value;
        const amount = parseInt(document.querySelector("[name='amount_to_pay']").value.replace(/[^\d]/g, ""));

        fetch("http://localhost/KTHDV_GK_IBANKING/api_gateway/index.php?service=payment&action=create", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                student_id,
                user_id: "<?php echo $userId; ?>",
                invoice_id,
                amount
            })
        })
        .then(res => res.json())
        .then(data => {
            createPaymentBtn.disabled = false;
            if (data.success || data.status === "success") {
                currentPaymentId = data.payment_id ?? data.paymentId ?? null;
                confirmMessage.innerHTML = "<p class='text-success'>✅ Giao dịch tạo thành công. OTP đã gửi đến email.</p>";
                otpSection.style.display = "block";
                document.querySelector("#createPaymentBtn").style.display = "none";
            } else {
                confirmMessage.innerHTML = `<p class='text-danger'>❌ ${data.message || "Không thể tạo giao dịch."}</p>`;
            }
        })
        .catch(err => {
            createPaymentBtn.disabled = false;
            confirmMessage.innerHTML = "<p class='text-danger'>Lỗi kết nối máy chủ.</p>";
            console.error(err);
        });
    });

    // Xác thực OTP
    verifyOtpBtn.addEventListener('click', function() {
        const otp = document.getElementById('otp_input').value.trim();
        otpMessage.textContent = "";

        if (!otp) {
            otpMessage.textContent = "Vui lòng nhập mã OTP.";
            return;
        }

        otpMessage.innerHTML = "🔄 Đang xác thực OTP...";

        fetch("http://localhost/KTHDV_GK_IBANKING/api_gateway/index.php?service=transaction&action=confirm", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                payment_id: currentPaymentId,
                user_id: "<?php echo $userId; ?>",
                otp_code: otp
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success || data.status === "success") {
                otpMessage.innerHTML = "<p class='text-success'>✅ Thanh toán thành công!</p>";
                setTimeout(() => location.reload(), 1500);
            } else {
                otpMessage.innerHTML = `<p class='text-danger'>❌ ${data.message || "Mã OTP không đúng."}</p>`;
            }
        })
        .catch(() => {
            otpMessage.innerHTML = "<p class='text-danger'>Không thể xác thực OTP.</p>";
        });
    });
});
</script>


</body>
</html>
