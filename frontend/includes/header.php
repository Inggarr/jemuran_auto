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

  <style>
    :root{
      --ink:#2b1842;
      --vio:#6478f5;
      --border:#efeaff;
      --chip:#e8ddff;
      --sb-bg:#c8c1ff;
    }

    html,body{margin:0}
    body{
      font-family:Inter,Arial,Helvetica,sans-serif;
      background:#f7f4ff;
      color:var(--ink);
    }

    /* ===== HEADER PUTIH TANPA CARD ===== */
    .topbar{
      height:64px;
      display:flex;
      align-items:center;
      justify-content:space-between;
      padding:0;
      position:sticky;
      top:0;
      z-index:1000;

      /* 🔥 Hilangkan efek card */
      background:#ffffff !important;
      box-shadow:none !important;
      border-bottom:none !important;
    }

    .left{
      display:flex;
      align-items:center;
      gap:12px;
      height:100%;
      padding:0 18px;
    }

    #menuBtn{
      background:none;
      border:none;
      cursor:pointer;
      font-size:22px;
      color:var(--vio);
    }

    .brand-logo img{
      height:115px;
      display:block;
    }

    .right{
      display:flex;
      align-items:center;
      gap:14px;
      padding-right:18px;
    }

    .notif-wrap{
      position:relative;
      width:26px;
      height:26px;
      cursor:pointer;
      display:flex;
      align-items:center;
      justify-content:center;
    }

    .notif-icon{
      width:26px;
      height:26px;
      stroke:var(--ink);
    }

    .avatar{
      width:34px;
      height:34px;
      border-radius:50%;
      object-fit:cover;
      border:2px solid var(--chip);
      background:#f4ecff;
    }

    .who{
      display:flex;
      flex-direction:column;
      align-items:flex-end;
      line-height:1.1
    }
    .who small{opacity:.7}

    @media(max-width:720px){
      .who{display:none}
    }

    /* ⭐ Hilangkan garis visual di bawah header */
    .layout{
      background:#ffffff !important;
    }

  </style>
</head>
<body>

<header class="topbar">
  <div class="left">
    <button id="menuBtn" aria-label="Menu">☰</button>
    <div class="brand-logo">
      <img src="<?= $BASE ?>assets/LOGO BARU.png" alt="Smart Clothesline Logo">
    </div>
  </div>

  <?php if($user): ?>
    <a href="<?= htmlspecialchars($profileUrl) ?>"
       style="text-decoration:none;color:inherit;display:flex;align-items:center;gap:14px">

      <div class="who">
        <div><?= htmlspecialchars($user['nama']) ?></div>
        <small><?= htmlspecialchars($user['role']) ?></small>
      </div>

      <div class="notif-wrap">
        <svg class="notif-icon" viewBox="0 0 24 24">
          <path d="M12 2a6 6 0 00-6 6v4.5l-1.7 2.9a1 1 0 00.9 1.6h14.6a1 1 0 00.9-1.6L18 12.5V8a6 6 0 00-6-6zm0 20a3 3 0 01-3-3h6a3 3 0 01-3 3z"
                fill="none" stroke="#2b1842" stroke-width="1.5"/>
        </svg>
      </div>

      <img class="avatar" src="<?= htmlspecialchars($fotoUrl) ?>" alt="Foto profil">

    </a>
  <?php else: ?>
    <div class="right">
      <div class="notif-wrap">
        <svg class="notif-icon" viewBox="0 0 24 24">
          <path d="M12 2a6 6 0 00-6 6v4.5l-1.7 2.9a1 1 0 00.9 1.6h14.6a1 1 0 00.9-1.6L18 12.5V8a6 6 0 00-6-6zm0 20a3 3 0 01-3-3h6a3 3 0 01-3 3z"
                fill="none" stroke="#2b1842" stroke-width="1.5"/>
        </svg>
      </div>

      <a href="<?= $BASE ?>frontend/auth/login.php" class="avatar">U</a>
    </div>
  <?php endif; ?>
</header>

<div class="layout" style="display:flex;min-height:calc(100vh - 64px);">
