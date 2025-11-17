<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user'])) {
  echo "unauthorized";
  exit;
}

$userId = (int)$_SESSION['user']['id'];
$deviceId = isset($_GET['device_id']) ? (int)$_GET['device_id'] : 0;

if ($deviceId <= 0) {
  echo "invalid";
  exit;
}

$stmt = $conn->prepare("SELECT status FROM devices WHERE id=? AND user_id=? LIMIT 1");
$stmt->bind_param("ii", $deviceId, $userId);
$stmt->execute();
$res = $stmt->get_result();

if ($row = $res->fetch_assoc()) {
  echo $row['status']; // cuma open / close
} else {
  echo "notfound";
}

$stmt->close();
$conn->close();
?>
