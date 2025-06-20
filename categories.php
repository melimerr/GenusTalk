<?php
session_start();
date_default_timezone_set('Asia/Jakarta');
include 'koneksi.php';

if (!isset($_SESSION['id']) || !isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$id = $_SESSION['id'];

// Ambil data user login
$query = "SELECT foto_profil FROM users WHERE id = $id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Foto profil login
$foto = !empty($user['foto_profil']) ? 'uploads/' . $user['foto_profil'] : 'assets/img/default-avatar.jpg';

$questionResult = null;

if (isset($_GET['tag'])) {
    $tag = mysqli_real_escape_string($conn, $_GET['tag']);
    $questionQuery = "
        SELECT q.*, u.username, u.foto_profil 
        FROM questions q 
        JOIN users u ON q.user_id = u.id 
        WHERE q.tags LIKE '%$tag%' 
        ORDER BY q.created_at DESC
    ";
} else {
    $questionQuery = "
        SELECT q.*, u.username, u.foto_profil 
        FROM questions q 
        JOIN users u ON q.user_id = u.id 
        ORDER BY q.created_at DESC
    ";
}

$questionResult = mysqli_query($conn, $questionQuery);

if (!function_exists('timeAgo')) {
    function timeAgo($datetime) {
        $timestamp = strtotime($datetime);
        $diff = time() - $timestamp;

        if ($diff < 60) return 'Baru saja';
        elseif ($diff < 3600) return floor($diff / 60) . ' menit yang lalu';
        elseif ($diff < 86400) return floor($diff / 3600) . ' jam yang lalu';
        elseif ($diff < 2592000) return floor($diff / 86400) . ' hari yang lalu';
        else return date('j F Y', $timestamp);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>GenusTalks - Categories</title>
  <link rel="stylesheet" href="dashboard.css?v=1.1" />
  <script src="https://unpkg.com/feather-icons"></script>
</head>
<body>
  <div class="container">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
      <div>
        <div class="sidebar-header">
          <h1 class="logo">GenusTalks</h1>
        </div>

        <nav class="menu">
          <a href="dashboard.php"><i data-feather="home"></i><span>Home</span></a>
          <a href="my_questions.php"><i data-feather="user-check"></i><span>My Questions</span></a>
          <a href="categories.php"><i data-feather="book-open"></i><span>Categories</span></a>
          <a href="trending.php"><i data-feather="trending-up"></i><span>Trending</span></a>
        </nav>
      </div>

      <div class="sidebar-footer">
        <button class="signout-btn" onclick="showLogoutModal()">Logout</button>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="main">
      <!-- Topbar -->
      <div class="topbar">
        <div class="topbar-left">
          <button class="toggle-sidebar icon-btn" id="toggleSidebarBtn">☰</button>
          <?php if (basename($_SERVER['PHP_SELF']) === 'dashboard.php'): ?>
            <button class="create-btn" onclick="location.href='create-question.html'">Create Question</button>
          <?php endif; ?>
        </div>

        <div class="topbar-right">
          <button class="darkmode-toggle icon-btn" id="darkModeBtn">
          <i id="darkIcon" data-feather="moon"></i>
          </button>
          <div class="user-profile" onclick="window.location.href='edit_profil.php'">
            <img src="<?php echo $foto; ?>" alt="Profil" class="profile-pic">
          </div>
        </div>
      </div>

      <!-- Categories List -->
      <section class="category-list">
        <h2>Kategori Populer</h2>
        <div class="categories-grid">
          <a href="categories.php?tag=teknologi" class="category-card">💻 Teknologi</a>
          <a href="categories.php?tag=pendidikan" class="category-card">🎓 Pendidikan</a>
          <a href="categories.php?tag=karier" class="category-card">💼 Karier</a>
          <a href="categories.php?tag=desain" class="category-card">🎨 Desain</a>
          <a href="categories.php?tag=bisnis" class="category-card">📈 Bisnis</a>
          <a href="categories.php?tag=pemrograman" class="category-card">⚙️ Pemrograman</a>
          <a href="categories.php?tag=buku" class="category-card">📚 Buku</a>
          <a href="categories.php?tag=psikologi" class="category-card">🧠 Psikologi</a>
          <a href="categories.php?tag=belajar" class="category-card">Belajar</a>
        </div>
      </section>

      <!-- Tampilkan Pertanyaan Berdasarkan Tag -->
      <div id="questionWrapper">
      <?php while ($row = mysqli_fetch_assoc($questionResult)): ?>
        <?php
          $fotoProfil = !empty($row['foto_profil']) ? 'uploads/' . $row['foto_profil'] : 'assets/img/default-avatar.jpg';
        ?>
        <div class="question-card">
          <div class="post">
            <div class="user-info">
              <img src="<?php echo $fotoProfil; ?>" alt="User Foto" class="profile-pic" style="width: 40px; height: 40px; border-radius: 50%; margin-right: 10px;">
              <div>
                <div class="username"><?php echo htmlspecialchars($row['username']); ?></div>
                <div class="time"><?= timeAgo($row['created_at']) ?></div>
              </div>
            </div>
            <div class="question">
              <h3><?php echo htmlspecialchars($row['title']); ?></h3>
              <p><?php echo htmlspecialchars($row['description']); ?></p>
            </div>
            <div class="tags">
              <?php
                $tags = explode(' ', $row['tags']);
                foreach ($tags as $tag) {
                  echo '<span>' . htmlspecialchars($tag) . '</span>';
                }
              ?>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    </main>
  </div>

  <footer class="main-footer">
  <p>© 2025 <strong>GenusTalks</strong>. All rights reserved.</p>
</footer>

  <!-- JavaScript -->
  <script>
  document.addEventListener('DOMContentLoaded', () => {
  const icon = document.getElementById('darkIcon');
  const isDark = localStorage.getItem('darkMode') === 'true';

  if (isDark) {
    document.body.classList.add('dark');
    icon.setAttribute('data-feather', 'sun');
  } else {
    icon.setAttribute('data-feather', 'moon');
  }

  feather.replace(); // render ikon awal
});

document.getElementById('darkModeBtn').addEventListener('click', () => {
  const icon = document.getElementById('darkIcon');
  const isDark = document.body.classList.toggle('dark');
  localStorage.setItem('darkMode', isDark);
  icon.setAttribute('data-feather', isDark ? 'sun' : 'moon');
  feather.replace(); // update ikon setelah toggle
});
  </script>


    <script>
  function showLogoutModal() {
    document.getElementById('logoutModal').classList.remove('hidden');
  }

  function closeLogoutModal() {
    document.getElementById('logoutModal').classList.add('hidden');
  }

  function confirmLogout() {
    window.location.href = 'logout.php';
  }
</script>


  <div id="logoutModal" class="modal hidden">
  <div class="modal-content">
    <p>Apakah Anda yakin ingin logout?</p>
    <div class="modal-actions">
      <button onclick="confirmLogout()" class="signout-btn">Ya, Logout</button>
      <button onclick="closeLogoutModal()" class="signout-btn cancel">Batal</button>
    </div>
  </div>
</div>

<script>
  document.getElementById("toggleSidebarBtn").addEventListener("click", function () {
    const sidebar = document.getElementById("sidebar");
    const main = document.querySelector(".main");
    sidebar.classList.toggle("collapsed");
    main.classList.toggle("collapsed");
  });
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
      const currentPage = window.location.pathname.split('/').pop();
      document.querySelectorAll('.menu a').forEach(link => {
        const linkPage = link.getAttribute('href');
        if (linkPage === currentPage || (currentPage === '' && linkPage === 'dashboard.php')) {
          link.classList.add('active');
        } else {
          link.classList.remove('active');
        }
      });
    });
  </script>

</body>
</html>
