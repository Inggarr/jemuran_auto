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
}<?php
if (session_status() === PHP_SESSION_NONE) session_start();
header("Content-Type: application/json");

require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user'])) {
    echo json_encode(["success" => false, "message" => "unauthorized"]);
    exit;
}

$userId = (int)$_SESSION['user']['id'];
$role   = $_SESSION['user']['role'] ?? 'user';

if ($role === "admin") {
    $sql = "
        SELECT d.id, d.nama_device, d.status, u.nama AS username
        FROM devices d
        JOIN users u ON d.user_id = u.id
        ORDER BY d.id ASC
    ";
    $stmt = $conn->prepare($sql);
} else {
    $sql = "
        SELECT d.id, d.nama_device, d.status, u.nama AS username
        FROM devices d
        JOIN users u ON d.user_id = u.id
        WHERE d.user_id = ?
        ORDER BY d.id ASC
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
}

$stmt->execute();
$res = $stmt->get_result();

$devices = [];
while ($row = $res->fetch_assoc()) {
    $devices[] = [
        "id"      => (int)$row["id"],
        "device"  => $row["nama_device"],
        "status"  => $row["status"],
        "user"    => $row["username"]
    ];
}

echo json_encode([
    "success" => true,
    "devices" => $devices
]);


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
