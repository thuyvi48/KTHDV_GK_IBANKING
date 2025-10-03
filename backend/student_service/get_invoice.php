<?php
header("Content-Type: application/json");
require_once("db.php");

$input = json_decode(file_get_contents("php://input"), true);
$mssv = $input['mssv'] ?? '';

if (!$mssv) {
    echo json_encode(["success" => false, "message" => "Thiếu MSSV"]);
    exit;
}

// Lấy thông tin sinh viên + hóa đơn gần nhất
$sql = "SELECT s.FULL_NAME, i.AMOUNT_DUE, i.STATUS 
        FROM STUDENTS s
        JOIN TUITION_INVOICES i ON s.STUDENT_ID = i.STUDENT_ID
        WHERE s.MSSV = ?
        ORDER BY i.CREATED_AT DESC LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $mssv);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode([
        "success" => true,
        "student_name" => $row['FULL_NAME'],
        "amount_due"   => $row['AMOUNT_DUE'],
        "status"       => $row['STATUS']
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Không tìm thấy hóa đơn cho MSSV này"]);
}
$stmt->close();
$conn->close();
