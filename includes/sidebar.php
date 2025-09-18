<?php

$page = $page ?? ($_GET['page'] ?? 'dashboard');
?>

<aside class="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <div class="logo-icon">
                <i class="fas fa-university"></i>
            </div>
            <div class="logo-text">
                <strong>iBanking</strong>
                <small>Ngân hàng số</small>
            </div>
        </div>
    </div>
    
    <nav class="sidebar-nav">
        <div class="nav-section">
            <h3>Chức năng chính</h3>
            <ul class="nav-menu">
                <li class="nav-item <?php echo ($page=='dashboard') ? 'active' : ''; ?>">
                    <a href="index.php?page=dashboard" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item <?php echo ($page=='customer-info') ? 'active' : ''; ?>">
                    <a href="index.php?page=customer-info" class="nav-link">
                        <i class="fas fa-user"></i>
                        <span>Thông tin khách hàng</span>
                    </a>
                </li>
                <li class="nav-item <?php echo ($page=='payment') ? 'active' : ''; ?>">
                    <a href="index.php?page=payment" class="nav-link">
                        <i class="fas fa-credit-card"></i>
                        <span>Thanh toán</span>
                    </a>
                </li>
                <li class="nav-item <?php echo ($page=='transaction') ? 'active' : ''; ?>">
                    <a href="index.php?page=transaction" class="nav-link">
                        <i class="fas fa-history"></i>
                        <span>Lịch sử giao dịch</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
    
    <div class="sidebar-footer">
        <div class="settings">
            <a href="#" class="settings-link">
                <i class="fas fa-cog"></i>
                <span>Cài đặt</span>
            </a>
        </div>
        <div class="logout">
            <a href="#" class="logout-link">
                <i class="fas fa-sign-out-alt"></i>
                <span>Đăng xuất</span>
            </a>
        </div>
    </div>
</aside>