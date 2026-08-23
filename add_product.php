<?php
require 'db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$msg = '';
$categories = $conn->query("SELECT * FROM categories ORDER BY category_name ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name         = trim($_POST['name']);
    $category_id  = (int) $_POST['category_id'];
    $price        = (float) $_POST['price'];
    $stock        = (int) $_POST['stock'];
    $colors       = trim($_POST['colors']);
    $description  = trim($_POST['description']);
    $image_name   = trim($_POST['image']);        // ลิงก์รูปหลัก
    $detail_image = trim($_POST['detail_image']); // ลิงก์รูปรายละเอียด

    if (!empty($name) && $price >= 0) {
        $stmt = $conn->prepare("INSERT INTO products (category_id, name, description, price, stock, colors, image, detail_image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issdisss", $category_id, $name, $description, $price, $stock, $colors, $image_name, $detail_image);
        
        if ($stmt->execute()) {
            header("Location: manage_products.php");
            exit;
        } else {
            $msg = 'เกิดข้อผิดพลาด ไม่สามารถเพิ่มสินค้าได้';
        }
    } else {
        $msg = 'กรุณากรอกข้อมูลให้ครบถ้วน';
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เพิ่มสินค้า / เติมสต็อก - GreenTail Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

<div class="container py-5" style="max-width: 650px;">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-success text-white text-center py-3 rounded-top-4">
            <h5 class="mb-0 fw-bold"><i class="fa-solid fa-plus-circle me-2"></i>เพิ่มสินค้าใหม่ / เติมของ</h5>
        </div>
        <div class="card-body p-4">

            <?php if ($msg): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($msg) ?></div>
            <?php endif; ?>

            <form method="post">
                <div class="mb-3">
                    <label class="form-label fw-bold">ชื่อสินค้า <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" required placeholder="เช่น เต็นท์สนาม 4 คน">
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">หมวดหมู่</label>
                        <select name="category_id" class="form-select">
                            <option value="0">General (ทั่วไป)</option>
                            <?php if ($categories): ?>
                                <?php while ($c = $categories->fetch_assoc()): ?>
                                    <option value="<?= $c['category_id'] ?>"><?= htmlspecialchars($c['category_name']) ?></option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">ราคา (บาท) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="price" class="form-control" required placeholder="0.00">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">จำนวนสต็อก (เติมของ) <span class="text-danger">*</span></label>
                        <input type="number" name="stock" class="form-control" value="10" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">ตัวเลือกสี (คั่นด้วยจุลภาค ,)</label>
                        <input type="text" name="colors" class="form-control" placeholder="เขียวขี้ม้า, ดำ, ส้ม">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">รายละเอียดสินค้า</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="ระบุรายละเอียด วัสดุ ขนาด ฯลฯ"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">ลิงก์รูปภาพสินค้าหลัก (URL)</label>
                    <input type="url" name="image" class="form-control" placeholder="https://example.com/main-image.jpg">
                    <small class="text-muted">รูปที่จะแสดงในหน้าแรกและหน้าแคตตาล็อก</small>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">ลิงก์รูปภาพรายละเอียดสินค้า (URL)</label>
                    <input type="url" name="detail_image" class="form-control" placeholder="https://example.com/detail-image.jpg">
                    <small class="text-muted">รูปภาพเพิ่มเติมสำหรับแสดงภายในหน้ารายละเอียดสินค้า</small>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success flex-grow-1 fw-bold"><i class="fa-solid fa-save me-1"></i> บันทึกสินค้า</button>
                    <a href="manage_products.php" class="btn btn-outline-secondary px-4">ยกเลิก</a>
                </div>
            </form>

        </div>
    </div>
</div>

</body>
</html>