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
