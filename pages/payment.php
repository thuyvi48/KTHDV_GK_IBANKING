<h1 class="page-title">Thanh toán học phí</h1>

<div class="form-section">
    <form action="#" method="post">
        <div class="form-group">
            <label for="studentId">Mã sinh viên</label>
            <input type="text" id="studentId" name="studentId" placeholder="VD: SV123456">
        </div>

        <div class="form-group">
            <label for="fullname">Họ và tên</label>
            <input type="text" id="fullname" name="fullname" placeholder="Nhập họ tên sinh viên">
        </div>

        <div class="form-group">
            <label for="semester">Học kỳ</label>
            <select id="semester" name="semester">
                <option value="">-- Chọn học kỳ --</option>
                <option value="1">Học kỳ 1 (2025)</option>
                <option value="2">Học kỳ 2 (2025)</option>
            </select>
        </div>

        <div class="form-group">
            <label for="amount">Số tiền cần thanh toán</label>
            <input type="number" id="amount" name="amount" value="5000000">
        </div>

        <button type="submit" class="btn btn-primary">Thanh toán ngay</button>
    </form>
</div>
