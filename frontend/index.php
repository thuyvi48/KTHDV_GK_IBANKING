<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
// Trang hiện tại
// ========================
$page = $_GET['page'] ?? 'dashboard';

// ========================
// Danh sách các trang yêu cầu đăng nhập
// ========================
$protected_pages = ['dashboard', 'customer-info', 'transaction'];

// ========================
// Check đăng nhập
// ========================
if (in_array($page, $protected_pages) && !isset($_SESSION['USER_ID'])) {
    header("Location: pages/login.php");
    exit();
}

// ========================
// Logout
// ========================
if ($page === 'logout') {
    session_destroy(); 
    header("Location: pages/login.php"); 
    exit();
}

// ========================
// Lấy thông tin user qua User Service
// ========================
if (isset($_SESSION['USER_ID'])) {
    if (!isset($_SESSION['USER_INFO'])) {
        $apiUrl = "http://localhost/KTHDV_GK_IBANKING/api_gateway/index.php?service=user&action=get_user&user_id=" . urlencode($_SESSION['USER_ID']);
        $userInfo = callAPI("GET", $apiUrl);
        if (!empty($userInfo)) {
            $_SESSION['USER_INFO'] = $userInfo;
        } else {
            $_SESSION['USER_INFO'] = ['full_name' => $_SESSION['USERNAME'] ?? 'Không tải được thông tin user'];
        }
    }
    $user = $_SESSION['USER_INFO'];
} else {
    $user = ['full_name' => 'Khách '];
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

</body>
</html>
