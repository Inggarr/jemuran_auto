<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Smart Clothesline - Login</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: "Poppins", sans-serif;
    }

    body {
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      background: url("../../assets/Bg.png") center/cover no-repeat fixed;
      background-size: cover;
    }

    /* LOGIN CONTAINER */
    .login-container {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      min-height: 80vh;
    }

    .login-box {
      background: rgba(255, 255, 255, 0.85);
      backdrop-filter: blur(6px);
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
      border-radius: 15px;
      padding: 40px 50px;
      width: 350px;
      text-align: center;
    }

    .login-box h2 {
      color: #b6a9ff;
      font-size: 26px;
      font-weight: 700;
      margin-bottom: 30px;
    }

    .input-group {
      position: relative;
      margin-bottom: 20px;
    }

    .input-group input {
      width: 100%;
      padding: 12px 15px;
      padding-left: 40px;
      border: 1.5px solid #73a3e6;
      border-radius: 25px;
      outline: none;
      font-size: 15px;
      color: #333;
      background-color: rgba(255,255,255,0.8);
    }

    .input-group input::placeholder {
      color: #a7b6ee;
    }

    .input-group i {
      position: absolute;
      left: 15px;
      top: 12px;
      color: #7fa3d9;
    }

    .btn {
      width: 100%;
      background: #438ce3;
      color: #fff;
      border: none;
      border-radius: 25px;
      padding: 12px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: 0.3s;
    }

    .btn:hover {
      background: #3276c8;
    }

    .signup-text {
      margin-top: 15px;
      font-size: 14px;
    }

    .signup-text a {
      color: #438ce3;
      text-decoration: none;
      font-weight: 500;
    }

    .signup-text a:hover {
      text-decoration: underline;
    }

  </style>
</head>
<body>
  <div class="login-container">
    <div class="login-box">
      <h2>Hi! Welcome</h2>
      <!-- ✅ Form tersambung ke backend -->
      <form action="../../backend/auth/auth_login.php" method="POST">
        <div class="input-group">
          <i></i>
          <input type="email" name="email" placeholder="Email" required />
        </div>
        <div class="input-group">
          <i></i>
          <input type="password" name="password" placeholder="Password" required />
        </div>
        <button type="submit" class="btn">Login</button>
        <p class="signup-text">
          Don't have an account? <a href="../auth/register.html">Sign up</a>
        </p>
      </form>
    </div>
  </div>
</body>
</html>
