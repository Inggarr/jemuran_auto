<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$namaUser = $_SESSION['user']['nama'] ?? 'User';
$fotoUser = $_SESSION['user']['foto_profile'] ?? 'user_default.jpg';
if (!isset($_SESSION['user'])) {
    header("Location: /jemuran_auto/frontend/auth/login.php");
    exit;
}

require_once __DIR__ . '/../../backend/config/database.php';

// Ambil data user
$user = $_SESSION['user'];
$namaUser = $user['nama'] ?? "Admin";

$BASE = "/jemuran_auto/";

/* ===== AMBIL DATA DEVICE ADMIN ===== */
$sql = "
    SELECT d.id, d.nama_device, d.status, u.nama AS username
    FROM devices d
    JOIN users u ON d.user_id = u.id
    ORDER BY d.id ASC
";
$res = $conn->query($sql);
$devices = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

$total = count($devices);
$active = count(array_filter($devices, fn($d) => $d['status'] === 'open'));
$offline = $total - $active;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard Admin - Smart Clothesline</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>

  <style>
    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: #f7f4ff;
      color: #2b2140;
      overflow-x: hidden;
    }
    a { text-decoration: none; color: inherit; }

    /* ====== LAYOUT ====== */
    .layout { display: flex; min-height: 100vh; }

    /* ============ SIDEBAR ============ */
    .sidebar {
      width: 260px;
      background: #f0e9ff;
      border-right: 1px solid rgba(0,0,0,0.04);
      display: flex;
      flex-direction: column;
      padding: 18px 18px 12px;
      position: sticky;
      top: 0;
      height: 100vh;
      transition: width 0.25s ease;
      overflow: hidden;
    }

    .sidebar.collapsed {
      width: 76px;
    }

    .sidebar-header {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 24px;
    }

    .burger {
      width: 34px;
      height: 34px;
      border-radius: 10px;
      border: 1px solid #d1c5ff;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      background: white;
      flex-shrink: 0;
    }

    .burger span {
      width: 16px;
      height: 2px;
      background: #5b3fd0;
      border-radius: 999px;
      position: relative;
    }

    .burger span::before,
    .burger span::after {
      content: "";
      position: absolute;
      left: 0;
      width: 16px;
      height: 2px;
      background: #5b3fd0;
      border-radius: 999px;
    }

    .burger span::before { top: -5px; }
    .burger span::after { top: 5px; }

    .brand {
      display: flex;
      flex-direction: column;
      gap: 2px;
      overflow: hidden;
      transition: opacity 0.2s ease;
    }

    .brand-title {
      font-weight: 700;
      font-size: 18px;
      color: #4a2a9d;
      white-space: nowrap;
    }

    .brand-sub {
      font-size: 11px;
      color: #9b88d9;
      white-space: nowrap;
    }

    .sidebar.collapsed .brand {
      opacity: 0;
      pointer-events: none;
    }

    .sidebar-menu {
      margin-top: 10px;
      display: flex;
      flex-direction: column;
      gap: 6px;
      flex: 1;
    }

    .menu-item {
      padding: 10px 12px;
      border-radius: 10px;
      font-size: 14px;
      display: flex;
      align-items: center;
      gap: 10px;
      color: #5a4a8f;
      cursor: pointer;
      transition: background 0.2s ease, transform 0.1s ease;
      white-space: nowrap;
    }

    .menu-item span.icon {
      width: 22px;
      height: 22px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .menu-item span.icon svg {
      width: 22px;
      height: 22px;
      stroke: #5a4a8f;
    }

    .menu-item.active span.icon svg {
      stroke: #3a257e;
    }

    .menu-item span.text {
      opacity: 1;
      transition: opacity 0.2s ease;
    }

    .sidebar.collapsed .menu-item span.text {
      opacity: 0;
      pointer-events: none;
    }

    .menu-item.active {
      background: #e1d6ff;
      font-weight: 600;
      color: #3a257e;
    }

    .menu-item:hover {
      background: #e9e1ff;
      transform: translateY(-1px);
    }

    .menu-item.disabled {
      opacity: 0.4;
      cursor: default;
    }

    .sidebar-footer {
      margin-top: 12px;
      font-size: 13px;
      transition: opacity 0.2s ease;
    }

    .sidebar.collapsed .sidebar-footer {
      opacity: 0;
      pointer-events: none;
    }

    .logout-btn {
      margin-top: 12px;
      padding: 10px 12px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 14px;
      color: #aa3c5f;
      cursor: pointer;
      background: #fbe8f0;
      border: 1px solid #f1cada;
      transition: 0.2s;
    }

    .logout-btn:hover {
      background: #f7d7e4;
      transform: translateY(-1px);
    }

    /* ====== MAIN CONTENT ====== */
    .dashboard-container { flex: 1; padding: 30px 50px; }

    /* ====== TOPBAR ====== */
    .topbar {
      display: flex; justify-content: flex-end; align-items: center;
      gap: 18px; margin-bottom: 25px;
    }
    .top-badge {
      padding: 6px 10px; border-radius: 999px;
      background: rgba(255,255,255,0.9);
      box-shadow: 0 2px 6px rgba(0,0,0,0.05);
      font-size: 11px; color: #8870e0;
    }
    .icon-bell {
      width: 32px; height: 32px; border-radius: 999px;
      background: #f7f2ff;
      display: flex; align-items: center; justify-content: center;
      font-size: 16px; color: #8b69ec;
      cursor: pointer; position: relative;
    }
    .icon-bell[data-count]::after {
      content: attr(data-count);
      min-width: 18px; height: 18px; padding: 2px 6px;
      background: #ff6b9a; color: white;
      font-size: 11px; border-radius: 999px;
      position: absolute; top: -6px; right: -8px;
      display: flex; align-items: center; justify-content: center;
    }
    .user-pill {
      display: flex; align-items: center; gap: 10px;
      padding: 4px 10px 4px 4px; border-radius: 999px;
      background: rgba(255,255,255,0.95);
      box-shadow: 0 2px 6px rgba(0,0,0,0.05);
      cursor: pointer;
    }
    .user-avatar {
      width: 34px; height: 34px; border-radius: 999px;
      background: linear-gradient(135deg,#f9a8ff,#8b5cf6);
      display: flex; align-items: center; justify-content: center;
      color: white; font-size: 16px; font-weight: 600;
    }
    .user-info small { font-size: 10px; color: #9c8bd3; }
    .user-info div { font-size: 13px; font-weight: 500; color: #453069; }

    /* ====== CARD & TABLE ====== */
    .stats-row { display: flex; gap: 25px; justify-content: space-between; flex-wrap: nowrap; margin-bottom: 30px; }
    .stat-card {
      flex: 1; background: #f9f5ff; border-radius: 20px;
      padding: 25px; border: 2px solid #e0caff;
      text-align: left; transition: 0.3s;
    }
    .stat-card h3 { font-size: 15px; font-weight: 600; color: #6b5a8e; margin: 0; }
    .stat-card p { font-size: 42px; font-weight: 700; color: #2b0f53; margin: 8px 0 0; }
    .stat-card.total { border-color: #d8c7ff; }
    .stat-card.online { border-color: #4a41ff; background: #edf0ff; }
    .stat-card.offline { border-color: #b9a0ff; }

    .table-box {
      background: #fff; border-radius: 15px; padding: 20px;
      box-shadow: 0 6px 20px rgba(80,60,160,0.08);
      overflow-x: auto;
    }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 15px 10px; border-bottom: 1px solid #eee; }
    th { color: #6b5a8e; font-weight: 600; }

    /* ====== TOGGLE ====== */
    .toggle-switch {
      position: relative; width: 70px; height: 32px;
      background: #ADB1EF; border-radius: 50px;
      cursor: pointer; transition: .3s;
    }
    .toggle-slider {
      position: absolute; top: 3px; left: 3px;
      width: 26px; height: 26px; background: #fff; border-radius: 50%;
      transition: .3s;
    }
    .toggle-switch.on { background: #7b7ce0; }
    .toggle-switch.on .toggle-slider { left: 41px; }
    .toggle-label { font-weight: 600; width: 50px; }

    .toggle-loader {
      position: absolute; top: 50%; left: 50%;
      width: 18px; height: 18px; margin-left: -9px; margin-top: -9px;
      border: 2px solid rgba(255,255,255,0.6);
      border-top-color: #4a41ff; border-radius: 50%;
      animation: spin .6s linear infinite; display: none; z-index: 10;
    }
    .toggle-switch.loading { pointer-events: none; opacity: 0.6; }
    @keyframes spin { 0%{transform:rotate(0);}100%{transform:rotate(360deg);} }
  </style>
</head>
<body>

<div class="layout">
  <!-- SIDEBAR -->
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
      <div class="burger" id="sidebarToggle">
        <span></span>
      </div>
      <div class="brand">
  <div class="brand-title" style="display:flex; align-items:center; gap:6px; font-size:20px; font-weight:700; color:#4a2a9d;">
      Smart
      <!-- Ikon Matahari Ungu -->
      <svg width="22" height="22" viewBox="0 0 24 24" fill="#b366ff">
        <circle cx="12" cy="12" r="5" fill="#c084fc"></circle>
        <g stroke="#c084fc" stroke-width="2">
          <line x1="12" y1="1" x2="12" y2="4"/>
          <line x1="12" y1="20" x2="12" y2="23"/>
          <line x1="1" y1="12" x2="4" y2="12"/>
          <line x1="20" y1="12" x2="23" y2="12"/>
          <line x1="4.2" y1="4.2" x2="6.4" y2="6.4"/>
          <line x1="17.6" y1="17.6" x2="19.8" y2="19.8"/>
          <line x1="4.2" y1="19.8" x2="6.4" y2="17.6"/>
          <line x1="17.6" y1="6.4" x2="19.8" y2="4.2"/>
        </g>
      </svg>
  </div>

  <div style="font-size:20px; font-weight:700; color:#4a2a9d; margin-top:-4px;">
      Clothesline
  </div>

  <div class="brand-sub">Admin Dashboard</div>
</div>

    </div>

    <div class="sidebar-menu">
      <!-- DASHBOARD -->
      <div class="menu-item active">
        <span class="icon">
          <!-- Heroicons Home -->
          <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 10.5L12 3l9 7.5"></path>
            <path d="M5.25 9.75V19.5A1.75 1.75 0 007 21.25h3.25v-5.5H13.5v5.5H17A1.75 1.75 0 0018.75 19.5V9.75"></path>
          </svg>
        </span>
        <span class="text">Dashboard</span>
      </div>

      <!-- PROFIL -->
      <div class="menu-item" onclick="window.location.href='profile_admin.php'">
        <span class="icon">
          <!-- Heroicons User -->
          <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M15.75 8.25a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"></path>
            <path d="M4.5 20.25a7.5 7.5 0 0115 0"></path>
          </svg>
        </span>
        <span class="text">Profil</span>
      </div>

      <!-- NOTIF -->
      <div class="menu-item" onclick="window.location.href='notif.php'">
        <span class="icon">
          <!-- Heroicons Bell -->
          <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14.25 18.75a2.25 2.25 0 11-4.5 0"></path>
            <path d="M4.5 9.75A7.5 7.5 0 0119.5 9.75c0 3.273.878 4.348 1.5 5.25H3c.622-.902 1.5-1.977 1.5-5.25z"></path>
          </svg>
        </span>
        <span class="text">Notifikasi</span>
      </div>

      <!-- ACTIVITY LOG (disabled) -->
      <div class="menu-item disabled">
        <span class="icon">
          <!-- Heroicons Clock -->
          <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 3.75A8.25 8.25 0 1120.25 12 8.25 8.25 0 0112 3.75z"></path>
            <path d="M12 7.5v4.5l2.25 2.25"></path>
          </svg>
        </span>
        <span class="text">Activity Log</span>
      </div>
    </div>

    <div class="sidebar-footer">
      <div style="font-size:11px;color:#9b8ad0;">Masuk sebagai</div>
      <div style="font-size:13px;font-weight:500;color:#5b3fd0;">
        <?php echo htmlspecialchars($namaUser); ?>
      </div>

      <a href="/jemuran_auto/backend/auth/auth_logout.php">
        <div class="logout-btn">
          <span>📤</span>
          <span>Logout</span>
        </div>
      </a>
    </div>
  </aside>
  <!-- MAIN CONTENT -->
  <div class="dashboard-container">

    <!-- ✅ TOPBAR DIPINDAH KE SINI -->
    <div class="topbar">
      <div class="top-badge">Mode monitoring • Auto refresh aktif</div>
      <div class="icon-bell" id="bellNotif" onclick="window.location.href='notif_admin.php'">🔔</div>
      <div class="user-pill" onclick="window.location.href='profile_admin.php'">
        <div class="user-avatar">
        <?php if ($fotoUser): ?>
            <img src="/jemuran_auto/assets/uploads/profile/<?= $fotoUser ?>?v=<?= time(); ?>"
     style="width:100%;height:100%;border-radius:999px;object-fit:cover;">
        <?php else: ?>
            <?= strtoupper(substr($namaUser, 0, 1)); ?>
        <?php endif; ?>
    </div>
        <div class="user-info">
          <small>Halo,</small>
          <div><?php echo htmlspecialchars($namaUser); ?></div>
        </div>
      </div>
    </div>

    <h1 class="page-title">Ringkasan Perangkat</h1>
    <p class="page-subtitle">Pantau seluruh perangkat Smart Clothesline</p>

    <div class="stats-row">
      <div class="stat-card total"><h3>Total Perangkat</h3><p id="stat-total"><?= $total ?></p></div>
      <div class="stat-card online"><h3>Perangkat Aktif</h3><p id="stat-active"><?= $active ?></p></div>
      <div class="stat-card offline"><h3>Perangkat Offline</h3><p id="stat-offline"><?= $offline ?></p></div>
    </div>

    <div class="table-box">
      <h3>Daftar Perangkat Pengguna</h3>
      <table>
        <thead><tr><th>Username</th><th>Device</th><th>Control</th></tr></thead>
        <tbody id="device-table-body">
          <?php if (empty($devices)): ?>
            <tr><td colspan="3" style="text-align:center;color:#888;">Tidak ada perangkat.</td></tr>
          <?php else: foreach ($devices as $d): ?>
            <tr>
              <td><?= htmlspecialchars($d['username']) ?></td>
              <td><?= htmlspecialchars($d['nama_device']) ?></td>
              <td>
                <div style="display:flex;align-items:center;gap:10px;">
                  <span class="toggle-label"><?= $d['status'] === 'open' ? 'ON' : 'OFF' ?></span>
                  <div class="toggle-switch <?= $d['status'] === 'open' ? 'on' : '' ?>" data-id="<?= $d['id'] ?>">
                    <div class="toggle-slider"></div>
                    <div class="toggle-loader"></div>
                  </div>
                </div>
              </td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
document.getElementById("sidebarToggle").addEventListener("click",()=> {
  document.getElementById("sidebar").classList.toggle("collapsed");
});

// Auto refresh data + toggle logic
document.addEventListener("DOMContentLoaded",()=>{initToggleButtons();startAutoRefresh();});
function showError(msg){const b=document.createElement('div');b.textContent=msg;}
function initToggleButtons(){
  document.querySelectorAll(".toggle-switch").forEach(el=>{
    const clone=el.cloneNode(true);el.replaceWith(clone);
    clone.addEventListener("click",()=>handleToggle(clone));
  });
}
function adjustCounters(delta){
  const a=document.getElementById("stat-active"),o=document.getElementById("stat-offline");
  let n=parseInt(a.textContent);n+=delta;a.textContent=n;
  o.textContent=parseInt(document.getElementById("stat-total").textContent)-n;
}
async function handleToggle(el){
  const label=el.parentElement.querySelector(".toggle-label"),loader=el.querySelector(".toggle-loader"),id=el.dataset.id;
  const prevOn=el.classList.contains("on"),willBeOn=!prevOn,newState=willBeOn?"open":"close";
  el.classList.toggle("on");el.classList.add("loading");label.textContent=willBeOn?"ON":"OFF";loader.style.display="block";
  adjustCounters(willBeOn?1:-1);
  try{
    const res=await fetch("/jemuran_auto/backend/device/device_control.php",{method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify({id,status:newState})});
    const data=await res.json();if(!data.success)throw new Error();
  }catch{
    el.classList.toggle("on");adjustCounters(willBeOn?-1:1);label.textContent=prevOn?"ON":"OFF";
  }finally{el.classList.remove("loading");loader.style.display="none";}
}
function startAutoRefresh(){
  setInterval(async()=>{
    try{
      const res=await fetch("/jemuran_auto/backend/device/get_device.php");
      const data=await res.json();
      const total=data.length,active=data.filter(x=>x.status==="open").length;
      document.getElementById("stat-total").textContent=total;
      document.getElementById("stat-active").textContent=active;
      document.getElementById("stat-offline").textContent=total-active;
      document.querySelectorAll("#device-table-body tr").forEach(tr=>{
        const toggle=tr.querySelector(".toggle-switch");if(!toggle||toggle.classList.contains("loading"))return;
        const device=data.find(x=>x.id==toggle.dataset.id),label=tr.querySelector(".toggle-label");if(!device)return;
        if(device.status==="open"){toggle.classList.add("on");label.textContent="ON";}else{toggle.classList.remove("on");label.textContent="OFF";}
      });
    }catch{}
  },5000);
}
</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
