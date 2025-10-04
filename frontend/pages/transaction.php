<?php
session_start();
$mssv = $_SESSION['MSSV'] ?? '';
$url = "http://localhost/KTHDV_GK_IBANKING/api_gateway/index.php?service=payment&action=list&mssv=" . urlencode($mssv);
$data = json_decode(file_get_contents($url), true);
?>

<h2>Lịch sử giao dịch</h2>
<table border="1" cellpadding="8">
    <tr>
        <th>Mã giao dịch</th>
        <th>Mã hóa đơn</th>
        <th>Số tiền</th>
        <th>Ngày thanh toán</th>
        <th>Trạng thái</th>
    </tr>
    <?php if (!empty($data['transactions'])): ?>
        <?php foreach ($data['transactions'] as $tran): ?>
        <tr>
            <td><?= $tran['TRANSACTION_ID'] ?></td>
            <td><?= $tran['INVOICE_ID'] ?></td>
            <td><?= number_format($tran['AMOUNT'], 0, ',', '.') ?> đ</td>
            <td><?= $tran['CREATED_AT'] ?></td>
            <td><?= $tran['STATUS'] ?></td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="5">Chưa có giao dịch nào</td></tr>
    <?php endif; ?>
</table>
