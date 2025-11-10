<?php
require_once __DIR__ . '/../../backend/middleware/auth_check.php';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<div class="content">
  <h3>Log Aktivitas Jemuran</h3>
  <p>Berikut log jemuran kamu (contoh dummy):</p>
  <table border="1" cellpadding="8" cellspacing="0">
    <tr><th>Tanggal</th><th>Aksi</th></tr>
    <tr><td>2025-11-10 08:45</td><td>Buka jemuran otomatis</td></tr>
    <tr><td>2025-11-10 09:30</td><td>Tutup jemuran (hujan terdeteksi)</td></tr>
  </table>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
