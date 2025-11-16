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
