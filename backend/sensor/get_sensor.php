<?php
require_once __DIR__ . '/../config/database.php';
header('Content-Type: application/json');

// Ambil data sensor terakhir
$result = $conn->query("SELECT * FROM sensor_data ORDER BY id DESC LIMIT 1");

if ($result && $result->num_rows > 0) {
  $row = $result->fetch_assoc();
  echo json_encode([
    "status" => "success",
    "data" => [
      "suhu" => $row['suhu'],
      "kelembapan" => $row['kelembapan'],
      "sensor_hujan" => $row['sensor_hujan'],
      "waktu" => $row['waktu']
    ]
  ]);
} else {
  echo json_encode(["status" => "error", "message" => "Belum ada data sensor"]);
}
$conn->close();
