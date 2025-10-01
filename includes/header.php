<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Đảm bảo BASE_URL và các hàm dùng chung đã được định nghĩa
require_once __DIR__ . "/../frontend/config.php";
require_once __DIR__ . "/functions.php"; // header.php nằm trong includes, functions.php cũng nằm trong includes

 // file chứa callAPI()

// Lấy thông tin user mặc định
$user = ['full_name' => 'Khách vãng lai'];

if (isset($_SESSION['USER_ID'])) {
    $apiUrl = BASE_URL . "backend/user_service/user_index.php";
    $resp = callAPI("GET", $apiUrl, ["id" => $_SESSION['USER_ID']]);
    
    if ($resp && isset($resp['full_name'])) {
        $user = $resp;
    } else {
        $user = ['full_name' => 'Không tải được thông tin user'];
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iMAGINE - <?php echo isset($namePage) ? $namePage : ''; ?></title>
    <link rel="icon" type="image/jpg" href="<?php echo BASE_URL; ?>assets/images/logo.jpg">

    <!-- CSS chung -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/pages.css">

    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<header class="main-header">
    <div class="header-left">
        <div class="logo">
            <div class="logo-icon">
                <img src="<?php echo BASE_URL; ?>assets/images/logo.jpg" alt="Logo iMAGINE" width="60" height="60">
            </div>
            <div class="logo-text">
                <h2>iMAGINE</h2>
            </div>
        </div>
    </div>
    
    <nav class="main-nav">
        <a href="<?php echo BASE_URL; ?>index.php" class="nav-item <?php echo ($page=='dashboard') ? 'active' : ''; ?>">Trang chủ</a>
        <a href="<?php echo BASE_URL; ?>index.php?page=service" class="nav-item <?php echo ($page=='service') ? 'active' : ''; ?>">Dịch vụ</a>
        <a href="<?php echo BASE_URL; ?>index.php?page=support" class="nav-item <?php echo ($page=='support') ? 'active' : ''; ?>">Hỗ trợ</a>
    </nav>

    <div class="header-right">
        <div class="user-info">
            <div class="notification">
                <a href="<?php echo BASE_URL; ?>pages/notification.php">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">1</span>
                </a>
            </div>
            <div class="user-profile">
                <div class="user-avatar">
                    <a href="<?php echo isset($_SESSION['USER_ID']) ? BASE_URL.'pages/customer-info.php' : BASE_URL.'pages/login.php'; ?>">
                        <i class="fas fa-user"></i>
                    </a>
                </div>
                <span class="user-name">
                    <a href="<?php echo isset($_SESSION['USER_ID']) ? BASE_URL.'pages/customer-info.php' : BASE_URL.'pages/login.php'; ?>" class="text-decoration-none">
                        <?php echo htmlspecialchars($user['full_name']); ?>
                    </a>
                </span>
            </div>
        </div>
    </div>
</header>
