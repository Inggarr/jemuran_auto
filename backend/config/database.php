<?php
// UBAH sesuai koneksi MySQL kamu
$DB_HOST = "localhost";
$DB_USER = "root";
$DB_PASS = "";
$DB_NAME = "db_jemuran_auto";

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
  http_response_code(500);
  die("Database connection failed: " . $conn->connect_error);
}
mysqli_set_charset($conn, 'utf8mb4');
