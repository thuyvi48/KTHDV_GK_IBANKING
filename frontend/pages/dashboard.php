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
            <h4>Người nộp tiền</h4>
            <label>Họ tên:</label>
            <input type="text" name="payer_name" value="<?php echo $payer_name; ?>" readonly>
            
            <label>Số điện thoại:</label>
            <input type="text" name="payer_phone" value="<?php echo $payer_phone; ?>" readonly>
            
            <label>Email:</label>
            <input type="email" name="payer_email" value="<?php echo $payer_email; ?>" readonly>

            <h4 style="grid-column:1 / -1">Thông tin học phí</h4>
            <label>MSSV:</label>
            <input type="text" id="mssv" name="mssv" placeholder="Nhập MSSV">
            <label>Họ tên sinh viên:</label>
            <input type="text" id="student_name" name="student_name" readonly>
            <label>Số tiền cần nộp:</label>
            <input type="text" id="amount" name="amount" readonly>

            <h4>Thông tin thanh toán</h4>
            <label>Số dư khả dụng:</label>
            <input type="text" name="balance" value="<?php echo number_format($account_balance, 0, ',', '.'); ?> đ" readonly>
            <label>Số tiền học phí cần thanh toán:</label>
            <input type="text" name="amount_to_pay" readonly>
            <input type="hidden" name="invoice_id">
            <input type="hidden" name="student_id">
            <div class="agree-submit">
                <label>
                    <input type="checkbox" name="agree"> Tôi đồng ý với các 
                    <span style="color:blue; margin:0 4px; cursor:pointer;">Thỏa thuận và Điều khoản</span> 
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
        <h5 class="modal-title"><strong>Xác nhận giao dịch</strong></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
      </div>
      <div class="modal-body">
        <div class="modal-readonly">
          <p><strong>MSSV:</strong> <span id="confirm_mssv"></span></p>
          <p><strong>Họ tên sinh viên:</strong> <span id="confirm_student_name"></span></p>
          <p><strong>Mã hóa đơn:</strong> <span id="confirm_invoice_id"></span></p>
          <p><strong>Số tiền:</strong> <span id="confirm_amount_display"></span></p>
        </div>

        <div id="confirmMessage" class="mt-2"></div>

        <!-- Form nhập OTP (ẩn ban đầu, sẽ hiện sau khi gửi OTP) -->
            <div id="otpSection" style="display:none; margin-top:20px;">
            <hr>
            <p>Mã OTP đã được gửi đến email của bạn. Vui lòng nhập mã để xác nhận thanh toán.</p>
            <input type="text" id="otp_input" class="form-control" placeholder="Nhập mã OTP">
            <div id="otpTimer" style="color: gray; margin-top: 5px;"></div>

            <!-- Bọc hai nút trong 1 div để dễ bố trí -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 10px;">
                <button type="button" id="resendOtpBtn" style="display:none;">Gửi lại</button>
                <button type="button" class="btn" id="verifyOtpBtn">Xác thực OTP</button>
            </div>

            <div id="otpMessage" class="mt-2" style="color:red;"></div>
            </div>

      </div>

      <div class="modal-footer">
        <button id="createPaymentBtn" type="button" class="btn">Tạo giao dịch & Gửi OTP</button>
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
  const confirmModalEl = document.getElementById("confirmModal");
  const confirmModal = new bootstrap.Modal(confirmModalEl);
  const createPaymentBtn = document.getElementById("createPaymentBtn");
  const confirmMessage = document.getElementById("confirmMessage");
  const otpSection = document.getElementById("otpSection");
  const verifyOtpBtn = document.getElementById("verifyOtpBtn");
  const otpMessage = document.getElementById("otpMessage");
  const resendOtpBtn = document.getElementById("resendOtpBtn");

  let currentPaymentId = null;
  let isInvoiceValid = false;
  let otpTimer = null;
  let countdown = 0;

  // Disable nút gửi ban đầu
  function disableSubmit(disabled) {
    submitBtn.disabled = disabled;
    submitBtn.style.cursor = disabled ? "not-allowed" : "pointer";
    submitBtn.style.opacity = disabled ? "0.6" : "1";
  }
  disableSubmit(true);

  // Format tiền
  if (balanceField?.value) {
    const raw = balanceField.value.replace(/[^\d]/g, "");
    balanceField.value = formatCurrency(parseInt(raw));
  }

  function toggleSubmitButton() {
    disableSubmit(!(agreeCheck.checked && isInvoiceValid));
  }

  // Khi MSSV mất focus → gọi API học phí
  mssvInput.addEventListener("blur", async function () {
    const mssv = this.value.trim();
    if (!mssv) {
      isInvoiceValid = false;
      toggleSubmitButton();
      return;
    }

    try {
      const res = await fetch(
        "http://localhost/KTHDV_GK_IBANKING/api_gateway/index.php?service=student&action=get_invoice",
        {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ mssv }),
        }
      );
      const data = await res.json();

      if (data.success) {
        document.querySelector("[name='student_name']").value =
          data.student_name || "";

        if (data.status === "PAID") {
          messageBox.textContent = "Hóa đơn đã được thanh toán.";
          messageBox.style.color = "green";
          document.querySelector("[name='amount']").value = "";
          document.querySelector("[name='amount_to_pay']").value = "";
          isInvoiceValid = false;
        } else {
          document.querySelector("[name='amount']").value =
            formatCurrency(data.amount_due);
          document.querySelector("[name='amount_to_pay']").value =
            formatCurrency(data.amount_due);
          document.querySelector("[name='invoice_id']").value = data.invoice_id;
          document.querySelector("[name='student_id']").value = data.student_id;
          messageBox.textContent = "";
          isInvoiceValid = true;
        }
      } else {
        messageBox.textContent = data.message;
        messageBox.style.color = "red";
        isInvoiceValid = false;
      }
    } catch (err) {
      messageBox.textContent = "Không thể kết nối máy chủ.";
      messageBox.style.color = "red";
      isInvoiceValid = false;
    }
    toggleSubmitButton();
  });

  agreeCheck.addEventListener("change", toggleSubmitButton);

  function showMessage(text, type = "error") {
    messageBox.textContent = text;
    messageBox.style.color = type === "success" ? "green" : "red";
    setTimeout(() => (messageBox.textContent = ""), 4000);
  }

  // Form gửi thanh toán
  document
    .getElementById("paymentForm")
    .addEventListener("submit", (e) => {
      e.preventDefault();
      if (submitBtn.disabled) return;

      const balance = parseInt(balanceField.value.replace(/[^\d]/g, ""));
      const amountToPay = parseInt(
        document
          .querySelector("[name='amount_to_pay']")
          .value.replace(/[^\d]/g, "")
      );

      if (isNaN(amountToPay) || amountToPay <= 0) {
        showMessage("Chưa có thông tin học phí cần thanh toán.");
        return;
      }

      if (balance >= amountToPay) {
        document.getElementById("confirm_mssv").textContent = mssvInput.value;
        document.getElementById("confirm_student_name").textContent =
          document.querySelector("[name='student_name']").value;
        document.getElementById("confirm_invoice_id").textContent =
          document.querySelector("[name='invoice_id']").value;
        document.getElementById("confirm_amount_display").textContent =
          document.querySelector("[name='amount_to_pay']").value;

        confirmMessage.innerHTML = "";
        otpSection.style.display = "none";
        confirmModal.show();
      } else {
        showMessage("Số dư khả dụng không đủ để thanh toán học phí.");
      }
    });

  // Hàm đếm ngược OTP
  function startOtpCountdown(seconds) {
    clearInterval(otpTimer);
    countdown = seconds;
    resendOtpBtn.style.display = "none";

    let otpMessageTimer = document.getElementById("otpTimer");
    if (!otpMessageTimer) {
      otpMessageTimer = document.createElement("p");
      otpMessageTimer.id = "otpTimer";
      otpMessageTimer.style.color = "gray";
      document
        .getElementById("otp_input")
        .insertAdjacentElement("afterend", otpMessageTimer);
    }

    otpTimer = setInterval(() => {
      if (countdown > 0) {
        otpMessageTimer.textContent = `OTP sẽ hết hạn sau ${countdown--}s`;
      } else {
        otpMessageTimer.textContent = "OTP đã hết hạn.";
        resendOtpBtn.style.display = "inline-block";
        clearInterval(otpTimer);
      }
    }, 1000);
  }

  // Gửi OTP (tạo giao dịch)
  createPaymentBtn.addEventListener("click", async () => {
    createPaymentBtn.disabled = true;
    confirmMessage.innerHTML =
      "Đang tạo giao dịch và gửi OTP...";

    const student_id = document.querySelector("[name='student_id']").value;
    const invoice_id = document.querySelector("[name='invoice_id']").value;
    const amount = parseInt(
      document
        .querySelector("[name='amount_to_pay']")
        .value.replace(/[^\d]/g, "")
    );

    try {
      const res = await fetch(
        "http://localhost/KTHDV_GK_IBANKING/api_gateway/index.php?service=payment&action=create",
        {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            student_id,
            userId: "<?php echo $userId; ?>",
            invoice_id,
            amount,
          }),
        }
      );
      const data = await res.json();

      createPaymentBtn.disabled = false;
      if (data.success) {
        currentPaymentId = data.payment_id;
        confirmMessage.innerHTML =
          "<p class='text-success'>Giao dịch tạo thành công. OTP đã gửi đến email.</p>";
        otpSection.style.display = "block";
        createPaymentBtn.style.display = "none";
        startOtpCountdown(data.otpExpiresIn || 30);
      } else {
        confirmMessage.innerHTML = `<p class='text-danger'>${data.message || "Không thể tạo giao dịch."}</p>`;
      }
    } catch {
      confirmMessage.innerHTML = "<p class='text-danger'>Lỗi kết nối máy chủ.</p>";
      createPaymentBtn.disabled = false;
    }
  });

  // Gửi lại OTP
  resendOtpBtn.addEventListener("click", async () => {
    resendOtpBtn.disabled = true;

    try {
      const res = await fetch(
        "http://localhost/KTHDV_GK_IBANKING/api_gateway/index.php?service=otp&action=resend",
        {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            payment_id: currentPaymentId,
            user_id: "<?php echo $userId; ?>",
          }),
        }
      );
      const data = await res.json();

      if (data.success) {
        resendOtpBtn.textContent = "Gửi lại";
        resendOtpBtn.disabled = false;
        startOtpCountdown(data.otpExpiresIn || 30);
      } else {
        otpMessage.innerHTML = `<p class='text-danger'>${data.message || "Không thể gửi lại OTP."}</p>`;
        resendOtpBtn.textContent = "Gửi lại";
        resendOtpBtn.disabled = false;
      }
    } catch {
      otpMessage.innerHTML = "<p class='text-danger'>Lỗi khi gửi lại OTP.</p>";
      resendOtpBtn.textContent = "Gửi lại";
      resendOtpBtn.disabled = false;
    }
  });

  // Xác thực OTP
  verifyOtpBtn.addEventListener("click", async () => {
    const otp = document.getElementById("otp_input").value.trim();
    if (!otp) {
      otpMessage.textContent = "Vui lòng nhập mã OTP.";
      return;
    }

    otpMessage.innerHTML =
      "<span style='color: green;'>Đang xác thực OTP...</span>";

    try {
      const res = await fetch(
        "http://localhost/KTHDV_GK_IBANKING/api_gateway/index.php?service=payment&action=confirm",
        {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            payment_id: currentPaymentId,
            user_id: "<?php echo $userId; ?>",
            otpCode: otp,
          }),
        }
      );
      const data = await res.json();

      if (data.success) {
        otpMessage.innerHTML = "<p class='text-success'>Thanh toán thành công!</p>";
        setTimeout(() => location.reload(), 1500);
      } else {
        otpMessage.innerHTML = `<p class='text-danger'>${data.message || "Mã OTP không đúng."}</p>`;
      }
    } catch {
      otpMessage.innerHTML = "<p class='text-danger'>Không thể xác thực OTP.</p>";
    }
  });
});
</script>
</body>
</html>
