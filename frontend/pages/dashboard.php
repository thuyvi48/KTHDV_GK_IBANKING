<?php
// Sample data (giả sử lấy từ DB sau khi user login)
$payer_name  = "Nguyen Van A";
$payer_phone = "0909xxxxxx";
$payer_email = "abc@tdtu.edu.vn";
$account_balance = 5000000;
?>

<div class="dashboard">
    <div class="dashboard-header">
        <h1>Thanh toán học phí</h1>
    </div>
    
    <!-- Account Info -->
    <div class="account-cards">
        <div class="account-card primary">
            <div class="card-header">
                <h3>Số dư khả dụng</h3>
            </div>
            <div class="card-balance">
                <span class="balance-amount">
                    <?php echo number_format($account_balance, 0, ',', '.'); ?> đ
                </span>
            </div>
        </div>
    </div>
    
    <!-- Payment Form -->
    <div class="payment-form">
        <form id="paymentForm">
            <h3>Người nộp tiền</h3>
            <label>Họ tên:</label>
            <input type="text" name="payer_name" value="<?php echo $payer_name; ?>" readonly>
            
            <label>Số điện thoại:</label>
            <input type="text" name="payer_phone" value="<?php echo $payer_phone; ?>" readonly>
            
            <label>Email:</label>
            <input type="email" name="payer_email" value="<?php echo $payer_email; ?>" readonly>

            <h3>Thông tin học phí</h3>
            <label>MSSV:</label>
            <input type="text" name="mssv" placeholder="Nhập MSSV">
            
            <label>Họ tên sinh viên:</label>
            <input type="text" name="student_name" readonly>
            
            <label>Số tiền cần nộp:</label>
            <input type="text" name="amount" readonly>

            <h3>Thông tin thanh toán</h3>
            <label>Số dư khả dụng:</label>
            <input type="text" name="balance" value="<?php echo number_format($account_balance, 0, ',', '.'); ?> đ" readonly>
            
            <label>Số tiền học phí cần thanh toán:</label>
            <input type="text" name="amount_to_pay" readonly>
            
            <label>
                <input type="checkbox" name="agree"> Tôi đồng ý với điều khoản
            </label>

            <button type="submit" disabled>Xác nhận giao dịch</button>
        </form>
    </div>
    
    <?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Sample data (giả sử lấy từ DB sau khi user login)
$payer_name  = "Nguyen Van A";
$payer_phone = "0909xxxxxx";
$payer_email = "abc@tdtu.edu.vn";
$account_balance = 5000000;

// Fake data cho recent transactions
$recent_transactions = [
    [
        "type" => "online_shopping",
        "description" => "Thanh toán Shopee",
        "date" => "2025-10-01 14:30",
        "amount" => -250000,
        "status" => "Hoàn tất"
    ],
    [
        "type" => "online_shopping",
        "description" => "Nạp tiền điện thoại",
        "date" => "2025-09-29 19:20",
        "amount" => -100000,
        "status" => "Hoàn tất"
    ],
    [
        "type" => "transfer",
        "description" => "Nhận tiền từ Bùi Văn B",
        "date" => "2025-09-28 10:15",
        "amount" => 1500000,
        "status" => "Hoàn tất"
    ],
    [
        "type" => "transfer",
        "description" => "Chuyển tiền đến Nguyễn Thị C",
        "date" => "2025-09-27 08:45",
        "amount" => -500000,
        "status" => "Đang xử lý"
    ],
];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Thanh toán học phí</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f8f9fa; }
        .dashboard-header h1 { margin-bottom: 20px; }
        .account-cards { display: flex; gap: 20px; margin-bottom: 30px; }
        .account-card { background: #fff; padding: 20px; border-radius: 8px; flex: 1; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        .primary { border-left: 5px solid #007bff; }
        .card-balance { font-size: 24px; font-weight: bold; color: #007bff; }
        .payment-form { background: #fff; padding: 20px; border-radius: 8px; margin-bottom: 30px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        .payment-form h3 { margin-top: 15px; margin-bottom: 10px; }
        .payment-form label { display: block; margin-top: 10px; }
        .payment-form input[type="text"], .payment-form input[type="email"] { width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ccc; margin-top: 5px; }
        .payment-form button { margin-top: 15px; padding: 10px 20px; background: #007bff; color: #fff; border: none; border-radius: 4px; cursor: not-allowed; }
        .recent-transactions { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .btn-view-all { background: #28a745; color: #fff; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; }
        .transactions-list { display: flex; flex-direction: column; gap: 15px; }
        .transaction-item { display: flex; align-items: center; background: #f9f9f9; padding: 10px; border-radius: 6px; }
        .transaction-icon { width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 50%; margin-right: 15px; color: #fff; }
        .transaction-icon.online_shopping { background: #007bff; }
        .transaction-icon.transfer { background: #17a2b8; }
        .transaction-details { flex: 1; }
        .transaction-details h4 { margin: 0; font-size: 14px; }
        .transaction-details p { margin: 2px 0 0; font-size: 12px; color: #666; }
        .transaction-amount { font-weight: bold; }
        .transaction-amount.positive { color: #28a745; }
        .transaction-amount.negative { color: #dc3545; }
        .transaction-status { font-size: 12px; color: #666; }
    </style>
</head>
<body>
    
    <!-- Recent Transactions -->
    <div class="recent-transactions">
        <div class="section-header">
            <h2>Giao dịch gần đây</h2>
            <p>4 giao dịch mới nhất</p>
            <button class="btn-view-all">Xem tất cả giao dịch</button>
        </div>
        
        <div class="transactions-list">
            <?php foreach($recent_transactions as $transaction): ?>
            <div class="transaction-item">
                <div class="transaction-icon <?php echo $transaction['type']; ?>">
                    <?php if($transaction['type'] == 'online_shopping'): ?>
                        <i class="fas fa-shopping-cart"></i>
                    <?php else: ?>
                        <i class="fas fa-exchange-alt"></i>
                    <?php endif; ?>
                </div>
                <div class="transaction-details">
                    <h4><?php echo $transaction['description']; ?></h4>
                    <p><?php echo $transaction['date']; ?></p>
                </div>
                <div class="transaction-amount <?php echo $transaction['amount'] > 0 ? 'positive' : 'negative'; ?>">
                    <?php echo $transaction['amount'] > 0 ? '+' : ''; ?><?php echo number_format($transaction['amount'], 0, ',', '.'); ?> đ
                    <div class="transaction-status"><?php echo $transaction['status']; ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
</body>
</html>
