<?php
session_start();
require_once 'includes/db.php'; 

// Danh sách các trang yêu cầu đăng nhập
$protected_pages = ['customer-info', 'transaction-history', 'edit-profile', 'change-password', 'pay-tuition'];

// Lấy trang hiện tại
$page = $_GET['page'] ?? 'dashboard';

// Kiểm tra nếu trang yêu cầu đăng nhập nhưng user chưa đăng nhập
if (in_array($page, $protected_pages) && !isset($_SESSION['USER_ID'])) {
    header("Location: login.php");
    exit();
}

$sql = "SELECT FULL_NAME FROM USERS LIMIT 1";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $user = ['name' => $row['FULL_NAME']];
} else {
    $user = ['name' => 'Khách vãng lai'];
}

$namePage = "Trang chủ";
include 'includes/header.php';
?>
    
    <div class="main-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
    <?php
    $file = "pages/$page.php";
    if (file_exists($file)) {
        include $file;
    } else {
        echo "<div class='alert alert-warning'>";
        echo "<h4>Trang không tồn tại!</h4>";
        echo "<p>Trang <strong>$page</strong> không được tìm thấy.</p>";
        echo "<a href='?page=dashboard' class='btn btn-primary'>Về trang chủ</a>";
        echo "</div>";
    }
    ?>
</main>

    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script>
        function updateTime() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('vi-VN', {
                hour: '2-digit',
                minute: '2-digit'
            });
            const timeElement = document.querySelector('.time-display');
            if (timeElement) {
                timeElement.textContent = 'Cập nhật: ' + timeStr;
            }
        }
        
        setInterval(updateTime, 1000);
        updateTime();
    </script>
</body>
</html>