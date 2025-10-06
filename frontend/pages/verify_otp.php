<?php
session_start();

// lấy otp_sent_time (giây) từ session nếu có, rồi unset để tránh reuse
$otp_sent_time = $_SESSION['otp_sent_time'] ?? null;
if ($otp_sent_time) {
    unset($_SESSION['otp_sent_time']);
}

// nếu chưa có email_reset thì về trang forgot
if (!isset($_SESSION['email_reset'])) {
    header("Location: forgot_pwd.php");
    exit;
}
$email = $_SESSION['email_reset'];

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = trim($_POST['otp'] ?? '');
    if (!$otp) {
        $error = "Nhập mã OTP!";
    } else {
        $url = "http://localhost/KTHDV_GK_IBANKING/api_gateway/index.php?service=otp&action=verify";
        $data = json_encode([
            "email" => $email,
            "otp"   => $otp
        ]);

        $options = [
            "http" => [
                "header" => "Content-Type: application/json",
                "method" => "POST",
                "content" => $data,
                "timeout" => 10
            ]
        ];

        $response = @file_get_contents($url, false, stream_context_create($options));
        $res = json_decode($response, true);
        if ($res && isset($res['success'])) {
            $success = $res['success'];
            // chuyển tiếp sau 2s
            header("Refresh:2; URL=reset_pwd.php");
            exit;
        } else {
            $error = $res['error'] ?? "OTP không hợp lệ!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>iMAGINE - Xác thực OTP</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/login.css">
</head>
<body>
<div class="container">
 <div class="row justify-content-center">
  <div class="col-md-6 col-lg-4">
   <div class="verify-container">
    <div class="verify-header">
      <h2 class="mb-0"><i class="fas fa-key me-2"></i>XÁC THỰC OTP</h2>
    </div>
    <div class="verify-body">
        <p class="text-center mb-4">
            Mã OTP đã được gửi tới email: <br>
            <b><?php echo htmlspecialchars($email); ?></b>
        </p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif (!empty($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo htmlspecialchars($success); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="otp" class="form-label fw-semibold">
                    <i class="fas fa-key me-1"></i> Nhập mã OTP
                </label>
                <div class="input-group">
                    <input type="text" class="form-control text-center" id="otp" name="otp" maxlength="6" required inputmode="numeric" pattern="\d{6}">
                    <span class="input-group-text fw-light" id="countdown" style="color:#6c757d; min-width:70px; text-align:center;">60s</span>
                </div>
            </div>

            <button type="submit" name="submit" class="btn btn-primary btn-verify w-100 mb-3">Xác nhận</button>
        </form>

        <div class="text-center"><a href="forgot_pwd.php" class="text-decoration-none" style="color:#3e5857"><i class="fas fa-arrow-left me-1"></i> Quay lại</a></div>
    </div>
   </div>

   <div class="text-center mt-3"><small class="text-white-50">© 2025 iMAGINE System. All rights reserved.</small></div>
  </div>
 </div>
</div>

<!-- nếu server truyền otp_sent_time thì set sessionStorage trước script chính -->
<?php if (!empty($otp_sent_time)): ?>
<script>
    // server trả về giây -> chuyển sang ms
    sessionStorage.setItem('otpSentTime', <?php echo ((int)$otp_sent_time * 1000); ?>);
</script>
<?php endif; ?>

<script>
const countdownEl = document.getElementById("countdown");
const otpInput = document.getElementById("otp");
const otpLabel = document.querySelector('label[for="otp"]');

const otpDuration = 60; // giây (phù hợp với backend)
let timer = null;

// Lấy thời gian OTP vừa gửi từ sessionStorage (ms)
let otpSentTime = sessionStorage.getItem('otpSentTime');
otpSentTime = otpSentTime ? parseInt(otpSentTime) : null;

function getTimeLeft() {
    if (!otpSentTime) return 0;
    const elapsed = Math.floor((Date.now() - otpSentTime) / 1000);
    return Math.max(otpDuration - elapsed, 0);
}

function startCountdown() {
    let timeLeft = getTimeLeft();
    if (timeLeft <= 0) {
        showResendLink();
        return;
    }

    otpInput.disabled = false;
    countdownEl.style.color = "#6c757d";
    countdownEl.textContent = timeLeft + "s";

    timer = setInterval(() => {
        timeLeft--;
        countdownEl.textContent = timeLeft + "s";

        if (timeLeft <= 0) {
            clearInterval(timer);
            showResendLink();
        }
    }, 1000);
}

function showResendLink() {
    clearInterval(timer);
    countdownEl.innerHTML = `<a href="#" id="resendOtp" style="color:#6c757d; font-weight:300; text-decoration:none;">Gửi lại</a>`;
    otpInput.disabled = false;

    document.getElementById("resendOtp").addEventListener("click", function(e) {
        e.preventDefault();
        resendOtp();
    });
}

function resendOtp() {
    fetch("http://localhost/KTHDV_GK_IBANKING/api_gateway/index.php?service=otp&action=send", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email: "<?php echo addslashes($email); ?>" })
    })
    .then(res => res.json())
    .then(data => {
        if (data && data.success) {
            otpLabel.innerHTML = `<i class="fas fa-key me-1"></i> Nhập mã OTP`;
            otpInput.value = "";
            // nếu backend trả otp_sent_time thì dùng nó, nếu không dùng Date.now()
            const t = (data.otp_sent_time ? data.otp_sent_time * 1000 : Date.now());
            otpSentTime = t;
            sessionStorage.setItem('otpSentTime', t);
            startCountdown();
        } else {
            otpLabel.innerHTML = `<i class="fas fa-key me-1"></i> Nhập mã OTP <small style="color:red;">(${(data && data.error) ? data.error : "Gửi OTP thất bại"})</small>`;
        }
    })
    .catch(err => {
        otpLabel.innerHTML = `<i class="fas fa-key me-1"></i> Nhập mã OTP <small style="color:red;">(Có lỗi khi gửi OTP)</small>`;
    });
}

// Dừng countdown khi submit form (để tránh race)
const form = document.querySelector('form');
form.addEventListener('submit', function() {
    clearInterval(timer);
});

// start nếu có otpSentTime
if (otpSentTime) {
    startCountdown();
} else {
    // nếu không có, hiển thị 'Gửi lại'
    showResendLink();
}
</script>
</body>
</html>
