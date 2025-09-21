<?php
include __DIR__ . '/../includes/db.php';

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}
$namePage = "Lịch sử giao dịch";

$user = $_SESSION['user'];
$user_id = $user['id'];

// Lấy giao dịch
$sql = "SELECT * FROM transactions WHERE user_id = $user_id ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<div class="container mt-4">
  <h2>Lịch sử giao dịch</h2>

  <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">Giao dịch học phí thành công!</div>
  <?php endif; ?>

  <table class="table table-striped">
    <thead>
      <tr>
        <th>Thời gian</th>
        <th>Loại giao dịch</th>
        <th>Số tiền</th>
        <th>Mô tả</th>
      </tr>
    </thead>
    <tbody>
      <?php while($row = mysqli_fetch_assoc($result)): ?>
        <tr>
          <td><?php echo $row['created_at']; ?></td>
          <td>
            <?php 
              if ($row['type'] === 'tuition') {
                echo "Đóng học phí";
              } else {
                echo ucfirst($row['type']);
              }
            ?>
          </td>
          <td><?php echo number_format($row['amount']); ?> đ</td>
          <td><?php echo $row['description']; ?></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
