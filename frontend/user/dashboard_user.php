<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$namaUser = $_SESSION['user']['nama'] ?? 'User';
$fotoUser = $_SESSION['user']['foto_profile'] ?? 'user_default.jpg';

// Biar aman, coba ambil dari $_SESSION['user'] dulu, kalau nggak ada baru fallback ke $_SESSION['nama']
if (isset($_SESSION['user']['nama'])) {
    $namaUser = $_SESSION['user']['nama'];
} else {
    $namaUser = $_SESSION['nama'] ?? 'User';
}

// Role kalau sewaktu-waktu mau dipakai
$roleUser = $_SESSION['role'] ?? ($_SESSION['user']['role'] ?? 'user');
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard User - Smart Clothesline</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

  <!-- Lottie Player untuk animasi -->
  <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>

  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: #f7f4ff;
      color: #2b2140;
      overflow-x: hidden;
    }

    a {
      text-decoration: none;
      color: inherit;
    }

    /* =================== LAYOUT =================== */
    .layout {
      display: flex;
      min-height: 100vh;
    }

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

    /* ============ MAIN ============ */
    .main {
      flex: 1;
      display: flex;
      flex-direction: column;
    }

    .topbar {
      display: flex;
      justify-content: flex-end;
      align-items: center;
      padding: 18px 30px 10px;
      gap: 18px;
    }

    .top-badge {
      padding: 6px 10px;
      border-radius: 999px;
      background: rgba(255,255,255,0.9);
      box-shadow: 0 2px 6px rgba(0,0,0,0.05);
      font-size: 11px;
      color: #8870e0;
    }

    .icon-bell {
      width: 32px;
      height: 32px;
      border-radius: 999px;
      background: #f7f2ff;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 16px;
      color: #8b69ec;
      cursor: pointer;
      position: relative;
    }

    /* 🔔 BADGE NOTIF (hanya muncul jika ada data-count) */
    .icon-bell[data-count]::after {
      content: attr(data-count);
      min-width: 18px;
      height: 18px;
      padding: 2px 6px;
      background: #ff6b9a;
      color: white;
      font-size: 11px;
      border-radius: 999px;
      position: absolute;
      top: -6px;
      right: -8px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .user-pill {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 4px 10px 4px 4px;
      border-radius: 999px;
      background: rgba(255,255,255,0.95);
      box-shadow: 0 2px 6px rgba(0,0,0,0.05);
      cursor: pointer;
    }

    .user-avatar {
      width: 34px;
      height: 34px;
      border-radius: 999px;
      background: linear-gradient(135deg,#f9a8ff,#8b5cf6);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 16px;
      font-weight: 600;
    }

    .user-info small {
      font-size: 10px;
      color: #9c8bd3;
    }

    .user-info div {
      font-size: 13px;
      font-weight: 500;
      color: #453069;
    }

    .content {
      padding: 10px 40px 30px;
    }

    .page-title {
      font-size: 18px;
      font-weight: 600;
      margin-bottom: 18px;
      color: #433164;
    }

    .dashboard-grid {
      display: grid;
      grid-template-columns: 2.2fr 1.1fr;
      gap: 24px;
      align-items: flex-start;
    }

    /* ===== STATUS CARD ===== */
    .status-card {
      background: #ffffff;
      border-radius: 22px;
      padding: 26px 28px;
      box-shadow: 0 10px 25px rgba(137, 95, 255, 0.15);
      display: grid;
      grid-template-columns: 1.4fr 1fr;
      align-items: center;
      gap: 14px;
    }

    .status-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 6px 10px;
      border-radius: 999px;
      background: #f2ecff;
      color: #7d5bf2;
      font-size: 11px;
      margin-bottom: 8px;
    }

    .status-label {
      font-size: 52px;
      font-weight: 800;
      color: #2b124d;
      margin-bottom: 8px;
    }

    .status-subtext {
      font-size: 13px;
      color: #7d749a;
    }

    .status-actions {
      margin-top: 24px;
      display: flex;
      gap: 14px;
      flex-wrap: wrap;
    }

    .btn-pill {
      border-radius: 999px;
      padding: 12px 26px;
      border: none;
      cursor: pointer;
      font-size: 15px;
      font-weight: 600;
      transition: 0.2s;
      box-shadow: 0 8px 20px rgba(0,0,0,0.12);
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }

    .btn-open {
      background: linear-gradient(135deg,#a5b4ff,#60a5fa);
      color: white;
    }

    .btn-close {
      background: linear-gradient(135deg,#e9d5ff,#f9a8d4);
      color: #4b164c;
    }

    .btn-open:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 22px rgba(96,165,250,0.35);
    }

    .btn-close:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 22px rgba(244,114,182,0.35);
    }

    .status-illustration {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 10px;
    }

    .status-chip {
      font-size: 12px;
      padding: 6px 12px;
      border-radius: 999px;
      background: #f4eeff;
      color: #7c6bf6;
    }

    @keyframes float {
      0% { transform: translateY(0); }
      50% { transform: translateY(-6px); }
      100% { transform: translateY(0); }
    }

    /* ===== SIDE PANEL ===== */
    .side-panel {
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    .panel-card {
      background: #ffffff;
      border-radius: 18px;
      padding: 16px 18px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.05);
      font-size: 13px;
    }

    .panel-title {
      font-size: 13px;
      font-weight: 600;
      margin-bottom: 10px;
      color: #433164;
    }

    .mode-toggle {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 14px;
    }

    .mode-labels {
      display: flex;
      flex-direction: column;
      gap: 4px;
    }

    .mode-status {
      font-weight: 600;
      color: #433164;
    }

    .mode-desc {
      font-size: 11px;
      color: #8276a3;
    }

    .last-update {
      font-size: 11px;
      color: #9a8ec2;
      margin-top: 4px;
    }

    .switch {
      position: relative;
      width: 56px;
      height: 30px;
      flex-shrink: 0;
    }

    .switch input {
      opacity: 0;
      width: 0;
      height: 0;
    }

    .slider {
      position: absolute;
      cursor: pointer;
      inset: 0;
      background: #e4ddff;
      border-radius: 999px;
      transition: 0.3s;
    }

    .slider::before {
      content: "";
      position: absolute;
      height: 22px;
      width: 22px;
      left: 5px;
      top: 4px;
      background: white;
      border-radius: 50%;
      box-shadow: 0 3px 8px rgba(0,0,0,0.25);
      transition: 0.3s;
    }

    .switch input:checked + .slider {
      background: linear-gradient(135deg,#a855f7,#6366f1);
    }

    .switch input:checked + .slider::before {
      transform: translateX(22px);
    }

    .activity-list {
      font-size: 12px;
      color: #5e547e;
    }

    .activity-item {
      margin-bottom: 6px;
      display: flex;
      justify-content: space-between;
      gap: 10px;
    }

    .activity-item span.time {
      color: #b09ad8;
      font-size: 11px;
    }

    /* ===== SENSOR ===== */
    .sensor-section {
      margin-top: 24px;
      display: grid;
      grid-template-columns: 1.3fr 1.1fr;
      gap: 20px;
    }

    .sensor-grid {
      display: grid;
      grid-template-columns: repeat(3, minmax(0,1fr));
      gap: 14px;
    }

    .sensor-card {
      background: #ffffff;
      border-radius: 16px;
      padding: 14px 14px 16px;
      box-shadow: 0 6px 18px rgba(0,0,0,0.06);
      display: flex;
      flex-direction: column;
      gap: 4px;
    }

    .sensor-label {
      font-size: 11px;
      color: #9a8fc3;
    }

    .sensor-value {
      font-size: 22px;
      font-weight: 700;
      color: #371d72;
    }

    .sensor-icon {
      font-size: 22px;
      margin-bottom: 4px;
    }

    .sensor-status {
      font-size: 11px;
      color: #746a9e;
    }

    .hint-card {
      background: #f7f0ff;
      border-radius: 16px;
      padding: 16px 18px;
      font-size: 12px;
      color: #5d4a99;
      box-shadow: 0 6px 16px rgba(0,0,0,0.04);
    }

    .hint-card strong {
      font-weight: 600;
    }

    /* Responsive */
    @media (max-width: 992px) {
      .layout {
        flex-direction: column;
      }
      .sidebar {
        position: relative;
        width: 100%;
        height: auto;
        flex-direction: row;
        align-items: center;
        gap: 10px;
      }
      .sidebar.collapsed {
        width: 100%;
      }
      .sidebar-menu {
        display: none;
      }
      .sidebar-footer {
        display: none;
      }
      .content {
        padding: 10px 18px 30px;
      }
      .dashboard-grid {
        grid-template-columns: 1fr;
      }
      .sensor-section {
        grid-template-columns: 1fr;
      }
    }
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

  <div class="brand-sub">User Dashboard</div>
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
      <div class="menu-item" onclick="window.location.href='profile_user.php'">
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

  <!-- MAIN -->
  <div class="main">
    <div class="topbar">
      <div class="top-badge">Mode aman • Auto close saat hujan</div>

      <!-- 🔔 Icon Notif dengan badge -->
      <div class="icon-bell" id="bellNotif" onclick="window.location.href='notif.php'">🔔</div>

      <div class="user-pill" onclick="window.location.href='profile_user.php'">

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
        <div><?= htmlspecialchars($namaUser) ?></div>
    </div>

</div>
    </div>

    <div class="content">
      <div class="page-title">Dashboard Jemuran</div>

      <div class="dashboard-grid">
        <!-- STATUS JEMURAN -->
        <div class="status-card">
          <div>
            <div class="status-badge">
              <span id="status-badge-text">Status Jemuran</span>
            </div>
            <div class="status-label" id="status-label">Tertutup</div>
            <div class="status-subtext" id="status-subtext">
              Jemuran saat ini dalam kondisi tertutup untuk melindungi cucian dari hujan.
            </div>

            <div class="status-actions">
              <button class="btn-pill btn-open" id="btn-open">
                <span>⬆</span> Buka Jemuran
              </button>
              <button class="btn-pill btn-close" id="btn-close">
                <span>⬇</span> Tutup Jemuran
              </button>
            </div>
          </div>

          <div class="status-illustration">
            <!-- Lottie animation -->
            <lottie-player
              id="status-emoji"
              src=""
              background="transparent"
              speed="1"
              style="width: 90px; height: 90px;"
              loop
              autoplay>
            </lottie-player>
            <div class="status-chip" id="status-chip">Cuaca: Hujan Ringan</div>
          </div>
        </div>

        <!-- PANEL KANAN -->
        <div class="side-panel">
          <div class="panel-card">
            <div class="panel-title">Mode Kendali</div>
            <div class="mode-toggle">
              <div class="mode-labels">
                <div class="mode-status" id="mode-status">Manual</div>
                <div class="mode-desc" id="mode-desc">
                  Tombol buka/tutup aktif. Saat mode otomatis ON, tombol manual akan terkunci.
                </div>
                <div class="last-update" id="last-update">
                  Terakhir diperbarui: -
                </div>
              </div>
              <label class="switch">
                <input type="checkbox" id="mode-auto-toggle">
                <span class="slider"></span>
              </label>
            </div>
          </div>

          <div class="panel-card">
            <div class="panel-title">Ringkasan Aktivitas Terakhir</div>
            <div class="activity-list" id="activity-list">
              <div class="activity-item">
                <span>Jemuran ditutup otomatis karena hujan</span>
                <span class="time">Baru saja</span>
              </div>
              <div class="activity-item">
                <span>Mode otomatis diaktifkan</span>
                <span class="time">5 menit lalu</span>
              </div>
              <div class="activity-item">
                <span>Pengguna buka jemuran secara manual</span>
                <span class="time">Hari ini • 09:12</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- SENSOR -->
      <div class="sensor-section">
        <div>
          <div style="font-size:14px;font-weight:600;color:#433164;margin-bottom:8px;">
            Data Sensor
          </div>
          <div class="sensor-grid">
            <div class="sensor-card">
              <div class="sensor-icon">🌡️</div>
              <div class="sensor-label">Suhu</div>
              <div class="sensor-value" id="sensor-temp">27°C</div>
              <div class="sensor-status">Nyaman untuk menjemur.</div>
            </div>
            <div class="sensor-card">
              <div class="sensor-icon">💧</div>
              <div class="sensor-label">Kelembapan</div>
              <div class="sensor-value" id="sensor-hum">68%</div>
              <div class="sensor-status" id="sensor-hum-status">Sedikit lembap, awas hujan.</div>
            </div>
            <div class="sensor-card">
              <div class="sensor-icon">☔</div>
              <div class="sensor-label">Status Hujan</div>
              <div class="sensor-value" id="sensor-rain">Hujan</div>
              <div class="sensor-status" id="sensor-rain-status">Jemuran menjaga cucian tetap aman.</div>
            </div>
          </div>
        </div>

        <div class="hint-card">
          <strong>Tip penggunaan:</strong><br>
          • Aktifkan <strong>mode otomatis</strong> saat Anda pergi atau sedang sibuk.<br>
          • Sistem akan menutup jemuran saat sensor hujan dan kelembapan tinggi terdeteksi.<br>
          • Anda tetap bisa memantau status dari dashboard ini kapan pun.
        </div>
      </div>

    </div>
  </div>
</div>

<script>
  // ======== Sidebar collapse ========
  const sidebar = document.getElementById('sidebar');
  const sidebarToggle = document.getElementById('sidebarToggle');

  sidebarToggle.addEventListener('click', () => {
    sidebar.classList.toggle('collapsed');
  });

  // ======== Last update helper ========
  function updateLastUpdate() {
    const el = document.getElementById('last-update');
    const now = new Date();
    const pad = (n) => n.toString().padStart(2,'0');
    el.textContent = 'Terakhir diperbarui: ' +
      pad(now.getHours()) + ':' +
      pad(now.getMinutes()) + ':' +
      pad(now.getSeconds());
  }

  // ======== State & elements ========
  const statusLabel = document.getElementById('status-label');
  const statusSubtext = document.getElementById('status-subtext');
  const statusEmojiPlayer = document.getElementById('status-emoji'); // <lottie-player>
  const statusChip = document.getElementById('status-chip');
  const btnOpen = document.getElementById('btn-open');
  const btnClose = document.getElementById('btn-close');
  const modeToggle = document.getElementById('mode-auto-toggle');
  const modeStatus = document.getElementById('mode-status');
  const modeDesc = document.getElementById('mode-desc');

  // ======== LOTTIE ANIMATION HELPERS ========
  function setSunnyAnimation() {
    statusEmojiPlayer.setAttribute(
      'src',
      'https://assets2.lottiefiles.com/packages/lf20_t5ueyive.json' // matahari cerah
    );
  }

  function setRainAnimation() {
    statusEmojiPlayer.setAttribute(
      'src',
      'https://assets10.lottiefiles.com/packages/lf20_hy4txm7l.json' // hujan
    );
  }

  function setMovingAnimation() {
    statusEmojiPlayer.setAttribute(
      'src',
      'https://assets2.lottiefiles.com/packages/lf20_j1adxtyb.json' // loading / moving
    );
  }

  // ======== Manual buttons enable/disable ========
  function setManualButtonsEnabled(enabled) {
    btnOpen.disabled = !enabled;
    btnClose.disabled = !enabled;
    const opacity = enabled ? 1 : 0.55;
    btnOpen.style.opacity = opacity;
    btnClose.style.opacity = opacity;
  }

  // ======== Set status view + animasi Lottie ========
  function setStatusView(mode) {
    if (mode === 'open') {
      statusLabel.textContent = 'Terbuka';
      statusSubtext.textContent = 'Jemuran sedang dibuka. Pastikan cuaca mendukung ya.';
      statusChip.textContent = 'Cuaca: Cerah / Berawan';
      setSunnyAnimation();
    } else if (mode === 'moving-open') {
      statusLabel.textContent = 'Membuka…';
      statusSubtext.textContent = 'Perintah buka jemuran sedang dikirim ke perangkat.';
      statusChip.textContent = 'Sedang memproses perintah buka';
      setMovingAnimation();
    } else if (mode === 'moving-close') {
      statusLabel.textContent = 'Menutup…';
      statusSubtext.textContent = 'Perintah tutup jemuran sedang dikirim ke perangkat.';
      statusChip.textContent = 'Sedang memproses perintah tutup';
      setMovingAnimation();
    } else {
      statusLabel.textContent = 'Tertutup';
      statusSubtext.textContent = 'Jemuran dalam kondisi tertutup untuk melindungi cucian dari hujan.';
      statusChip.textContent = 'Cuaca: Hujan Ringan';
      setRainAnimation();
    }
    updateLastUpdate();
  }

  // ======== Tombol buka / tutup ========
  btnOpen.addEventListener('click', () => {
    if (modeToggle.checked) return; // kalau otomatis, manual dikunci
    setStatusView('moving-open');

    // TODO: Panggil endpoint backend buka jemuran di sini
    // fetch('../../backend/device/device_control.php', { method:'POST', body: formData })

    setTimeout(() => {
      setStatusView('open');
    }, 1200);
  });

  btnClose.addEventListener('click', () => {
    if (modeToggle.checked) return;
    setStatusView('moving-close');

    // TODO: Panggil endpoint backend tutup jemuran di sini

    setTimeout(() => {
      setStatusView('closed');
    }, 1200);
  });

  // ======== Mode otomatis ========
  modeToggle.addEventListener('change', () => {
    if (modeToggle.checked) {
      modeStatus.textContent = 'Otomatis';
      modeDesc.textContent = 'Sistem akan menutup jemuran berdasarkan sensor. Tombol manual dinonaktifkan.';
      setManualButtonsEnabled(false);

      // TODO: Endpoint set mode otomatis (mode=1)
    } else {
      modeStatus.textContent = 'Manual';
      modeDesc.textContent = 'Tombol buka/tutup kembali aktif. Cocok untuk kontrol penuh oleh pengguna.';
      setManualButtonsEnabled(true);

      // TODO: Endpoint matikan mode otomatis (mode=0)
    }
    updateLastUpdate();
  });

  // ======== BADGE NOTIF 🔔 (ambil jumlah unread tiap 3 detik) ========
  setInterval(() => {
    fetch('../../backend/notif/unread_count.php')
      .then(res => res.text())
      .then(count => {
        const bell = document.getElementById('bellNotif');
        if (!bell) return;

        const num = parseInt(count);
        if (num > 0) {
          bell.setAttribute('data-count', num);
        } else {
          bell.removeAttribute('data-count');
        }
      })
      .catch(() => {});
  }, 3000);

  // ======== Init ========
  setStatusView('closed'); // default: tertutup + animasi hujan
  setManualButtonsEnabled(true);
</script>

</body>
</html>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
