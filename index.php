<?php
session_start();
require_once 'includes/db.php'; 

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
    $page = $_GET['page'] ?? 'dashboard';
    $file = "pages/$page.php";
    if (file_exists($file)) {
        include $file;
    } else {
        echo "<p>Trang không tồn tại!</p>";
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
            document.querySelector('.time-display').textContent = 'Cập nhật: ' + timeStr;
        }
        
        setInterval(updateTime, 1000);
        updateTime();
    </script>
</body>
</html>