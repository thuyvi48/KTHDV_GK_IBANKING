<?php
session_start();

// ========================
// Hàm gọi API
// ========================
function callAPI($method, $url, $data = false) {
    $ch = curl_init();
    switch (strtoupper($method)) {
        case "POST":
            curl_setopt($ch, CURLOPT_POST, 1);
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            }
            break;
        default: // GET
            if ($data && is_array($data)) {
                $url = sprintf("%s?%s", $url, http_build_query($data));
            }
            break;
    }
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    curl_close($ch);
    return json_decode($result, true);
}

// ========================
// Danh sách các trang yêu cầu đăng nhập
// ========================
$protected_pages = [
    //'customer-info',
    //'transaction',
    'edit-profile',
    'change-password',
    'pay-tuition'
];

// Trang hiện tại
$page = $_GET['page'] ?? 'dashboard';

// ========================
// Logout
// ========================
if ($page === 'logout') {
    session_destroy(); 
    header("Location: pages/login.php"); 
    exit();
}

// ========================
// Check đăng nhập
// ========================
if (in_array($page, $protected_pages) && !isset($_SESSION['user_id'])) {
    // Nếu chưa login mà vào trang cần bảo vệ → redirect login
    header("Location: pages/login.php");
    exit();
}

// ========================
// Lấy thông tin user qua User Service
// ========================
if (isset($_SESSION['user_id'])) {
    $apiUrl = "http://localhost/KTHDV_GK_IBANKING/backend/user_service/index.php";
    $user = callAPI("GET", $apiUrl, ["id" => $_SESSION['user_id']]);

    if (!$user) {
        $user = ['full_name' => $_SESSION['username'] ?? 'Không tải được thông tin user'];
    }
} else {
    $user = ['full_name' => 'Khách'];
}

// ========================
// Render giao diện
// ========================
$namePage = ucfirst($page);
require_once __DIR__ . '/../includes/header.php';
?>

<div class="main-container">
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
    
    <main class="main-content">
    <?php
    $file = __DIR__ . "/pages/$page.php";
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

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

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
