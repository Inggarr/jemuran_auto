<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Register Jemuran Auto</title></head>
<body>
<h2>Register</h2>
<form id="formRegister">
  <label>Nama</label><br><input type="text" name="nama" required><br>
  <label>Email</label><br><input type="email" name="email" required><br>
  <label>Password</label><br><input type="password" name="password" required><br>
  <button type="submit">Daftar</button>
</form>
<p id="msg" style="color:red;"></p>
<p>Sudah punya akun? <a href="login.php">Login</a></p>
<script>
document.getElementById('formRegister').addEventListener('submit', async(e)=>{
  e.preventDefault();
  const data=new FormData(e.target);
  const res=await fetch('../../backend/auth/auth_register.php',{method:'POST',body:data});
  const out=await res.json();
  const msg=document.getElementById('msg');
  if(out.status==='success'){
    msg.style.color='green';msg.textContent='Pendaftaran berhasil! Mengalihkan...';
    setTimeout(()=>{window.location.href='login.php'},1000);
  }else{msg.textContent=out.message;}
});
</script>
</body>
</html>
