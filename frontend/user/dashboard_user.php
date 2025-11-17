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
    gap: 25px;
    flex-wrap: wrap;
    margin-top: 30px;
  }

  .title-row h1 {
    font-size: clamp(64px, 8vw, 110px);
    color: #2b0f53;
    font-weight: 800;
    margin: 0;
  }

  .wx-img {
    width: clamp(180px, 16vw, 230px);
    height: auto;
    transition: transform .4s ease, opacity .4s ease;
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
      <div class="deg"><?= htmlspecialchars($tempDisplay) ?></div>
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

  // fungsi update tampilan utama
  function updateView(status) {
    const isOpen = status === "open";
    title.textContent = isOpen ? "Terbuka" : "Tertutup";
    icon.src = isOpen 
      ? "<?= $BASE ?>assets/weather_sun.png"
      : "<?= $BASE ?>assets/weather_rain_sun.png";
    tempValue.textContent = isOpen ? "33°C" : "27°C";
  }

  // fungsi indikator realtime
  function showCheckingStatus(isChecking) {
    if (isChecking) {
      realtimeStatus.textContent = "🔄 Memeriksa status jemuran...";
      realtimeStatus.style.color = "#7a52cc";
    } else {
      realtimeStatus.textContent = "✅ Status jemuran terkini diperbarui.";
      realtimeStatus.style.color = "#2d6a2d";
    }
  }

  // fungsi ambil status terbaru
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

  // pertama kali dijalankan
  fetchStatus();
  // jalankan setiap 2 detik
  setInterval(fetchStatus, 2000);
});
</script>


<?php require_once __DIR__ . '/../includes/footer.php'; ?>
<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user'])) { header("Location: /jemuran_auto/frontend/auth/login.php"); exit; }

$user   = $_SESSION['user'];
$userId = (int)$user['id'];
$BASE   = "/jemuran_auto/";

// lokasi icon (SESUAIKAN DENGAN PUNYAMU)
$ICON_WEATHER = $BASE . "assets/weather_rain_sun.png";
$ICON_THERMO  = $BASE . "assets/thermo.png";

/* DB */
require_once __DIR__ . '/../../backend/config/database.php';

/* Ambil daftar device milik user */
$devices = [];
if (isset($conn)) {
  $sql  = "SELECT id, nama_device FROM devices WHERE user_id=? ORDER BY id ASC";
  $stmt = $conn->prepare($sql);
  if ($stmt === false) { die("Query prepare gagal (devices): " . $conn->error); }

  $stmt->bind_param("i", $userId);
  if ($stmt->execute()) {
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) $devices[] = $row;
  } else {
    die("Query execute gagal (devices): " . $stmt->error);
  }
  $stmt->close();
}

/* Pilih device aktif (via ?dev=ID atau default device pertama) */
$activeId = isset($_GET['dev']) ? (int)$_GET['dev'] : (int)($devices[0]['id'] ?? 0);

/* Ambil status terakhir device aktif */
$status = [
  'is_open'   => 0,
  'temp_c'    => null,
  'weather'   => 'cerah',
  'updated_at'=> null
];

if ($activeId) {
  $stmt = $conn->prepare("
    SELECT is_open, temp_c, weather, updated_at
    FROM device_status
    WHERE device_id=?
    ORDER BY updated_at DESC
    LIMIT 1
  ");
  if ($stmt) {
    $stmt->bind_param("i", $activeId);
    if ($stmt->execute()) {
      $res = $stmt->get_result();
      if ($row = $res->fetch_assoc()) $status = $row;
    }
    $stmt->close();
  }
}

/* Mapping ikon cuaca sederhana (sebagai teks/fallback kalau gambar ga kebaca) */
function weather_icon($w){
  $w = strtolower(trim((string)$w));
  return match(true) {
    str_contains($w,'hujan')   => '🌧️',
    str_contains($w,'mendung') => '☁️',
    str_contains($w,'cerah')   => '🌤️',
    default                    => '⛅',
  };
}

$isOpen = (int)($status['is_open'] ?? 0) === 1;
$title  = $isOpen ? 'Terbuka' : 'Tertutup';
$icon   = weather_icon($status['weather'] ?? '');
$temp   = isset($status['temp_c']) ? (float)$status['temp_c'] : null;

/* Teks suhu di layar -> kalau belum ada data, pakai 27°C */
$tempDisplay = $temp !== null ? number_format($temp, 0) . '°C' : '27°C';

/* ====== VIEW ====== */
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<style>
  .sc-home{ padding:28px; }
  .hero{
    background: transparent; /* tidak ada kotak */
    border: none;            /* hilangkan garis */
    border-radius: 0;        /* tidak bulat */
    padding: 20px 0;         /* padding simple */
    box-shadow: none;        /* hilangkan bayangan */
  }
  .title-row{
    display:flex; align-items:center; gap:18px; flex-wrap:wrap; justify-content:center;
  }
  .title-row h1{
    font-size: clamp(42px, 6vw, 96px);
    color:#2b0f53; margin:0; font-weight:800; letter-spacing:.5px; text-align:center;
  }
  .wx{ display:flex; align-items:center; justify-content:center; }
  .wx-img{
    width: clamp(70px, 9vw, 110px);
    height:auto;
    display:block;
  }
  .title-row .wx-fallback{
    font-size: clamp(44px, 7vw, 96px);
  }

  .cta-row{
    display:flex; gap:22px; justify-content:center; margin:26px 0 8px;
    flex-wrap:wrap;
  }
  .btn-pill{
    border:none; cursor:pointer; font-weight:800; font-size:20px;
    padding:16px 26px; border-radius:999px;
    box-shadow:0 6px 18px rgba(100,120,245,.18);
  }
  .btn-open { background:#dcd6ff; color:#392167; }
  .btn-close{ background:#e4e0ff; color:#26104d; }

  .btn-pill:disabled{
    opacity:.6; cursor:default; box-shadow:none;
  }

  .temp-row{
    display:flex; align-items:center; gap:16px; justify-content:center;
    margin-top:30px; color:#2b0f53;
  }
  .temp-row .deg{
    font-size: clamp(42px, 6vw, 100px);
    font-weight:800;
  }
  .thermo-img{
    width: clamp(60px, 7vw, 90px);
    height:auto;
    display:block;
  }

  .meta{ text-align:center; margin-top:10px; color:#6b5a8e; font-size:13px; }

  @media (max-width:900px){
    .sc-home{ padding:18px; }
  }


  /* =============================== */
  /* >>>>>   Tambahan agar ke tengah   <<<<< */
  /* =============================== */

  #content-wrapper {
    margin-left: 450px !important;
    padding: 30px;
    transition: margin-left .3s ease;

    display: flex;
    flex-direction: column;
    align-items: center; /* supaya konten benar² center */
}

</style>

<div class="sc-home">

  <!-- hero status sesuai desain -->
  <section class="hero">
    <div class="title-row">
      <h1><?= htmlspecialchars($title) ?></h1>

      <!-- Ikon cuaca dari gambar -->
      <div class="wx">
        <img src="<?= htmlspecialchars($ICON_WEATHER) ?>"
             alt="<?= htmlspecialchars($status['weather'] ?? 'Cuaca') ?>"
             class="wx-img"
             onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
        <!-- fallback emoji kalau gambar gagal load -->
        <div class="wx-fallback" style="display:none;"><?= $icon ?></div>
      </div>
    </div>

    <div class="cta-row">
      <form method="post" action="/jemuran_auto/backend/device/control.php">
        <input type="hidden" name="device_id" value="<?= (int)$activeId ?>">
        <input type="hidden" name="action" value="open">
        <button class="btn-pill btn-open" type="submit" <?= $activeId ? '' : 'disabled' ?>>
          Buka Jemuran
        </button>
      </form>

      <form method="post" action="/jemuran_auto/backend/device/control.php">
        <input type="hidden" name="device_id" value="<?= (int)$activeId ?>">
        <input type="hidden" name="action" value="close">
        <button class="btn-pill btn-close" type="submit" <?= $activeId ? '' : 'disabled' ?>>
          Tutup Jemuran
        </button>
      </form>
    </div>

    <div class="temp-row">
      <img src="<?= htmlspecialchars($ICON_THERMO) ?>" alt="Suhu" class="thermo-img">
      <div class="deg"><?= htmlspecialchars($tempDisplay) ?></div>
    </div>

    <?php if(!empty($status['updated_at'])): ?>
      <div class="meta">
        Terakhir diperbarui:
        <?= htmlspecialchars(date('d-m-Y H:i', strtotime($status['updated_at']))) ?>
      </div>
    <?php endif; ?>
  </section>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
