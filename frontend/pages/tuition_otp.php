<div class="container mt-4">
  <h2>Xác thực OTP</h2>

  <div id="errorBox"></div>

  <form id="otpForm">
    <label>Nhập mã OTP đã gửi về email</label>
    <input type="text" name="otp" id="otp" class="form-control" required>
    <button type="submit" class="btn btn-primary mt-3">Xác nhận</button>
  </form>
</div>

<script>
// Giả sử transaction_id & user_id đã lưu ở sessionStorage từ bước "Tạo giao dịch"
const transactionId = sessionStorage.getItem("transaction_id");
const userId = sessionStorage.getItem("user_id");

document.getElementById("otpForm").addEventListener("submit", async function(e){
    e.preventDefault();

    const otpCode = document.getElementById("otp").value;

    try {
        // Gọi OTP Service verify
        let response = await fetch("http://localhost/backend/otp_service/verify.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                user_id: userId,
                otp_code: otpCode,
                type: "tuition"
            })
        });
        let otpResult = await response.json();

        if (!otpResult.success) {
            document.getElementById("errorBox").innerHTML =
              `<div class="alert alert-danger">${otpResult.message}</div>`;
            return;
        }

        // Nếu OTP OK thì gọi Payment Service confirm
        let payRes = await fetch("http://localhost/backend/payment_service/confirm.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                transaction_id: transactionId,
                user_id: userId
            })
        });

        let payResult = await payRes.json();

        if (payResult.success) {
            alert("Thanh toán thành công!");
            window.location.href = "transaction.php?success=1";
        } else {
            document.getElementById("errorBox").innerHTML =
              `<div class="alert alert-danger">${payResult.message}</div>`;
        }

    } catch (err) {
        console.error(err);
        document.getElementById("errorBox").innerHTML =
          `<div class="alert alert-danger">Có lỗi xảy ra, vui lòng thử lại.</div>`;
    }
});
</script>
