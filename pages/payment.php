<?php include '../includes/header.php'; ?>
<div class="main-content">
  <?php include '../includes/sidebar.php'; ?>

  <div class="content">
    <h2>Thanh toán học phí</h2>
    <p>Thực hiện các giao dịch chuyển tiền và thanh toán</p>

    <div class="payment">
      <div class="form-box">
        <h3>Thông tin giao dịch</h3>
        <form>
          <label>Loại giao dịch</label>
          <select>
            <option>Thanh toán học phí</option>
            <option>Chuyển khoản khác</option>
          </select>

          <label>Số tài khoản trường học</label>
          <input type="text" placeholder="Nhập số tài khoản của trường">

          <label>Tên trường</label>
          <input type="text" placeholder="Ví dụ: Trường Đại học XYZ">

          <label>Số tiền</label>
          <input type="number" placeholder="Nhập số tiền học phí">

          <label>Nội dung</label>
          <textarea placeholder="Ví dụ: Thanh toán học phí kỳ 1"></textarea>

          <button type="submit" class="btn">Thanh toán</button>
        </form>
      </div>

      <div class="side-box">
        <div class="card">
          <h4>Số dư khả dụng</h4>
          <p class="balance">2,450,000 đ</p>
        </div>

        <div class="card">
          <h4>Chuyển nhanh</h4>
          <ul>
            <li>Nguyễn Thị B (VCB - 1234567890)</li>
            <li>Trần Văn C (BIDV - 0987654321)</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
