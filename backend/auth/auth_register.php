<?php
session_start();
require_once __DIR__ . '/../config/database.php';
header('Content-Type: application/json');

$payload = json_decode(file_get_contents("php://input"), true);

if ($payload) {
  $nama     = trim($payload['nama'] ?? '');
  $email    = trim($payload['email'] ?? '');
  $password = (string)($payload['password'] ?? '');
} else {
  $nama     = trim($_POST['nama'] ?? '');
  $email    = trim($_POST['email'] ?? '');
  $password = (string)($_POST['password'] ?? '');
}

if ($nama === '' || $email === '' || $password === '') {
  echo json_encode(["status"=>"error","message"=>"Data tidak lengkap"]);
  exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  echo json_encode(["status"=>"error","message"=>"Format email tidak valid"]);
  exit;
}

$role = 'user';
$foto_default = 'assets/uploads/profile/default.png';

$cek = $conn->prepare("SELECT 1 FROM users WHERE email = ? LIMIT 1");
$cek->bind_param("s", $email);
$cek->execute();
$cek->store_result();
if ($cek->num_rows > 0) {
  echo json_encode(["status"=>"error","message"=>"Akun sudah terdaftar"]);
  $cek->close();
  exit;
}
$cek->close();

$stmt = $conn->prepare("INSERT INTO users (nama, email, password_hash, role, foto_profile) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $nama, $email, $password, $role, $foto_default);

if ($stmt->execute()) {
  echo json_encode(["status"=>"success","message"=>"Pendaftaran berhasil"]);
} else {
  echo json_encode(["status"=>"error","message"=>"Gagal mendaftar: ".$conn->error]);
}

$stmt->close();
$conn->close();
