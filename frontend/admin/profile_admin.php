<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$namaUser  = $_SESSION['user']['nama'] ?? 'User';
$emailUser = $_SESSION['user']['email'] ?? 'user@email.com';
$roleUser  = $_SESSION['user']['role'] ?? 'User';
$password  = '*****';
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profil User - Smart Clothesline</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;}
body{
  margin:0;
  font-family:'Poppins',sans-serif;
  background:url('/jemuran_auto/assets/bg.png') no-repeat center center fixed;
  background-size:cover;
  min-height:100vh;
  overflow-x:hidden;
  color:#3b2b68;
  display:flex;
}

/* =============== SIDEBAR BASE =============== */
.sidebar {
  width: 230px;
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
  z-index: 200;
}
.sidebar.collapsed { width: 76px; }

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

/* =============== MAIN CONTENT =============== */
main {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 40px 20px;
  position: relative;
}

/* ===== NOTIF BELL ===== */
.notif {
  position: absolute;
  top: 18px;
  right: 40px;
  cursor: pointer;
  width: 26px;
  height: 26px;
}
.notif svg {
  width: 100%;
  height: 100%;
  stroke: #7b5de6;
  fill: none;
}
.notif::after {
  content: "2";
  position: absolute;
  top: -5px;
  right: -6px;
  background: #ff5277;
  color: white;
  font-size: 10px;
  border-radius: 50%;
  width: 15px;
  height: 15px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
}

/* ===== FORM PROFILE ===== */
.profile-pic {
  position: relative;
  width: 150px;
  height: 150px;
  border-radius: 50%;
  overflow: hidden;
  box-shadow: 0 6px 16px rgba(0,0,0,0.15);
  margin-bottom: 24px;
}
.profile-pic img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.edit-icon {
  position: absolute;
  bottom: 8px;
  right: 8px;
  background: white;
  border-radius: 50%;
  width: 30px;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 2px 6px rgba(0,0,0,0.2);
  cursor: pointer;
  color: #7b5de6;
  transition: 0.2s;
}
.edit-icon:hover { transform: scale(1.1); background: #f0e9ff; }

form {
  display: flex;
  flex-direction: column;
  gap: 16px;
  width: 100%;
  max-width: 480px;
}
.input-wrap {
  display: flex;
  align-items: center;
  gap: 8px;
  background: rgba(255,255,255,0.6);
  border: 1.5px solid #b9a4ff;
  border-radius: 12px;
  padding: 10px 14px;
  color: #4a3c7f;
  backdrop-filter: blur(6px);
}
.input-wrap svg {
  width: 20px;
  height: 20px;
  stroke: #7b5de6;
}
.input-wrap input {
  border: none;
  background: transparent;
  flex: 1;
  font-size: 14px;
  color: #4a3c7f;
  outline: none;
}
.btn {
  align-self: flex-end;
  padding: 10px 26px;
  border: none;
  border-radius: 20px;
  background: linear-gradient(135deg,#8b5cf6,#60a5fa);
  color: white;
  font-weight: 500;
  cursor: pointer;
  transition: .2s;
}
.btn:hover { transform: translateY(-2px); }
</style>
</head>
<body>

<!-- ===== SIDEBAR ===== -->
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
      
  </div>

      <div style="font-size:20px;font-weight:700;color:#4a2a9d;margin-top:-4px;">Clothesline</div>
      <div class="brand-sub">Admin Dashboard</div>
    </div>
  </div>

  <div class="sidebar-menu">
    <div class="menu-item" onclick="window.location.href='dashboard_Admin.php'">
      <span class="icon">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
          <path d="M3 10.5L12 3l9 7.5"></path>
          <path d="M5.25 9.75V19.5A1.75 1.75 0 007 21.25h3.25v-5.5H13.5v5.5H17A1.75 1.75 0 0018.75 19.5V9.75"></path>
        </svg>
      </span>
      <span class="text">Dashboard</span>
    </div>

    <div class="menu-item active" onclick="window.location.href='profile_Adminphp'">
      <span class="icon">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
          <path d="M15.75 8.25a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/>
          <path d="M4.5 20.25a7.5 7.5 0 0115 0"/>
        </svg>
      </span>
      <span class="text">Profil</span>
    </div>

    <div class="menu-item" onclick="window.location.href='notif.php'">
      <span class="icon">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
          <path d="M14.25 18.75a2.25 2.25 0 11-4.5 0"></path>
          <path d="M4.5 9.75A7.5 7.5 0 0119.5 9.75c0 3.273.878 4.348 1.5 5.25H3c.622-.902 1.5-1.977 1.5-5.25z"></path>
        </svg>
      </span>
      <span class="text">Notifikasi</span>
    </div>
  </div>

  <div class="sidebar-footer">
    <div style="font-size:11px;color:#9b8ad0;">Masuk sebagai</div>
    <div style="font-size:13px;font-weight:500;color:#5b3fd0;"><?= htmlspecialchars($namaUser); ?></div>
    <a href="/jemuran_auto/backend/auth/auth_logout.php">
      <div class="logout-btn"><span>📤</span><span>Logout</span></div>
    </a>
  </div>
</aside>

<!-- ===== MAIN CONTENT ===== -->
<main>

  <div class="notif" onclick="window.location.href='notif.php'">
    <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <path d="M14 18c0 1.1-.9 2-2 2s-2-.9-2-2"/>
      <path d="M18 8a6 6 0 00-12 0c0 7-3 9-3 9h18s-3-2-3-9z"/>
    </svg>
  </div>

  <div class="profile-pic">
    <?php 
$fotoUser = $_SESSION['user']['foto_profile'] ?? 'user_default.jpg';
$fotoPath = "/jemuran_auto/assets/uploads/profile/" . $fotoUser;
?>
<img src="<?= $fotoPath ?>?v=<?= time(); ?>" id="previewFoto">
    <label for="fotoUpload" class="edit-icon">✏️</label>
    <input type="file" id="fotoUpload" accept="image/*" style="display:none;">
  </div>

  <form id="profileForm">
    <div class="input-wrap">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
        <path d="M15.75 8.25a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/>
        <path d="M4.5 20.25a7.5 7.5 0 0115 0"/>
      </svg>
      <input type="text" id="nama" value="<?= htmlspecialchars($namaUser); ?>" placeholder="Nama">
    </div>

    <div class="input-wrap">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
        <path d="M22 6L12 13 2 6"/>
      </svg>
      <input type="email" id="email" value="<?= htmlspecialchars($emailUser); ?>" placeholder="Email">
    </div>

    <div class="input-wrap">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="7" r="4"/>
        <path d="M6 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"/>
      </svg>
      <input type="text" id="role" value="<?= htmlspecialchars($roleUser); ?>" readonly>
    </div>

    <div class="input-wrap">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
      </svg>
      <input type="password" id="password" value="<?= htmlspecialchars($password); ?>">
    </div>

    <button class="btn" type="button" id="editBtn">Edit</button>
  </form>
</main>

<script>
document.getElementById('sidebarToggle').addEventListener('click',()=>{
  document.getElementById('sidebar').classList.toggle('collapsed');
});
const fotoInput=document.getElementById('fotoUpload');
const preview=document.getElementById('previewFoto');
fotoInput.addEventListener('change',(e)=>{
  const file=e.target.files[0];
  if(file) preview.src=URL.createObjectURL(file);
});
const editBtn=document.getElementById('editBtn');
const inputs=document.querySelectorAll('#profileForm input');
let editable=false;
editBtn.addEventListener('click', () => {
  editable = !editable;
  inputs.forEach(i => {
    if (i.id !== 'role') i.readOnly = !editable;
  });

  if (!editable) {
      // Ketika tombol berubah dari 'Simpan' → 'Edit'
      submitProfil();
  }

  editBtn.textContent = editable ? 'Simpan' : 'Edit';
});


function submitProfil() {
  const fd = new FormData();
  fd.append("nama", document.getElementById("nama").value);
  fd.append("email", document.getElementById("email").value);

  const foto = document.getElementById("fotoUpload").files[0];
  if (foto) fd.append("foto", foto);

  fetch("/jemuran_auto/backend/profile/profile_update.php", {
    method: "POST",
    body: fd
  })
  .then(r => r.json())
  .then(d => {
    alert(d.msg);
    
    if (d.status) {
    // Update UI langsung tanpa reload
    document.getElementById("nama").value = fd.get("nama");
    document.getElementById("email").value = fd.get("email");

    if (d.foto) {
        preview.src = "/jemuran_auto/assets/uploads/profile/" + d.foto + "?v=" + Date.now();
    }

    // Matikan mode edit
    editable = false;
    editBtn.textContent = "Edit";
    inputs.forEach(i => { if (i.id !== 'role') i.readOnly = true; });

    alert("Profil berhasil diperbarui!");
}

  });
}

function updateEmail() {
  fetch("/jemuran_auto/backend/profile/update_email.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: new URLSearchParams({
      email: document.getElementById("email").value
    })
  })
  .then(r => r.json())
  .then(d => alert(d.msg));
}

function updatePassword() {
  fetch("/jemuran_auto/backend/profile/update_password.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: new URLSearchParams({
      password_lama: document.getElementById("password").value,
      password_baru: prompt("Masukkan password baru:")
    })
  })
  .then(r => r.json())
  .then(d => alert(d.msg));
}
</script>
</body>
</html>
