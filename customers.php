<?php
require 'db.php';
require 'header.php';

// ระบบลบข้อมูลลูกค้า
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    
    // ลบลูกค้า (ปรับเปลี่ยนชื่อตาราง/คอลัมน์ PK ให้ตรงกับ DB ของคุณ เช่น user_id หรือ id)
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: customers.php");
    exit;
}

// ค้นหาลูกค้า
$search = trim($_GET['search'] ?? '');
$where = [];
if ($search !== '') {
    $s = $conn->real_escape_string($search);
    $where[] = "(fullname LIKE '%$s%' OR email LIKE '%$s%' OR phone LIKE '%$s%')";
}

$sql = "SELECT * FROM users";
if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY user_id DESC";
$result = $conn->query($sql);
?>

<div class="container py-4">

    <!-- การ์ดหัวข้อจัดการลูกค้า (ตรงตามรูปภาพ) -->
    <div class="row justify-content-center mb-4">
        <div class="col-12 col-sm-8 col-md-5 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 text-center p-4 bg-white">
                <div class="mb-3">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light p-3" style="width: 70px; height: 70px;">
                        <i class="fa-solid fa-user-group text-success fs-1"></i>
                    </div>
                </div>
                <h5 class="fw-bold text-dark mb-1">จัดการลูกค้า</h5>
                <p class="text-muted small mb-0">ดูและแก้ไขข้อมูลลูกค้า</p>
            </div>
        </div>
    </div>

    <!-- ส่วนตารางและค้นหาข้อมูลลูกค้า -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white py-3 px-4 d-flex flex-wrap align-items-center justify-content-between border-bottom">
            <h5 class="fw-bold text-success mb-2 mb-md-0">
                <i class="fa-solid fa-users text-success me-2"></i>รายชื่อลูกค้าทั้งหมด
            </h5>
            
            <!-- ฟอร์มค้นหา -->
            <form method="get" action="customers.php" class="d-flex align-items-center gap-2 m-0" style="max-width: 300px;">
                <div class="input-group">
                    <input type="text" name="search" class="form-control form-control-sm rounded-pill-start border-success" placeholder="ค้นหาชื่อ, อีเมล, เบอร์โทร..." value="<?= htmlspecialchars($search) ?>">
                    <button type="submit" class="btn btn-sm btn-success rounded-pill-end px-3">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4" style="width: 80px;">รหัส</th>
                            <th>ชื่อ - นามสกุล</th>
                            <th>อีเมล</th>
                            <th>เบอร์โทรศัพท์</th>
                            <th class="text-center" style="width: 120px;">การจัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while ($user = $result->fetch_assoc()): ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-secondary">#<?= $user['user_id'] ?></td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($user['fullname'] ?? $user['username'] ?? 'ไม่ระบุ') ?></div>
                                    </td>
                                    <td class="text-muted"><?= htmlspecialchars($user['email'] ?? '-') ?></td>
                                    <td class="text-muted"><?= htmlspecialchars($user['phone'] ?? '-') ?></td>
                                    <td class="text-center">
                                        <a href="customers.php?delete_id=<?= $user['user_id'] ?>" 
                                           class="btn btn-outline-danger btn-sm rounded-circle p-2" 
                                           onclick="return confirm('คุณต้องการลบข้อมูลลูกค้ารายนี้หรือไม่?');"
                                           title="ลบข้อมูล">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">ไม่พบข้อมูลลูกค้า</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

</body>
</html>