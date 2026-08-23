<?php
require 'db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ตรวจสอบสิทธิ์การใช้งาน
if (($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: index.php");
    exit;
}

$msg = '';
$msg_type = 'success';

// บันทึกหมวดหมู่ใหม่
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $cat_name  = trim($_POST['category_name'] ?? '');
    $cat_image = trim($_POST['category_image'] ?? '');

    if (!empty($cat_name)) {
        $stmt = $conn->prepare("INSERT INTO categories (category_name, category_image) VALUES (?, ?)");
        $stmt->bind_param("ss", $cat_name, $cat_image);
        if ($stmt->execute()) {
            $msg = "เพิ่มหมวดหมู่ '" . htmlspecialchars($cat_name) . "' เรียบร้อยแล้ว!";
        } else {
            $msg = "เกิดข้อผิดพลาดในการเพิ่มหมวดหมู่";
            $msg_type = 'danger';
        }
    }
}

// ลบสินค้า
if (isset($_GET['delete'])) {
    $del_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $del_id);
    if ($stmt->execute()) {
        header("Location: manage_products.php");
        exit;
    }
}

// ลบหมวดหมู่
if (isset($_GET['delete_cat'])) {
    $del_cat_id = intval($_GET['delete_cat']);
    $stmt = $conn->prepare("DELETE FROM categories WHERE category_id = ?");
    $stmt->bind_param("i", $del_cat_id);
    if ($stmt->execute()) {
        header("Location: manage_products.php");
        exit;
    }
}

// ดึงข้อมูลสินค้าและหมวดหมู่
$products   = $conn->query("SELECT p.*, c.category_name FROM products p LEFT JOIN categories c ON p.category_id = c.category_id ORDER BY p.product_id DESC");
$categories = $conn->query("SELECT * FROM categories ORDER BY category_id DESC");

// ฟังก์ชันดึง URL รูปภาพ
function getImgSrc($img) {
    if (empty($img)) return 'https://via.placeholder.com/60';
    if (strpos($img, 'http://') === 0 || strpos($img, 'https://') === 0) return $img;
    return 'uploads/' . $img;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการสินค้า - GreenTail Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">

<div class="container py-4">

    <!-- Header & Action Buttons -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-success mb-1"><i class="fa-solid fa-boxes-stacked me-2"></i>จัดการสินค้า</h3>
            <p class="text-muted mb-0">รายการสินค้าและหมวดหมู่ทั้งหมดในระบบ</p>
        </div>
        <div class="d-flex gap-2">
            <a href="admin.php" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> กลับหน้าแอดมิน</a>
            <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#categoryModal"><i class="fa-solid fa-tags me-1"></i> จัดการหมวดหมู่</button>
            <a href="add_product.php" class="btn btn-success fw-bold"><i class="fa-solid fa-plus me-1"></i> เพิ่มสินค้าใหม่</a>
        </div>
    </div>

    <?php if ($msg): ?>
        <div class="alert alert-<?= $msg_type ?> alert-dismissible fade show mb-4" role="alert">
            <?= $msg ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- ตารางรายการสินค้า -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width: 100px;">รูปภาพ</th>
                        <th>ชื่อสินค้า</th>
                        <th>ราคา</th>
                        <th>รายละเอียด</th>
                        <th class="text-end pe-4">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($products && $products->num_rows > 0): ?>
                        <?php while ($row = $products->fetch_assoc()): ?>
                            <tr>
                                <td class="ps-4">
                                    <img src="<?= htmlspecialchars(getImgSrc($row['image'])) ?>" class="rounded-3" style="width: 50px; height: 50px; object-fit: cover;" alt="">
                                </td>
                                <td>
                                    <div class="fw-bold"><?= htmlspecialchars($row['name']) ?></div>
                                    <?php if (!empty($row['category_name'])): ?>
                                        <span class="badge bg-light text-success border border-success me-1"><?= htmlspecialchars($row['category_name']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-bold text-success">฿<?= number_format($row['price'], 2) ?></td>
                                <td class="text-muted small text-truncate" style="max-width: 250px;">
                                    <?= htmlspecialchars($row['description'] ?? '') ?>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="edit_product.php?id=<?= $row['product_id'] ?>" class="btn btn-sm btn-outline-warning text-dark"><i class="fa-solid fa-pen-to-square"></i> แก้ไข</a>
                                    <a href="manage_products.php?delete=<?= $row['product_id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('ยืนยันที่จะลบสินค้านี้?');"><i class="fa-solid fa-trash"></i> ลบ</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">ยังไม่มีรายการสินค้าในระบบ</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal สำหรับเพิ่ม/จัดการหมวดหมู่สินค้า -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold" id="categoryModalLabel"><i class="fa-solid fa-folder-plus me-2"></i>จัดการหมวดหมู่สินค้า</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                
                <!-- ฟอร์มเพิ่มหมวดหมู่ -->
                <form method="post" class="card border p-3 mb-4 bg-light rounded-3">
                    <h6 class="fw-bold text-success mb-3"><i class="fa-solid fa-plus me-1"></i>เพิ่มหมวดหมู่ใหม่</h6>
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label small fw-bold">ชื่อหมวดหมู่ <span class="text-danger">*</span></label>
                            <input type="text" name="category_name" class="form-control form-control-sm" placeholder="เช่น รองเท้า, เต็นท์" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-bold">ลิงก์รูปภาพหมวดหมู่ (URL)</label>
                            <input type="url" name="category_image" class="form-control form-control-sm" placeholder="https://example.com/cat-icon.png">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" name="add_category" class="btn btn-success btn-sm w-100 fw-bold"><i class="fa-solid fa-save me-1"></i> บันทึก</button>
                        </div>
                    </div>
                </form>

                <!-- รายการหมวดหมู่ที่มีอยู่ -->
                <h6 class="fw-bold text-secondary mb-2">รายการหมวดหมู่ทั้งหมด</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 60px;" class="text-center">รูป</th>
                                <th>ชื่อหมวดหมู่</th>
                                <th style="width: 80px;" class="text-center">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($categories && $categories->num_rows > 0): ?>
                                <?php while ($cat = $categories->fetch_assoc()): ?>
                                    <tr>
                                        <td class="text-center">
                                            <?php if (!empty($cat['category_image'])): ?>
                                                <img src="<?= htmlspecialchars(getImgSrc($cat['category_image'])) ?>" style="width: 30px; height: 30px; object-fit: cover;" class="rounded-circle">
                                            <?php else: ?>
                                                <i class="fa-solid fa-tag text-muted"></i>
                                            <?php endif; ?>
                                        </td>
                                        <td class="fw-bold text-dark"><?= htmlspecialchars($cat['category_name']) ?></td>
                                        <td class="text-center">
                                            <a href="manage_products.php?delete_cat=<?= $cat['category_id'] ?>" class="btn btn-outline-danger btn-sm p-1 px-2" onclick="return confirm('ยืนยันลบหมวดหมู่นี้?');">
                                                <i class="fa-solid fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted small py-3">ยังไม่มีหมวดหมู่สินค้า</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

</body>
</html>