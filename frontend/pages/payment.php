<?php
if (!isset($_SESSION['USER_ID'])) {
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . '/../includes/db.php';

$message = "";

// Lấy danh sách sinh viên từ bảng students
$students = $conn->query("SELECT MSSV, FULL_NAME, TUITION FROM students");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mssv   = $_POST['mssv'] ?? '';
    $amount = $_POST['amount'] ?? 0;

    if ($mssv && $amount > 0) {
        $transactionId = "T" . rand(100, 999);
        $otpId = "O" . rand(100, 999);
        $otpCode = rand(100000, 999999);
        $now = date("Y-m-d H:i:s");
        $expire = date("Y-m-d H:i:s", strtotime("+5 minutes"));

        // Thêm vào bảng transactions (PENDING)
        $stmt = $conn->prepare("INSERT INTO transactions 
        (TRANSACTION_ID, MSSV, OPT_ID, USER_ID, AMOUNT, TRANSACTION_DATE, STATUS, DESCRIPTION) 
        VALUES (?, ?, ?, ?, ?, ?, 'PENDING', ?)");
        $desc = "Thanh toán học phí MSSV " . $mssv;
        $stmt->bind_param("ssissss", $transactionId, $mssv, $otpId, $_SESSION['USER_ID'], $amount, $now, $desc);
        $stmt->execute();
        $stmt->close();


        // Thêm OTP
        $stmt = $conn->prepare("INSERT INTO otp (OPT_ID, TRANSACTION_ID, CODE, CREATED_AT, EXPIRES_AT, IS_USED) 
            VALUES (?, ?, ?, ?, ?, 0)");
        $stmt->bind_param("sssss", $otpId, $transactionId, $otpCode, $now, $expire);
        $stmt->execute();
        $stmt->close();

        $message = "Mã OTP của bạn là: <b>$otpCode</b>. Vui lòng xác nhận trong 5 phút.";
    } else {
        $message = "Vui lòng chọn sinh viên và nhập số tiền hợp lệ.";
    }
}
?>

<div class="payment">
    <h2>Thanh toán học phí</h2>
    <?php if ($message): ?>
        <p style="color:green;"><?= $message ?></p>
    <?php endif; ?>
    <form method="post">
        <div>
            <label>MSSV:</label>
            <select name="mssv" required>
                <option value="">-- Chọn sinh viên --</option>
                <?php while ($s = $students->fetch_assoc()): ?>
                    <option value="<?= $s['MSSV'] ?>">
                        <?= $s['MSSV'] ?> - <?= $s['FULL_NAME'] ?> (Học phí: <?= number_format($s['TUITION']) ?>đ)
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div>
            <label>Số tiền thanh toán:</label>
            <input type="number" name="amount" min="1000" required>
        </div>
        <div>
            <button type="submit">Thanh toán</button>
        </div>
    </form>
</div>
