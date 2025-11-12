<?php
include_once '../config/database.php'; // panggil koneksi database

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Ambil input dari form
  $nama = trim($_POST['nama']);
  $email = trim($_POST['email']);
  $password = $_POST['password'];
  $confirm = $_POST['confirm_password'];

  // Validasi sederhana
  if (empty($nama) || empty($email) || empty($password) || empty($confirm)) {
    die("Semua field harus diisi!");
  }

  if ($password !== $confirm) {
    die("Password dan konfirmasi tidak cocok!");
  }

  // 🔍 Cek apakah email sudah terdaftar
  $cek = $conn->prepare("SELECT id FROM users WHERE email = ?");
  if (!$cek) {
    die("Query prepare gagal: " . $conn->error);
  }

  $cek->bind_param("s", $email);
  $cek->execute();
  $result = $cek->get_result();

  if ($result && $result->num_rows > 0) {
    die("Email sudah terdaftar!");
  }
  $cek->close();

  // 🔑 Hash password
  $password_hash = password_hash($password, PASSWORD_DEFAULT);

  // 🔹 Masukkan data ke tabel users
  $insert = $conn->prepare("INSERT INTO users (nama, email, password_hash, role, foto_profile) VALUES (?, ?, ?, 'user', NULL)");
  if (!$insert) {
    die("Query prepare gagal (insert): " . $conn->error);
  }

  $insert->bind_param("sss", $nama, $email, $password_hash);

  if ($insert->execute()) {
    echo "Registrasi berhasil!";
    header("Location: ../../frontend/auth/login.php");
    exit;
  } else {
    die("Gagal registrasi: " . $insert->error);
  }

  $insert->close();
}

$conn->close();
?>
