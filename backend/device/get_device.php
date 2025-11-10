<?php
session_start();
require_once __DIR__ . '/../config/database.php';
header('Content-Type: application/json');

$user_id = $_SESSION['user']['id'] ?? null;
if (!$user_id) {
  echo json_encode(["status" => "error", "message" => "Unauthorized"]);
  exit;
}

$query = $conn->prepare("SELECT nama_device, status, created_at FROM devices WHERE user_id = ? LIMIT 1");
$query->bind_param("i", $user_id);
$query->execute();
$res = $query->get_result();

if ($res && $res->num_rows > 0) {
  $device = $res->fetch_assoc();
  echo json_encode([
    "status" => "success",
    "device" => $device
  ]);
} else {
  echo json_encode(["status" => "error", "message" => "Device tidak ditemukan"]);
}

$conn->close();
