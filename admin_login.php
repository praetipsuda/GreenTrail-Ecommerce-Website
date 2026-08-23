<?php
require 'db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// หากล็อกอินเป็น Admin อยู่แล้ว ให้ไปหน้าจัดการแอดมินทันที
if (($_SESSION['role'] ?? '') === 'admin') {
    header("Location: admin.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($username) && !empty($password)) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        // ตรวจสอบรหัสผ่านถูกต้องหรือไม่
        if ($user && password_verify($password, $user['password'])) {
            // เช็กสิทธิ์ว่าต้องเป็น 'admin' เท่านั้น
            if ($user['role'] === 'admin') {
                $_SESSION['user_id']  = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role']     = $user['role'];

                header("Location: admin.php");
                exit;
            } else {
                $error = 'บัญชีนี้ไม่มีสิทธิ์เข้าใช้งาน (สำหรับผู้ดูแลระบบเท่านั้น)';
            }
        } else {
            $error = 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง';
        }
    } else {
        $error = 'กรุณากรอกข้อมูลให้ครบถ้วน';
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบแอดมิน - GreenTail</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #eaf5eb; font-family: 'Segoe UI', Tahoma, sans-serif; }
        .admin-card { border: none; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); overflow: hidden; }
        .admin-header { background-color: #2e7d32; color: #ffffff; padding: 25px 20px; text-align: center; }
    </style>
</head>
<body class="d-flex align-items-center vh-100">

<div class="container">
    <div class="card admin-card mx-auto" style="max-width: 420px;">
        <div class="admin-header">
            <i class="fa-solid fa-user-shield fs-1 mb-2"></i>
            <h4 class="fw-bold mb-0">เข้าสู่ระบบแอดมิน</h4>
            <small class="text-white-50">สำหรับผู้ดูแลระบบ GreenTail เท่านั้น</small>
        </div>
        <div class="card-body p-4">

            <?php if ($error): ?>
                <div class="alert alert-danger py-2 small text-center mb-3">
                    <i class="fa-solid fa-triangle-exclamation me-1"></i><?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="post">
                <div class="mb-3">
                    <label class="form-label fw-bold small text-secondary">ชื่อผู้ใช้แอดมิน (Username)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-user text-muted"></i></span>
                        <input type="text" name="username" class="form-control border-start-0 bg-light" required placeholder="กรอกชื่อผู้ใช้แอดมิน">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold small text-secondary">รหัสผ่าน (Password)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-lock text-muted"></i></span>
                        <input type="password" name="password" class="form-control border-start-0 bg-light" required placeholder="กรอกรหัสผ่าน">
                    </div>
                </div>

                <button type="submit" class="btn btn-success w-100 py-2 fw-bold mb-3" style="background-color: #2e7d32; border: none;">
                    <i class="fa-solid fa-right-to-bracket me-2"></i>เข้าสู่ระบบหลังบ้าน
                </button>
                
                <div class="text-center">
                    <a href="index.php" class="text-muted small text-decoration-none">
                        <i class="fa-solid fa-arrow-left me-1"></i>กลับสู่หน้าหลักร้านค้า
                    </a>
                </div>
            </form>

        </div>
    </div>
</div>

</body>
</html>