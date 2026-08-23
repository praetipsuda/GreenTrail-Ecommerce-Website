<?php
require 'db.php';
require 'header.php';

$search = trim($_GET['search'] ?? '');
$cat_id = intval($_GET['cat'] ?? 0);

// ดึงรายการหมวดหมู่สินค้าทั้งหมด
$categories = $conn->query("SELECT * FROM categories ORDER BY category_name ASC");

// ดึงสินค้าขายดี 4 อันดับแรก (คำนวณจากยอดรวมการสั่งซื้อใน order_items)
$bestseller_sql = "
    SELECT p.*, COALESCE(SUM(oi.quantity), 0) AS total_sold 
    FROM products p 
    LEFT JOIN order_items oi ON p.product_id = oi.product_id 
    GROUP BY p.product_id 
    ORDER BY total_sold DESC, p.product_id DESC 
    LIMIT 4
";
$bestseller_result = $conn->query($bestseller_sql);

// รวมเงื่อนไขค้นหาและตัวกรองหมวดหมู่
$where = [];
if ($search !== '') {
    $where[] = "name LIKE '%" . $conn->real_escape_string($search) . "%'";
}
if ($cat_id > 0) {
    $where[] = "category_id = " . $cat_id;
}

$sql = "SELECT * FROM products";
if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY product_id DESC";
$result = $conn->query($sql);
?>

<div class="container py-4">

    <!-- Hero Banner -->
    <div class="card border-0 mb-4 shadow-sm" style="border-radius: 20px; overflow: hidden;">
        <img src="Greentail.png" class="w-100 img-fluid d-block" style="max-height: 600px; object-fit: cover;" alt="GreenTail Banner">
    </div>

    <!-- ช่องค้นหาสินค้า -->
    <div class="row justify-content-center mb-4">
        <div class="col-md-6">
            <form method="get" action="index.php#products">
                <?php if ($cat_id > 0): ?>
                    <input type="hidden" name="cat" value="<?= $cat_id ?>">
                <?php endif; ?>
                <div class="input-group">
                    <input type="text" name="search" class="form-control rounded-pill-start border-0 shadow-sm ps-4" placeholder="ค้นหาสินค้า เช่น รองเท้า, ไฟฉาย..." value="<?= htmlspecialchars($search) ?>">
                    <button type="submit" class="btn btn-success rounded-pill-end px-4 shadow-sm"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
            </form>
        </div>
    </div>

    <!-- ปุ่มเลือกหมวดหมู่สินค้าพร้อมรูปภาพขนาดใหญ่ขึ้น -->
    <div id="products" class="mb-5 text-center">
        <div class="d-flex flex-wrap justify-content-center gap-2 align-items-center">
            <!-- ปุ่มทั้งหมด -->
            <a href="index.php?search=<?= urlencode($search) ?>#products" 
               class="btn <?= $cat_id === 0 ? 'btn-success fw-bold' : 'btn-outline-success' ?> rounded-pill px-3 py-2 d-inline-flex align-items-center gap-2">
                <i class="fa-solid fa-border-all fs-5"></i>
                <span>ทั้งหมด</span>
            </a>

            <?php if ($categories && $categories->num_rows > 0): ?>
                <?php while ($c = $categories->fetch_assoc()): ?>
                    <?php 
                        // ตรวจสอบรูปภาพหมวดหมู่ (รองรับทั้ง URL และโฟลเดอร์ uploads)
                        $c_img = $c['category_image'] ?? '';
                        if (!empty($c_img)) {
                            if (strpos($c_img, 'http://') === 0 || strpos($c_img, 'https://') === 0) {
                                $c_img_src = $c_img;
                            } else {
                                $c_img_src = 'uploads/' . $c_img;
                            }
                        } else {
                            $c_img_src = '';
                        }
                    ?>
                    <a href="index.php?cat=<?= $c['category_id'] ?>&search=<?= urlencode($search) ?>#products" 
                       class="btn <?= $cat_id == $c['category_id'] ? 'btn-success fw-bold' : 'btn-outline-success' ?> rounded-pill px-3 py-2 d-inline-flex align-items-center gap-2">
                        <?php if (!empty($c_img_src)): ?>
                            <img src="<?= htmlspecialchars($c_img_src) ?>" alt="" style="width: 100px; height: 100px; object-fit: cover;" class="rounded-circle">
                        <?php else: ?>
                            <i class="fa-solid fa-tag fs-5"></i>
                        <?php endif; ?>
                        <span><?= htmlspecialchars($c['category_name']) ?></span>
                    </a>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- ================= ส่วนสินค้าขายดี ================= -->
    <?php if (empty($search) && $cat_id === 0 && $bestseller_result && $bestseller_result->num_rows > 0): ?>
        <div class="mb-5">
            <div class="d-flex align-items-center mb-3">
                <h4 class="fw-bold text-success mb-0">
                    <i class="fa-solid fa-fire text-danger me-2"></i>สินค้าขายดีประจำร้าน
                </h4>
            </div>

            <div class="row g-3">
                <?php while ($b = $bestseller_result->fetch_assoc()): ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <a href="product_detail.php?id=<?= $b['product_id'] ?>" class="text-decoration-none text-dark">
                            <div class="card product-card h-100 p-2 text-center position-relative">
                                <!-- Badge ป้ายขายดี -->
                                <span class="position-absolute top-0 start-0 bg-danger text-white px-2 py-1 rounded-end fw-bold" style="font-size: 0.75rem; z-index: 5;">
                                    🔥 ขายดี
                                </span>

                                <?php 
                                    $b_raw_img = $b['image'] ?? '';
                                    if (!empty($b_raw_img)) {
                                        if (strpos($b_raw_img, 'http://') === 0 || strpos($b_raw_img, 'https://') === 0) {
                                            $b_img_src = $b_raw_img;
                                        } else {
                                            $b_img_src = 'uploads/' . $b_raw_img;
                                        }
                                    } else {
                                        $b_img_src = 'https://via.placeholder.com/200';
                                    }
                                ?>
                                <img src="<?= htmlspecialchars($b_img_src) ?>" class="card-img-top product-img" alt="<?= htmlspecialchars($b['name']) ?>">
                                
                                <div class="card-body p-2 d-flex flex-column justify-content-between">
                                    <h6 class="card-title text-truncate fw-bold mb-1" title="<?= htmlspecialchars($b['name']) ?>">
                                        <?= htmlspecialchars($b['name']) ?>
                                    </h6>
                                    <?php if ($b['total_sold'] > 0): ?>
                                        <div class="text-muted small mb-1">
                                            <i class="fa-solid fa-bag-shopping me-1 text-warning"></i>ขายแล้ว <?= number_format($b['total_sold']) ?> ชิ้น
                                        </div>
                                    <?php endif; ?>
                                    <div class="text-success fw-bold fs-6">
                                        <?= number_format($b['price'], 2) ?> บาท
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    <?php endif; ?>
    <!-- ================= จบส่วนสินค้าขายดี ================= -->

    <!-- หัวข้อรายการสินค้า -->
    <div class="d-flex align-items-center mb-3">
        <h4 class="fw-bold text-success mb-0"><i class="fa-solid fa-leaf me-2"></i>สินค้า GreenTail</h4>
    </div>

    <!-- รายการสินค้า -->
    <div class="row g-3">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <a href="product_detail.php?id=<?= $row['product_id'] ?>" class="text-decoration-none text-dark">
                        <div class="card product-card h-100 p-2 text-center">
                            <?php 
                                $raw_img = $row['image'] ?? '';
                                if (!empty($raw_img)) {
                                    if (strpos($raw_img, 'http://') === 0 || strpos($raw_img, 'https://') === 0) {
                                        $img_src = $raw_img;
                                    } else {
                                        $img_src = 'uploads/' . $raw_img;
                                    }
                                } else {
                                    $img_src = 'https://via.placeholder.com/200';
                                }
                            ?>
                            <img src="<?= htmlspecialchars($img_src) ?>" class="card-img-top product-img" alt="<?= htmlspecialchars($row['name']) ?>">
                            <div class="card-body p-2 d-flex flex-column justify-content-between">
                                <h6 class="card-title text-truncate fw-bold mb-1" title="<?= htmlspecialchars($row['name']) ?>">
                                    <?= htmlspecialchars($row['name']) ?>
                                </h6>
                                <div class="text-success fw-bold fs-6">
                                    <?= number_format($row['price'], 2) ?> บาท
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <p class="text-muted fs-5">ไม่พบสินค้าที่ต้องการ</p>
            </div>
        <?php endif; ?>
    </div>

</div>

</body>
</html>