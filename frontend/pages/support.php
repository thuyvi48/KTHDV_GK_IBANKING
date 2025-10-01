<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Giả định các file PHPMailer đã được require đúng cách
require __DIR__ . '/../../vendor/PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/../../vendor/PHPMailer-master/src/SMTP.php';
require __DIR__ . '/../../vendor/PHPMailer-master/src/Exception.php';

$success = '';
$error = '';

// Khởi tạo các biến để lưu trữ dữ liệu hoặc để trống
$fullname = $_POST['fullname'] ?? '';
$email    = $_POST['email'] ?? '';
$subject  = $_POST['subject'] ?? '';
$message  = $_POST['message'] ?? '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    if ($fullname && $email && $subject && $message) {
        $mail = new PHPMailer(true);

        $mail->CharSet  = 'UTF-8';   
        $mail->Encoding = 'base64';
        
        try {
            // Cấu hình Mailtrap SMTP
            $mail->isSMTP();
            $mail->Host       = 'sandbox.smtp.mailtrap.io'; // host của Mailtrap
            $mail->SMTPAuth   = true;
            $mail->Username   = 'dd9e8e310ed63e'; // thay bằng username trong Mailtrap
            $mail->Password   = '3b16bf85dafb78'; // thay bằng password trong Mailtrap
            $mail->Port       = 2525; // hoặc 587
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

            // Người gửi (dùng email test, không cần thật)
            $mail->setFrom('support@gmail.com', 'Customer Support');
            // Người nhận (admin)
            $mail->addAddress("phanthuyvi1004@gmail.com", "Admin Website");

            // Nội dung email
            $mail->isHTML(true);
            $mail->Subject = "Yêu cầu hỗ trợ: " . $subject;
            $mail->Body    = "
                <h3>Yêu cầu hỗ trợ từ khách hàng</h3>
                <p><b>Họ tên:</b> $fullname</p>
                <p><b>Email:</b> $email</p>
                <p><b>Chủ đề:</b> $subject</p>
                <p><b>Nội dung:</b><br>" . nl2br(htmlspecialchars($message)) . "</p>
            ";

            $mail->send();
            $success = "Yêu cầu của bạn đã được gửi thành công.";
            
            // XÓA DỮ LIỆU INPUT SAU KHI GỬI THÀNH CÔNG
            $fullname = '';
            $email    = '';
            $subject  = ''; // Giá trị này sẽ được dùng để kiểm tra "selected"
            $message  = '';
            
        } catch (Exception $e) {
            $error = "Không thể gửi email. Lỗi: {$mail->ErrorInfo}";
        }
    } else {
        $error = "Vui lòng nhập đầy đủ thông tin.";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Form Liên Hệ</title>
    <style>
        

        .container {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }

        input[type="text"],
        input[type="email"],
        select,
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box; 
            font-size: 16px;
        }
    </style>
</head>
<body>

<div class="container">

    <form action="" method="post">
        <div class="form-group">
            <label for="fullname">Họ và tên</label>
            <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($fullname); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email liên hệ</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
        </div>
        <div class="form-group">
            <label for="subject">Chủ đề</label>
            <select id="subject" name="subject" required>
                <?php $selected_subject = $subject; ?>
                <option value="payment" <?php if ($selected_subject === 'payment') echo 'selected'; ?>>Vấn đề thanh toán</option>
                <option value="account" <?php if ($selected_subject === 'account') echo 'selected'; ?>>Vấn đề tài khoản</option>
                <option value="service" <?php if ($selected_subject === 'service') echo 'selected'; ?>>Vấn đề dịch vụ</option>
                <option value="other" <?php if ($selected_subject === 'other') echo 'selected'; ?>>Khác</option>
            </select>
        </div>
        <div class="form-group">
            <label for="message">Nội dung</label>
            <textarea id="message" name="message" rows="5" required><?php echo htmlspecialchars($message); ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Gửi yêu cầu</button>
    </form>

    <?php if (!empty($success)): ?>
        <p class="alert-success"><?php echo $success; ?></p>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <p class="alert-error"><?php echo $error; ?></p>
    <?php endif; ?>

</div>

</body>
</html>