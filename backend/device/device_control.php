<?php
header("Content-Type: application/json");
session_start();

require_once __DIR__ . '/../config/database.php';

// ==== CEK LOGIN ====
if (!isset($_SESSION['user'])) {
    echo json_encode(["success" => false, "message" => "unauthorized"]);
    exit;
}

/* ==== BACA JSON BODY ==== */
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

$deviceId  = intval($data['id'] ?? 0);
$newStatus = $data['status'] ?? '';

if ($deviceId <= 0 || !in_array($newStatus, ['open', 'close'])) {
    echo json_encode(["success" => false, "message" => "invalid parameter"]);
    exit;
}

// Konversi open/close → ON/OFF (sesuaikan DB kamu)
$finalStatus = ($newStatus === "open") ? "open" : "close";

/* ==== UPDATE ==== */
$stmt = $conn->prepare("UPDATE devices SET status=? WHERE id=?");
$stmt->bind_param("si", $finalStatus, $deviceId);
$stmt->execute();

$updated = ($stmt->affected_rows >= 0);

$stmt->close();
$conn->close();

echo json_encode([
    "success" => $updated,
    "id" => $deviceId,
    "status" => $finalStatus
]);
