<?php
require_once __DIR__ . '/../config/database.php';
header('Content-Type: application/json');

session_start();

$user_id = $_SESSION['user']['id'] ?? null;
$role    = $_SESSION['user']['role'] ?? 'user';

// Kalau admin, ambil semua log; kalau user, cuma log miliknya
if ($role === 'admin') {
  $query = "SELECT logs.id, users.nama, devices.nama_device, logs.aksi, logs.waktu
            FROM logs
            JOIN users ON logs.user_id = users.id
            JOIN devices ON logs.device_id = devices.id
            ORDER BY logs.waktu DESC";
  $stmt = $conn->prepare($query);
} else {
  $query = "SELECT logs.id, devices.nama_device, logs.aksi, logs.waktu
            FROM logs
            JOIN devices ON logs.device_id = devices.id
            WHERE logs.user_id = ?
            ORDER BY logs.waktu DESC";
  $stmt = $conn->prepare($query);
  $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$result = $stmt->get_result();

$logs = [];
while ($row = $result->fetch_assoc()) {
  $logs[] = $row;
}

echo json_encode([
  "status" => "success",
  "total"  => count($logs),
  "data"   => $logs
]);

$stmt->close();
$conn->close();
?>
