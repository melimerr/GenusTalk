<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id']) || !isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$id = $_SESSION['id'];

// Ambil data user termasuk nama file foto
$query = "SELECT foto_profil FROM users WHERE id = $id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Jika tidak ada foto, pakai default
$foto = !empty($user['foto_profil']) ? 'uploads/' . $user['foto_profil'] : 'assets/img/default-avatar.jpg';
?>


<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Buat Pertanyaan - GenusTalks</title>
  <link rel="stylesheet" href="dashboard.css?v=1.2"/>


<style>

    body, html {
  margin: 0;
  padding: 0;
}

main.main {
  height: 100vh;
  display: flex;
  flex-direction: column;
  box-sizing: border-box;
}

.create-form {
  flex: 1;
  padding: 20px 60px;
  overflow-y: auto;
  box-sizing: border-box;
}

.create-form form {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.create-form h2 {
  margin-bottom: 10px;
  font-size: 24px;
  text-align: center;
}

.create-form input,
.create-form textarea {
  width: 100%;
  padding: 10px;
  font-size: 16px;
  border: 1px solid #ccc;
  border-radius: 6px;
}

.create-form .create-btn {
  padding: 12px;
  font-size: 16px;
  background-color: #7a0029;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

</style>

</head>
<body>

    <!-- Main Content -->
    <main class="main">

      <!-- Create Question Form -->
      <div class="create-form">
  <h2>Buat Pertanyaan</h2>
  <form action="save_question.php" method="post" enctype="multipart/form-data">
    <label for="title">Judul Pertanyaan</label>
    <input type="text" id="title" name="title" placeholder="Contoh: Bagaimana cara belajar JavaScript dengan efektif?" required />

    <label for="description">Deskripsi</label>
    <textarea id="description" name="description" rows="4" placeholder="Tuliskan detail pertanyaan kamu di sini..." required></textarea>

    <label for="tags">Tag</label>
    <input type="text" id="tags" name="tags" placeholder="Contoh: #javascript #belajar" />
    <div class="tags-note">Gunakan tanda koma atau spasi untuk memisahkan tag.</div>

    <label for="gambar">Upload Gambar:</label>
    <input type="file" name="gambar" id="gambar" accept="image/*">

    <button type="submit" class="create-btn">Kirim Pertanyaan</button>
  </form>
</div>
    </main>
  </div>

  <!-- Toggle Sidebar & Darkmode -->
  <script>
    const toggleBtn = document.getElementById('toggleSidebarBtn');
    const sidebar = document.getElementById('sidebar');
    const container = document.querySelector('.container');
    const darkModeBtn = document.getElementById('darkModeBtn');

    toggleBtn.addEventListener('click', () => {
      sidebar.classList.toggle('hidden');
      container.classList.toggle('full-width');

      // Ganti warna tombol ☰
      toggleBtn.style.color = sidebar.classList.contains('hidden') ? '#7a0029' : 'white';
    });

    document.addEventListener('DOMContentLoaded', () => {
      const isDark = localStorage.getItem('darkMode') === 'true';
      if (isDark) {
        document.body.classList.add('dark');
        darkModeBtn.textContent = '☀️';
      }
    });

    darkModeBtn.addEventListener('click', () => {
      document.body.classList.toggle('dark');
      const isDark = document.body.classList.contains('dark');
      localStorage.setItem('darkMode', isDark);
      darkModeBtn.textContent = isDark ? '☀️' : '🌙';
    });
  </script>

  <script>
  feather.replace();
</script>

</body>
</html>
