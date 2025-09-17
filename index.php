<?php
session_start();
$user = [
    'name' => 'Nguyễn Văn An',
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iBanking - Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/pages.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="main-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
    <?php
    $page = $_GET['page'] ?? 'dashboard'; // mặc định dashboard
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