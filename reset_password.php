<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Reset Password</title>
  <link rel="stylesheet" href="auth-style.css">
</head>
<body>
  <div class="container">
    <h2>Reset Password</h2>
    <form action="simpan_password_baru.php" method="post">
      <label for="password_baru">Password Baru:</label>
      <input type="password" id="password_baru" name="password_baru" required>
      <button type="submit">Reset Password</button>
      <a href="login.php">Kembali ke Login</a>
    </form>
  </div>
</body>
</html>
