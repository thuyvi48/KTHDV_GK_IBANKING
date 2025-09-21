<?php
// File: includes/db.php
// Cấu hình kết nối database

// Kiểm tra xem file đã được include chưa
if (defined('DB_INCLUDED')) {
    return;
}
define('DB_INCLUDED', true);

$servername = "127.0.0.1";  // hoặc "localhost"
$username = "root";         // username MySQL
$password = "";             // password MySQL (để trống nếu không có)
$dbname = "ibanking";       // tên database

try {
    // Tạo kết nối MySQLi
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Kiểm tra kết nối
    if ($conn->connect_error) {
        die("Kết nối thất bại: " . $conn->connect_error);
    }
    
    // Thiết lập charset để hỗ trợ tiếng Việt
    $conn->set_charset("utf8");
    
    // Bắt đầu session nếu chưa có
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
} catch (Exception $e) {
    die("Lỗi kết nối database: " . $e->getMessage());
}

// Hàm helper để escape string
if (!function_exists('escape_string')) {
    function escape_string($str) {
        global $conn;
        return $conn->real_escape_string($str);
    }
}

// Hàm helper để thực hiện query an toàn
if (!function_exists('safe_query')) {
    function safe_query($sql, $params = [], $types = "") {
        global $conn;
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            return false;
        }
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        return $stmt->get_result();
    }
}

// Hàm kiểm tra đăng nhập
if (!function_exists('is_logged_in')) {
    function is_logged_in() {
        return isset($_SESSION['USER_ID']) && !empty($_SESSION['USER_ID']);
    }
}

// Hàm lấy thông tin user hiện tại
if (!function_exists('get_current_user')) {
    function get_current_user() {
        global $conn;
        
        if (!is_logged_in()) {
            return null;
        }
        
        $userId = $_SESSION['USER_ID'];
        $stmt = $conn->prepare("SELECT * FROM users WHERE USER_ID = ?");
        $stmt->bind_param("s", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
}

// Hàm format số tiền
if (!function_exists('format_currency')) {
    function format_currency($amount) {
        return number_format($amount, 0, ',', '.') . ' đ';
    }
}

// Hàm format datetime
if (!function_exists('format_datetime')) {
    function format_datetime($datetime) {
        return date('d/m/Y H:i:s', strtotime($datetime));
    }
}

// Hàm format date
if (!function_exists('format_date')) {
    function format_date($date) {
        return date('d/m/Y', strtotime($date));
    }
}
?>