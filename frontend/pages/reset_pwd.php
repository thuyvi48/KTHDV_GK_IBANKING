<?php
session_start();

// Nếu chưa xác thực OTP thì quay lại forgot_pwd
if (!isset($_SESSION['email_reset'])) {
    header("Location: ./forgot_pwd.php");
    exit;
}

$email = $_SESSION['email_reset'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $password = trim($_POST['password'] ?? '');
    $confirm  = trim($_POST['confirm'] ?? '');

    if (empty($password) || empty($confirm)) {
        $error = "Vui lòng nhập đầy đủ mật khẩu!";
    } elseif ($password !== $confirm) {
        $error = "Mật khẩu xác nhận không khớp!";
    } else {
        // Gọi API để reset mật khẩu
        $url = "http://localhost/KTHDV_GK_IBANKING/api_gateway/index.php?service=otp&action=reset_pwd";
        $data = ["email" => $email, "password" => $password];

        $options = [
            "http" => [
                "header"  => "Content-Type: application/json",
                "method"  => "POST",
                "content" => json_encode($data)
            ]
        ];
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        if ($result === FALSE) {
            $error = "Không thể kết nối server!";
        } else {
            $res = json_decode($result, true);
            if (isset($res['success'])) {
                $success = "Đặt lại mật khẩu thành công! Đang chuyển về trang đăng nhập...";
                unset($_SESSION['email_reset']);
                header("Refresh: 2; URL=login.php");
                exit;
            } else {
                $error = $res['error'] ?? "Có lỗi xảy ra!";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iMAGINE - Đặt lại mật khẩu</title>
    <link rel="icon" type="image/jpg" href="../frontend/assets/images/logo.jpg">
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
                    <h2 class="mb-0">
                        <i class="fas fa-lock me-2"></i>
                        ĐẶT LẠI MẬT KHẨU
                    </h2>
                </div>

                <div class="forgot-body">
                    <p class="text-center mb-4">
                        Email đang đặt lại mật khẩu: <br>
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
                            <label for="password" class="form-label fw-semibold">
                                <i class="fas fa-key me-1"></i> Mật khẩu mới
                            </label>
                            <input type="password" 
                                   class="form-control" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Nhập mật khẩu mới"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="confirm" class="form-label fw-semibold">
                                <i class="fas fa-key me-1"></i> Xác nhận mật khẩu
                            </label>
                            <input type="password" 
                                   class="form-control" 
                                   id="confirm" 
                                   name="confirm" 
                                   placeholder="Nhập lại mật khẩu"
                                   required>
                        </div>

                        <button type="submit" name="submit" class="btn btn-primary btn-forgot w-100 mb-3">
                            Đặt lại mật khẩu
                        </button>
                    </form>

                    <div class="text-center">
                        <a href="login.php" class="text-decoration-none">
                            <i class="fas fa-arrow-left me-1"></i> Quay lại đăng nhập
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
