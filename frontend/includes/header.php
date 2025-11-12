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
  <title>Smart Clothesline</title>

  <!-- ====== HEADER STYLE (logo di atas warna sidebar) ====== -->
  <style>
    :root{
      --ink:#2b1842;        /* teks ungu tua */
      --vio:#6478f5;        /* ikon ☰ */
      --border:#efeaff;
      --chip:#e8ddff;
      --bell:#ffb900;
      --sb-bg:#c8c1ff;      /* warna sidebar */
    }

    html,body{margin:0}
    body{
      font-family:Inter,Arial,Helvetica,sans-serif;
      background:#f7f4ff;
      color:var(--ink);
    }

    .topbar{
      height:64px;
      display:flex;align-items:center;justify-content:space-between;
      padding:0;
      border-bottom:1px solid var(--border);
      box-shadow:0 1px 6px rgba(0,0,0,.04);
      position:sticky;top:0;z-index:1000;
      background:#fff;
    }

    .left{
      display:flex;align-items:center;gap:12px;
      background:var(--sb-bg);   /* << warna ungu sidebar */
      height:100%;
      padding:0 18px;
      border-top-right-radius:12px;
      border-bottom-right-radius:12px;
    }

    #menuBtn{
      background:none;border:none;cursor:pointer;
      font-size:22px;color:var(--vio);line-height:1;
    }

    /* logo */
    .brand-logo img{
      height:115px;
      width:auto;
      display:block;
    }

    .right{display:flex;align-items:center;gap:14px;padding-right:18px;}
    .notif{
      background:none;border:0;cursor:pointer;
      font-size:20px;color:var(--bell);line-height:1;
    }

    .avatar{
      width:34px;height:34px;border-radius:50%;object-fit:cover;
      border:2px solid var(--chip);background:#f4ecff;
      display:flex;align-items:center;justify-content:center;
      font-weight:700;color:var(--ink);text-decoration:none;
    }
    .avatar:hover{filter:brightness(1.03)}
    .who{display:flex;flex-direction:column;align-items:flex-end;line-height:1.1}
    .who small{opacity:.7}

    @media (max-width:720px){ .who{display:none} }
  </style>
</head>
<body>

<!-- ====== HEADER ====== -->
<header class="topbar">
  <div class="left">
    <button id="menuBtn" aria-label="Menu">☰</button>
    <div class="brand-logo">
      <img src="<?= $BASE ?>assets/logo.png" alt="Smart Clothesline Logo">
    </div>
  </div>

  <?php if($user): ?>
    <a href="<?= htmlspecialchars($profileUrl) ?>" style="text-decoration:none;color:inherit;display:flex;align-items:center;gap:10px">
      <div class="who">
        <div><?= htmlspecialchars($user['nama']) ?></div>
        <small><?= htmlspecialchars($user['role']) ?></small>
      </div>
      <span class="notif" title="Notifikasi">🔔</span>
      <img class="avatar" src="<?= htmlspecialchars($fotoUrl) ?>" alt="Foto profil">
    </a>
  <?php else: ?>
    <div class="right">
      <span class="notif" title="Notifikasi">🔔</span>
      <a href="<?= $BASE ?>frontend/auth/login.php" class="avatar" title="Masuk">U</a>
    </div>
  <?php endif; ?>
</header>

<div class="layout" style="display:flex;min-height:calc(100vh - 64px);">
