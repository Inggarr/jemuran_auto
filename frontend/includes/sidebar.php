<?php
if (session_status()===PHP_SESSION_NONE) session_start();
$role = $_SESSION['user']['role'] ?? 'user';
$dash = $role==='admin' ? "../admin/dashboard_admin.php" : "../user/dashboard_user.php";
$log  = $role==='admin' ? "../admin/log_admin.php"       : "../user/log_user.php";
?>

<!-- ===== Sidebar (tanpa tulisan Smart Clothesline) ===== -->
<style>
  :root{
    --sb-bg:#c8c1ff;       /* ungu lembut */
    --sb-hover:#bdb3ff;
    --sb-text:#ffffff;
    --sb-inactive:.55;
    --hd-h:64px;           /* tinggi header */
    --sb-w:260px;          /* lebar sidebar normal */
    --sb-w-mini:64px;      /* lebar saat collapse */
  }

  /* panel */
  #sidebar{
    position:fixed; left:0; top:var(--hd-h); bottom:0;
    width:var(--sb-w);
    background:var(--sb-bg);
    padding:16px 12px;
    display:flex; flex-direction:column;
    border-right:1px solid #e9e4ff;
    transform:translateX(0);
    transition:transform .28s ease, width .28s ease, padding .28s ease;
    z-index:20;
  }

  /* biar nyambung ke header */
  #sidebar::before{
    content:""; position:absolute; left:0; right:0; top:-6px; height:6px;
    background:var(--sb-bg);
  }

  /* nav list */
  .sb-nav{ list-style:none; margin:0; padding:0; display:flex; flex-direction:column; gap:8px; }
  .sb-item{
    display:flex; align-items:center; gap:12px;
    padding:12px 14px; border-radius:16px; text-decoration:none;
    color:var(--sb-text); opacity:var(--sb-inactive);
  }
  .sb-item .ico{ width:20px; text-align:center; }
  .sb-item:hover{ background:var(--sb-hover); opacity:1; }
  .sb-item.active{ background:var(--sb-hover); opacity:1; font-weight:700; }
  .sb-item.disabled{ opacity:.35; pointer-events:none; }

  /* logout di bawah */
  .sb-foot{ margin-top:auto; }
  .sb-logout{ opacity:.75; }
  .sb-logout:hover{ opacity:1; }

  /* konten offset */
  #content{ margin-left:var(--sb-w); transition:margin-left .28s ease; }

  /* COLLAPSE */
  #sidebar.mini{ width:var(--sb-w-mini); padding:16px 8px; }
  #sidebar.mini .sb-text{ display:none; }
  #content.mini{ margin-left:var(--sb-w-mini); }

  /* tablet/ponsel: mulai dari mini */
  @media (max-width:900px){
    #sidebar{ width:var(--sb-w-mini); padding:16px 8px; }
    #sidebar .sb-text{ display:none; }
    #content{ margin-left:var(--sb-w-mini); }
  }
</style>

<aside id="sidebar">
  <!-- (hapus brand di sini, langsung mulai menu) -->
  <ul class="sb-nav">
    <li>
      <a href="<?= $dash ?>" class="sb-item" data-key="dashboard">
        <span class="ico"></span><span class="sb-text">Dashboard</span>
      </a>
    </li>
    <li>
      <a href="<?= $log ?>" class="sb-item disabled" data-key="log">
        <span class="ico"></span><span class="sb-text">Activity Log</span>
      </a>
    </li>
  </ul>

  <div class="sb-foot">
    <a href="../../backend/auth/auth_logout.php" class="sb-item sb-logout">
      <span class="ico"></span><span class="sb-text">Logout</span>
    </a>
  </div>
</aside>

<!-- konten -->
<main id="content">

<script>
document.addEventListener("DOMContentLoaded", () => {
  const menuBtn = document.getElementById("menuBtn");
  const sidebar = document.getElementById("sidebar");
  const content = document.getElementById("content");

  /* tandai active otomatis berdasar URL */
  const here = location.pathname.replace(/\/+$/,'').toLowerCase();
  document.querySelectorAll('#sidebar .sb-item').forEach(a=>{
    const href = (a.getAttribute('href')||'').replace(/\/+$/,'').toLowerCase();
    if(href && here === href){ a.classList.add('active'); a.classList.remove('disabled'); }
  });

  /* collapse/expand */
  let mini = window.matchMedia('(max-width:900px)').matches;
  function applyMini(){ sidebar.classList.toggle('mini', mini); content.classList.toggle('mini', mini); }
  applyMini();

  if(menuBtn){
    menuBtn.addEventListener('click', () => { mini = !mini; applyMini(); });
  }

  window.addEventListener('resize', () => {
    const shouldMini = window.matchMedia('(max-width:900px)').matches;
    if (shouldMini !== mini) { mini = shouldMini; applyMini(); }
  });
});
</script>
