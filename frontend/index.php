<?php
session_start();

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
$protected_pages = ['customer-info', 'transaction-history', 'edit-profile', 'change-password', 'pay-tuition'];

// Lấy trang hiện tại
$page = $_GET['page'] ?? 'dashboard';

// Nếu user chưa đăng nhập và truy cập trang cần bảo vệ -> về login
if (in_array($page, $protected_pages) && !isset($_SESSION['USER_ID'])) {
    header("Location: login.php");
    exit();
}

// ========================
// Lấy thông tin user qua User Service
// ========================
if (isset($_SESSION['USER_ID'])) {
    // Gọi đúng endpoint API (index.php của user_service)
    $apiUrl = "http://localhost/KTHDV_GK_IBANKING/backend/user_service/user_index.php";
    $user = callAPI("GET", $apiUrl, ["id" => $_SESSION['USER_ID']]);
    if (!$user) {
        $user = ['full_name' => 'Không tải được thông tin user'];
    }
} else {
    $user = ['full_name' => 'Khách vãng lai'];
}

// ========================
// Render giao diện
// ========================
$namePage = "Trang chủ";
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
