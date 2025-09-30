<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = $_POST['otp'];

    if (isset($_SESSION['otp']) && $otp == $_SESSION['otp'] && time() < $_SESSION['otp_expire']) {
        $payer = $_SESSION['user'];
        $txn = $_SESSION['tuition_pending'];
        $amount = $txn['amount'];

        // Trừ tiền
        $sql = "UPDATE accounts SET balance = balance - $amount WHERE id = {$payer['id']}";
        mysqli_query($conn, $sql);

        // Lưu giao dịch
        $sql = "INSERT INTO transactions(user_id, type, amount, description) 
                VALUES ({$payer['id']}, 'tuition', $amount, 'Đóng học phí cho MSSV {$txn['mssv']}')";
        mysqli_query($conn, $sql);

        // Xóa OTP
        unset($_SESSION['otp'], $_SESSION['otp_expire'], $_SESSION['tuition_pending']);

        // Gửi email xác nhận
        mail($payer['email'], "Thanh toán thành công", "Bạn đã đóng {$amount}đ học phí cho MSSV {$txn['mssv']}");

        header("Location: transaction.php?success=1");
        exit;
    } else {
        $error = "Mã OTP không hợp lệ hoặc đã hết hạn.";
    }
}
?>

<div class="container mt-4">
  <h2>Xác thực OTP</h2>

  <?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
  <?php endif; ?>

  <form method="POST">
    <label>Nhập mã OTP đã gửi về email</label>
    <input type="text" name="otp" class="form-control" required>
    <button type="submit" class="btn btn-primary mt-3">Xác nhận</button>
  </form>
</div>
