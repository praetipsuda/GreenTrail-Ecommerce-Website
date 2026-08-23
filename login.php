<?php
require 'db.php';
session_start();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id']  = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['role'];
            header("Location: index.php");
            exit;
        }
    }
    $error = 'อีเมลหรือรหัสผ่านไม่ถูกต้อง';
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เข้าสู่ระบบ - GreenTail</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #5bb85d; display: flex; align-items: center; justify-content: center; min-height: 100vh; font-family: sans-serif; }
        .auth-card { background: #fff; border-radius: 12px; padding: 35px 30px; width: 100%; max-width: 400px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .btn-green { background-color: #43a047; color: white; width: 100%; border: none; padding: 10px; border-radius: 6px; font-weight: bold; }
        .btn-green:hover { background-color: #388e3c; color: white; }
    </style>
</head>
<body>
<div class="auth-card">
    <div class="text-center mb-4">
        <h4 class="fw-bold text-success"><i class="fa-solid fa-leaf me-2"></i>เข้าสู่ระบบ GreenTail</h4>
    </div>
    <?php if ($error): ?>
        <div class="alert alert-danger py-2 mb-3"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="input-group mb-3">
            <span class="input-group-text bg-light"><i class="fa-solid fa-envelope text-muted"></i></span>
            <input type="email" name="email" class="form-control" placeholder="อีเมล" required>
        </div>
        <div class="input-group mb-3">
            <span class="input-group-text bg-light"><i class="fa-solid fa-lock text-muted"></i></span>
            <input type="password" name="password" class="form-control" placeholder="รหัสผ่าน" required>
        </div>
        <button type="submit" class="btn btn-green mb-3">เข้าสู่ระบบ</button>
    </form>
    <div class="text-center small">
        <p class="mb-1">ยังไม่มีบัญชี? <a href="register.php" class="text-success text-decoration-none">สมัครสมาชิก</a></p>
        <a href="index.php" class="text-secondary text-decoration-none"><i class="fa-solid fa-house"></i> กลับหน้าแรก</a>
    </div>
</div>
</body>
</html>