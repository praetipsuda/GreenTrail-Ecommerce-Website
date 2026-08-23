<?php
require 'db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ล้างตะกร้าสินค้าทั้งหมด
if (isset($_GET['action']) && $_GET['action'] === 'clear') {
    unset($_SESSION['cart']);
    header("Location: cart.php");
    exit;
}

// ลบสินค้าเฉพาะชิ้น
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $delete_id = intval($_GET['id']);
    unset($_SESSION['cart'][$delete_id]);
    header("Location: cart.php");
    exit;
}

// อัปเดตจำนวนสินค้า
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart'])) {
    if (isset($_POST['qty']) && is_array($_POST['qty'])) {
        foreach ($_POST['qty'] as $p_id => $q) {
            $q = intval($q);
            if ($q <= 0) {
                unset($_SESSION['cart'][$p_id]);
            } else {
                $_SESSION['cart'][$p_id] = $q;
            }
        }
    }
    header("Location: cart.php");
    exit;
}

require 'header.php';

// ดึงข้อมูลสินค้าจากฐานข้อมูลตาม ID ที่มีใน Session Cart
$cart_items = [];
$total_price = 0;

if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    // ปรับรูปแบบข้อมูล Session ให้ปลอดภัย
    $clean_cart = [];
    foreach ($_SESSION['cart'] as $p_id => $val) {
        $p_id = intval($p_id);
        $qty = is_array($val) ? intval($val['qty'] ?? 1) : intval($val);
        if ($p_id > 0 && $qty > 0) {
            $clean_cart[$p_id] = $qty;
        }
    }
    $_SESSION['cart'] = $clean_cart;

    if (!empty($_SESSION['cart'])) {
        $ids = implode(',', array_map('intval', array_keys($_SESSION['cart'])));
        $sql = "SELECT * FROM products WHERE product_id IN ($ids)";
        $res = $conn->query($sql);

        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $p_id = $row['product_id'];
                $qty = $_SESSION['cart'][$p_id] ?? 1;
                $row['qty'] = $qty;
                $row['subtotal'] = $row['price'] * $qty;
                $total_price += $row['subtotal'];
                $cart_items[] = $row;
            }
        }
    }
}

function getImageUrl($img) {
    if (empty($img)) return 'https://via.placeholder.com/100?text=No+Image';
    if (strpos($img, 'http://') === 0 || strpos($img, 'https://') === 0) {
        return $img;
    }
    return 'uploads/' . $img;
}
?>

<div class="container py-4">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-success text-white text-center py-3 rounded-top-4">
            <h4 class="mb-0 fw-bold"><i class="fa-solid fa-cart-shopping me-2"></i>ตะกร้าสินค้า GreenTail</h4>
        </div>
        <div class="card-body p-4">
            <?php if (!empty($cart_items)): ?>
                <form method="post" action="cart.php">
                    <input type="hidden" name="update_cart" value="1">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="table-light text-center">
                                <tr>
                                    <th style="width: 40%;">สินค้า</th>
                                    <th>ราคา</th>
                                    <th style="width: 130px;">จำนวน</th>
                                    <th>รวม</th>
                                    <th>จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cart_items as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="<?= htmlspecialchars(getImageUrl($item['image'] ?? '')) ?>" class="rounded-3" style="width: 60px; height: 60px; object-fit: cover;">
                                                <div>
                                                    <div class="fw-bold"><?= htmlspecialchars($item['name']) ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center"><?= number_format($item['price'], 2) ?> ฿</td>
                                        <td class="text-center">
                                            <input type="number" name="qty[<?= $item['product_id'] ?>]" value="<?= $item['qty'] ?>" min="1" class="form-control text-center mx-auto" style="max-width: 80px;">
                                        </td>
                                        <td class="text-center fw-bold text-success"><?= number_format($item['subtotal'], 2) ?> ฿</td>
                                        <td class="text-center">
                                            <a href="cart.php?action=delete&id=<?= $item['product_id'] ?>" class="btn btn-sm btn-outline-danger rounded-circle" onclick="return confirm('ยืนยันการลบสินค้านี้?');">
                                                <i class="fa-solid fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <button type="submit" class="btn btn-outline-secondary btn-sm rounded-pill"><i class="fa-solid fa-rotate me-1"></i> อัปเดตจำนวน</button>
                        <a href="cart.php?action=clear" class="btn btn-outline-danger btn-sm rounded-pill" onclick="return confirm('ยืนยันการล้างตะกร้าทั้งหมด?');"><i class="fa-solid fa-trash-can me-1"></i> ล้างตะกร้า</a>
                    </div>
                </form>

                <hr class="my-4">

                <div class="text-end mb-4">
                    <h5 class="fw-bold">ยอดรวมทั้งหมด: <span class="text-success fs-3"><?= number_format($total_price, 2) ?> บาท</span></h5>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="index.php" class="btn btn-outline-success px-4 rounded-pill">กลับไปซื้อสินค้าต่อ</a>
                    <a href="checkout.php" class="btn btn-success px-4 rounded-pill fw-bold">ดำเนินการชำระเงิน</a>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fa-solid fa-basket-shopping text-muted fs-1 mb-3"></i>
                    <h5 class="text-muted">ไม่มีสินค้าในตะกร้า</h5>
                    <a href="index.php" class="btn btn-success rounded-pill px-4 mt-3">เลือกซื้อสินค้าเลย</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>