<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    echo "ID tidak ditemukan.";
    exit;
}

$post_id = intval($_GET['id']);
$user_id = $_SESSION['id'];

$stmt = $conn->prepare("SELECT title, description, tags, image FROM questions WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $post_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Postingan tidak ditemukan atau bukan milik Anda.";
    exit;
}

$post = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Edit Postingan</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Roboto', sans-serif;
      background-color: #f4f4f4;
      color: #333;
    }

    .container {
      max-width: 600px;
      margin: 80px auto;
      background-color: #fff;
      padding: 30px 40px;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    h2 {
      text-align: center;
      color: #7a0029;
      margin-bottom: 25px;
    }

    label {
      display: block;
      margin-top: 15px;
      font-weight: bold;
    }

    input[type="text"],
    textarea {
      width: 100%;
      padding: 10px;
      margin-top: 6px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 15px;
    }

    textarea {
      resize: vertical;
      min-height: 150px;
    }

    button[type="submit"] {
      margin-top: 25px;
      padding: 10px 20px;
      background-color: #7a0029;
      color: #fff;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      cursor: pointer;
      width: 100%;
      transition: background-color 0.3s ease;
    }

    button[type="submit"]:hover {
      background-color: #5a0020;
    }

    p {
      text-align: center;
      margin-top: 20px;
    }

    a {
      text-decoration: none;
      color: #7a0029;
      font-weight: bold;
    }

    a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- Edit form: edit_post.php -->
    <form action="proses_edit_post.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $post_id ?>">
    <input type="hidden" name="gambar_lama" value="<?= htmlspecialchars($post['image']) ?>">

    <label for="judul">Judul:</label>
    <input type="text" name="judul" id="judul" value="<?= htmlspecialchars($post['title']) ?>" required>

    <label for="isi">Isi:</label>
    <textarea name="isi" id="isi" rows="6" required><?= htmlspecialchars($post['description']) ?></textarea>

    <label for="tags">Tag (pisahkan dengan spasi)</label>
    <input type="text" name="tags" id="tags" value="<?= htmlspecialchars($post['tags']); ?>" placeholder>

    <!-- Tambahkan ini di dalam <form> -->
    <?php if (!empty($post['image'])): ?>
      <p>Gambar saat ini:</p>
      <img src="uploads/<?= htmlspecialchars($post['image']) ?>" alt="Gambar" style="max-width: 300px;"><br>
      <label><input type="checkbox" name="hapus_gambar" value="1"> Hapus gambar ini</label><br><br>
    <?php endif; ?>

    <label for="gambar">Ganti Gambar (opsional):</label><br>
    <input type="file" name="gambar" id="gambar" accept="image/*"><br><br>


    <button type="submit">Simpan Perubahan</button> 
    </form>

      <form action="hapus_post.php" method="POST" onsubmit="return confirm('Yakin ingin menghapus postingan ini?');">
      <input type="hidden" name="id" value="<?= $post_id ?>">
      <button type="submit" name="hapus" style="background-color: crimson; color: white; margin-top: 10px;">Hapus Postingan</button>
    </form>
    <p><a href="dashboard.php">← Kembali ke Dashboard</a></p>
  </div>
</body>
</html>
