<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/database.php';

// ===== CEK LOGIN =====
if (!isset($_SESSION['user'])) {
  header("Location: /jemuran_auto/frontend/auth/login.php");
  exit;
}

// ===== DATA DARI FORM =====
$userId   = (int)($_SESSION['user']['id'] ?? 0);
$deviceId = isset($_POST['device_id']) ? (int)$_POST['device_id'] : 0;
$action   = trim($_POST['action'] ?? '');

// ===== VALIDASI =====
if ($deviceId <= 0 || ($action !== 'open' && $action !== 'close')) {
  echo "<script>alert('Data tidak lengkap atau salah format!');history.back();</script>";
  exit;
}

// ===== CEK DEVICE MILIK USER =====
$stmt = $conn->prepare("SELECT id, nama_device FROM devices WHERE id=? AND user_id=? LIMIT 1");
$stmt->bind_param("ii", $deviceId, $userId);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
  echo "<script>alert('Device tidak ditemukan untuk user ini!');history.back();</script>";
  exit;
}
$device = $res->fetch_assoc();
$stmt->close();

// ===== UPDATE STATUS =====
$newStatus = ($action === 'open') ? 'open' : 'close';
$stmt = $conn->prepare("UPDATE devices SET status=?, created_at=NOW() WHERE id=? AND user_id=?");
$stmt->bind_param("sii", $newStatus, $deviceId, $userId);
$stmt->execute();

$success = $stmt->affected_rows > 0;
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Smart Clothesline - Device Control</title>
  <style>
    body {
      background: #faf7ff;
      color: #2b0f53;
      font-family: 'Poppins', sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      text-align: center;
    }
    .card {
      background: #fff;
      border-radius: 24px;
      padding: 50px 70px;
      box-shadow: 0 8px 30px rgba(0,0,0,0.1);
      max-width: 520px;
      width: 100%;
    }
    h1 {
      font-size: 48px;
      margin-bottom: 10px;
      color: #341b65;
    }
    .icon {
      font-size: 64px;
      margin-bottom: 10px;
    }
    p {
      font-size: 20px;
      color: #5b479c;
      margin-top: 8px;
      line-height: 1.6;
    }
    .btn {
      display: inline-block;
      margin-top: 35px;
      padding: 14px 36px;
      background: #7a52cc;
      color: white;
      font-weight: 600;
      border-radius: 999px;
      text-decoration: none;
      font-size: 18px;
      box-shadow: 0 4px 14px rgba(122,82,204,0.25);
      transition: all 0.25s ease;
    }
    .btn:hover {
      background: #5e3fb0;
      transform: translateY(-2px);
    }
  </style>
</head>
<body>
  <div class="card">
    <?php if ($success): ?>
      <div class="icon">✅</div>
      <h1>Berhasil!</h1>
      <p>Status <b>jemuran otomatis</b> telah diperbarui.</p>
    <?php else: ?>
      <div class="icon">ℹ️</div>
      <h1>Tidak Ada Perubahan</h1>
      <p>Status jemuran sudah <b><?= strtoupper($newStatus) ?></b> atau tidak ada perubahan.</p>
    <?php endif; ?>

    <!-- Kembali langsung ke dashboard_user.php -->
    <a href="/jemuran_auto/frontend/user/dashboard_user.php" class="btn">⬅️ Kembali ke Dashboard</a>
  </div>
</body>
</html>
<?php
session_start();
require_once __DIR__ . '/../config/database.php';
header('Content-Type: application/json');

$user_id = $_SESSION['user']['id'] ?? null;
if (!$user_id) {
  echo json_encode(["status" => "error", "message" => "Unauthorized"]);
  exit;
}

$action = $_POST['action'] ?? '';

if (!in_array($action, ['open', 'close'])) {
  echo json_encode(["status" => "error", "message" => "Aksi tidak valid"]);
  exit;
}

// Ambil device user
$get = $conn->prepare("SELECT id FROM devices WHERE user_id = ? LIMIT 1");
$get->bind_param("i", $user_id);
$get->execute();
$res = $get->get_result();
$device = $res->fetch_assoc();
$device_id = $device['id'] ?? null;

if (!$device_id) {
  echo json_encode(["status" => "error", "message" => "Device belum terdaftar"]);
  exit;
}

// Update status device
$query = $conn->prepare("UPDATE devices SET status = ? WHERE id = ?");
$query->bind_param("si", $action, $device_id);
if ($query->execute()) {
  // Simpan log
  $aksi = $action === 'open' ? "Buka jemuran" : "Tutup jemuran";
  $conn->query("INSERT INTO logs (user_id, device_id, aksi) VALUES ($user_id, $device_id, '$aksi')");

  echo json_encode(["status" => "success", "message" => "Jemuran berhasil di " . ($action === 'open' ? "buka" : "tutup")]);
} else {
  echo json_encode(["status" => "error", "message" => "Gagal mengubah status device"]);
}

$conn->close();
