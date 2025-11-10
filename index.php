<?php
session_start();

/**
 * Jika SUDAH login → lempar ke dashboard sesuai role
 * Jika BELUM login → lempar ke halaman tamu (produk_tamu.php)
 */
if (!empty($_SESSION['user'])) {
  if ($_SESSION['user']['role'] === 'admin') {
    header('Location: /jemuran_auto/frontend/admin/dashboard_admin.php');
  } else {
    header('Location: /jemuran_auto/frontend/user/dashboard_user.php');
  }
  exit;
}

// tamu (belum login) → ke halaman produk tamu
header('Location: /jemuran_auto/frontend/tamu/produk_tamu.php');
exit;
