<?php
require_once __DIR__ . '/../../backend/middleware/admin_only.php';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<div class="content">
  <h3>Dashboard Admin</h3>
  <p>Halo Admin, <?= htmlspecialchars($_SESSION['user']['nama']); ?>!</p>

  <!-- Status Sensor -->
  <div id="sensor" style="background:#f3f4f6;padding:15px;border-radius:10px;width:300px;">
    <h4>Data Sensor Terbaru</h4>
    <p><b>Suhu:</b> <span id="suhu">--</span> °C</p>
    <p><b>Kelembapan:</b> <span id="kelembapan">--</span> %</p>
    <p><b>Hujan:</b> <span id="hujan">--</span></p>
    <small>Terakhir update: <span id="waktu">--</span></small>
  </div>

  <!-- Kontrol Jemuran -->
  <div style="margin-top:20px;">
    <h4>Kontrol Manual</h4>
    <button id="openBtn" style="padding:10px 20px;background:#10b981;color:white;border:none;border-radius:6px;">Buka Jemuran</button>
    <button id="closeBtn" style="padding:10px 20px;background:#ef4444;color:white;border:none;border-radius:6px;">Tutup Jemuran</button>
    <p id="msg" style="margin-top:10px;color:green;"></p>
  </div>
</div>

<script>
async function getSensor() {
  const res = await fetch('../../backend/sensor/get_sensor.php');
  const data = await res.json();
  if (data.status === 'success') {
    document.getElementById('suhu').textContent = data.suhu;
    document.getElementById('kelembapan').textContent = data.kelembapan;
    document.getElementById('hujan').textContent = data.hujan == 1 ? "Hujan" : "Cerah";
    document.getElementById('waktu').textContent = data.waktu;
  }
}
setInterval(getSensor, 5000);
getSensor();

async function controlJemuran(action) {
  const form = new FormData();
  form.append('action', action);
  const res = await fetch('../../backend/device/device_control.php', { method: 'POST', body: form });
  const data = await res.json();
  const msg = document.getElementById('msg');
  msg.textContent = data.message;
  msg.style.color = data.status === 'success' ? 'green' : 'red';
}

document.getElementById('openBtn').addEventListener('click', ()=>controlJemuran('open'));
document.getElementById('closeBtn').addEventListener('click', ()=>controlJemuran('close'));
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>
