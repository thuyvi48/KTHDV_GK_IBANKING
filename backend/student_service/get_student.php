<?php
header("Content-Type: application/json");
require_once("db.php"); // file db.php kết nối tới database studentdb

// Lấy id từ query string (có thể là STUDENT_ID hoặc MSSV)
$id = $_GET['id'] ?? '';

if (!$id) {
    echo json_encode(["success" => false, "message" => "Thiếu tham số id"]);
    exit;
}

// Truy vấn sinh viên theo MSSV (nếu MSSV là số) hoặc STUDENT_ID (nếu là chuỗi)
$sql = "SELECT s.STUDENT_ID, s.MSSV, s.FULL_NAME, s.EMAIL, t.INVOICE_ID, t.SEMESTER, 
               t.AMOUNT_DUE, t.AMOUNT_PAID, t.STATUS
        FROM STUDENTS s
        LEFT JOIN TUITION_INVOICES t ON s.STUDENT_ID = t.STUDENT_ID
        WHERE s.MSSV = ? OR s.STUDENT_ID = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $id, $id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode([
        "success" => true,
        "student" => [
            "student_id" => $row['STUDENT_ID'],
            "mssv"       => $row['MSSV'],
            "full_name"  => $row['FULL_NAME'],
            "email"      => $row['EMAIL'],
            "invoice"    => [
                "invoice_id"  => $row['INVOICE_ID'],
                "semester"    => $row['SEMESTER'],
                "amount_due"  => $row['AMOUNT_DUE'],
                "amount_paid" => $row['AMOUNT_PAID'],
                "status"      => $row['STATUS']
            ]
        ]
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Không tìm thấy sinh viên"]);
}

$stmt->close();
$conn->close();

    document.querySelector("[name='mssv']").addEventListener("blur", function() {
        let mssv = this.value.trim();
        if (!mssv) return;

        fetch("http://localhost/KTHDV_GK_IBANKING/api_gateway/index.php?service=student&action=get&mssv=" + encodeURIComponent(mssv))
        .then(res => res.json())
        .then(data => {
            if (data && data.success) {
                document.querySelector("[name='student_name']").value = data.student.FULL_NAME;
                document.querySelector("[name='amount']").value = data.invoice.AMOUNT_DUE + " đ";
                document.querySelector("[name='amount_to_pay']").value = data.invoice.AMOUNT_DUE + " đ";
                document.querySelector("[name='invoice_id']").value = data.invoice.INVOICE_ID; // ẩn
            } else {
                alert(data.message || "Không tìm thấy MSSV");
            }
        });
    });