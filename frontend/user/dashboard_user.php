<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user'])) { 
  header("Location: /jemuran_auto/frontend/auth/login.php"); 
  exit; 
}

$user   = $_SESSION['user'];
$userId = (int)$user['id'];
$BASE   = "/jemuran_auto/";

// lokasi icon
$ICON_WEATHER_SUN  = $BASE . "assets/weather_sun.png";
$ICON_WEATHER_RAIN = $BASE . "assets/weather_rain_sun.png";
$ICON_THERMO       = $BASE . "assets/thermo.png";

/* DB */
require_once __DIR__ . '/../../backend/config/database.php';

/* Ambil daftar device milik user */
$devices = [];
if (isset($conn)) {
  $sql  = "SELECT id, nama_device FROM devices WHERE user_id=? ORDER BY id ASC";
  $stmt = $conn->prepare($sql);
  if ($stmt) {
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) $devices[] = $row;
    $stmt->close();
  }
}

/* Pilih device aktif */
$activeId = isset($_GET['dev']) ? (int)$_GET['dev'] : (int)($devices[0]['id'] ?? 0);

/* Ambil status device aktif */
$status = ['status' => 'close'];
if ($activeId) {
  $stmt = $conn->prepare("SELECT status FROM devices WHERE id=? LIMIT 1");
  $stmt->bind_param("i", $activeId);
  $stmt->execute();
  $res = $stmt->get_result();
  if ($row = $res->fetch_assoc()) $status = $row;
  $stmt->close();
}

/* Tentukan tampilan awal */
$isOpen = ($status['status'] ?? 'close') === 'open';
$title  = $isOpen ? 'Terbuka' : 'Tertutup';
$tempDisplay = $isOpen ? '33°C' : '27°C';
$ICON_WEATHER = $isOpen ? $ICON_WEATHER_SUN : $ICON_WEATHER_RAIN;

/* ====== VIEW ====== */
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<style>
  body {
    background-color: #FFFBFF;
  }

  .sc-home {
    padding: 40px 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
  }

  .title-row {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 20px;
    flex-wrap: wrap;
    margin-top: 35px;
  }

  .title-row h1 {
    font-size: clamp(50px, 8vw, 100px);
    color: #2b0f53;
    font-weight: 800;
    margin: 0;
  }

  /* default semua icon */
  .wx-img {
    width: clamp(100px, 15vw, 200px);
    height: auto;
    transition: transform .4s ease, opacity .4s ease;
  }

  /* hanya untuk icon matahari penuh */
  .wx-img[src*="weather_sun.png"] {
    transform: scale(2);
    transform-origin: center center;
  }

  .cta-row {
    display: flex;
    gap: 30px;
    justify-content: center;
    flex-wrap: wrap;
    margin-top: 40px;
  }

  .btn-pill {
    border: none;
    cursor: pointer;
    font-weight: 700;
    font-size: 22px;
    padding: 20px 38px;
    border-radius: 999px;
    transition: all 0.25s ease;
    box-shadow: 0 6px 20px rgba(80, 60, 160, 0.15);
  }

  .btn-open {
    background: #bcd0ff;
    color: #2d0f5b;
  }

  .btn-close {
    background: #d8d0ff;
    color: #2d0f5b;
  }

  .btn-pill.active {
    filter: brightness(1.1);
    transform: scale(1.05);
  }

  .temp-row {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 25px;
    margin-top: 60px;
  }

  .thermo-img {
    width: clamp(90px, 9vw, 140px);
    height: auto;
  }

  .deg {
    font-size: clamp(72px, 9vw, 130px);
    font-weight: 800;
    color: #2b0f53;
    transition: opacity 0.3s ease;
  }

  .meta {
    text-align: center;
    margin-top: 14px;
    color: #6b5a8e;
    font-size: 14px;
  }

  .alert-box {
    margin-top: 10px;
    padding: 10px 20px;
    background: #e9f5e9;
    border: 1px solid #b6e0b8;
    color: #2d6a2d;
    border-radius: 8px;
    text-align: center;
    font-weight: 500;
  }
</style>

<!-- ====== HERO SECTION ====== -->
<div class="sc-home">
  <?php if(isset($_GET['updated'])): ?>
    <div class="alert-box">✅ Status jemuran berhasil diperbarui.</div>
    <div id="realtimeStatus" class="meta">🔄 Memeriksa status jemuran...</div>
  <?php endif; ?>

  <section class="hero">
    <div class="title-row">
      <h1 id="statusTitle"><?= htmlspecialchars($title) ?></h1>
      <img id="weatherIcon" src="<?= htmlspecialchars($ICON_WEATHER) ?>" alt="Cuaca" class="wx-img">
    </div>

    <div class="cta-row">
      <!-- Tombol Buka Jemuran -->
      <form action="<?= $BASE ?>backend/device/device_control.php" method="POST" style="display:inline;">
        <input type="hidden" name="device_id" value="<?= htmlspecialchars($activeId) ?>">
        <input type="hidden" name="action" value="open">
        <button type="submit" class="btn-pill btn-open <?= $isOpen ? 'active' : '' ?>">Buka Jemuran</button>
      </form>

      <!-- Tombol Tutup Jemuran -->
      <form action="<?= $BASE ?>backend/device/device_control.php" method="POST" style="display:inline;">
        <input type="hidden" name="device_id" value="<?= htmlspecialchars($activeId) ?>">
        <input type="hidden" name="action" value="close">
        <button type="submit" class="btn-pill btn-close <?= !$isOpen ? 'active' : '' ?>">Tutup Jemuran</button>
      </form>
    </div>

    <div class="temp-row">
      <img src="<?= htmlspecialchars($ICON_THERMO) ?>" alt="Suhu" class="thermo-img">
      <div class="deg" id="tempValue"><?= htmlspecialchars($tempDisplay) ?></div>
    </div>
  </section>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const title = document.getElementById("statusTitle");
  const icon = document.getElementById("weatherIcon");
  const tempValue = document.getElementById("tempValue");
  const realtimeStatus = document.getElementById("realtimeStatus");

  let currentStatus = "<?= $isOpen ? 'open' : 'close' ?>";
  let loading = false;

  function updateView(status) {
    const isOpen = status === "open";
    title.textContent = isOpen ? "Terbuka" : "Tertutup";
    icon.src = isOpen 
      ? "<?= $BASE ?>assets/weather_sun.png"
      : "<?= $BASE ?>assets/weather_rain_sun.png";
    tempValue.textContent = isOpen ? "33°C" : "27°C";
  }

  function showCheckingStatus(isChecking) {
    if (isChecking) {
      realtimeStatus.textContent = "🔄 Memeriksa status jemuran...";
      realtimeStatus.style.color = "#7a52cc";
    } else {
      realtimeStatus.textContent = "✅ Status jemuran terkini diperbarui.";
      realtimeStatus.style.color = "#2d6a2d";
    }
  }

  async function fetchStatus() {
    if (loading) return;
    loading = true;
    showCheckingStatus(true);

    try {
      const res = await fetch("<?= $BASE ?>backend/device/get_device.php?device_id=<?= $activeId ?>");
      const text = (await res.text()).trim();

      if (text === "open" || text === "close") {
        if (text !== currentStatus) {
          currentStatus = text;
          updateView(currentStatus);
        }
        showCheckingStatus(false);
      }
    } catch (err) {
      realtimeStatus.textContent = "⚠️ Gagal memperbarui status.";
      realtimeStatus.style.color = "red";
      console.error(err);
    } finally {
      loading = false;
    }
  }

  fetchStatus();
  setInterval(fetchStatus, 2000);
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
