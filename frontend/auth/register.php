<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register - Smart Clothesline</title>

  <!-- Font Awesome untuk ikon -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      background: url('Bg.png') no-repeat center center fixed;
      background-size: cover;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      align-items: center;
    }

    .register-box {
      background: rgba(255, 255, 255, 0.9);
      padding: 50px 40px;
      border-radius: 20px;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.05);
      text-align: center;
      width: 420px;
      margin-top: 60px;
    }

    h2 {
      font-size: 26px;
      color: #5a7efc;
      font-weight: 700;
      margin-bottom: 30px;
    }

    .input-field {
      position: relative;
      margin-bottom: 20px;
    }

    .input-field input {
      width: 100%;
      padding: 14px 45px;
      border-radius: 25px;
      border: 1px solid #9abaf9;
      font-size: 14px;
      outline: none;
      background-color: transparent;
      color: #333;
    }

    .input-field i {
      position: absolute;
      left: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: #a3aafc;
      font-size: 18px;
    }

    .input-field input::placeholder {
      color: #b6b7ff;
    }

    button {
      width: 100%;
      background: linear-gradient(to right, #4d9efc, #5a7efc);
      border: none;
      padding: 14px;
      border-radius: 25px;
      color: white;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      box-shadow: 0 4px 10px rgba(92, 140, 255, 0.3);
      transition: 0.3s ease;
    }

    button:hover {
      transform: translateY(-2px);
    }

    p {
      font-size: 14px;
      margin-top: 20px;
      color: #333;
    }

    p a {
      color: #3b63f5;
      text-decoration: none;
      font-weight: 500;
    }

    p a:hover {
      text-decoration: underline;
    }

  </style>
</head>

<body>
  <div class="register-box">
    <h2>Buat Akun</h2>
    <form action="../../backend/auth/auth_register.php" method="POST">
      <div class="input-field">
        <i class="fa fa-user"></i>
        <input type="text" name="nama" placeholder="Nama Lengkap" required>
      </div>
      <div class="input-field">
        <i class="fa fa-envelope"></i>
        <input type="email" name="email" placeholder="Email" required>
      </div>
      <div class="input-field">
        <i class="fa fa-lock"></i>
        <input type="password" name="password" placeholder="Password" required>
      </div>
      <div class="input-field">
        <i class="fa fa-lock"></i>
        <input type="password" name="confirm_password" placeholder="Konfirmasi Password" required>
      </div>
      <button type="submit">Daftar</button>
    </form>
    <p>Sudah punya akun? <a href="login.php">Masuk di sini</a></p>
  </div>
</body>
</html>
