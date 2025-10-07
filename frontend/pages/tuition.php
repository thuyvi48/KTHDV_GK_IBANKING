<?php
session_start();
include 'db.php'; // file kết nối database
include 'customer-info.php'; // để lấy thông tin user đăng nhập

// Lấy thông tin người dùng đã đăng nhập
$payer = $_SESSION['user'];

// Nếu submit form
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $mssv = $_POST['mssv'];
    $student_name = $_POST['student_name'];
    $amount = $_POST['tuition_amount'];
    
    // Kiểm tra số dư
    if ($payer['balance'] < $amount) {
        $error = "Số dư không đủ để thanh toán học phí!";
    } else {
        $transaction_id = uniqid("TRANS");
        $user_id = $payer['user_id'];
        $student_service_url = "http://localhost/KTHDV_GK_IBANKING/api_gateway/index.php?service=student&action=get_invoice";
        $invoice_payload = json_encode(["mssv" => $mssv]);

        $ch = curl_init($student_service_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $invoice_payload);
        $invoice_response = curl_exec($ch);
        curl_close($ch);

        $invoice_data = json_decode($invoice_response, true);
        $invoice_id = $invoice_data['invoice_id'] ?? "INV" . time();
        $student_id = $invoice_data['student_id'] ?? '';
        $payment_payload = json_encode([
            "student_id" => $student_id,
            "user_id" => $user_id,
            "invoice_id" => $invoice_id,
            "amount" => $amount
        ]);

        $payment_url = "http://localhost/KTHDV_GK_IBANKING/api_gateway/index.php?service=payment&action=create";

        $ch = curl_init($payment_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payment_payload);
        $payment_response = curl_exec($ch);
        curl_close($ch);

        $payment_result = json_decode($payment_response, true);

        if (!empty($payment_result['success'])) {
            // Lưu thông tin giao dịch chờ xác nhận OTP
            $_SESSION['tuition_pending'] = [
                'transaction_id' => $payment_result['payment_id'] ?? uniqid("PAY"),
                'mssv' => $mssv,
                'student_name' => $student_name,
                'amount' => $amount,
                'invoice_id' => $invoice_id
            ];

            echo "
            <script>
                sessionStorage.setItem('payment_id', '{$payment_result['payment_id']}');
                sessionStorage.setItem('user_id', '{$user_id}');
                sessionStorage.setItem('mssv', '$mssv');
                sessionStorage.setItem('student_name', '$student_name');
                sessionStorage.setItem('amount', '$amount');
                sessionStorage.setItem('invoice_id', '$invoice_id');
                window.location.href = 'tuition-otp.php';
            </script>";
            exit;
        } else {
            $error = $payment_result['message'] ?? "Không thể tạo payment hoặc gửi OTP!";
        }
    }
}
?>

<div class="container mt-4">
  <h2>Đóng học phí</h2>

  <?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
  <?php endif; ?>

  <form method="POST">
    <!-- Người nộp tiền -->
    <div class="card mb-3">
      <div class="card-header">Người nộp tiền</div>
      <div class="card-body row">
        <div class="col-md-4">
          <label>Họ tên</label>
          <input type="text" class="form-control" value="<?php echo $payer['fullname']; ?>" readonly>
        </div>
        <div class="col-md-4">
          <label>Số điện thoại</label>
          <input type="text" class="form-control" value="<?php echo $payer['phone']; ?>" readonly>
        </div>
        <div class="col-md-4">
          <label>Email</label>
          <input type="text" class="form-control" value="<?php echo $payer['email']; ?>" readonly>
        </div>
      </div>
    </div>

    <!-- Thông tin học phí -->
    <div class="card mb-3">
      <div class="card-header">Thông tin học phí</div>
      <div class="card-body row">
        <div class="col-md-4">
          <label>MSSV</label>
          <input type="text" class="form-control" name="mssv" required>
        </div>
        <div class="col-md-4">
          <label>Họ tên sinh viên</label>
          <input type="text" class="form-control" name="student_name" required>
        </div>
        <div class="col-md-4">
          <label>Số tiền cần nộp</label>
          <input type="number" class="form-control" name="tuition_amount" required>
        </div>
      </div>
    </div>

    <!-- Thanh toán -->
    <div class="card mb-3">
      <div class="card-header">Thông tin thanh toán</div>
      <div class="card-body">
        <p><b>Số dư khả dụng:</b> <?php echo number_format($payer['balance']); ?> đ</p>
        <p><input type="checkbox" required> Tôi đồng ý với các điều khoản & điều kiện</p>
      </div>
    </div>
    <input type="hidden" name="student_id">
    <input type="hidden" name="invoice_id">
    <button type="submit" class="btn btn-success">Xác nhận giao dịch</button>
  </form>
</div>