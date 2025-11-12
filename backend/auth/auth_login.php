<?php
if (session_status()===PHP_SESSION_NONE) session_start();
require_once __DIR__.'/../config/database.php';

// cegah output nyasar
ob_start();

$email = trim($_POST['email'] ?? '');
$pass  = $_POST['password'] ?? '';

$stmt = $conn->prepare("SELECT id,nama,email,password_hash,role,foto_profile FROM users WHERE email=? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();

if (!$user || !password_verify($pass, $user['password_hash'])) {
    // simpan flash message lalu balik ke login
    $_SESSION['flash'] = "Email atau password salah.";
    header("Location: /jemuran_auto/frontend/auth/login.php");
    exit;
}

// set session user
$_SESSION['user'] = [
  'id'   => (int)$user['id'],
  'nama' => $user['nama'],
  'email'=> $user['email'],
  'role' => $user['role'],
  'foto' => $user['foto_profile'] ?? null
];

// redirect sesuai role
if ($user['role'] === 'admin') {
    header("Location: /jemuran_auto/frontend/admin/dashboard_admin.php");
} else {
    header("Location: /jemuran_auto/frontend/user/dashboard_user.php");
}
exit;
f3
