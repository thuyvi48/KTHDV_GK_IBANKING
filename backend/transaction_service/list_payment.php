<?php
header('Content-Type: application/json');
require_once "db.php";

$sql = "SELECT * FROM PAYMENTS ORDER BY CREATED_AT DESC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $payments = [];
    while ($row = $result->fetch_assoc()) {
        $payments[] = $row;
    }
    echo json_encode(["success" => true, "data" => $payments]);
} else {
    echo json_encode(["success" => false, "message" => "Không có dữ liệu"]);
}

$conn->close();
?>
