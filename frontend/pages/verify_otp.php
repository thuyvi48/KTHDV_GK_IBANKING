<?php
session_start();
if (!isset($_SESSION['email_reset'])) header("Location: forgot_pwd.php");
$email = $_SESSION['email_reset'];

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = trim($_POST['otp'] ?? '');
    if (!$otp) $error = "Nhập mã OTP!";

    else {
        $url = "http://localhost/KTHDV_GK_IBANKING/api_gateway/index.php?service=otp&action=verify";
        $data = json_encode([
            "email" => $_SESSION['email_reset'],
            "otp"   => $_POST['otp']
        ]);

        $options = [
            "http" => [
                "header" => "Content-Type: application/json",
                "method" => "POST",
                "content" => $data
            ]
        ];

        $response = file_get_contents($url, false, stream_context_create($options));
        $res = json_decode($response, true);
        if (isset($res['success'])) {
            $success = $res['success'];
            header("Refresh:2; URL=reset_pwd.php");
            exit;
        } else $error = $res['error'] ?? "OTP không hợp lệ!";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>iMAGINE - Xác thực OTP</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<style>
body {
    background: url('../assets/images/bg-login.jpg') no-repeat center center fixed;
    background-size: cover; 
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
.forgot-container {
    background: rgba(255, 255, 255, 0.9); 
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    overflow: hidden;
    backdrop-filter: blur(8px);
}
.forgot-header {
    background: linear-gradient(135deg, #3e5857 100%);
    color: white;
    padding: 2rem;
    text-align: center;
}
.forgot-body {
    padding: 2rem;
}
.form-control {
    border-radius: 10px;
    padding: 12px 15px;
    border: 2px solid #f0f0f0;
    transition: all 0.3s ease;
}
.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}
.btn-forgot {
    background: linear-gradient(135deg, #3e5857 100%);
    border: none;
    padding: 12px;
    border-radius: 10px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
}
.btn-forgot:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
}
.alert {
    border-radius: 10px;
    border: none;
}
</style>
</head>
<body>
<div class="container">
<div class="row justify-content-center">
<div class="col-md-6 col-lg-4">
<div class="forgot-container">
    <div class="forgot-header">
        <h2 class="mb-0"><i class="fas fa-key me-2"></i>XÁC THỰC OTP</h2>
    </div>
    <div class="forgot-body">
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
                    <input type="text" 
                        class="form-control text-center" 
                        id="otp" 
                        name="otp" 
                        maxlength="6" 
                        required>
                    <span class="input-group-text fw-light" 
                        id="countdown" 
                        style="color:#6c757d; min-width:70px; text-align:center;">
                        60s
                    </span>
                </div>
            </div>

            <button type="submit" name="submit" class="btn btn-primary btn-forgot w-100 mb-3">
                Xác nhận
            </button>
        </form>

        <div class="text-center">
            <a href="forgot_pwd.php" class="text-decoration-none" style="color:#3e5857">
                <i class="fas fa-arrow-left me-1"></i> Quay lại
            </a>
        </div>
    </div>
</div>
<div class="text-center mt-3">
    <small class="text-white-50">
        © 2025 iMAGINE System. All rights reserved.
    </small>
</div>
</div>
</div>
</div>
<script>
const countdownEl = document.getElementById("countdown");
const otpInput = document.getElementById("otp");
const otpLabel = document.querySelector('label[for="otp"]');

const otpDuration = 60; // giây
let timer = null;

// Lấy thời gian OTP vừa gửi từ sessionStorage
let otpSentTime = sessionStorage.getItem('otpSentTime');
otpSentTime = otpSentTime ? parseInt(otpSentTime) : null;

// Tính thời gian còn lại
function getTimeLeft() {
    if (!otpSentTime) return 0;
    const elapsed = Math.floor((Date.now() - otpSentTime) / 1000);
    return Math.max(otpDuration - elapsed, 0);
}

// Start countdown
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

// Hiển thị link gửi lại
function showResendLink() {
    clearInterval(timer);
    countdownEl.innerHTML = `<a href="#" id="resendOtp" style="color:#6c757d; font-weight:300; text-decoration:none;">Gửi lại</a>`;
    otpInput.disabled = false;

    document.getElementById("resendOtp").addEventListener("click", function(e) {
        e.preventDefault();
        resendOtp();
    });
}

// Gửi lại OTP
function resendOtp() {
    fetch("http://localhost/KTHDV_GK_IBANKING/api_gateway/index.php?service=otp&action=send", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email: "<?php echo $email; ?>" })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            otpLabel.innerHTML = `<i class="fas fa-key me-1"></i> Nhập mã OTP`;
            otpInput.value = "";
            otpSentTime = Date.now();
            sessionStorage.setItem('otpSentTime', otpSentTime);
            startCountdown();
        } else {
            otpLabel.innerHTML = `<i class="fas fa-key me-1"></i> Nhập mã OTP <small style="color:red; font-weight:300;">(${data.error || "Gửi OTP thất bại"})</small>`;
        }
    })
    .catch(err => {
        otpLabel.innerHTML = `<i class="fas fa-key me-1"></i> Nhập mã OTP <small style="color:red; font-weight:300;">(Có lỗi khi gửi OTP)</small>`;
    });
}

// Dừng countdown khi submit form (nhập OTP)
const form = document.querySelector('form');
form.addEventListener('submit', function() {
    clearInterval(timer);
});

// Nếu OTP vừa được gửi, start countdown
if (otpSentTime) {
    startCountdown();
}
</script>


</body>
</html>
