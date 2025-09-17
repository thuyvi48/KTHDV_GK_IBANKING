<?php
// Giả lập dữ liệu khách hàng (sau này có thể lấy từ CSDL)
$customer = [
    'name' => 'Nguyễn Văn An',
    'dob' => '1995-06-15',
    'email' => 'an.nguyen@example.com',
    'phone' => '0987654321',
    'address' => '123 Đường ABC, Quận 1, TP.HCM'
];
?>

<h1 class="page-title">Thông tin khách hàng</h1>

<div class="form-section">
    <form action="#" method="post">
        <div class="form-group">
            <label for="name">Họ và tên</label>
            <input type="text" id="name" name="name" value="<?php echo $customer['name']; ?>">
        </div>

        <div class="form-group">
            <label for="dob">Ngày sinh</label>
            <input type="date" id="dob" name="dob" value="<?php echo $customer['dob']; ?>">
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo $customer['email']; ?>">
        </div>

        <div class="form-group">
            <label for="phone">Số điện thoại</label>
            <input type="text" id="phone" name="phone" value="<?php echo $customer['phone']; ?>">
        </div>

        <div class="form-group">
            <label for="address">Địa chỉ</label>
            <textarea id="address" name="address" rows="3"><?php echo $customer['address']; ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Cập nhật thông tin</button>
    </form>
</div>
