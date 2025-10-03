<?php
require_once dirname(__DIR__) . '../frontend/config.php';
$page = $page ?? ($_GET['page'] ?? 'dashboard');
?>

<aside class="sidebar">
    <nav class="sidebar-nav">
        <div class="nav-section">
            <ul class="nav-menu">
                <li class="nav-item <?php echo ($page=='dashboard') ? 'active' : ''; ?>">
                    <a href="<?= BASE_URL ?>index.php?page=dashboard" class="nav-link">
                        <i class="fas fa-credit-card"></i>
                        <span><strong>Thanh toán</strong></span>
                    </a>
                </li>
                <li class="nav-item <?php echo ($page=='customer-info') ? 'active' : ''; ?>">
                    <a href="<?= BASE_URL ?>index.php?page=customer-info" class="nav-link">
                        <i class="fas fa-user"></i>
                        <span><strong>Thông tin khách hàng</strong></span>
                    </a>
                </li>
                <li class="nav-item <?php echo ($page=='transaction') ? 'active' : ''; ?>">
                    <a href="<?= BASE_URL ?>index.php?page=transaction" class="nav-link">
                        <i class="fas fa-history"></i>
                        <span><strong>Lịch sử <br>giao dịch</strong></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-cog"></i>
                        <span><strong>Cài đặt</strong></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>index.php?page=logout" class="nav-link">
                        <i class="fas fa-sign-out-alt"></i>
                        <span><strong>Đăng xuất</strong></span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</aside>
