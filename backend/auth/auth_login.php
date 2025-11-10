<?php
session_start();
require_once __DIR__ . '/../config/database.php';
header('Content-Type: application/json');

$payload = json_decode(file_get_contents("php://input"), true);
if ($payload) {
  $email = trim($payload['email'] ?? '');
  $password = (string)($payload['password'] ?? '');
} else {
  $email = trim($_POST['email'] ?? '');
  $password = (string)($_POST['password'] ?? '');
}

if ($email === '' || $password === '') {
  echo json_encode(["status"=>"error","message"=>"Email dan password wajib diisi"]);
  exit;
}

$stmt = $conn->prepare("SELECT id, nama, email, password_hash, role, foto_profile FROM users WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();
$stmt->close();

if ($user && $user['password_hash'] === $password) {
  $_SESSION['user'] = [
    'id' => $user['id'],
    'nama' => $user['nama'],
    'email' => $user['email'],
    'role' => $user['role'],
    'foto' => $user['foto_profile']
  ];
  echo json_encode(["status"=>"success","message"=>"Login berhasil","role"=>$user['role']]);
} else {
  echo json_encode(["status"=>"error","message"=>"Email atau password salah"]);
}

$conn->close();
