<?php
if (session_status()===PHP_SESSION_NONE) session_start();
$role = $_SESSION['user']['role'] ?? 'user';
$dash = $role==='admin' ? "../admin/dashboard_admin.php" : "../user/dashboard_user.php";
$log  = $role==='admin' ? "../admin/log_admin.php"       : "../user/log_user.php";
?>
<aside id="sidebar" style="width:220px;background:#1f2937;color:#e5e7eb;padding:20px 10px;
  position:fixed;top:60px;left:0;height:calc(100vh - 60px);
  transform:translateX(0);transition:transform .3s ease;z-index:20;">
  <ul style="list-style:none;margin:0;padding:0;display:grid;gap:10px;">
    <li><a href="<?= $dash ?>" style="display:flex;align-items:center;gap:8px;
        padding:10px 12px;background:#374151;border-radius:8px;color:#e5e7eb;text-decoration:none;">🏠 Dashboard</a></li>
    <li><a href="<?= $log ?>" style="display:flex;align-items:center;gap:8px;
        padding:10px 12px;background:#374151;border-radius:8px;color:#e5e7eb;text-decoration:none;">📜 Log</a></li>
    <li><a href="../../backend/auth/auth_logout.php" style="display:flex;align-items:center;gap:8px;
        padding:10px 12px;background:#b91c1c;border-radius:8px;color:#fff;text-decoration:none;">🚪 Logout</a></li>
  </ul>
</aside>

<main id="content" style="flex:1;margin-left:220px;padding:20px;transition:margin-left .3s ease;">
<script>
document.addEventListener("DOMContentLoaded", () => {
  const menuBtn = document.getElementById("menuBtn");
  const sidebar = document.getElementById("sidebar");
  const content = document.getElementById("content");
  if(!menuBtn || !sidebar || !content) return;

  let open = true;
  menuBtn.addEventListener("click", () => {
    open = !open;
    if(open){
      sidebar.style.transform = "translateX(0)";
      content.style.marginLeft = "220px";
    } else {
      sidebar.style.transform = "translateX(-240px)";
      content.style.marginLeft = "0";
    }
  });
});
</script>
