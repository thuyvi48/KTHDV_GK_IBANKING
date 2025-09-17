<header class="main-header">
    <div class="header-left">
        <div class="logo">
            <div class="logo-icon">
                <i class="fas fa-university"></i>
            </div>
            <div class="logo-text">
                <h2>iBanking</h2>
                <span>Ngân hàng số an toàn</span>
            </div>
        </div>
    </div>
    
    <nav class="main-nav">
        <a href="../index.php" class="nav-link active">Trang chủ</a>
        <a href="../pages/service.php" class="nav-link">Dịch vụ</a>
        <a href="../pages/support.php" class="nav-link">Hỗ trợ</a>
    </nav>
    
    <div class="header-right">
        
        <div class="user-info">
            <div class="hotline">
                <i class="fas fa-phone"></i>
                Hotline
            </div>
            <div class="notification">
                <i class="fas fa-bell"></i>
                <span class="notification-badge">1</span>
            </div>
            <div class="user-profile">
                <div class="user-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="user-details">
                    <span class="user-name"><?php echo $user['name']; ?></span>
                </div>
            </div>
        </div>
    </div>
</header>