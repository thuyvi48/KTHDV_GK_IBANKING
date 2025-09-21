<?php
session_start();

// Lưu tên user trước khi destroy session để hiển thị thông báo
$user_name = $_SESSION['FULL_NAME'] ?? 'Người dùng';

// Hủy tất cả session
session_destroy();

// Xóa cookie session nếu có
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

// Bắt đầu session mới để hiển thị thông báo
session_start();
$_SESSION['logout_success'] = true;
$_SESSION['logout_user'] = $user_name;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng xuất - iBanking</title>
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
        .logout-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            animation: fadeInUp 0.8s ease-out;
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .logout-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .logout-body {
            padding: 2rem;
            text-align: center;
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            color: white;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
            color: white;
        }
        .btn-home {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            color: white;
            margin-left: 10px;
        }
        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(108, 117, 125, 0.3);
            color: white;
        }
        .success-icon {
            font-size: 4rem;
            color: #28a745;
            margin-bottom: 1rem;
            animation: bounceIn 1s ease-out;
        }
        @keyframes bounceIn {
            0% { transform: scale(0.3); opacity: 0; }
            50% { transform: scale(1.05); }
            70% { transform: scale(0.9); }
            100% { transform: scale(1); opacity: 1; }
        }
        .countdown {
            font-size: 0.9rem;
            color: #6c757d;
            margin-top: 1rem;
        }
        .logout-message {
            font-size: 1.1rem;
            color: #495057;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="logout-container">
                    <div class="logout-header">
                        <h2 class="mb-0">
                            <i class="fas fa-university me-2"></i>
                            iBanking
                        </h2>
                        <p class="mb-0 mt-2 opacity-75">Đăng xuất thành công</p>
                    </div>
                    
                    <div class="logout-body">
                        <div class="success-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        
                        <div class="logout-message">
                            <strong>Tạm biệt, <?php echo htmlspecialchars($user_name); ?>!</strong>
                            <br>
                            Bạn đã đăng xuất khỏi hệ thống thành công.
                            <br>
                            <small class="text-muted">Cảm ơn bạn đã sử dụng dịch vụ iBanking.</small>
                        </div>
                        
                        <div class="alert alert-success border-0" role="alert">
                            <i class="fas fa-shield-alt me-2"></i>
                            Phiên đăng nhập của bạn đã được kết thúc an toàn
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <a href="login.php" class="btn-login">
                                <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập lại
                            </a>
                            <a href="index.php" class="btn-home">
                                <i class="fas fa-home me-2"></i>Trang chủ
                            </a>
                        </div>
                        
                        <div class="countdown">
                            <i class="fas fa-clock me-1"></i>
                            Tự động chuyển về trang đăng nhập sau <span id="countdown">10</span> giây
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="text-muted">
                            <h6><i class="fas fa-lightbulb me-2"></i>Lời khuyên bảo mật:</h6>
                            <small>
                                • Luôn đăng xuất khi sử dụng máy tính chung<br>
                                • Không chia sẻ thông tin đăng nhập với người khác<br>
                                • Thay đổi mật khẩu định kỳ để bảo mật tài khoản
                            </small>
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
        // Countdown timer
        let timeLeft = 10;
        const countdownElement = document.getElementById('countdown');
        
        const timer = setInterval(function() {
            timeLeft--;
            countdownElement.textContent = timeLeft;
            
            if (timeLeft <= 0) {
                clearInterval(timer);
                window.location.href = 'login.php';
            }
        }, 1000);
        
        // Clear logout session data after showing
        <?php 
        unset($_SESSION['logout_success']);
        unset($_SESSION['logout_user']);
        ?>
        
        // Prevent back button after logout
        history.pushState(null, null, location.href);
        window.onpopstate = function () {
            history.go(1);
        };
        
        // Show confirmation if user tries to leave
        window.addEventListener('beforeunload', function (e) {
            if (timeLeft > 0) {
                e.preventDefault();
                e.returnValue = '';
            }
        });
    </script>
</body>
</html>