<?php include '../includes/header.php'; ?>
<div class="main-content">
  <?php include '../includes/sidebar.php'; ?>

  <div class="content">
    <h2>Thông tin khách hàng</h2>
    <p>Quản lý và cập nhật thông tin cá nhân</p>

    <div class="customer-info">
      <div class="info-left">
        <h3>Thông tin cá nhân</h3>
        <form>
          <label>Họ và tên</label>
          <input type="text" value="Nguyễn Văn An" disabled>
          
          <label>Email</label>
          <input type="email" value="nguyen.van.an@email.com" disabled>
          
          <label>Ngày sinh</label>
          <input type="text" value="15/08/1990" disabled>
          
          <label>Địa chỉ</label>
          <input type="text" value="123 Đường ABC, Quận 1, TP.HCM" disabled>

          <label>CCCD/CMND</label>
          <input type="text" value="025123456789" disabled>
          
          <label>Số điện thoại</label>
          <input type="text" value="0901234567" disabled>
        </form>
      </div>

      <div class="info-right">
        <div class="card">
          <h4>Tài khoản ngân hàng</h4>
          <div class="account-box">•••• •••• •••• 3456</div>
          <button class="btn">Sao chép STK</button>
        </div>

        <div class="card">
          <h4>Trạng thái bảo mật</h4>
          <ul>
            <li>Xác thực 2 lớp: <span class="badge success">Đã bật</span></li>
            <li>Xác thực email: <span class="badge success">Đã xác thực</span></li>
            <li>Xác thực SMS: <span class="badge success">Đã xác thực</span></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
