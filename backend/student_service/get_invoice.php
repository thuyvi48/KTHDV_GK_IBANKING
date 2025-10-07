<?php
header("Content-Type: application/json");
require_once("db.php");

// 1️⃣ Nhận MSSV từ POST JSON hoặc GET
$raw = file_get_contents("php://input");
file_put_contents("debug_input_student.txt", $raw);

$input = json_decode($raw, true);
$mssv = $input['mssv'] ?? ($_GET['id'] ?? ''); 

if (!$mssv) {
    echo json_encode(["success" => false, "message" => "Thiếu MSSV"]);
    exit;
}

// 2️⃣ Lấy student_id từ MSSV
$stmt = $conn->prepare("SELECT STUDENT_ID, FULL_NAME FROM STUDENTS WHERE MSSV = ?");
$stmt->bind_param("s", $mssv); // MSSV thường là string
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Không tìm thấy sinh viên"]);
    exit;
}

$student = $res->fetch_assoc();
$student_id = $student['STUDENT_ID'];
$student_name = $student['FULL_NAME'];
$stmt->close();

// 3️⃣ Lấy hóa đơn gần nhất theo student_id
$stmt2 = $conn->prepare("
    SELECT INVOICE_ID, AMOUNT_DUE, STATUS 
    FROM TUITION_INVOICES 
    WHERE STUDENT_ID = ?
    ORDER BY CREATED_AT DESC LIMIT 1
");
$stmt2->bind_param("s", $student_id);
$stmt2->execute();
$res2 = $stmt2->get_result();

if ($res2->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Không tìm thấy hóa đơn cho sinh viên này"]);
    exit;
}

$invoice = $res2->fetch_assoc();
$stmt2->close();
$conn->close();

// 4️⃣ Trả dữ liệu ra
echo json_encode([
    "success" => true,
    "student_id"   => $student_id,
    "student_name" => $student_name,
    "invoice_id"   => $invoice['INVOICE_ID'],
    "amount_due"   => $invoice['AMOUNT_DUE'],
    "status"       => $invoice['STATUS']
]);
