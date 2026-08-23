<?php
require 'db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ตรวจสอบสิทธิ์ Admin (ถ้าไม่ใช่ admin ให้เด้งกลับไปหน้า login)
if (($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: admin_login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เมนูผู้ดูแลระบบ GreenTail</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, sans-serif;
            min-height: 100vh;
        }
        .admin-header {
            background-color: #43a047;
            color: #ffffff;
            padding: 14px 0;
            text-align: center;
            font-size: 1.25rem;
            font-weight: 600;
        }
        .menu-card {
            background: #ffffff;
            border: none;
            border-radius: 12px;
            padding: 35px 15px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            text-decoration: none;
            color: #333;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
        }
        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            color: #333;
        }
        .menu-icon {
            font-size: 2.5rem;
            color: #43a047;
            margin-bottom: 20px;
        }
        .menu-title {
            font-weight: 700;
            font-size: 1rem;
            color: #2c3e50;
            margin-bottom: 8px;
        }
        .menu-subtitle {
            font-size: 0.8rem;
            color: #7f8c8d;
            margin: 0;
        }
        .btn-back-main {
            background-color: #ff4d4f;
            color: #ffffff;
            border-radius: 8px;
            padding: 10px 24px;
            font-weight: 600;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-back-main:hover {
            background-color: #ff2a2d;
            color: #ffffff;
        }
    </style>
</head>
<body>

<!-- แถบหัวข้อด้านบน -->
<div class="admin-header shadow-sm">
    <i class="fa-solid fa-gear me-2"></i>เมนูผู้ดูแลระบบ GreenTail
</div>

<div class="container py-5">
    <!-- เมนูการ์ด 5 ช่องเรียงแนวนอน -->
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-4 justify-content-center mb-5">
        
        <!-- 1. จัดการสินค้า -->
        <div class="col">
            <a href="manage_products.php" class="menu-card">
                <div class="menu-icon"><i class="fa-solid fa-box-archive"></i></div>
                <div class="menu-title">จัดการสินค้า</div>
                <p class="menu-subtitle">เพิ่ม แก้ไข หรือ ลบสินค้า</p>
            </a>
        </div>

        <!-- 2. จัดการลูกค้า -->
        <div class="col">
            <a href="customers.php" class="menu-card">
                <div class="menu-icon"><i class="fa-solid fa-user-group"></i></div>
                <div class="menu-title">จัดการลูกค้า</div>
                <p class="menu-subtitle">ดูและแก้ไขข้อมูลลูกค้า</p>
            </a>
        </div>

        <!-- 3. จัดการออเดอร์ -->
        <div class="col">
            <a href="orders.php" class="menu-card">
                <div class="menu-icon"><i class="fa-solid fa-cart-shopping"></i></div>
                <div class="menu-title">จัดการออเดอร์</div>
                <p class="menu-subtitle">ตรวจสอบและจัดการคำสั่งซื้อ</p>
            </a>
        </div>

        <!-- 4. รายได้และยอดขายรายเดือน -->
        <div class="col">
            <a href="monthly_report.php" class="menu-card">
                <div class="menu-icon"><i class="fa-solid fa-chart-line"></i></div>
                <div class="menu-title">รายได้และยอดขายรายเดือน</div>
                <p class="menu-subtitle">ดูรายได้ ยอดขาย และแนวโน้มประจำเดือน</p>
            </a>
        </div>

        <!-- 5. 10 อันดับสินค้าขายดี -->
        <div class="col">
            <a href="top10_bestsellers.php" class="menu-card">
                <div class="menu-icon"><i class="fa-solid fa-trophy"></i></div>
                <div class="menu-title">10 อันดับสินค้าขายดี</div>
                <p class="menu-subtitle">ดูสินค้าที่ขายดีที่สุด 10 อันดับ</p>
            </a>
        </div>

    </div>

    <!-- ปุ่มกลับไปหน้าหลัก -->
    <div class="text-center">
        <a href="index.php" class="btn-back-main">
            <i class="fa-solid fa-house"></i> กลับหน้าหลัก
        </a>
    </div>
</div>

</body>
</html>