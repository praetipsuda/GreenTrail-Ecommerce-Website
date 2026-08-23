<?php
ob_start();
require 'db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$msg = "";
$msg_type = "";

// ระบบอัปเดตสถานะคำสั่งซื้อ (ส่งค่าภาษาอังกฤษตรงกับ Database)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = intval($_POST['order_id']);
    $status   = trim($_POST['status']);

    if ($order_id > 0 && !empty($status)) {
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
        if ($stmt) {
            $stmt->bind_param("si", $status, $order_id);
            if ($stmt->execute()) {
                $msg = "อัปเดตสถานะคำสั่งซื้อ #$order_id สำเร็จแล้ว!";
                $msg_type = "success";
            } else {
                $msg = "เกิดข้อผิดพลาด: " . $stmt->error;
                $msg_type = "danger";
            }
            $stmt->close();
        }
    }
}

// ระบบลบคำสั่งซื้อ
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    
    $del_items = $conn->prepare("DELETE FROM order_items WHERE order_id = ?");
    if ($del_items) {
        $del_items->bind_param("i", $delete_id);
        $del_items->execute();
        $del_items->close();
    }
    
    $del_order = $conn->prepare("DELETE FROM orders WHERE order_id = ?");
    if ($del_order) {
        $del_order->bind_param("i", $delete_id);
        $del_order->execute();
        $del_order->close();
    }

    header("Location: orders.php");
    exit;
}

// ดึงรายการคำสั่งซื้อทั้งหมด
$sql = "SELECT * FROM orders ORDER BY order_id DESC";
$orders_result = $conn->query($sql);

require 'header.php';

function getImageUrl($img) {
    if (empty($img)) return 'https://via.placeholder.com/100?text=No+Image';
    if (strpos($img, 'http://') === 0 || strpos($img, 'https://') === 0) {
        return $img;
    }
    return 'uploads/' . $img;
}
?>

<div class="container py-4">
    <?php if (!empty($msg)): ?>
        <div class="alert alert-<?= $msg_type ?> alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i><?= htmlspecialchars($msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="fw-bold text-success mb-0">
            <i class="fa-solid fa-boxes-packing me-2"></i>ระบบจัดการคำสั่งซื้อ (Admin)
        </h4>
        <a href="index.php" class="btn btn-outline-success btn-sm rounded-pill">
            <i class="fa-solid fa-store me-1"></i> หน้าร้านค้า
        </a>
    </div>

    <?php if ($orders_result && $orders_result->num_rows > 0): ?>
        <div class="row g-4">
            <?php while ($order = $orders_result->fetch_assoc()): ?>
                <?php $curr_status = trim($order['status'] ?? ''); ?>
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        
                        <!-- Header คำสั่งซื้อ -->
                        <div class="card-header bg-white py-3 px-4 d-flex flex-wrap align-items-center justify-content-between border-bottom">
                            <div class="mb-2 mb-md-0">
                                <span class="fw-bold text-secondary me-3">คำสั่งซื้อ #<?= $order['order_id'] ?></span>
                                <small class="text-muted"><i class="fa-regular fa-calendar me-1"></i><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></small>
                            </div>
                            
                            <div class="d-flex align-items-center gap-2">
                                <!-- ฟอร์มเปลี่ยนสถานะ -->
                                <form method="post" action="orders.php" class="d-flex align-items-center gap-2 m-0">
                                    <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                                    <span class="small text-muted d-none d-sm-inline">สถานะ:</span>
                                    
                                    <select name="status" class="form-select form-select-sm rounded-pill fw-bold border-success" style="width: auto;">
                                        <option value="pending" <?= ($curr_status === 'pending') ? 'selected' : '' ?>>⏳ รอดำเนินการ</option>
                                        <option value="shipping" <?= ($curr_status === 'shipping') ? 'selected' : '' ?>>🚚 กำลังจัดส่ง</option>
                                        <option value="completed" <?= ($curr_status === 'completed') ? 'selected' : '' ?>>✅ จัดส่งสำเร็จ</option>
                                        <option value="cancelled" <?= ($curr_status === 'cancelled') ? 'selected' : '' ?>>❌ ยกเลิก</option>
                                    </select>
                                    
                                    <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 fw-bold">
                                        บันทึก
                                    </button>
                                </form>

                                <!-- ปุ่มลบคำสั่งซื้อ -->
                                <a href="orders.php?delete_id=<?= $order['order_id'] ?>" 
                                   class="btn btn-outline-danger btn-sm rounded-circle px-2 py-1 ms-2" 
                                   onclick="return confirm('ยืนยันที่จะลบคำสั่งซื้อ #<?= $order['order_id'] ?> หรือไม่?');"
                                   title="ลบคำสั่งซื้อ">
                                    <i class="fa-solid fa-trash-can"></i>
                                </a>
                            </div>
                        </div>

                        <!-- รายละเอียดสินค้าในคำสั่งซื้อ -->
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
                                                                 style="width: 50px; height: 50px; object-fit: cover;"
                                                                 onerror="this.onerror=null; this.src='https://via.placeholder.com/100?text=No+Image';">
                                                            <div>
                                                                <span class="fw-bold d-block"><?= htmlspecialchars($item_name) ?></span>
                                                                <?php if (!empty($item['color'])): ?>
                                                                    <small class="text-muted">ตัวเลือก: <?= htmlspecialchars($item['color']) ?></small>
                                                                <?php endif; ?>
                                                            </div>
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

                            <!-- ข้อมูลลูกค้าและการชำระเงิน -->
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
                <h5 class="text-muted">ยังไม่มีรายการสั่งซื้อในระบบ</h5>
            </div>
        </div>
    <?php endif; ?>
</div>

</body>
</html>