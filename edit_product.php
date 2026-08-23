<?php
require 'db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ตรวจสอบสิทธิ์ admin
if (($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: index.php");
    exit;
}

$id = intval($_GET['id'] ?? 0);
$msg = '';
$msg_type = 'danger';

// ดึงข้อมูลสินค้าเดิม
$stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    header("Location: manage_products.php");
    exit;
}

// บันทึกการแก้ไข
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name         = trim($_POST['name'] ?? '');
    $price        = floatval($_POST['price'] ?? 0);
    $category_id  = intval($_POST['category_id'] ?? 0);
    $description  = trim($_POST['description'] ?? '');
    $image        = trim($_POST['image'] ?? '');
    $detail_image = trim($_POST['detail_image'] ?? '');

    if (!empty($name) && $price >= 0) {
        $update_stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, category_id = ?, description = ?, image = ?, detail_image = ? WHERE product_id = ?");
        $update_stmt->bind_param("sdisssi", $name, $price, $category_id, $description, $image, $detail_image, $id);
        
        if ($update_stmt->execute()) {
            header("Location: manage_products.php");
            exit;
        } else {
            $msg = "เกิดข้อผิดพลาดในการอัปเดตข้อมูล";
        }
    } else {
        $msg = "กรุณากรอกข้อมูลที่จำเป็นให้ครบถ้วน";
    }
}

// ดึงรายการหมวดหมู่สินค้าสำหรับตัวเลือก dropdown
$categories = $conn->query("SELECT * FROM categories ORDER BY category_name ASC");

// ฟังก์ชันสำหรับแปลงเส้นทางรูปภาพ
function resolveImg($img_path) {
    if (empty($img_path)) return '';
    if (strpos($img_path, 'http://') === 0 || strpos($img_path, 'https://') === 0) {
        return $img_path;
    }
    return 'uploads/' . $img_path;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขสินค้า - GreenTail Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-success text-white py-3 rounded-top-4">
                    <h5 class="mb-0 fw-bold"><i class="fa-solid fa-pen-to-square me-2"></i>แก้ไขข้อมูลสินค้า</h5>
                </div>
                <div class="card-body p-4">
                    
                    <?php if ($msg): ?>
                        <div class="alert alert-<?= $msg_type ?> alert-dismissible fade show mb-3" role="alert">
                            <?= $msg ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label fw-bold">ชื่อสินค้า <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">ราคา (บาท) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="price" class="form-control" value="<?= htmlspecialchars($product['price']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">หมวดหมู่สินค้า</label>
                                <select name="category_id" class="form-select">
                                    <option value="0">-- เลือกหมวดหมู่ --</option>
                                    <?php if ($categories && $categories->num_rows > 0): ?>
                                        <?php while ($cat = $categories->fetch_assoc()): ?>
                                            <option value="<?= $cat['category_id'] ?>" <?= $product['category_id'] == $cat['category_id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($cat['category_name']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">รายละเอียดสินค้า</label>
                            <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                        </div>

                        <!-- ลิงก์รูปภาพหลัก -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">ลิงก์รูปภาพหลักสินค้า (URL หรือชื่อไฟล์)</label>
                            <input type="text" name="image" class="form-control" value="<?= htmlspecialchars($product['image'] ?? '') ?>" placeholder="https://example.com/main.jpg">
                            <?php if (!empty($product['image'])): ?>
                                <div class="mt-2 text-center">
                                    <small class="text-muted d-block mb-1">รูปหลักปัจจุบัน:</small>
                                    <img src="<?= htmlspecialchars(resolveImg($product['image'])) ?>" class="img-thumbnail rounded-3" style="max-height: 100px; object-fit: cover;">
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- ลิงก์รูปภาพรายละเอียดสินค้า -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">ลิงก์รูปภาพรายละเอียดสินค้า (URL หรือชื่อไฟล์)</label>
                            <input type="text" name="detail_image" class="form-control" value="<?= htmlspecialchars($product['detail_image'] ?? '') ?>" placeholder="https://example.com/detail.jpg">
                            <?php if (!empty($product['detail_image'])): ?>
                                <div class="mt-2 text-center">
                                    <small class="text-muted d-block mb-1">รูปรายละเอียดปัจจุบัน:</small>
                                    <img src="<?= htmlspecialchars(resolveImg($product['detail_image'])) ?>" class="img-thumbnail rounded-3" style="max-height: 100px; object-fit: cover;">
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="manage_products.php" class="btn btn-outline-secondary px-4"><i class="fa-solid fa-arrow-left me-1"></i> ยกเลิก</a>
                            <button type="submit" class="btn btn-success fw-bold px-4"><i class="fa-solid fa-save me-1"></i> บันทึกการแก้ไข</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>