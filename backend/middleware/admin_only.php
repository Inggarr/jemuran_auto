<?php
require_once __DIR__ . '/auth_check.php';
if ($_SESSION['user']['role'] !== 'admin') {
  header("Location: ../../frontend/user/dashboard_user.php");
  exit;
}
