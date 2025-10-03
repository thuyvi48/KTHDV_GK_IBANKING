<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Sample data giả sử lấy sau khi user login
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
// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit();
// }

$userId = $_SESSION['user_id'] ?? "U001"; 

// Gọi API user_service
$apiUrl = "http://localhost/KTHDV_GK_IBANKING/backend/user_service/get_user.php?user_id=" . urlencode($userId);
$response = file_get_contents($apiUrl);
$userData = json_decode($response, true);

$payer_name       = $userData['FULL_NAME'] ?? '';
$payer_phone      = $userData['PHONE'] ?? '';
$payer_email      = $userData['EMAIL'] ?? '';
$account_balance  = $userData['BALANCE'] ?? 0;

$account_balance = $userData['BALANCE'] ?? 0;

$transApi = "http://localhost/KTHDV_GK_IBANKING/backend/transaction_service/get_transaction.php?user_id=" . urlencode($userId) . "&limit=4";
$transResponse = file_get_contents($transApi);
$recent_transactions = json_decode($transResponse, true) ?? [];
?>

<div class="dashboard">
    <div class="dashboard-header">
        <h1><strong>Thanh toán</strong></h1>
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
            <h2>Người nộp tiền</h2>
            <label>Họ tên:</label>
            <input type="text" name="payer_name" value="<?php echo $payer_name; ?>" readonly>
            
            <label>Số điện thoại:</label>
            <input type="text" name="payer_phone" value="<?php echo $payer_phone; ?>" readonly>
            
            <label>Email:</label>
            <input type="email" name="payer_email" value="<?php echo $payer_email; ?>" readonly>

            <h2 style="grid-column:1 / -1">Thông tin học phí</h2>

                <label>MSSV:</label>
                <input type="text" id="mssv" name="mssv" placeholder="Nhập MSSV">

                <label>Họ tên sinh viên:</label>
                <input type="text" id="student_name" name="student_name" readonly>

                <label>Số tiền cần nộp:</label>
                <input type="text" id="amount" name="amount" readonly>

            <h2>Thông tin thanh toán</h2>
            <label>Số dư khả dụng:</label>
            <input type="text" name="balance" value="<?php echo number_format($account_balance, 0, ',', '.'); ?> đ" readonly>
            
            <label>Số tiền học phí cần thanh toán:</label>
            <input type="text" name="amount_to_pay" readonly>
            
            <div class="agree-submit">
                <label>
                    <input type="checkbox" name="agree"> Tôi đồng ý với điều khoản
                </label>
                <button type="submit" disabled>Xác nhận giao dịch</button>
            </div>
        </form>
    </div>
    

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Thanh toán học phí</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <style>
body {
    font-family: 'Roboto', sans-serif;
}


        .dashboard-header h1 { margin-bottom: 20px; }
        .account-cards { display: flex; gap: 20px; margin-bottom: 30px;}
        .account-card { background: #fff; padding: 20px; border-radius: 8px; flex: 1; box-shadow: 0 2px 6px rgba(0,0,0,0.1);}
        .primary { border-left: 5px solid #131516ff; }
        .card-balance { font-size: 24px;}
        .payment-form{ background: #fff; padding: 20px; border-radius: 8px; margin-bottom: 30px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        .payment-form h2 { margin-top: 15px; margin-bottom: 10px; }
        .payment-form label { margin-top: 10px;     font-weight: 400;   color: #3e5857;    }
        .payment-form input[type="text"], .payment-form input[type="email"] { width: 250px; padding: 8px; border-radius: 4px; border: 1px solid #ccc; margin-top: 5px; }
        .payment-form button { margin-top: 15px; padding: 10px 20px; color: #fff; border: none; border-radius: 4px; cursor: not-allowed; }
        .recent-transactions { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .btn-view-all { background: #3e5857; color: #fff; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; }
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

        .agree-submit {
    display: flex;
    align-items: center;
    justify-content: space-between; /* Cách đều 2 bên */
    margin-top: 15px;
}

.agree-submit label {
    display: flex;
    align-items: center;
    font-weight: normal;
}

.agree-submit button {
    margin-top: 0; /* bỏ margin-top mặc định */
    padding: 10px 20px;
    background: #3e5857;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: not-allowed;
}
h1 {
    font-weight: bold;
}
    </style>
</head>
<body>

<div class="dashboard">
    <div class="dashboard-header">
        <h1><strong>Thanh toán</strong></h1>
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
            <h2>Người nộp tiền</h2>
            <label>Họ tên:</label>
            <input type="text" name="payer_name" value="<?php echo $payer_name; ?>" readonly>

            <label>Số điện thoại:</label>
            <input type="text" name="payer_phone" value="<?php echo $payer_phone; ?>" readonly>

            <label>Email:</label>
            <input type="email" name="payer_email" value="<?php echo $payer_email; ?>" readonly>

            <h2>Thông tin học phí</h2>
            <label>MSSV:</label>
            <input type="text" name="mssv" placeholder="Nhập MSSV">

            <label>Họ tên sinh viên:</label>
            <input type="text" name="student_name" readonly>

            <label>Số tiền cần nộp:</label>
            <input type="text" name="amount" readonly>

            <h2>Thông tin thanh toán</h2>
            <label>Số dư khả dụng:</label>
            <input type="text" name="balance" value="<?php echo number_format($account_balance, 0, ',', '.'); ?> đ" readonly>

            <label>Số tiền học phí cần thanh toán:</label>
            <input type="text" name="amount_to_pay" readonly>

            <input type="hidden" name="invoice_id" value="">

            <div class="agree-submit">
                <label>
                    <input type="checkbox" name="agree"> Tôi đồng ý với điều khoản
                </label>
                <button type="submit" disabled>Xác nhận giao dịch</button>
            </div>
        </form>
    </div>

    <!-- Recent Transactions -->
    <div class="recent-transactions">
        <div class="section-header">
            <h2>Giao dịch gần đây</h2>
            <p>4 giao dịch mới nhất</p>
            <button class="btn-view-all" onclick="window.location.href='invoice_history.php'">Xem tất cả giao dịch</button>
        </div>
        <div class="transactions-list">
            <?php foreach($recent_transactions as $transaction): 
                $amount = $transaction['CHANGE_AMOUNT'] ?? 0;
                $status = $transaction['STATUS'] ?? '';
                $description = $transaction['DESCRIPTION'] ?? '';
                $type = strtolower($transaction['TYPE'] ?? 'transfer'); // DEBIT/CREDIT
                $date = $transaction['CREATED_AT'] ?? '';
            ?>
            <div class="transaction-item">
                <div class="transaction-icon <?php echo $type; ?>">
                    <?php if($type == 'online_shopping'): ?>
                        <i class="fas fa-shopping-cart"></i>
                    <?php else: ?>
                        <i class="fas fa-exchange-alt"></i>
                    <?php endif; ?>
                </div>
                <div class="transaction-details">
                    <h4><?php echo $description; ?></h4>
                    <p><?php echo $date; ?></p>
                </div>
                <div class="transaction-amount <?php echo $amount > 0 ? 'positive' : 'negative'; ?>">
                    <?php echo $amount > 0 ? '+' : ''; ?><?php echo number_format($amount, 0, ',', '.'); ?> đ
                    <div class="transaction-status"><?php echo $status; ?></div>
                </div>
            </div>
            <?php endforeach; ?>

        </div>
    </div>
</div>

<script>
document.querySelector("[name='mssv']").addEventListener("blur", function() {
    let mssv = this.value.trim();
    if (!mssv) return;

    fetch("http://localhost/KTHDV_GK_IBANKING/api_gateway/index.php?service=student&action=get_invoice", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ mssv: mssv })
    })
    .then(res => res.json())
    .then(res => {
        if (res.success) {
            document.querySelector("[name='student_name']").value = res.student_name;
            document.querySelector("[name='amount']").value = res.amount_due.toLocaleString("vi-VN") + " đ";
            document.querySelector("[name='amount_to_pay']").value = res.amount_due.toLocaleString("vi-VN") + " đ";
            document.querySelector("[name='invoice_id']").value = res.invoice_id;
        } else {
            alert(res.message);
            document.querySelector("[name='student_name']").value = "";
            document.querySelector("[name='amount']").value = "";
            document.querySelector("[name='amount_to_pay']").value = "";
            document.querySelector("[name='invoice_id']").value = "";
        }
    });
});

document.getElementById("paymentForm").addEventListener("submit", function(e) {
    e.preventDefault();

    let data = {
        payer_name: document.querySelector("[name='payer_name']").value,
        payer_phone: document.querySelector("[name='payer_phone']").value,
        payer_email: document.querySelector("[name='payer_email']").value,
        mssv: document.querySelector("[name='mssv']").value,
        student_name: document.querySelector("[name='student_name']").value,
        amount_to_pay: document.querySelector("[name='amount_to_pay']").value
    };

    fetch("http://localhost/KTHDV_GK_IBANKING/api_gateway/index.php?service=payment&action=create", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(res => {
        if (res.success) {
            alert("Thanh toán thành công. Mã giao dịch: " + res.payment_id);

            // 👉 Chuyển hướng sang transaction.php, truyền theo payment_id
            window.location.href = "transaction.php?payment_id=" + res.payment_id;
        } else {
            alert("Thanh toán thất bại: " + res.message);
        }
    });
});

// Bắt sự kiện tick vào "Tôi đồng ý"
document.querySelector("[name='agree']").addEventListener("change", function() {
    const submitBtn = document.querySelector(".agree-submit button");
    if (this.checked) {
        submitBtn.disabled = false;
        submitBtn.style.cursor = "pointer"; 
    } else {
        submitBtn.disabled = true;
        submitBtn.style.cursor = "not-allowed";
    }
});
</script>

</body>
</html>
<script>
document.getElementById("mssv").addEventListener("blur", function() {
    let mssv = this.value.trim();
    if (mssv.length === 0) return;

    fetch("http://localhost/KTHDV_GK_IBANKING/backend/student_service/get_student.php?mssv=" + encodeURIComponent(mssv))
        .then(resp => resp.json())
        .then(data => {
            if (data && !data.error) {
                // Fill form
                document.getElementById("student_name").value = data.FULL_NAME || "";
                document.getElementById("amount").value = 
                    (data.AMOUNT ? new Intl.NumberFormat('vi-VN').format(data.AMOUNT) : 0) + " đ";
            } else {
                alert("Không tìm thấy thông tin sinh viên!");
                document.getElementById("student_name").value = "";
                document.getElementById("amount").value = "";
            }
        })
        .catch(err => {
            console.error(err);
            alert("Lỗi khi lấy thông tin sinh viên!");
        });
});
</script>
