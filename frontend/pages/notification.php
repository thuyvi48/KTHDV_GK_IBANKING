<?php
$page = "notification";
$namePage = "Thông báo";
include("../../includes/header.php");

// Lấy user_id demo
$user_id = "U001";
$url = "http://localhost/IBANKING/api_gateway.php?service=transaction&action=list&user_id=" . urlencode($user_id);

$response = @file_get_contents($url);
$transactions = $response ? json_decode($response, true) : [];

echo "<h2>Thông báo giao dịch</h2>";

if (empty($transactions) || isset($transactions['error'])) {
    echo "<p>Không có thông báo nào.</p>";
} else {
    echo "<ul>";
    foreach ($transactions as $t) {
        echo "<li>{$t['DESCRIPTION']} (Số dư: {$t['BALANCE_AFTER']})</li>";
    }
    echo "</ul>";
}

include("../../includes/footer.php");
?>

