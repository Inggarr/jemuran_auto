<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user'])) {
  header("Location: /jemuran_auto/frontend/auth/login.php");
  exit;
}

require_once __DIR__ . '/../../backend/config/database.php';

$user = $_SESSION['user'];
$BASE = "/jemuran_auto/";

/* Ambil semua device dari database */
$sql = "
  SELECT d.id, d.nama_device, d.status, u.nama AS username
  FROM devices d
  JOIN users u ON d.user_id = u.id
  ORDER BY d.id ASC
";
$res = $conn->query($sql);
$devices = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

/* Hitung statistik */
$total = count($devices);
$active = count(array_filter($devices, fn($d) => $d['status'] === 'open'));
$offline = $total - $active;

/* Header dan sidebar */
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<style>
body {
  background: #FFFBFF;
  font-family: "Inter", sans-serif;
}

/* Layout utama */
.dashboard-container {
  padding: 30px 50px;
}

/* ===== Kartu statistik ===== */
.stats-cards {
  display: flex;
  gap: 20px;
  flex-wrap: wrap;
  justify-content: center;
  margin-bottom: 40px;
}

.card-stat {
  flex: 1 1 250px;
  background: #f8f5ff;
  border-radius: 15px;
  text-align: center;
  padding: 20px;
  color: #2b0f53;
  font-weight: 700;
  transition: all 0.3s ease;
  border: 2px solid transparent;
}

.card-stat.active {
  border: 2px solid #4a41ff;
  background: #edf0ff;
}

.card-stat h2 {
  font-size: 50px;
  margin: 5px 0 0;
}

/* ===== Tabel ===== */
.table-box {
  background: #fff;
  border-radius: 15px;
  padding: 20px 20px;
  box-shadow: 0 6px 20px rgba(80, 60, 160, 0.08);
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
  font-size: 15px;
}

th, td {
  padding: 15px 10px;
  text-align: left;
  border-bottom: 1px solid #eee;
}

th {
  color: #6b5a8e;
  font-weight: 600;
}

td {
  color: #2b0f53;
  font-weight: 500;
}

/* =======================================
   TOGGLE SWITCH CONTROL
   ======================================= */
.toggle-switch {
  position: relative;
  width: 70px;
  height: 32px;
  background: #ADB1EF;
  border-radius: 50px;
  cursor: pointer;
  transition: 0.3s;
}

.toggle-switch input {
  display: none;
}

.toggle-slider {
  position: absolute;
  top: 3px;
  left: 3px;
  width: 26px;
  height: 26px;
  background: white;
  border-radius: 50%;
  transition: 0.3s;
}

.toggle-switch.on {
  background: #7b7ce0;
}

.toggle-switch.on .toggle-slider {
  left: 41px;
}

.toggle-wrapper {
  display: flex;
  align-items: center;
  gap: 10px;
}

.toggle-label {
  font-weight: 600;
  color: #2b0f53;
  width: 40px;
  text-transform: uppercase;
  font-size: 14px;
}

/* Responsif */
@media (max-width: 768px) {
  .dashboard-container {
    padding: 20px;
  }

  .card-stat h2 {
    font-size: 36px;
  }

  table {
    font-size: 14px;
  }
}
</style>

<div class="dashboard-container">
  <!-- ===== Kartu Statistik ===== -->
  <div class="stats-cards">
    <div class="card-stat">
      <div>Total Perangkat</div>
      <h2><?= $total ?></h2>
    </div>
    <div class="card-stat active">
      <div>Perangkat Aktif</div>
      <h2><?= $active ?></h2>
    </div>
    <div class="card-stat">
      <div>Perangkat Offline</div>
      <h2><?= $offline ?></h2>
    </div>
  </div>

  <!-- ===== Tabel Daftar Device ===== -->
  <div class="table-box">
    <table>
      <thead>
        <tr>
          <th>Username</th>
          <th>Device</th>
          <th>Control</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($devices)): ?>
          <tr><td colspan="3" style="text-align:center;color:#888;">Tidak ada data perangkat.</td></tr>
        <?php else: ?>
          <?php foreach ($devices as $d): ?>
            <tr>
              <td><?= htmlspecialchars($d['username']) ?></td>
              <td><?= htmlspecialchars($d['nama_device']) ?></td>

              <!-- TOGGLE SWITCH -->
              <td>
  <div class="toggle-wrapper">
    <span class="toggle-label"><?= $d['status'] === 'open' ? 'ON' : 'OFF' ?></span>

    <div class="toggle-switch <?= $d['status'] === 'open' ? 'on' : '' ?>"
         onclick="toggleDevice(<?= $d['id'] ?>, this)">
      <input type="checkbox" <?= $d['status'] === 'open' ? 'checked' : '' ?>>
      <div class="toggle-slider"></div>
    </div>
  </div>
</td>

            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
function toggleDevice(id, element) {
    let isOn = element.classList.toggle("on");

    // Ubah teks ON/OFF sebelah toggle
    let label = element.parentElement.querySelector(".toggle-label");
    label.textContent = isOn ? "ON" : "OFF";

    fetch("/jemuran_auto/backend/api/update_status.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            id: id,
            status: isOn ? "open" : "close"
        })
    });
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
