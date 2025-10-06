<?php
session_start();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameOrEmail = trim($_POST['email'] ?? '');

    if (empty($usernameOrEmail)) {
        $error = "Vui lòng nhập email!";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iMAGINE - Quên mật khẩu</title>
    <link rel="icon" type="image/jpg" href="../assets/images/logo.jpg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: url('../assets/images/videoframe_3875.png') no-repeat center center fixed;
            background-size: cover; 
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Roboto', sans-serif;
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
                        <h2 class="mb-0">
                            <i class="fas fa-lock" style="color:white;"></i>
                            QUÊN MẬT KHẨU
                        </h2>
                    </div>
                    
                    <div class="forgot-body">
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
                                <label for="email" class="form-label fw-semibold">
                                    <i class="fas fa-envelope me-1"></i> Nhập email
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="email" 
                                       name="email" 
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                       placeholder="Ví dụ: 5230001@student.tdtu.edu.vn"
                                       required>
                            </div>

                            <button type="submit" name="submit" class="btn btn-primary btn-forgot w-100 mb-3">
                                Gửi mã
                            </button>

                        </form>
                        <?php
                            if (isset($_POST['submit'])) {
                                $email = $_POST['email'];

                                // Gọi API Gateway để gửi OTP
                                $url = "http://localhost/KTHDV_GK_IBANKING/api_gateway/index.php?service=otp&action=send";
                                
                                $data = ["email" => $email];
                                $options = [
                                    "http" => [
                                        "header"  => "Content-Type: application/json",
                                        "method"  => "POST",
                                        "content" => json_encode($data)
                                    ]
                                ];
                                $context  = stream_context_create($options);
                                $result = file_get_contents($url, false, $context);

                                $res = json_decode($result, true);
                                if ($res && isset($res['success'])) {
                                    $_SESSION['email_reset'] = $email;
                                    $_SESSION['otp_sent_time'] = $res['otp_sent_time'] ?? time();
                                    header("Location: ./verify_otp.php");
                                    exit;
                                } else {
                                // xử lý lỗi
                                }
                            }
                            ?>
                                                <div class="text-center">
                            <a href="login.php" class="text-decoration-none" style="color:#3e5857">
                                <i class="fas fa-arrow-left me-1"></i> Quay lại
                            </a>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <small class="text-white-50">
                        © 2025 iBanking System. All rights reserved.
                    </small>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
