<?php
header('Content-Type: application/json');
require_once 'db.php';

if (!isset($_GET['mssv'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing MSSV"]);
    exit;
}

$mssv = $_GET['mssv'];

// Lấy thông tin sinh viên
$sql_student = "SELECT STUDENT_ID, FULL_NAME FROM STUDENTS WHERE MSSV = ?";
$stmt = $conn->prepare($sql_student);
$stmt->bind_param("s", $mssv);
$stmt->execute();
$result = $stmt->get_result();

if ($student = $result->fetch_assoc()) {
    $student_id = $student['STUDENT_ID'];

    // Tính tổng số tiền cần nộp từ bảng TUITION_INVOICES
    $sql_amount = "SELECT SUM(AMOUNT_DUE - AMOUNT_PAID) AS AMOUNT 
                   FROM TUITION_INVOICES 
                   WHERE STUDENT_ID = ?";
    $stmt2 = $conn->prepare($sql_amount);
    $stmt2->bind_param("s", $student_id);
    $stmt2->execute();
    $res_amount = $stmt2->get_result();
    $amount = $res_amount->fetch_assoc()['AMOUNT'] ?? 0;

    $response = [
        "STUDENT_ID" => $student['STUDENT_ID'],
        "FULL_NAME" => $student['FULL_NAME'],
        "AMOUNT" => $amount
    ];
    echo json_encode($response, JSON_UNESCAPED_UNICODE);

    $stmt2->close();
} else {
    http_response_code(404);
    echo json_encode(["error" => "Student not found"]);
}

$stmt->close();
$conn->close();
