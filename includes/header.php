<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iBanking - <?php echo $namePage ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/pages.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<header class="main-header">
    <div class="header-left">
        <div class="logo">
            <div class="logo-icon">
                <i class="fas fa-university"></i>
            </div>
            <div class="logo-text">
                <h2>iBanking</h2>
            </div>
        </div>
    </div>
    
    <nav class="main-nav">
    <a href="index.php" 
       class="nav-item <?php echo ($page=='index') ? 'active' : ''; ?>">
       Trang chủ
    </a>

    <a href="index.php?page=service" 
       class="nav-item <?php echo ($page=='service') ? 'active' : ''; ?>">
       Dịch vụ
    </a>

    <a href="index.php?page=support" 
       class="nav-item <?php echo ($page=='support') ? 'active' : ''; ?>">
       Hỗ trợ
    </a>
</nav>

    
    <div class="header-right">
        
        <div class="user-info">
            <div class="notification">
                <a href="notification.php">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">1</span>
                </a>
            </div>
            <div class="user-profile">
                <div class="user-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <span class="user-name">
                <?php echo isset($user['full_name']) ? $user['full_name'] : 'Khách vãng lai'; ?>
                </span>
            </div>
        </div>
    </div>
</header>