<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ล้างข้อมูล Session ทั้งหมด
$_SESSION = array();

// ลบ Cookie ของ Session
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// ทำลาย Session
session_destroy();

// ส่งผู้ใช้งานกลับไปหน้าหลัก
header("Location: index.php");
exit;
?>