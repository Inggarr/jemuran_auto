<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Login Jemuran Auto</title></head>
<body>
<h2>Login</h2>
<form id="formLogin">
  <label>Email</label><br><input type="email" name="email" required><br>
  <label>Password</label><br><input type="password" name="password" required><br>
  <button type="submit">Login</button>
</form>
<p id="msg" style="color:red;"></p>
<p>Belum punya akun? <a href="register.php">Register</a></p>
<script>
document.getElementById('formLogin').addEventListener('submit', async(e)=>{
  e.preventDefault();
  const data=new FormData(e.target);
  const res=await fetch('../../backend/auth/auth_login.php',{method:'POST',body:data});
  const out=await res.json();
  const msg=document.getElementById('msg');
  if(out.status==='success'){
    msg.style.color='green';
    msg.textContent='Login berhasil! Mengalihkan...';
    setTimeout(()=>{
      if(out.role==='admin'){
        window.location.href='../admin/dashboard_admin.php';
      }else{
        window.location.href='../user/dashboard_user.php';
      }
    },800);
  }else{
    msg.textContent=out.message;
  }
});
</script>
</body>
</html>
