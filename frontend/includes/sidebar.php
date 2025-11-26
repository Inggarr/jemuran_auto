<?php
if (session_status()===PHP_SESSION_NONE) session_start();
$role = $_SESSION['user']['role'] ?? 'user';

$dash = $role==='admin' ? "../admin/dashboard_admin.php" : "../user/dashboard_user.php";
$log  = $role==='admin' ? "../admin/log_admin.php"       : "../user/log_user.php";

$BASE = "/jemuran_auto/";
?>

<!-- ===== Sidebar dengan ICON GAMBAR ===== -->
<style>
  :root{
    --sb-bg:#EBE8F9;
    --sb-hover:#DCD7F3;
    --sb-text:#000000;
    --sb-active:#4588E2;
    --sb-inactive:1;
    --hd-h:64px;
    --sb-w:260px;
    --sb-w-mini:64px;
  }

  #sidebar{
    position:fixed; left:0; top:var(--hd-h); bottom:0;
    width:var(--sb-w);
    background:var(--sb-bg);
    padding:15px 12px;
    display:flex; flex-direction:column;
    border-right:none;
    transition:transform .28s ease, width .28s ease, padding .28s ease;
    z-index:20;
  }

  #sidebar::before{
    content:""; position:absolute; left:0; right:0; top:-6px; height:6px;
    background:var(--sb-bg);
  }

  .sb-nav{
    list-style:none; margin:0; padding:0;
    display:flex; flex-direction:column; gap:6px;
  }

  .sb-item{
    position:relative;
    display:flex; align-items:center; gap:12px;
    padding:10px 14px;
    border-radius:999px;
    text-decoration:none;
    color:var(--sb-text);
    opacity:var(--sb-inactive);
    transition:background .18s ease, color .18s ease, transform .12s ease;
  }

  .sb-item:hover{
    background:var(--sb-hover);
    color:var(--sb-active);
    opacity:1;
    transform:translateX(2px);
  }

  .sb-item.active{
    background:var(--sb-hover);
    color:var(--sb-active);
    opacity:1;
    font-weight:600;
  }

  .sb-item.active::before{
    content:"";
    position:absolute;
    left:6px; top:8px; bottom:8px;
    width:3px;
    border-radius:999px;
    background:var(--sb-active);
  }

  .sb-item.disabled{
    opacity:.35;
    pointer-events:none;
  }

  .sb-foot{ margin-top:auto; }
  .sb-logout{ opacity:.75; }
  .sb-logout:hover{ opacity:1; }

  .ico{
    width:22px; height:22px;
    display:inline-block;
  }

  .ico img{
    width:100%; height:100%;
    object-fit:contain;
    filter:brightness(0) saturate(100%) invert(13%) sepia(5%) saturate(2952%) hue-rotate(225deg) brightness(95%) contrast(90%);
    transition:filter .2s;
  }

  .sb-item:hover .ico img,
  .sb-item.active .ico img{
    filter:invert(39%) sepia(71%) saturate(507%) hue-rotate(190deg) brightness(92%) contrast(95%);
  }

  #content{ margin-left:var(--sb-w); transition:margin-left .28s ease; }

  #sidebar.mini{ width:var(--sb-w-mini); padding:16px 8px; }
  #sidebar.mini .sb-text{ display:none; }
  #content.mini{ margin-left:var(--sb-w-mini); }

  @media (max-width:900px){
    #sidebar{ width:var(--sb-w-mini); padding:16px 8px; }
    #sidebar .sb-text{ display:none; }
    #content{ margin-left:var(--sb-w-mini); }
  }
</style>

<aside id="sidebar">
  <ul class="sb-nav">
    <li>
      <a href="<?= $dash ?>" class="sb-item" data-key="dashboard">
        <span class="ico">
          <img src="<?= $BASE ?>assets/home.png" alt="Dashboard">
        </span>
        <span class="sb-text">Dashboard</span>
      </a>
    </li>
    <li>
      <a href="<?= $log ?>" class="sb-item disabled" data-key="log">
        <span class="ico">
          <img src="<?= $BASE ?>assets/laptop.png" alt="Activity Log">
        </span>
        <span class="sb-text">Activity Log</span>
      </a>
    </li>
  </ul>

  <div class="sb-foot">
    <a href="../../backend/auth/auth_logout.php" class="sb-item sb-logout">
      <span class="ico">
        <img src="<?= $BASE ?>assets/logout.png" alt="Logout">
      </span>
      <span class="sb-text">Logout</span>
    </a>
  </div>
</aside>

<main id="content">

<script>
document.addEventListener("DOMContentLoaded", () => {
  const menuBtn = document.getElementById("menuBtn");
  const sidebar = document.getElementById("sidebar");
  const content = document.getElementById("content");

  const here = location.pathname.replace(/\/+$/,'').toLowerCase();
  document.querySelectorAll('#sidebar .sb-item').forEach(a=>{
    const href = (a.getAttribute('href')||'').replace(/\/+$/,'').toLowerCase();
    if(href && here === href){
      a.classList.add('active');
      a.classList.remove('disabled');
    }
  });

  let mini = window.matchMedia('(max-width:900px)').matches;
  function applyMini(){
    sidebar.classList.toggle('mini', mini);
    content && content.classList.toggle('mini', mini);
  }
  applyMini();

  if(menuBtn){
    menuBtn.addEventListener('click', () => {
      mini = !mini;
      applyMini();
    });
  }

  window.addEventListener('resize', () => {
    const shouldMini = window.matchMedia('(max-width:900px)').matches;
    if (shouldMini !== mini) {
      mini = shouldMini;
      applyMini();
    }
  });
});
</script>
