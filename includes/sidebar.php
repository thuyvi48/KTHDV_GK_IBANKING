<?php

$page = $page ?? ($_GET['page'] ?? 'dashboard');
?>

<aside class="sidebar">
    
    <nav class="sidebar-nav">
        <div class="nav-section">
            <ul class="nav-menu">
                <li class="nav-item <?php echo ($page=='dashboard') ? 'active' : ''; ?>">
                    <a href="index.php?page=dashboard" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Trang chủ</span>
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

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-cog"></i>
                        <span>Cài đặt</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="logout.php" class="nav-link">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Đăng xuất</span>
                    </a>
                </li>
            </ul>
            
        </div>
    </nav>
    

</aside>