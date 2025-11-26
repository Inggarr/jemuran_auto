<?php 
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user'])) { 
  header("Location: /jemuran_auto/frontend/auth/login.php"); 
  exit; 
}

$user   = $_SESSION['user'];
$userId = (int)$user['id'];
$BASE   = "/jemuran_auto/";

// ICONS
$ICON_WEATHER_SUN  = $BASE . "assets/weather_sun.png";
$ICON_WEATHER_RAIN = $BASE . "assets/weather_rain_sun.png";
$ICON_THERMO       = $BASE . "assets/thermo.png";

// DB CONNECT
require_once __DIR__ . '/../../backend/config/database.php';


/* ============================================================
   HANDLE Aksi Buka / Tutup Jemuran → TANPA device_control.php
   ============================================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['device_id'], $_POST['action'])) {

    $deviceId = intval($_POST['device_id']);
    $action   = ($_POST['action'] === 'open') ? 'open' : 'close';

    // Update status device
    $stmt = $conn->prepare("UPDATE devices SET status=? WHERE id=? AND user_id=?");
    $stmt->bind_param("sii", $action, $deviceId, $userId);
    $stmt->execute();
    $stmt->close();

    // reload halaman agar UI langsung berubah
    header("Location: dashboard_user.php?dev=" . $deviceId);
    exit;
}


/* ============================================================
   AMBIL daftar device user
   ============================================================ */
$devices = [];
$stmt = $conn->prepare("SELECT id, nama_device FROM devices WHERE user_id=? ORDER BY id ASC");
$stmt->bind_param("i", $userId);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) $devices[] = $row;
$stmt->close();


// device aktif
$activeId = isset($_GET['dev']) ? intval($_GET['dev']) : ($devices[0]['id'] ?? 0);


/* ============================================================
   AMBIL status awal device
   ============================================================ */
$status = "close";
$stmt = $conn->prepare("SELECT status FROM devices WHERE id=? AND user_id=? LIMIT 1");
$stmt->bind_param("ii", $activeId, $userId);
$stmt->execute();
$res = $stmt->get_result();
if ($row = $res->fetch_assoc()) $status = $row['status'];
$stmt->close();

$isOpen = ($status === "open");
$title  = $isOpen ? "Terbuka" : "Tertutup";
$temp   = $isOpen ? "33°C" : "27°C";
$ICON_WEATHER = $isOpen ? $ICON_WEATHER_SUN : $ICON_WEATHER_RAIN;


// include header, sidebar
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<style>
  body { background-color: #FFFBFF; }
  .sc-home { padding: 40px 20px; display:flex; flex-direction:column; align-items:center; justify-content:center; }
  .title-row { display:flex; align-items:center; justify-content:center; gap:20px; flex-wrap:wrap; margin-top:35px; }
  .title-row h1 { font-size: clamp(50px,8vw,100px); color:#2b0f53; font-weight:800; margin:0; }
  .wx-img { width: clamp(100px,15vw,200px); height:auto; transition: .4s; }
  .wx-img[src*="weather_sun.png"] { transform: scale(2); }
  .cta-row { display:flex; gap:30px; justify-content:center; flex-wrap:wrap; margin-top:40px; }
  .btn-pill { border:none; cursor:pointer; font-weight:700; font-size:22px; padding:20px 38px;
              border-radius:999px; transition:.25s; box-shadow:0 6px 20px rgba(80,60,160,.15); }
  .btn-open { background:#bcd0ff; color:#2d0f5b; }
  .btn-close { background:#d8d0ff; color:#2d0f5b; }
  .btn-pill.active { filter:brightness(1.1); transform:scale(1.05); }
  .temp-row { display:flex; align-items:center; justify-content:center; gap:25px; margin-top:60px; }
  .thermo-img { width:clamp(90px,9vw,140px); height:auto; }
  .deg { font-size:clamp(72px,9vw,130px); font-weight:800; color:#2b0f53; }
</style>

<div class="sc-home">

  <section class="hero">

    <div class="title-row">
      <h1 id="statusTitle"><?= $title ?></h1>
      <img id="weatherIcon" src="<?= $ICON_WEATHER ?>" class="wx-img">
    </div>

    <div class="cta-row">

      <!-- BUKA -->
      <form method="POST">
        <input type="hidden" name="device_id" value="<?= $activeId ?>">
        <input type="hidden" name="action" value="open">
        <button class="btn-pill btn-open <?= $isOpen ? 'active' : '' ?>">Buka Jemuran</button>
      </form>

      <!-- TUTUP -->
      <form method="POST">
        <input type="hidden" name="device_id" value="<?= $activeId ?>">
        <input type="hidden" name="action" value="close">
        <button class="btn-pill btn-close <?= !$isOpen ? 'active' : '' ?>">Tutup Jemuran</button>
      </form>

    </div>

    <div class="temp-row">
      <img src="<?= $ICON_THERMO ?>" class="thermo-img">
      <div class="deg" id="tempValue"><?= $temp ?></div>
    </div>

  </section>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const title = document.getElementById("statusTitle");
  const icon = document.getElementById("weatherIcon");
  const temp = document.getElementById("tempValue");

  function updateUI(status) {
    const isOpen = status === "open";
    title.textContent = isOpen ? "Terbuka" : "Tertutup";
    icon.src = isOpen 
      ? "<?= $BASE ?>assets/weather_sun.png"
      : "<?= $BASE ?>assets/weather_rain_sun.png";
    temp.textContent = isOpen ? "33°C" : "27°C";
  }

  async function reloadStatus() {
    const res = await fetch("<?= $BASE ?>backend/device/get_device.php?id=<?= $activeId ?>");
    const status = (await res.text()).trim();
    if (status === "open" || status === "close") updateUI(status);
  }

  setInterval(reloadStatus, 2000);
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
