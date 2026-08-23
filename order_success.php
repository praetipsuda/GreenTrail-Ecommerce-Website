<?php
require 'db.php';
require 'header.php';

$order_id = (int) ($_GET['id'] ?? 0);
?>

<div class="container py-5 text-center">
    <div class="card border-0 shadow-sm mx-auto p-4" style="max-width: 500px; border-radius: 16px;">
        <div class="text-success display-1 mb-3"><i class="fa-solid fa-circle-check"></i></div>
        <h3 class="fw-bold text-success mb-2">สั่งซื้อสินค้าสำเร็จ!</h3>
        <p class="text-muted mb-4">หมายเลขคำสั่งซื้อของคุณคือ <strong>#<?= sprintf('%05d', $order_id) ?></strong></p>
        <p class="text-secondary small mb-4">ทางร้านจะดำเนินการจัดส่งสินค้าโดยเร็วที่สุด ขอบคุณที่ใช้นบริการ GreenTail ครับ</p>
        <div>
            <a href="index.php" class="btn btn-green px-4 py-2">กลับสู่หน้าหลัก</a>
        </div>
    </div>
</div>

</body>
</html>