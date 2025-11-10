<?php
session_start();
require_once __DIR__ . '/../config/database.php';
header('Content-Type: application/json');

$user_id = $_SESSION['user']['id'] ?? null;
if (!$user_id) {
  echo json_encode(["status" => "error", "message" => "Unauthorized"]);
  exit;
}

$nama = trim($_POST['nama'] ?? '');
$foto_path = null;

if (empty($nama)) {
  echo json_encode(["status" => "error", "message" => "Nama tidak boleh kosong"]);
  exit;
}

// Upload foto jika ada
if (!empty($_FILES['foto']['name'])) {
  $targetDir = "../../assets/uploads/profile/";
  $fileName = time() . "_" . basename($_FILES['foto']['name']);
  $targetFilePath = $targetDir . $fileName;

  if (move_uploaded_file($_FILES["foto"]["tmp_name"], $targetFilePath)) {
    $foto_path = "assets/uploads/profile/" . $fileName;
  } else {
    echo json_encode(["status" => "error", "message" => "Gagal upload foto"]);
    exit;
  }
}

// Update ke database
if ($foto_path) {
  $stmt = $conn->prepare("UPDATE users SET nama = ?, foto_profile = ? WHERE id = ?");
  $stmt->bind_param("ssi", $nama, $foto_path, $user_id);
} else {
  $stmt = $conn->prepare("UPDATE users SET nama = ? WHERE id = ?");
  $stmt->bind_param("si", $nama, $user_id);
}

if ($stmt->execute()) {
  $_SESSION['user']['nama'] = $nama;
  if ($foto_path) $_SESSION['user']['foto'] = $foto_path;
  echo json_encode(["status" => "success", "message" => "Profil berhasil diperbarui"]);
} else {
  echo json_encode(["status" => "error", "message" => "Gagal memperbarui profil"]);
}

$stmt->close();
$conn->close();
