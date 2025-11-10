<?php
require_once __DIR__ . '/../config/database.php';
header('Content-Type: application/json');

session_start();

$user_id   = $_SESSION['user']['id'] ?? null;
$device_id = $_POST['device_id'] ?? null;
$aksi      = trim($_POST['aksi'] ?? '');

if (!$user_id || !$device_id || $aksi === '') {
  echo json_encode(["status" => "error", "message" => "Data log tidak lengkap"]);
  exit;
}

// Simpan log ke database
$stmt = $conn->prepare("INSERT INTO logs (user_id, device_id, aksi) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $user_id, $device_id, $aksi);

if ($stmt->execute()) {
  echo json_encode(["status" => "success", "message" => "Log berhasil disimpan"]);
} else {
  echo json_encode(["status" => "error", "message" => "Gagal menyimpan log"]);
}

$stmt->close();
$conn->close();
?>
