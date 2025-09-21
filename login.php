<?php
session_start();
require_once 'includes/db.php';

// Nếu đã đăng nhập thì chuyển về trang chủ
if (isset($_SESSION['USER_ID'])) {
    header("Location: index.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    // Validate input
    if (empty($username) || empty($password)) {
        $error = "Vui lòng nhập đầy đủ thông tin!";
    } else {
        try {
            // Sử dụng USENAME như trong database (không phải USERNAME)
            $stmt = $conn->prepare("SELECT USER_ID, FULL_NAME, PASSWORD FROM users WHERE USENAME = ? OR EMAIL = ?");
            $stmt->bind_param("ss", $username, $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                // Nếu sử dụng password_hash, thì dùng password_verify
                // if (password_verify($password, $row['PASSWORD'])) {
                
                // Hiện tại dùng so sánh trực tiếp (không an toàn)
                if ($password === $row['PASSWORD']) {
                    $_SESSION['USER_ID'] = $row['USER_ID'];
                    $_SESSION['FULL_NAME'] = $row['FULL_NAME'];
                    
                    // Cập nhật thời gian đăng nhập cuối
                    $updateStmt = $conn->prepare("UPDATE users SET LAST_LOGIN = NOW() WHERE USER_ID = ?");
                    $updateStmt->bind_param("i", $row['USER_ID']);
                    $updateStmt->execute();
                    
                    header("Location: index.php");
                    exit();
                } else {
                    $error = "Sai tài khoản hoặc mật khẩu!";
                }
            } else {
                $error = "Sai tài khoản hoặc mật khẩu!";
            }
            $stmt->close();
        } catch (Exception $e) {
            $error = "Lỗi hệ thống! Vui lòng thử lại sau.";
            error_log("Login error: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - iBanking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .login-body {
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
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        .alert {
            border-radius: 10px;
            border: none;
        }
        .input-group-text {
            background: #f8f9fa;
            border: 2px solid #f0f0f0;
            border-right: none;
        }
        .form-control.with-icon {
            border-left: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="login-container">
                    <div class="login-header">
                        <h2 class="mb-0">
                            <i class="fas fa-university me-2"></i>
                            iBanking
                        </h2>
                        <p class="mb-0 mt-2 opacity-75">Đăng nhập vào hệ thống</p>
                    </div>
                    
                    <div class="login-body">
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <?php echo htmlspecialchars($error); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="username" class="form-label fw-semibold">
                                    <i class="fas fa-user me-1"></i>Tên đăng nhập hoặc Email
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
                                    <i class="fas fa-lock me-1"></i>Mật khẩu
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
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="rememberMe" name="remember">
                                <label class="form-check-label" for="rememberMe">
                                    Ghi nhớ đăng nhập
                                </label>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-login w-100 mb-3">
                                <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập
                            </button>
                        </form>
                        
                        <div class="text-center">
                            <a href="forgot-password.php" class="text-decoration-none">
                                <i class="fas fa-question-circle me-1"></i>Quên mật khẩu?
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

        // Auto dismiss alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // Focus on first input
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('username').focus();
        });
    </script>
</body>
</html>