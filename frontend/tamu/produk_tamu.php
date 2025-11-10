<?php if (session_status()===PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Produk Jemuran Auto</title>
  <style>
    body{margin:0;font-family:Arial,Helvetica,sans-serif;background:#f5f5f5;color:#222}
    .top{background:#3498db;color:#fff;padding:12px 20px;font-weight:bold}
    .wrap{max-width:900px;margin:20px auto;padding:0 16px}
    .card{background:#fff;border:1px solid #ddd;border-radius:10px;padding:16px;margin-bottom:16px}
    .btn{display:inline-block;padding:8px 12px;border-radius:8px;border:none;cursor:pointer;color:#fff;background:#3498db;text-decoration:none}
    .btn:hover{background:#2c80b4}
  </style>
</head>
<body>
  <div class="top">🧺 Jemuran Auto — Produk Tamu</div>
  <div class="wrap">
    <div class="card">
      <h2>Kenapa Jemuran Auto?</h2>
      <p>Jemuran otomatis dengan sensor hujan, suhu, dan kelembapan. Bisa buka/tutup otomatis maupun manual dari dashboard.</p>
      <a class="btn" href="/jemuran_auto/frontend/auth/login.php">Masuk</a>
      <a class="btn" href="/jemuran_auto/frontend/auth/register.php" style="background:#10b981">Daftar</a>
    </div>

    <div class="card">
      <h3>Fitur</h3>
      <ul>
        <li>Kontrol buka/tutup dari web</li>
        <li>Otomatis tutup saat hujan</li>
        <li>Monitoring suhu & kelembapan</li>
      </ul>
    </div>
  </div>
</body>
</html>
