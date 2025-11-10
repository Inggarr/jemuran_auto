<?php
if (session_status()===PHP_SESSION_NONE) session_start();
$user = $_SESSION['user'] ?? null;
$BASE = "/jemuran_auto/";

$defaultFoto = $BASE."assets/uploads/profile/default.png";
$fotoUrl = ($user && !empty($user['foto']) && strpos($user['foto'],'assets/')===0)
  ? $BASE.$user['foto'] : $defaultFoto;
$profileUrl = $BASE.(($user['role']??'user')==='admin'
  ? "frontend/admin/profile_admin.php"
  : "frontend/user/profile_user.php");
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Jemuran Auto</title>
  <style>
    body{margin:0;font-family:Arial,Helvetica,sans-serif;background:#f5f5f5;color:#222}
    .topbar{background:#3498db;color:#fff;display:flex;align-items:center;justify-content:space-between;padding:10px 20px}
    #menuBtn{background:none;border:none;color:#fff;font-size:24px;cursor:pointer}
    .avatar{width:40px;height:40px;border-radius:50%;object-fit:cover;border:2px solid #10b981}
  </style>
</head>
<body>
<header class="topbar">
  <div style="display:flex;align-items:center;gap:10px">
    <button id="menuBtn">☰</button>
    <strong style="font-size:20px;display:flex;align-items:center;gap:6px">🧺 Jemuran Auto</strong>
  </div>
  <?php if($user): ?>
    <a href="<?= htmlspecialchars($profileUrl) ?>" style="display:flex;align-items:center;gap:10px;color:#fff;text-decoration:none">
      <div style="text-align:right;line-height:1.1">
        <div><?= htmlspecialchars($user['nama']) ?></div>
        <div style="font-size:12px;opacity:.85"><?= htmlspecialchars($user['role']) ?></div>
      </div>
      <img class="avatar" src="<?= htmlspecialchars($fotoUrl) ?>" alt="Foto profil">
    </a>
  <?php endif; ?>
</header>

<div class="layout" style="display:flex;min-height:calc(100vh - 60px);">
