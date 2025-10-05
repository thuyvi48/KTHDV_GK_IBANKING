<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$user_id = $_SESSION['user_id']; // test hardcode nếu session trống

$url = "http://localhost/KTHDV_GK_IBANKING/api_gateway/index.php?service=transaction&action=list&user_id=" . urlencode($user_id);
$response = @file_get_contents($url);
$data = json_decode($response, true);

$status_map = [
    'PENDING' => ['label' => 'Đang chờ xử lý', 'class' => 'pending'],
    'DONE'    => ['label' => 'Hoàn tất', 'class' => 'success'],
    'FAILED'  => ['label' => 'Thất bại', 'class' => 'failed'],
];
?>

<div class="recent-transactions">
    <div class="section-header">
        <h2>Lịch sử giao dịch</h2>
    </div>

    <div class="transactions-list">
        <?php if (!empty($data['data'])): ?>
            <?php foreach ($data['data'] as $tran): ?>
                <div class="transaction-item">
                    <div class="transaction-icon <?= strtolower($tran['TYPE']) ?>">
                        <?= strtoupper($tran['TYPE'][0]) ?>
                    </div>
                    <div class="transaction-details">
                        <h4><?= $tran['DESCRIPTION'] ?></h4>
                        <p>Mã GD: <?= $tran['TRANSACTION_ID'] ?> | Mã HĐ: <?= $tran['PAYMENT_ID'] ?? '-' ?></p>
                        <?php 
                        $tran_status = $status_map[$tran['STATUS']] ?? ['label'=>$tran['STATUS'], 'class'=>''];
                        ?>
                        <span class="transaction-status <?= $tran_status['class'] ?>">
                            <?= $tran_status['label'] ?>
                        </span>
                    </div>
                    <div class="transaction-amount <?= $tran['TYPE']=='CREDIT' ? 'positive':'negative' ?>">
                        <?= number_format($tran['CHANGE_AMOUNT'],0,',','.') ?> đ
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Chưa có giao dịch nào</p>
        <?php endif; ?>
    </div>
</div>
