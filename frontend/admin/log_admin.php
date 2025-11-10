<?php
require_once __DIR__ . '/../../backend/middleware/admin_only.php';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<div class="content">
  <h3>Log Aktivitas Pengguna</h3>
  <p>Berikut daftar aktivitas semua pengguna (contoh dummy):</p>
  <table border="1" cellpadding="8" cellspacing="0">
    <tr><th>Nama</th><th>Aksi</th><th>Waktu</th></tr>
    <tr><td>User 1</td><td>Buka jemuran</td><td>2025-11-10 08:45</td></tr>
    <tr><td>User 2</td><td>Tutup jemuran</td><td>2025-11-10 09:30</td></tr>
  </table>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
