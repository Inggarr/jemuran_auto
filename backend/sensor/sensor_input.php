<?php
require_once __DIR__ . '/../config/database.php';
header('Content-Type: application/json');

// Ambil data dari request (GET / POST)
$device_id    = $_POST['device_id'] ?? $_GET['device_id'] ?? null;
$suhu         = $_POST['suhu'] ?? $_GET['suhu'] ?? null;
$kelembapan   = $_POST['kelembapan'] ?? $_GET['kelembapan'] ?? null;
$sensor_hujan = $_POST['sensor_hujan'] ?? $_GET['sensor_hujan'] ?? null;

if ($device_id === null || $suhu === null || $kelembapan === null || $sensor_hujan === null) {
  echo json_encode(["status" => "error", "message" => "Data tidak lengkap"]);
  exit;
}

// Simpan ke database
$stmt = $conn->prepare("INSERT INTO sensor_data (device_id, suhu, kelembapan, sensor_hujan) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iddd", $device_id, $suhu, $kelembapan, $sensor_hujan);

if ($stmt->execute()) {
  echo json_encode(["status" => "success", "message" => "Data sensor disimpan"]);
} else {
  echo json_encode(["status" => "error", "message" => "Gagal menyimpan data sensor"]);
}

$stmt->close();
$conn->close();
