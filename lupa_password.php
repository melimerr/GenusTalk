<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Lupa Password</title>
  <link rel="stylesheet" href="auth-style.css">
</head>
<body>
  <div class="container">
    <h2>Lupa Password</h2>
    <form action="verifikasi_user.php" method="post">
      <label for="username">Masukkan Username atau Email:</label>
      <input type="text" id="username" name="username" required>
      <button type="submit">Lanjut</button>
      <a href="login.php">Kembali ke Login</a>
    </form>
  </div>
</body>
</html>
