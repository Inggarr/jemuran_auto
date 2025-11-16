<?php
if (session_status()===PHP_SESSION_NONE) session_start();
$role = $_SESSION['user']['role'] ?? 'user';

$dash = $role==='admin' ? "../admin/dashboard_admin.php" : "../user/dashboard_user.php";
$log  = $role==='admin' ? "../admin/log_admin.php"       : "../user/log_user.php";
?>

<!-- ===== Sidebar (tanpa tulisan Smart Clothesline) ===== -->
<style>
  :root{
    /* WARNA PALLETE */
    --sb-bg:#EBE8F9;          /* latar sidebar */
    --sb-hover:#DCD7F3;       /* hover bg */
    --sb-text:#000000;        /* teks normal (hitam) */
    --sb-active:#4588E2;      /* teks aktif (biru) */
    --sb-inactive:1;

    --hd-h:64px;              /* tinggi header */
    --sb-w:260px;             /* lebar sidebar normal */
    --sb-w-mini:64px;         /* lebar saat collapse */
  }

  /* panel */
  #sidebar{
    position:fixed; left:0; top:var(--hd-h); bottom:0;
    width:var(--sb-w);
    background:var(--sb-bg);
    padding:15px 12px;
    display:flex; flex-direction:column;
    border-right:none; /* biar gak kayak card */
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
  .sb-nav{
    list-style:none; margin:0; padding:0;
    display:flex; flex-direction:column; gap:6px;
  }

  .sb-item{
    position:relative;
    display:flex; align-items:center; gap:10px;
    padding:10px 14px;
    border-radius:999px;                 /* pill */
    text-decoration:none;
    color:var(--sb-text);
    opacity:var(--sb-inactive);
    background:transparent;
    font-size:14px;
    transition:background .18s ease, color .18s ease, transform .12s ease;
  }

  .sb-item .ico{
    width:22px; text-align:center;       /* icon kecil di kiri */
    font-size:16px;
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

  /* garis kecil indikator di kiri item aktif */
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

  /* logout di bawah */
  .sb-foot{ margin-top:auto; }
  .sb-logout{ opacity:.75; }
  .sb-logout:hover{ opacity:1; }

  /* konten offset */
  #content{
    margin-left:var(--sb-w);
    transition:margin-left .28s ease;
  }

  /* COLLAPSE */
  #sidebar.mini{
    width:var(--sb-w-mini);
    padding:16px 8px;
  }
  #sidebar.mini .sb-text{
    display:none;
  }
  #content.mini{
    margin-left:var(--sb-w-mini);
  }

  /* tablet/ponsel: mulai dari mini */
  @media (max-width:900px){
    #sidebar{ width:var(--sb-w-mini); padding:16px 8px; }
    #sidebar .sb-text{ display:none; }
    #content{ margin-left:var(--sb-w-mini); }
  }
</style>

<aside id="sidebar">
  <!-- langsung menu -->
  <ul class="sb-nav">
    <li>
      <a href="<?= $dash ?>" class="sb-item" data-key="dashboard">
        <span class="ico">🏠</span>
        <span class="sb-text">Dashboard</span>
      </a>
    </li>
    <li>
      <a href="<?= $log ?>" class="sb-item disabled" data-key="log">
        <span class="ico">📄</span>
        <span class="sb-text">Activity Log</span>
      </a>
    </li>
  </ul>

  <div class="sb-foot">
    <a href="../../backend/auth/auth_logout.php" class="sb-item sb-logout">
      <span class="ico">⏏️</span>
      <span class="sb-text">Logout</span>
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
    if(href && here === href){
      a.classList.add('active');
      a.classList.remove('disabled');
    }
  });

  /* collapse/expand */
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
