<?php
// Sample data
$account_balance = 2450000;
$savings_balance = 15000000;
$credit_limit = 50000000;
$credit_due_date = '25/12';

$recent_transactions = [
    [
        'type' => 'online_shopping',
        'description' => 'Mua sắm online',
        'amount' => -2100000,
        'date' => '1 ngày trước',
        'status' => 'Hoàn thành'
    ],
    [
        'type' => 'cashback',
        'description' => 'Hoàn tiền cashback',
        'amount' => 150000,
        'date' => '2 ngày trước',
        'status' => 'Hoàn thành'
    ]
];
?>

<div class="dashboard">
    <div class="dashboard-header">
        <h1>Dashboard</h1>
        <p>Tổng quan tài khoản ngân hàng của bạn</p>
    </div>
    
    <!-- Account Cards -->
    <div class="account-cards">
        <!-- Main Account -->
        <div class="account-card primary">
            <div class="card-header">
                <div class="card-title">
                    <h3>Tài khoản chính</h3>
                    <div class="card-actions">
                        <i class="fas fa-copy"></i>
                    </div>
                </div>
                <div class="account-number">**** 1234</div>
            </div>
            <div class="card-balance">
                <span class="balance-amount"><?php echo number_format($account_balance, 0, ',', '.'); ?> đ</span>
                <div class="balance-change">
                    <i class="fas fa-arrow-up"></i>
                    +12% so với tháng trước
                </div>
            </div>
        </div>
        
        <!-- Savings Account -->
        <div class="account-card savings">
            <div class="card-header">
                <h3>Tiết kiệm</h3>
                <div class="savings-info">Số tiết kiệm không kỳ hạn</div>
            </div>
            <div class="card-balance">
                <span class="balance-amount"><?php echo number_format($savings_balance, 0, ',', '.'); ?> đ</span>
                <div class="interest-rate">
                    <i class="fas fa-percentage"></i>
                    Lãi suất: 0.5%/năm
                </div>
            </div>
        </div>
        
        <!-- Credit Card -->
        <div class="account-card credit">
            <div class="card-header">
                <h3>Thẻ tín dụng</h3>
                <div class="credit-info">Hạn mức khả dụng</div>
            </div>
            <div class="card-balance">
                <span class="balance-amount"><?php echo number_format($credit_limit, 0, ',', '.'); ?> đ</span>
                <div class="due-date">
                    <i class="fas fa-exclamation-circle"></i>
                    Chu kỳ thanh toán: <?php echo $credit_due_date; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="quick-actions">
        <h2>Thao tác nhanh</h2>
        <p>Các giao dịch thường dùng</p>
        
        <div class="actions-grid">
            <div class="action-item">
                <div class="action-icon transfer">
                    <i class="fas fa-exchange-alt"></i>
                </div>
                <div class="action-content">
                    <h4>Chuyển tiền</h4>
                    <p>Chuyển tiền nhanh</p>
                </div>
            </div>
            
            <div class="action-item">
                <div class="action-icon payment">
                    <i class="fas fa-credit-card"></i>
                </div>
                <div class="action-content">
                    <h4>Thanh toán</h4>
                    <p>Thanh toán hóa đơn</p>
                </div>
            </div>
            
            <div class="action-item">
                <div class="action-icon savings">
                    <i class="fas fa-piggy-bank"></i>
                </div>
                <div class="action-content">
                    <h4>Tiết kiệm</h4>
                    <p>Gửi tiết kiệm</p>
                </div>
            </div>
            
            <div class="action-item">
                <div class="action-icon loan">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
                <div class="action-content">
                    <h4>Vay vốn</h4>
                    <p>Vay tiêu dùng</p>
                </div>
            </div>
        </div>
    </div>
    
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
                        <i class="fas fa-undo-alt"></i>
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