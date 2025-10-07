<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'db.php'; // file kết nối database

// Kiểm tra MSSV có được truyền không
if (!isset($_GET['mssv'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing MSSV"]);
    exit;
}

$mssv = $_GET['mssv'];

// Lấy thông tin sinh viên kèm tất cả hóa đơn học phí
$sql = "
    SELECT s.STUDENT_ID, s.MSSV, s.FULL_NAME, s.EMAIL, s.CREATED_AT,
           t.INVOICE_ID, t.SEMESTER, t.AMOUNT_DUE, t.AMOUNT_PAID, t.STATUS, t.CREATED_AT AS INVOICE_CREATED_AT, t.UPDATED_AT
    FROM STUDENTS s
    LEFT JOIN TUITION_INVOICES t ON s.STUDENT_ID = t.STUDENT_ID
    WHERE s.MSSV = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("d", $mssv);
$stmt->execute();
$result = $stmt->get_result();

$student = null;
$invoices = [];

while ($row = $result->fetch_assoc()) {
    if (!$student) {
        // Lấy thông tin sinh viên
        $student = [
            "STUDENT_ID" => $row['STUDENT_ID'],
            "MSSV" => $row['MSSV'],
            "FULL_NAME" => $row['FULL_NAME'],
            "EMAIL" => $row['EMAIL'],
            "CREATED_AT" => $row['CREATED_AT'],
        ];
    }

    // Nếu có hóa đơn, push vào mảng
    if ($row['INVOICE_ID']) {
        $invoices[] = [
            "INVOICE_ID" => $row['INVOICE_ID'],
            "SEMESTER" => $row['SEMESTER'],
            "AMOUNT_DUE" => $row['AMOUNT_DUE'],
            "AMOUNT_PAID" => $row['AMOUNT_PAID'],
            "STATUS" => $row['STATUS'],
            "CREATED_AT" => $row['INVOICE_CREATED_AT'],
            "UPDATED_AT" => $row['UPDATED_AT'],
        ];
    }
}

if ($student) {
    $student['INVOICES'] = $invoices;
    echo json_encode($student, JSON_UNESCAPED_UNICODE);
} else {
    http_response_code(404);
    echo json_encode(["error" => "Student not found"]);
}

$stmt->close();
$conn->close();
