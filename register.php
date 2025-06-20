<?php
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (fullname, username, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $fullname, $username, $password);

    if ($stmt->execute()) {
        echo "<script>
                alert('Pendaftaran berhasil! Silakan login.');
                window.location.href = 'login.html';
              </script>";
        exit();
    } else {
        echo "Terjadi kesalahan: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Daftar - GenusTalks</title>
    <style>
      * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
      }

      body {
        font-family: Poppins;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        background-color: white;
      }

      header {
        display: flex;
        justify-content: flex-end;
        padding: 0px;
        background-color: transparent;
      }

      header a {
        margin-left: 20px;
        text-decoration: none;
        color: #800020;
        font-weight: bold;
      }

      .container {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        align-items: center;
        padding: 15px;
      }

      .left-section {
        flex: 1;
        min-width: 500px;
        max-width: 300px;
      }

      .left-section h1 {
        font-size: 24px;
        margin-bottom: 25px;
        color: #800020;
      }

      .form-box {
        background-color: white;
        border-radius: 12px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        padding: 24px;
        border: 1px solid #ccc;
      }

      .form-box h2 {
        text-align: center;
        color: #800020;
        margin-bottom: 50px;
      }

      input[type="text"],
      input[type="email"],
      input[type="password"] {
        width: 100%;
        padding: 12px;
        margin: 8px 0;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 16px;
      }

      button {
        width: 100%;
        padding: 12px;
        background-color: #800020;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
      }

      button:hover {
        background-color: #66001a;
      }

      .form-footer {
        margin-top: 12px;
        text-align: center;
        font-size: 14px;
      }

      .form-footer a {
        color: #800020;
        text-decoration: none;
        font-style: italic;
      }

      .right-section {
        flex: 1;
        min-width: 300px;
        background-color: #800020;
        color: white;
        border-top-left-radius: 50px;
        border-bottom-left-radius: 50px;
        padding: 40px 30px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 25px;
      }

      .feature {
        display: flex;
        align-items: center;
        gap: 12px;
      }

      .feature-icon {
        background-color: white;
        color: #800020;
        border-radius: 8px;
        padding: 10px;
        font-weight: bold;
        min-width: 40px;
        text-align: center;
      }

      .feature-text {
        font-size: 16px;
      }

      @media screen and (max-width: 768px) {
        .container {
          flex-direction: column;
          padding: 20px;
        }

        .right-section {
          border-radius: 20px;
          margin-top: 20px;
        }
      }
    </style>
  </head>
  <body>

    <div class="container">
      <div class="left-section">
        <h1>Gabung bersama Genusian lain di GenusTalks!</h1>
        <div class="form-box">
          <h2>Daftar Sekarang</h2>
          
          <form action="register.php" method="POST">
  <input type="text" name="fullname" id="fullname" placeholder="Nama Lengkap" required />
  <input type="email" name="email" id="email" placeholder="Email" required />
  <input type="text" name="username" id="username" placeholder="Username" required />
  <input type="password" name="password" id="password" placeholder="Password" required />
  <button type="submit">Daftar</button>
</form>


          <div class="form-footer">
            Sudah punya akun? <a href="login.html">Masuk</a> di sini.
          </div>
        </div>
      </div>

      <div class="right-section">
        <div class="feature">
          <div class="feature-icon">?</div>
          <div class="feature-text">Bertanya apapun</div>
        </div>
        <div class="feature">
          <div class="feature-icon">✔</div>
          <div class="feature-text">Jawaban Terpercaya</div>
        </div>
        <div class="feature">
          <div class="feature-icon">🤝</div>
          <div class="feature-text">Komunitas aktif</div>
        </div>
      </div>
    </div>
  </body>
</html>
