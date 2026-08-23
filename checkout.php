<?php
require 'db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ตรวจสอบและดึงข้อมูลสินค้าจากตะกร้า
$cart_items = [];
$total_price = 0;

if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
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

// ถ้าไม่มีสินค้าในตะกร้า ให้ส่งกลับหน้า cart.php
if (empty($cart_items)) {
    header("Location: cart.php");
    exit;
}

// ประมวลผลเมื่อกดสั่งซื้อ
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $address  = trim($_POST['address'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $payment  = trim($_POST['payment_method'] ?? 'เก็บเงินปลายทาง');
    $user_id  = $_SESSION['user_id'] ?? 0;

    if (!empty($fullname) && !empty($address) && !empty($phone)) {
        // บันทึกคำสั่งซื้อลงตาราง orders
        $stmt = $conn->prepare("INSERT INTO orders (user_id, fullname, address, phone, total_price, payment_method, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())");
        if ($stmt) {
            $stmt->bind_param("isssds", $user_id, $fullname, $address, $phone, $total_price, $payment);
            if ($stmt->execute()) {
                $order_id = $stmt->insert_id ? $stmt->insert_id : $conn->insert_id;

                // บันทึกรายการสินค้าลงตาราง order_items
                if ($order_id > 0) {
                    $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, color, quantity, price) VALUES (?, ?, ?, ?, ?)");
                    if ($item_stmt) {
                        foreach ($cart_items as $item) {
                            $p_id  = intval($item['product_id']);
                            $color = $item['color'] ?? '';
                            $qty   = intval($item['qty']);
                            $price = floatval($item['price']);

                            $item_stmt->bind_param("iisid", $order_id, $p_id, $color, $qty, $price);
                            $item_stmt->execute();
                        }
                        $item_stmt->close();
                    }
                }
            }
            $stmt->close();
        }

        // ล้างตะกร้าสินค้าหลังจากสั่งซื้อสำเร็จ
        unset($_SESSION['cart']);
        echo "<script>alert('สั่งซื้อสินค้าเรียบร้อยแล้ว!'); window.location.href='my_orders.php';</script>";
        exit;
    } else {
        $msg = "กรุณากรอกข้อมูลจัดส่งให้ครบถ้วน";
    }
}

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
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    
                    <h4 class="fw-bold text-success text-center mb-4">ตรวจสอบสินค้าก่อนสั่งซื้อ</h4>

                    <?php if ($msg): ?>
                        <div class="alert alert-danger rounded-3 mb-3"><?= htmlspecialchars($msg) ?></div>
                    <?php endif; ?>

                    <!-- สรุปรายการสินค้า -->
                    <div class="table-responsive mb-3">
                        <table class="table align-middle text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>รูป</th>
                                    <th class="text-start">สินค้า</th>
                                    <th>ราคา/ชิ้น</th>
                                    <th>จำนวน</th>
                                    <th>รวม</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cart_items as $item): ?>
                                    <tr>
                                        <td>
                                            <img src="<?= htmlspecialchars(getImageUrl($item['image'] ?? '')) ?>" class="rounded-3" style="width: 50px; height: 50px; object-fit: cover;">
                                        </td>
                                        <td class="text-start fw-bold"><?= htmlspecialchars($item['name']) ?></td>
                                        <td><?= number_format($item['price'], 2) ?></td>
                                        <td><?= $item['qty'] ?></td>
                                        <td class="fw-bold text-success"><?= number_format($item['subtotal'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="text-end mb-4">
                        <h5 class="fw-bold">ราคารวมทั้งหมด: <span class="text-success fs-4"><?= number_format($total_price, 2) ?> บาท</span></h5>
                    </div>

                    <hr class="my-4">

                    <!-- ฟอร์มกรอกข้อมูลจัดส่ง -->
                    <form method="post" action="checkout.php">
                        <div class="mb-3">
                            <label class="form-label fw-bold">ชื่อผู้สั่งซื้อ:</label>
                            <input type="text" name="fullname" class="form-control rounded-3" value="<?= htmlspecialchars($_SESSION['username'] ?? 'prae') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">ที่อยู่จัดส่ง:</label>
                            <textarea name="address" class="form-control rounded-3" rows="3" required>หอพักเรือนเพ็ญ 803 มหาสารคาม</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">เบอร์โทรศัพท์:</label>
                            <input type="text" name="phone" class="form-control rounded-3" value="0654799814" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">วิธีชำระเงิน:</label>
                            <select name="payment_method" id="payment_method" class="form-select rounded-3" onchange="toggleBankSection()">
                                <option value="เก็บเงินปลายทาง">เก็บเงินปลายทาง</option>
                                <option value="โอนเงินผ่านธนาคาร">โอนเงินผ่านธนาคาร</option>
                            </select>
                        </div>

                        <!-- ================= ส่วนแสดงบัญชีธนาคารและปุ่มคัดลอก (ซ่อน/แสดง อัตโนมัติ) ================= -->
                        <div id="bank_section" class="card p-3 mb-4 bg-light border-0 rounded-3 text-start" style="display: none;">
                            <label class="form-label fw-bold text-success mb-2">
                                <i class="fa-solid fa-building-columns me-1"></i>บัญชีสำหรับโอนเงิน:
                            </label>
                            
                            <div class="card p-3 bg-white border-0 shadow-sm rounded-3">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                    <div>
                                        <strong style="color: #138f2d;" class="fs-6">
                                            <i class="fa-solid fa-credit-card me-1"></i>ธนาคารกสิกรไทย (KBANK)
                                        </strong>
                                        <div class="mt-1">
                                            <span class="text-muted small">เลขบัญชี:</span>
                                            <span id="bank_acc_num" class="fw-bold text-dark fs-5 ms-1">123-4-56789-0</span>
                                        </div>
                                        <small class="text-muted">ชื่อบัญชี: บจก. กรีนเทล (GreenTail)</small>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-outline-success btn-sm rounded-pill px-3 py-2 fw-bold" onclick="copyAccountNumber()">
                                            <i class="fa-regular fa-copy me-1"></i> คัดลอกเลขบัญชี
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success w-100 py-2 rounded-3 fw-bold">ยืนยันสั่งซื้อ</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleBankSection() {
    const paymentMethod = document.getElementById('payment_method').value;
    const bankSection = document.getElementById('bank_section');
    
    if (paymentMethod === 'โอนเงินผ่านธนาคาร') {
        bankSection.style.display = 'block';
    } else {
        bankSection.style.display = 'none';
    }
}

function copyAccountNumber() {
    const rawAccNum = '1234567890'; // เลขบัญชีสำหรับคัดลอก (ไม่มีขีด)
    navigator.clipboard.writeText(rawAccNum).then(() => {
        alert('คัดลอกหมายเลขบัญชีเรียบร้อยแล้ว!');
    }).catch(err => {
        alert('คัดลอกไม่สำเร็จ กรุณาคัดลอกด้วยตนเอง');
    });
}

// ตรวจสอบสถานะการเลือกเมื่อโหลดหน้า
document.addEventListener('DOMContentLoaded', toggleBankSection);
</script>

</body>
</html>