<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . "/../config.php";

$userId = $_SESSION['USER_ID'] ?? null;

$user = [
    "FULL_NAME" => "",
    "EMAIL" => "",
    "PHONE" => "",
    "BALANCE" => 0
];

if ($userId) {
    $apiUrl = "http://localhost/KTHDV_GK_IBANKING/api_gateway/index.php";
    $resp = callAPI("GET", $apiUrl, [
        "service" => "user",
        "action"  => "get_user",
        "user_id" => $userId
    ]);

    if ($resp && isset($resp['FULL_NAME'])) {
        $user = $resp;
    } else {
        $user['FULL_NAME'] = "Không tải được thông tin người dùng";
    }
} else {
    echo "<p style='color:red;'>Bạn chưa đăng nhập!</p>";
    exit;
}
?>

<div class="customer-info-page container mt-4">
    <h1 class="mb-4">Thông tin tài khoản</h1>

    <div class="cust-form-card p-4">
        <form class="cust-form" id="userForm">
            <div class="cust-form-row">
                <div class="cust-form-group">
                    <label>Họ và tên</label>
                    <input type="text" value="<?php echo htmlspecialchars($user['FULL_NAME']); ?>" readonly>
                </div>
                <div class="cust-form-group">
                    <label>Email</label>
                    <input type="email" id="email" value="<?php echo htmlspecialchars($user['EMAIL']); ?>" readonly>
                </div>
                <div class="cust-form-group">
                    <label>Số điện thoại</label>
                    <input type="text" id="phone" value="<?php echo htmlspecialchars($user['PHONE']); ?>" readonly>
                </div>
            </div>

    <div class="cust-form-row align-items-end">
        <div class="cust-form-group" style="flex: 1;">
            <label>Số dư tài khoản</label>
            <input type="text" 
                value="<?php echo number_format($user['BALANCE'], 0, ',', '.'); ?> ₫" 
                readonly 
                style="max-width: 180px;">
        </div>
            <!-- Nút thao tác -->
            <div class="text-center mt-4 btn-group-inline">
                <button type="button" id="btnEdit" class="btn btn-success px-3">Chỉnh sửa</button>
                <div id="editActions" style="display: none; gap: 10px;">
                    <button type="button" id="btnSave" class="btn px-3">Lưu</button>
                    <button type="button" id="btnCancel" class="btn btn-secondary px-3">Hủy</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const btnEdit = document.getElementById("btnEdit");
    const btnSave = document.getElementById("btnSave");
    const btnCancel = document.getElementById("btnCancel");
    const emailInput = document.getElementById("email");
    const phoneInput = document.getElementById("phone");

    // Thêm phần hiển thị thông báo
    const messageBox = document.createElement("p");
    messageBox.id = "updateMessage";
    messageBox.style.marginTop = "15px";
    messageBox.style.fontWeight = "bold";
    messageBox.style.textAlign = "center";
    messageBox.style.transition = "opacity 0.5s ease";
    document.querySelector(".cust-form-card").appendChild(messageBox);

    function showMessage(text, color) {
        messageBox.textContent = text;
        messageBox.style.color = color;
        messageBox.style.opacity = "1";
        setTimeout(() => {
            messageBox.style.opacity = "0";
        }, 5000);
    }

    // Khi nhấn "Chỉnh sửa"
    btnEdit.addEventListener("click", () => {
        emailInput.removeAttribute("readonly");
        phoneInput.removeAttribute("readonly");
        btnEdit.style.display = "none";
        document.getElementById("editActions").style.display = "flex"; 
    });

    // Khi nhấn "Hủy"
    btnCancel.addEventListener("click", () => {
        window.location.reload();
    });

    // Khi nhấn "Lưu thay đổi"
    btnSave.addEventListener("click", async () => {
        const email = emailInput.value.trim();
        const phone = phoneInput.value.trim();

        if (!email.match(/^[^@\s]+@[^@\s]+\.[^@\s]+$/)) {
            showMessage("Email không hợp lệ!", "red");
            return;
        }
        if (!phone.match(/^[0-9]{9,11}$/)) {
            showMessage("Số điện thoại không hợp lệ (9–11 số)!", "red");
            return;
        }

        const data = {
            user_id: "<?php echo $userId; ?>",
            email,
            phone
        };

        try {
            const res = await fetch("http://localhost/KTHDV_GK_IBANKING/api_gateway/index.php?service=user&action=update_user", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(data)
            });
            const result = await res.json();

            if (result.success) {
                showMessage("Cập nhật thành công!", "green");
                emailInput.setAttribute("readonly", true);
                phoneInput.setAttribute("readonly", true);
                btnSave.style.display = "none";
                btnCancel.style.display = "none";
                btnEdit.style.display = "inline-block";
            } else {
                showMessage(result.message || "Cập nhật thất bại!", "red");
            }
        } catch (err) {
            showMessage("Không thể kết nối máy chủ!", "red");
        }
    });
});
</script>
