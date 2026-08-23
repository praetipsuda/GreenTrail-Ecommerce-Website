<?php
require 'db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_id DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders_result = $stmt->get_result();

require 'header.php';

function getImageUrl($img) {
    if (empty($img)) return 'https://via.placeholder.com/100?text=No+Image';
    if (strpos($img, 'http://') === 0 || strpos($img, 'https://') === 0) {
        return $img;
    }
    return 'uploads/' . $img;
}

function renderStatusBadge($status) {
    switch (strtolower($status)) {
        case 'shipping':
        case 'กำลังจัดส่ง':
            return '<span class="badge bg-info text-dark rounded-pill px-3 py-2 fs-6"><i class="fa-solid fa-truck-fast me-1"></i> กำลังจัดส่ง</span>';
        case 'completed':
        case 'จัดส่งสำเร็จ':
            return '<span class="badge bg-success rounded-pill px-3 py-2 fs-6"><i class="fa-solid fa-circle-check me-1"></i> จัดส่งสำเร็จ</span>';
        case 'cancelled':
        case 'ยกเลิก':
            return '<span class="badge bg-danger rounded-pill px-3 py-2 fs-6"><i class="fa-solid fa-circle-xmark me-1"></i> ยกเลิก</span>';
        default:
            return '<span class="badge bg-warning text-dark rounded-pill px-3 py-2 fs-6"><i class="fa-solid fa-clock me-1"></i> รอดำเนินการ</span>';
    }
}
?>

<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="fw-bold text-success mb-0"><i class="fa-solid fa-receipt me-2"></i>คำสั่งซื้อของฉัน</h4>
        <a href="index.php" class="btn btn-outline-success btn-sm rounded-pill"><i class="fa-solid fa-arrow-left me-1"></i> ช้อปปิ้งต่อ</a>
    </div>

    <?php if ($orders_result && $orders_result->num_rows > 0): ?>
        <div class="row g-4">
            <?php while ($order = $orders_result->fetch_assoc()): ?>
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        
                        <div class="card-header bg-white py-3 px-4 d-flex flex-wrap align-items-center justify-content-between border-bottom">
                            <div>
                                <span class="fw-bold text-secondary me-3">หมายเลขคำสั่งซื้อ #<?= $order['order_id'] ?></span>
                                <small class="text-muted"><i class="fa-regular fa-calendar me-1"></i><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></small>
                            </div>
                            <div>
                                <?= renderStatusBadge($order['status']) ?>
                            </div>
                        </div>

                        <div class="card-body p-4">
                            <div class="table-responsive mb-3">
                                <table class="table align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 50%;">สินค้า</th>
                                            <th class="text-center">ราคา/ชิ้น</th>
                                            <th class="text-center">จำนวน</th>
                                            <th class="text-end">รวม</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $item_stmt = $conn->prepare("SELECT oi.*, p.name AS product_name, p.image AS product_image FROM order_items oi LEFT JOIN products p ON oi.product_id = p.product_id WHERE oi.order_id = ?");
                                        $item_stmt->bind_param("i", $order['order_id']);
                                        $item_stmt->execute();
                                        $items_result = $item_stmt->get_result();

                                        if ($items_result && $items_result->num_rows > 0):
                                            while ($item = $items_result->fetch_assoc()):
                                                $img_file  = !empty($item['product_image']) ? $item['product_image'] : ($item['image'] ?? '');
                                                $item_name = !empty($item['product_name']) ? $item['product_name'] : ($item['name'] ?? 'สินค้า');
                                                $subtotal  = $item['price'] * $item['quantity'];
                                        ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-3">
                                                            <img src="<?= htmlspecialchars(getImageUrl($img_file)) ?>" 
                                                                 alt="<?= htmlspecialchars($item_name) ?>" 
                                                                 class="rounded-3 border" 
                                                                 style="width: 55px; height: 55px; object-fit: cover;"
                                                                 onerror="this.onerror=null; this.src='https://via.placeholder.com/100?text=No+Image';">
                                                            <span class="fw-bold"><?= htmlspecialchars($item_name) ?></span>
                                                        </div>
                                                    </td>
                                                    <td class="text-center"><?= number_format($item['price'], 2) ?> ฿</td>
                                                    <td class="text-center"><?= $item['quantity'] ?></td>
                                                    <td class="text-end fw-bold text-success"><?= number_format($subtotal, 2) ?> ฿</td>
                                                </tr>
                                        <?php 
                                            endwhile;
                                        else: 
                                        ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-3">ไม่มีรายการสินค้าในคำสั่งซื้อนี้</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                            <hr>

                            <div class="row align-items-center">
                                <div class="col-md-7 mb-3 mb-md-0">
                                    <p class="mb-1 text-muted small"><strong>ผู้รับ:</strong> <?= htmlspecialchars($order['fullname']) ?> (<?= htmlspecialchars($order['phone']) ?>)</p>
                                    <p class="mb-1 text-muted small"><strong>ที่อยู่จัดส่ง:</strong> <?= htmlspecialchars($order['address']) ?></p>
                                    <p class="mb-0 text-muted small"><strong>การชำระเงิน:</strong> <?= htmlspecialchars($order['payment_method']) ?></p>
                                </div>
                                <div class="col-md-5 text-md-end">
                                    <span class="text-muted me-2">ยอดรวมสุทธิ:</span>
                                    <span class="fs-4 fw-bold text-success"><?= number_format($order['total_price'], 2) ?> บาท</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="card border-0 shadow-sm rounded-4 text-center py-5">
            <div class="card-body">
                <i class="fa-solid fa-box-open text-muted fs-1 mb-3"></i>
                <h5 class="text-muted">ยังไม่มีรายการสั่งซื้อ</h5>
                <a href="index.php" class="btn btn-success rounded-pill px-4 mt-3">เริ่มสั่งซื้อสินค้า</a>
            </div>
        </div>
    <?php endif; ?>
</div>

</body>
</html>