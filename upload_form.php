<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: login.html");
  exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Upload Foto Profil</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      padding: 20px;
    }

    form {
      margin-top: 20px;
    }

    input[type="file"] {
      margin-bottom: 10px;
    }

    button {
      padding: 8px 16px;
      background-color: #7a0029;
      color: white;
      border: none;
      cursor: pointer;
    }

    button:hover {
      background-color: #5c0020;
    }

    .message {
      margin-top: 15px;
      padding: 10px;
      border-radius: 5px;
    }

    .success {
      background-color: #d4edda;
      color: #155724;
    }

    .error {
      background-color: #f8d7da;
      color: #721c24;
    }
  </style>
</head>
<body>
  <h2>Upload Foto Profil</h2>

  <?php if (isset($_GET['success'])): ?>
    <div class="message success">Upload berhasil!</div>
  <?php elseif (isset($_GET['error'])): ?>
    <div class="message error"><?= htmlspecialchars($_GET['error']) ?></div>
  <?php endif; ?>

  <form action="upload_photo.php" method="post" enctype="multipart/form-data">
    <label for="photo">Pilih foto (jpg/png):</label><br>
    <input type="file" name="photo" accept="image/jpeg,image/png" required><br>
    <button type="submit">Upload</button>
  </form>
</body>
</html>
