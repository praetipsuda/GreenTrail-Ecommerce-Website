<?php
require 'db.php';
require 'header.php';

// ดึงข้อมูลสินค้าขายดี 10 อันดับแรก (นับจำนวนชิ้นและยอดขายรวม)
$top10_sql = "
    SELECT 
        p.product_id, 
        p.name, 
        p.price, 
        p.image, 
        COALESCE(SUM(oi.quantity), 0) AS total_sold,
        COALESCE(SUM(oi.quantity * oi.price), 0) AS total_revenue
    FROM products p
    LEFT JOIN order_items oi ON p.product_id = oi.product_id
    GROUP BY p.product_id, p.name, p.price, p.image
    ORDER BY total_sold DESC, total_revenue DESC, p.product_id DESC
    LIMIT 10
";
$top10_result = $conn->query($top10_sql);
?>

<div class="container py-4">

    <!-- การ์ดหัวข้อ 10 อันดับสินค้าขายดี (ตรงตามรูปภาพ) -->
    <div class="row justify-content-center mb-4">
        <div class="col-12 col-sm-8 col-md-5 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 text-center p-4 bg-white">
                <div class="mb-3">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light p-3" style="width: 70px; height: 70px;">
                        <i class="fa-solid fa-trophy text-success fs-1"></i>
                    </div>
                </div>
                <h5 class="fw-bold text-dark mb-1">10 อันดับสินค้าขายดี</h5>
                <p class="text-muted small mb-0">ดูสินค้าที่ขายดีที่สุด 10 อันดับ</p>
            </div>
        </div>
    </div>

    <!-- ตารางจัดอันดับ 10 อันดับสินค้าขายดี -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
            <h6 class="fw-bold text-success mb-0">
                <i class="fa-solid fa-ranking-star me-2 text-warning"></i>ตารางอันดับสินค้าขายดีประจำร้าน
            </h6>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 text-center" style="width: 90px;">อันดับ</th>
                            <th style="width: 80px;">รูปภาพ</th>
                            <th>ชื่อสินค้า</th>
                            <th class="text-end">ราคา/ชิ้น</th>
                            <th class="text-center">ยอดขาย (ชิ้น)</th>
                            <th class="text-end pe-4">รายได้รวม (บาท)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($top10_result && $top10_result->num_rows > 0): ?>
                            <?php 
                            $rank = 1;
                            while ($row = $top10_result->fetch_assoc()): 
                                // จัดการรูปภาพ
                                $raw_img = $row['image'] ?? '';
                                if (!empty($raw_img)) {
                                    if (strpos($raw_img, 'http://') === 0 || strpos($raw_img, 'https://') === 0) {
                                        $img_src = $raw_img;
                                    } else {
                                        $img_src = 'uploads/' . $raw_img;
                                    }
                                } else {
                                    $img_src = 'https://via.placeholder.com/80';
                                }

                                // ป้ายเหรียญอันดับ 1-3
                                $badge_html = '';
                                if ($rank === 1) {
                                    $badge_html = '<span class="badge bg-warning text-dark fs-6 rounded-circle p-2 shadow-sm"><i class="fa-solid fa-crown"></i> 1</span>';
                                } elseif ($rank === 2) {
                                    $badge_html = '<span class="badge bg-secondary text-white fs-6 rounded-circle p-2 shadow-sm"><i class="fa-solid fa-medal"></i> 2</span>';
                                } elseif ($rank === 3) {
                                    $badge_html = '<span class="badge bg-danger text-white fs-6 rounded-circle p-2 shadow-sm"><i class="fa-solid fa-award"></i> 3</span>';
                                } else {
                                    $badge_html = '<span class="fw-bold text-muted fs-6">#' . $rank . '</span>';
                                }
                            ?>
                                <tr>
                                    <td class="ps-4 text-center"><?= $badge_html ?></td>
                                    <td>
                                        <img src="<?= htmlspecialchars($img_src) ?>" alt="" class="rounded-3 border" style="width: 50px; height: 50px; object-fit: cover;">
                                    </td>
                                    <td>
                                        <a href="product_detail.php?id=<?= $row['product_id'] ?>" class="text-decoration-none fw-bold text-dark hover-success">
                                            <?= htmlspecialchars($row['name']) ?>
                                        </a>
                                    </td>
                                    <td class="text-end text-muted"><?= number_format($row['price'], 2) ?> ฿</td>
                                    <td class="text-center">
                                        <span class="badge bg-success-subtle text-success fs-6 px-3 rounded-pill fw-bold">
                                            <?= number_format($row['total_sold']) ?> ชิ้น
                                        </span>
                                    </td>
                                    <td class="text-end pe-4 fw-bold text-success fs-6">
                                        <?= number_format($row['total_revenue'], 2) ?> ฿
                                    </td>
                                </tr>
                            <?php 
                            $rank++;
                            endwhile; 
                            ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">ยังไม่มีข้อมูลยอดขายสินค้า</td>
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