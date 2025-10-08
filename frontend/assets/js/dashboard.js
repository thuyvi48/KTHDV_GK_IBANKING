function formatCurrency(amount) {
    return new Intl.NumberFormat("vi-VN").format(amount) + " đ";
}

document.addEventListener("DOMContentLoaded", () => {
    const balanceField = document.querySelector("[name='balance']");
    const submitBtn = document.querySelector(".agree-submit button");
    const agreeCheck = document.querySelector("[name='agree']");
    const mssvInput = document.querySelector("[name='mssv']");
    const messageBox = document.getElementById("message");

    // Modal
    const confirmModalEl = document.getElementById('confirmModal');
    const confirmModal = new bootstrap.Modal(confirmModalEl);
    const createPaymentBtn = document.getElementById('createPaymentBtn');
    const confirmMessage = document.getElementById('confirmMessage');
    const otpSection = document.getElementById('otpSection');
    const verifyOtpBtn = document.getElementById('verifyOtpBtn');
    const otpMessage = document.getElementById('otpMessage');

    let currentPaymentId = null;
    let isInvoiceValid = false;

    // Disable nút gửi ban đầu
    submitBtn.disabled = true;
    submitBtn.style.cursor = "not-allowed";
    submitBtn.style.opacity = "0.6";

    // Format tiền
    if (balanceField && balanceField.value) {
        let raw = balanceField.value.replace(/[^\d]/g, "");
        balanceField.value = formatCurrency(parseInt(raw));
    }

    function toggleSubmitButton() {
        if (agreeCheck.checked && isInvoiceValid) {
            submitBtn.disabled = false;
            submitBtn.style.cursor = "pointer";
            submitBtn.style.opacity = "1";
        } else {
            submitBtn.disabled = true;
            submitBtn.style.cursor = "not-allowed";
            submitBtn.style.opacity = "0.6";
        }
    }

    // Khi MSSV mất focus → gọi API học phí
    mssvInput.addEventListener("blur", function() {
        const mssv = this.value.trim();
        if (!mssv) {
            isInvoiceValid = false;
            toggleSubmitButton();
            return;
        }

        fetch("http://localhost/KTHDV_GK_IBANKING/api_gateway/index.php?service=student&action=get_invoice", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ mssv })
        })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                if (res.status === "PAID") {
                    messageBox.textContent = "Hóa đơn đã được thanh toán.";
                    messageBox.style.color = "green";
                    isInvoiceValid = false;
                    toggleSubmitButton();
                    return;
                }
                document.querySelector("[name='student_name']").value = res.student_name;
                document.querySelector("[name='amount']").value = formatCurrency(res.amount_due);
                document.querySelector("[name='amount_to_pay']").value = formatCurrency(res.amount_due);
                document.querySelector("[name='invoice_id']").value = res.invoice_id;
                document.querySelector("[name='student_id']").value = res.student_id;
                messageBox.textContent = "";
                isInvoiceValid = true;
            } else {
                messageBox.textContent = res.message;
                messageBox.style.color = "red";
                isInvoiceValid = false;
            }
            toggleSubmitButton();
        })
        .catch(() => {
            messageBox.textContent = "Không thể kết nối máy chủ.";
            messageBox.style.color = "red";
            isInvoiceValid = false;
            toggleSubmitButton();
        });
    });

    agreeCheck.addEventListener("change", toggleSubmitButton);

    function showMessage(text, type = "error") {
        messageBox.textContent = text;
        messageBox.style.color = (type === "success") ? "green" : "red";
        setTimeout(() => (messageBox.textContent = ""), 5000);
    }

    // Gửi form
    document.getElementById("paymentForm").addEventListener("submit", function(e) {
        e.preventDefault();
        if (submitBtn.disabled) return;

        const balance = parseInt(balanceField.value.replace(/[^\d]/g, ""));
        const amountToPay = parseInt(document.querySelector("[name='amount_to_pay']").value.replace(/[^\d]/g, ""));

        if (isNaN(amountToPay) || amountToPay <= 0) {
            showMessage("Chưa có thông tin học phí cần thanh toán.");
            return;
        }

        if (balance >= amountToPay) {
            document.getElementById('confirm_mssv').textContent = mssvInput.value;
            document.getElementById('confirm_student_name').textContent = document.querySelector("[name='student_name']").value;
            document.getElementById('confirm_invoice_id').textContent = document.querySelector("[name='invoice_id']").value;
            document.getElementById('confirm_amount_display').textContent = document.querySelector("[name='amount_to_pay']").value;
            confirmMessage.innerHTML = "";
            otpSection.style.display = "none";
            confirmModal.show();
        } else {
            showMessage("Số dư khả dụng không đủ để thanh toán học phí.");
        }
    });

    // Gửi OTP
    createPaymentBtn.addEventListener('click', function() {
        createPaymentBtn.disabled = true;
        confirmMessage.innerHTML = "Đang tạo giao dịch và gửi OTP...";

        const student_id = document.querySelector("[name='student_id']").value;
        const invoice_id = document.querySelector("[name='invoice_id']").value;
        const amount = parseInt(document.querySelector("[name='amount_to_pay']").value.replace(/[^\d]/g, ""));

        fetch("http://localhost/KTHDV_GK_IBANKING/api_gateway/index.php?service=payment&action=create", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                student_id,
                userId: "<?php echo $userId; ?>",
                invoice_id,
                amount
            })
        })
        .then(res => res.json())
        .then(data => {
            createPaymentBtn.disabled = false;
            if (data.success || data.status === "success") {
                currentPaymentId = data.payment_id ?? data.paymentId ?? null;
                confirmMessage.innerHTML = "<p class='text-success'> Giao dịch tạo thành công. OTP đã gửi đến email.</p>";
                otpSection.style.display = "block";
                document.querySelector("#createPaymentBtn").style.display = "none";
            } else {
                confirmMessage.innerHTML = `<p class='text-danger'>${data.message || "Không thể tạo giao dịch."}</p>`;
            }
        })
        .catch(err => {
            createPaymentBtn.disabled = false;
            confirmMessage.innerHTML = "<p class='text-danger'>Lỗi kết nối máy chủ.</p>";
            console.error(err);
        });
    });

    // Xác thực OTP
    verifyOtpBtn.addEventListener('click', function() {
        const otp = document.getElementById('otp_input').value.trim();
        otpMessage.textContent = "";

        if (!otp) {
            otpMessage.textContent = "Vui lòng nhập mã OTP.";
            return;
        }

        otpMessage.innerHTML = "<span style='color: green;'>Đang xác thực OTP...</span>";

        fetch("http://localhost/KTHDV_GK_IBANKING/api_gateway/index.php?service=payment&action=confirm", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                payment_id: currentPaymentId,
                user_id: "<?php echo $userId; ?>",
                otpCode: otp
            })
        })

        .then(res => res.json())
        .then(data => {
            if (data.success || data.status === "success") {
                otpMessage.innerHTML = "<p class='text-success'>Thanh toán thành công!</p>";
                setTimeout(() => location.reload(), 1500);
            } else {
                otpMessage.innerHTML = `<p class='text-danger'>${data.message || "Mã OTP không đúng."}</p>`;
            }
        })
        .catch(() => {
            otpMessage.innerHTML = "<p class='text-danger'>Không thể xác thực OTP.</p>";
        });
    });
});