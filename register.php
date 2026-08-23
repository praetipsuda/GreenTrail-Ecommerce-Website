<?php
require 'db.php';
session_start();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];
    $phone    = trim($_POST['phone']);
    $address  = trim($_POST['address']);

    if ($password !== $confirm) {
        $error = 'รหัสผ่านทั้งสองช่องไม่ตรงกัน';
    } else {
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $error = 'อีเมลนี้ถูกใช้งานแล้ว';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, phone, address) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $username, $email, $hashed, $phone, $address);
            if ($stmt->execute()) {
                header("Location: login.php");
                exit;
            } else {
                $error = 'เกิดข้อผิดพลาดในการลงทะเบียน';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>สมัครสมาชิก - GreenTail</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #5bb85d; display: flex; align-items: center; justify-content: center; min-height: 100vh; font-family: sans-serif; }
        .auth-card { background: #fff; border-radius: 12px; padding: 30px; width: 100%; max-width: 420px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .btn-green { background-color: #43a047; color: white; width: 100%; border: none; padding: 10px; border-radius: 6px; font-weight: bold; }
        .btn-green:hover { background-color: #388e3c; color: white; }
    </style>
</head>
<body>
<div class="auth-card">
    <div class="text-center mb-3">
        <h4 class="fw-bold text-success"><i class="fa-solid fa-user-plus me-2"></i>สมัครสมาชิก GreenTail</h4>
    </div>
    <?php if ($error): ?>
        <div class="alert alert-danger py-2 mb-3"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="mb-2"><input type="text" name="username" class="form-control" placeholder="ชื่อผู้ใช้" required></div>
        <div class="mb-2"><input type="email" name="email" class="form-control" placeholder="อีเมล" required></div>
        <div class="mb-2"><input type="password" name="password" class="form-control" placeholder="รหัสผ่าน" required></div>
        <div class="mb-2"><input type="password" name="confirm_password" class="form-control" placeholder="ยืนยันรหัสผ่าน" required></div>
        <div class="mb-2"><input type="text" name="phone" class="form-control" placeholder="เบอร์โทร"></div>
        <div class="mb-3"><textarea name="address" class="form-control" rows="2" placeholder="ที่อยู่"></textarea></div>
        <button type="submit" class="btn btn-green">สมัครสมาชิก</button>
    </form>
    <div class="text-center mt-3 small">
        <p class="mb-1">มีบัญชีอยู่แล้ว? <a href="login.php" class="text-success text-decoration-none">เข้าสู่ระบบ</a></p>
        <a href="index.php" class="text-secondary text-decoration-none"><i class="fa-solid fa-house"></i> กลับหน้าแรก</a>
    </div>
</div>
</body>
</html>