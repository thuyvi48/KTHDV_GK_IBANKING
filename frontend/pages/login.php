<?php
session_start();

$error_username = "";
$error_password = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username)) {
        $error_username = "Vui lòng nhập tên đăng nhập hoặc email";
    }
    if (empty($password)) {
        $error_password = "Vui lòng nhập mật khẩu";
    }

    if (empty($error_username) && empty($error_password)) {
        $url = "http://localhost/KTHDV_GK_IBANKING/api_gateway/index.php?service=auth&action=login";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            "username" => $username,
            "password" => $password
        ]));

        $response = curl_exec($ch);
        curl_close($ch);

        if ($response === false) {
            $error_username = "Không thể kết nối server";
        } else {
            $data = json_decode($response, true);

            if (!empty($data['success'])) {
                $_SESSION['USER_ID'] = $data['user_id'];
                $_SESSION['USERNAME'] = $data['username'];

                header("Location: ../index.php?page=dashboard");
                exit();
            } else {
                $message = $data['message'] ?? "Đăng nhập thất bại";
                if (stripos($message, 'mật khẩu') !== false) {
                    $error_password = "Sai mật khẩu";
                } else {
                    $error_username = $message;
                }
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
    <title>iMAGINE - Đăng nhập</title>
    <link rel="icon" type="image/png" href="../assets/images/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/login.css">

</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="login-container">
                    <div class="login-header">
                        <h2 class="mb-0">
                            <i class="fas fa-user" style="color:white;"></i>
                            ĐĂNG NHẬP
                        </h2>
                    </div>
                    
                    <div class="login-body">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="username" class="form-label fw-semibold">
                                  </i>Tên đăng nhập hoặc Email
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-user text-muted"></i>
                                    </span>
                                    <input type="text" 
                                           class="form-control with-icon" 
                                           id="username" 
                                           name="username" 
                                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                                           placeholder="Nhập tên đăng nhập hoặc email"
                                           required
                                           autocomplete="username">
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="password" class="form-label fw-semibold">
                                    </i>Mật khẩu
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock text-muted"></i>
                                    </span>
                                    <input type="password" 
                                           class="form-control with-icon" 
                                           id="password" 
                                           name="password" 
                                           placeholder="Nhập mật khẩu"
                                           required
                                           autocomplete="current-password">
                                    <button class="btn btn-outline-secondary" 
                                            type="button" 
                                            onclick="togglePassword()"
                                            id="passwordToggle">
                                        <i class="fas fa-eye" id="passwordIcon"></i>
                                    </button>
                                </div>
                                    <?php if (!empty($error_username) || !empty($error_password)): ?>
                                        <p style="color: red; font-size: 13px; margin-top: 5px;">
                                            <?php 
                                                if (!empty($error_username)) {
                                                    echo $error_username;
                                                } elseif (!empty($error_password)) {
                                                    echo $error_password;
                                                }
                                            ?>
                                        </p>
                                    <?php endif; ?>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-login w-100 mb-3">Đăng nhập
                            </button>
                        </form>
                        
                        <div class="text-center">
                            <a href="forgot_pwd.php" class="text-decoration-none" style="color:#3e5857">
                                Quên mật khẩu?
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const passwordIcon = document.getElementById('passwordIcon');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                passwordIcon.className = 'fas fa-eye-slash';
            } else {
                passwordField.type = 'password';
                passwordIcon.className = 'fas fa-eye';
            }
        }

        // Focus on first input
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('username').focus();
        });
    </script>
</body>
</html>