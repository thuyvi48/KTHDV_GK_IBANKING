<?php
require_once __DIR__ . '/db.php';

header('Content-Type: application/json');

// Nhận user_id thay vì payment_id
if (!isset($_GET['user_id'])) {
    echo json_encode(["error" => "Thiếu tham số user_id"]);
    exit;
}

$user_id = $_GET['user_id'];

try {
    $stmt = $conn->prepare("
        SELECT STATUS 
        FROM PAYMENTS 
        WHERE USER_ID = ? 
        ORDER BY CREATED_AT DESC 
        LIMIT 1
    ");
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode(["STATUS" => $row['STATUS']]);
    } else {
        echo json_encode(["STATUS" => "pending"]);
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}

$conn->close();
?>
