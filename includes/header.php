<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Đảm bảo BASE_URL và các hàm dùng chung đã được định nghĩa
require_once __DIR__ . "/../frontend/config.php";

 // file chứa callAPI()

// Lấy thông tin user mặc định
$user = ['full_name' => 'Khách'];

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    // Gọi API y hệt dashboard
    $apiUrl = "http://localhost/KTHDV_GK_IBANKING/backend/user_service/get_user.php?user_id=" . urlencode($userId);
    $response = file_get_contents($apiUrl);
    $resp = json_decode($response, true);

    if ($resp && isset($resp['FULL_NAME'])) {
        $user = [
            'full_name' => $resp['FULL_NAME'],
            'email'     => $resp['EMAIL'] ?? '',
        ];
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
    <link rel="icon" type="image/png" href="../frontend/assets/images/logo.png">

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
                <img src="<?php echo BASE_URL; ?>assets/images/logo.png" alt="Logo iMAGINE" width="60" height="60">
            </div>
            <div class="logo-text">
                <h2>iMAGINE</h2>
            </div>
        </div>
    </div>

    <div class="header-right">
        <div class="user-info">
            <div class="notification">
                <a href="<?php echo BASE_URL; ?>pages/notification.php">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">1</span>
                </a>
            </div>
            <div class="user-profile">
                <a href="<?php echo isset($_SESSION['user_id']) ? BASE_URL.'pages/customer_info.php' : BASE_URL.'pages/login.php'; ?>" 
                class="user-link" style="color:black; text-decoration:none;">
                    <i class="fas fa-user"></i>
                    <span class="user-name"><?php echo htmlspecialchars($user['full_name']); ?></span>
                </a>
            </div>
        </div>
    </div>
</header>
