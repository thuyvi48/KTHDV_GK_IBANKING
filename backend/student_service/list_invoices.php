<?php
header("Content-Type: application/json");
require_once("db.php");

$student_id = $_GET['student_id'] ?? '';

if ($student_id) {
    $sql = "SELECT i.INVOICE_ID, i.SEMESTER, i.AMOUNT_DUE, i.AMOUNT_PAID, i.STATUS, i.CREATED_AT, i.UPDATED_AT,
                   s.STUDENT_ID, s.MSSV, s.FULL_NAME, s.EMAIL
            FROM TUITION_INVOICES i
            JOIN STUDENTS s ON i.STUDENT_ID = s.STUDENT_ID
            WHERE i.STUDENT_ID = ?
            ORDER BY i.CREATED_AT DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT i.INVOICE_ID, i.SEMESTER, i.AMOUNT_DUE, i.AMOUNT_PAID, i.STATUS, i.CREATED_AT, i.UPDATED_AT,
                   s.STUDENT_ID, s.MSSV, s.FULL_NAME, s.EMAIL
            FROM TUITION_INVOICES i
            JOIN STUDENTS s ON i.STUDENT_ID = s.STUDENT_ID
            ORDER BY i.CREATED_AT DESC";
    $result = $conn->query($sql);
}

$invoices = [];
while ($row = $result->fetch_assoc()) {
    $invoices[] = $row;
}

echo json_encode(["success" => true, "invoices" => $invoices]);

if (isset($stmt)) $stmt->close();
$conn->close();
