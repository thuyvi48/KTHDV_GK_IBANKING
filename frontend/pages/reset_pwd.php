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
    <link rel="icon" type="image/jpg" href="../assets/images/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/login.css">
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="reset-container">
                <div class="reset-header">
                    <h2 class="mb-0">
                        <i class="fas fa-lock me-2"></i>
                        ĐẶT LẠI MẬT KHẨU
                    </h2>
                </div>

                <div class="reset-body">
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

                        <button type="submit" name="submit" class="btn btn-primary btn-reset w-100 mb-3">
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
