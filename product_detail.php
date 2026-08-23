<?php
require 'db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id = intval($_GET['id'] ?? 0);

// ระบบประมวลผลเมื่อกดปุ่ม ใส่ตะกร้า หรือ ซื้อเลย
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $qty = intval($_POST['quantity'] ?? 1);
    if ($qty < 1) $qty = 1;

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id] += $qty;
    } else {
        $_SESSION['cart'][$id] = $qty;
    }

    if ($_POST['action'] === 'buy_now') {
        header("Location: cart.php");
    } else {
        header("Location: product_detail.php?id=" . $id . "&added=1");
    }
    exit;
}

require 'header.php';

$stmt = $conn->prepare("SELECT p.*, c.category_name FROM products p LEFT JOIN categories c ON p.category_id = c.category_id WHERE p.product_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    header("Location: index.php");
    exit;
}

// ฟังก์ชันสำหรับเช็กว่ารูปเป็น URL หรือไฟล์ในเครื่อง
function getImageUrl($img) {
    if (empty($img)) return 'https://via.placeholder.com/400?text=No+Image';
    if (strpos($img, 'http://') === 0 || strpos($img, 'https://') === 0) {
        return $img;
    }
    return 'uploads/' . $img;
}

$main_img = getImageUrl($product['image'] ?? '');
$detail_img = !empty($product['detail_image']) ? getImageUrl($product['detail_image']) : '';
?>

<div class="container py-4">

    <!-- การแจ้งเตือนเมื่อกดใส่ตะกร้าสำเร็จ -->
    <?php if (isset($_GET['added'])): ?>
        <div class="alert alert-success alert-dismissible fade show mb-3 rounded-3" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> เพิ่มสินค้าลงตะกร้าเรียบร้อยแล้ว!
            <a href="cart.php" class="alert-link ms-2">ดูตะกร้าสินค้า</a>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <a href="index.php" class="btn btn-success btn-sm mb-3"><i class="fa-solid fa-arrow-left me-1"></i>กลับหน้าหลัก</a>

    <div class="card border-0 shadow-sm rounded-4 p-3 mb-4">
        <div class="row g-4 align-items-center">
            <!-- รูปภาพสินค้าหลัก -->
            <div class="col-md-6 text-center">
                <img src="<?= htmlspecialchars($main_img) ?>" class="img-fluid rounded-3 style-main-img" style="max-height: 400px; object-fit: contain;" alt="<?= htmlspecialchars($product['name']) ?>">
            </div>

            <!-- ข้อมูลสินค้า -->
            <div class="col-md-6">
                <h3 class="fw-bold"><?= htmlspecialchars($product['name']) ?></h3>
                <h4 class="text-success fw-bold my-3"><?= number_format($product['price'], 2) ?> บาท</h4>
                
                <p class="text-muted mb-2"><strong>หมวดหมู่:</strong> <?= htmlspecialchars($product['category_name'] ?? 'ทั่วไป') ?></p>
                <?php if (!empty($product['colors'])): ?>
                    <p class="text-muted mb-2"><strong>สีที่มี:</strong> <?= htmlspecialchars($product['colors']) ?></p>
                <?php endif; ?>
                <p class="text-secondary"><?= nl2br(htmlspecialchars($product['description'] ?? '')) ?></p>

                <!-- ฟอร์มปุ่มกดสั่งซื้อ -->
                <form method="post" class="mt-4">
                    <input type="hidden" name="quantity" value="1">
                    <div class="d-flex gap-2">
                        <button type="submit" name="action" value="add_to_cart" class="btn btn-success px-4 flex-grow-1 py-2 fw-bold">
                            <i class="fa-solid fa-cart-shopping me-2"></i>ใส่ตะกร้า
                        </button>
                        <button type="submit" name="action" value="buy_now" class="btn btn-warning text-white px-4 flex-grow-1 py-2 fw-bold" style="background-color: #ff6f00; border:none;">
                            <i class="fa-solid fa-bolt me-2"></i>ซื้อเลย
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- แสดงรูปรายละเอียดสินค้าเพิ่มเติม (ถ้ามี) -->
    <?php if ($detail_img): ?>
        <div class="card border-0 shadow-sm rounded-4 p-4 text-center">
            <h5 class="fw-bold text-success mb-3 text-start"><i class="fa-solid fa-circle-info me-2"></i>รายละเอียดเพิ่มเติม</h5>
            <img src="<?= htmlspecialchars($detail_img) ?>" class="img-fluid rounded-3 mx-auto" style="max-width: 100%; height: auto;" alt="รายละเอียดสินค้า">
        </div>
    <?php endif; ?>
</div>

</body>
</html>