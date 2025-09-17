<?php include '../includes/header.php'; ?>
<div class="main-content">
  <?php include '../includes/sidebar.php'; ?>

  <div class="content">
    <h2>Lịch sử giao dịch</h2>
    <p>Theo dõi tất cả các giao dịch của bạn</p>

    <div class="filters">
      <input type="text" placeholder="Tìm theo mô tả hoặc tài khoản">
      <select><option>Tất cả loại giao dịch</option></select>
      <select><option>Tất cả danh mục</option></select>
      <select><option>Tất cả thời gian</option></select>
      <button class="btn">Xuất báo cáo</button>
    </div>

    <div class="transaction-list">
      <div class="transaction-item success">
        <p><b>Lương tháng 11/2023</b></p>
        <small>2023-11-15 10:30 | Công ty ABC</small>
        <span class="amount">+12,000,000 đ</span>
      </div>

      <div class="transaction-item danger">
        <p><b>Thanh toán học phí kỳ 1</b></p>
        <small>2023-11-14 14:22 | Trường ĐH XYZ</small>
        <span class="amount">-5,000,000 đ</span>
      </div>
    </div>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
