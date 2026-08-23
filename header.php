<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GreenTail - อุปกรณ์เดินป่า Outdoor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #eaf5eb; font-family: 'Segoe UI', Tahoma, sans-serif; color: #333; }
        .navbar { background-color: #ffffff; box-shadow: 0 2px 10px rgba(0,0,0,0.04); }
        .navbar-brand { font-size: 1.4rem; font-weight: bold; color: #2e7d32 !important; }
        .nav-link { color: #2e7d32 !important; font-weight: 600; margin-right: 15px; }
        .nav-link:hover { color: #1b5e20 !important; }
        .btn-green { background-color: #43a047; color: white; border: none; border-radius: 6px; font-weight: bold; }
        .btn-green:hover { background-color: #388e3c; color: white; }
        .btn-orange { background-color: #ff7043; color: white; border: none; border-radius: 6px; font-weight: bold; }
        .btn-orange:hover { background-color: #f4511e; color: white; }
        .product-card { border: none; border-radius: 12px; background: #fff; transition: transform 0.2s, box-shadow 0.2s; box-shadow: 0 2px 8px rgba(0,0,0,0.03); }
        .product-card:hover { transform: translateY(-4px); box-shadow: 0 6px 15px rgba(0,0,0,0.08); }
        .product-img { height: 160px; object-fit: contain; padding: 10px; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <i class="fa-solid fa-leaf me-2 text-success"></i>GreenTail
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">หน้าแรก</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php#products">สินค้าทั้งหมด</a></li>
            </ul>
            <div class="d-flex align-items-center gap-3">
                <a href="cart.php" class="btn btn-outline-success position-relative rounded-pill px-3">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        <?php 
                            $cart_count = 0;
                            if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
                                foreach ($_SESSION['cart'] as $item) {
                                    if (is_array($item)) {
                                        $cart_count += intval($item['qty'] ?? 1);
                                    } else {
                                        $cart_count += intval($item);
                                    }
                                }
                            }
                            echo $cart_count;
                        ?>
                    </span>
                </a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="my_orders.php" class="btn btn-sm btn-outline-success rounded-pill px-3">
                        <i class="fa-solid fa-box-archive me-1"></i> คำสั่งซื้อของฉัน
                    </a>
                    <span class="fw-bold text-success"><i class="fa-solid fa-user me-1"></i><?= htmlspecialchars($_SESSION['username']) ?></span>
                    <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                        <a href="admin.php" class="btn btn-sm btn-warning fw-bold">Admin</a>
                    <?php endif; ?>
                    <a href="logout.php" class="btn btn-sm btn-outline-danger">ออกจากระบบ</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-sm btn-success">เข้าสู่ระบบ</a>
                    <a href="register.php" class="btn btn-sm btn-outline-success">สมัครสมาชิก</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>