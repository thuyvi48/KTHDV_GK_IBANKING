<?php
header("Content-Type: application/json");
require_once "db.php";

if (!isset($_GET['user_id']) && !isset($_GET['email'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing user_id or email"]);
    exit;
}

if (isset($_GET['user_id'])) {
    $sql = "SELECT USER_ID, FULL_NAME, EMAIL, PHONE, BALANCE FROM USERS WHERE USER_ID = ?";
    $param = $_GET['user_id'];
} else {
    $sql = "SELECT USER_ID, FULL_NAME, EMAIL, PHONE, BALANCE FROM USERS WHERE EMAIL = ?";
    $param = $_GET['email'];
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $param);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode($row);
} else {
    http_response_code(404);
    echo json_encode(["error" => "User not found"]);
}
