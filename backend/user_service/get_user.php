<?php
header("Content-Type: application/json");
require_once "db.php";

if (!isset($_GET['user_id'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing user_id"]);
    exit;
}

$userId = $_GET['user_id'];

$sql = "SELECT USER_ID, FULL_NAME, EMAIL, PHONE, BALANCE, PASSWORD FROM USERS WHERE USER_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode($row);
} else {
    http_response_code(404);
    echo json_encode(["error" => "User not found"]);
}
