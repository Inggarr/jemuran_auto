<?php
require_once __DIR__ . '/../../backend/middleware/auth_check.php';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';

$user = $_SESSION['user'];
?>
<div class="content">
  <h3>Profil Saya</h3>

  <div style="display:flex;gap:20px;align-items:center;">
    <img src="../../<?= htmlspecialchars($user['foto']); ?>" alt="Profile" width="100" height="100" style="border-radius:50%;border:3px solid #10b981;object-fit:cover;">
    <div>
      <p><b>Nama:</b> <?= htmlspecialchars($user['nama']); ?></p>
      <p><b>Email:</b> <?= htmlspecialchars($user['email']); ?></p>
      <p><b>Role:</b> <?= htmlspecialchars($user['role']); ?></p>
    </div>
  </div>

  <hr style="margin:20px 0;">

  <h4>Ubah Profil</h4>
  <form id="formProfile" enctype="multipart/form-data">
    <label>Nama Baru</label><br>
    <input type="text" name="nama" value="<?= htmlspecialchars($user['nama']); ?>" required><br><br>
    <label>Ganti Foto Profil</label><br>
    <input type="file" name="foto" accept="image/*"><br><br>
    <button type="submit" style="background:#10b981;color:#fff;padding:8px 16px;border:none;border-radius:6px;">Simpan</button>
  </form>

  <p id="msg" style="margin-top:10px;"></p>
</div>

<script>
document.getElementById('formProfile').addEventListener('submit', async (e) => {
  e.preventDefault();
  const form = new FormData(e.target);
  const res = await fetch('../../backend/profile/profile_update.php', { method: 'POST', body: form });
  const data = await res.json();
  const msg = document.getElementById('msg');
  msg.textContent = data.message;
  msg.style.color = data.status === 'success' ? 'green' : 'red';
  if (data.status === 'success') setTimeout(()=>location.reload(), 1000);
});
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>
