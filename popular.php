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
  <title>GenusTalks - Popular Questions</title>
  <link rel="stylesheet" href="dashboard.css?v=1.1" />
</head>
<body>
  <div class="container">
    <!-- Sidebar -->
     <aside class="sidebar" id="sidebar">
  <!-- Bagian Atas -->
  <div>
    <div class="sidebar-header">
      <h1 class="logo">GenusTalks</h1>
    </div>

    <nav class="menu">
      <a href="dashboard.php"><i data-feather="home"></i><span>Home</span></a>
      <a href="categories.php"><i data-feather="book-open"></i><span>Categories</span></a>
      <a href="recent.php"><i data-feather="clock"></i><span>Recent</span></a>
      <a href="popular.php"><i data-feather="trending-up"></i><span>Popular</span></a>
    </nav>
  </div>

  <!-- Bagian Bawah -->
  <div class="sidebar-footer">
    <a href="logout.php" class="signout-btn">Sign Out</a>
  </div>
</aside>

    <!-- Main Content -->
   <main class="main">
      <!-- Topbar -->
      <div class="topbar">
        <div class="topbar-left">
          <button class="toggle-sidebar icon-btn" id="toggleSidebarBtn">☰</button>
        </div>
        <div class="topbar-center">
  <input type="text" class="search-bar" id="searchInput" placeholder="Cari pertanyaan populer..." />
  <i data-feather="search" id="searchBtn" style="cursor:pointer;"></i>
</div>
        <div class="topbar-right">
          <button class="darkmode-toggle icon-btn" id="darkModeBtn">🌙</button>
          <div class="user-profile" onclick="window.location.href='edit_profil.php'">
          <img src="<?php echo $foto; ?>" alt="Profil" class="profile-pic">
           </div>
        </div>
      </div>

      <!-- Popular Questions -->
      <section class="popular-questions">
  <h2>Pertanyaan Populer</h2>

  

  <?php
  $query = "
  SELECT q.id, q.title, q.description, q.upvotes, q.tags, u.username
  FROM questions q
  JOIN users u ON q.user_id = u.id
  ORDER BY q.upvotes DESC
  LIMIT 10
";
$result = mysqli_query($conn, $query);
if (!$result) {
    die("Query error: " . mysqli_error($conn));
}

      if (mysqli_num_rows($result) > 0) {
      while ($row = mysqli_fetch_assoc($result)) {
          echo '<div class="question-card">';
          echo '<h3>📌 ' . htmlspecialchars($row['title']) . '</h3>';
          echo '<p>' . htmlspecialchars($row['description']) . '</p>';
          echo '<div class="meta">';
          echo '<span>👤 ' . htmlspecialchars($row['username']) . '</span> • ';
          echo '<span>👍 ' . (int)$row['upvotes'] . ' upvotes</span>';
          echo '</div>';
          echo '<div class="tags">';
          if (!empty($row['tags'])) {
              $tagList = explode(',', $row['tags']);
              foreach ($tagList as $tag) {
                  echo '<span>' . htmlspecialchars($tag) . '</span> ';
              }
          }
          echo '</div>';
          echo '</div>';
      }
  } else {
      echo '<p id="noResults" style="padding: 16px; background: #fff3f3; border: 1px solid #ffbdbd; border-radius: 8px; text-align: center; color: #b30000;">Tidak ada pertanyaan populer ditemukan.</p>';
  }
  ?>
</section>
    </main>
  </div>

  <footer class="main-footer">
  <p>© 2025 <strong>GenusTalks</strong>. All rights reserved.</p>
</footer>

  <!-- Script -->
  <script>
    const toggleBtn = document.getElementById('toggleSidebarBtn');
    const sidebar = document.getElementById('sidebar');
    const container = document.querySelector('.container');
    const darkModeBtn = document.getElementById('darkModeBtn');

    toggleBtn.addEventListener('click', () => {
      sidebar.classList.toggle('hidden');
      container.classList.toggle('full-width');
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
  document.addEventListener('DOMContentLoaded', () => {
    const searchBtn = document.getElementById('searchBtn');
    const searchInput = document.getElementById('searchInput');

    function filterQuestions() {
  const keyword = searchInput.value.toLowerCase();
  const questions = document.querySelectorAll('.question-card');
  let found = false;

  questions.forEach((card) => {
    const text = card.innerText.toLowerCase();
    const match = text.includes(keyword);
    card.style.display = match ? 'block' : 'none';
    if (match) found = true;
  });

  // Tampilkan atau sembunyikan pesan "tidak ada hasil"
  const noResults = document.getElementById('noResults');
  noResults.style.display = found ? 'none' : 'block';
}

    searchBtn.addEventListener('click', function(e) {
      e.preventDefault(); // penting!
      filterQuestions();
    });

    searchInput.addEventListener('keyup', function(e) {
      if (e.key === 'Enter') {
        filterQuestions();
      }
    });
  });
</script>

  <script src="https://unpkg.com/feather-icons"></script>

  <script>
  feather.replace();
</script>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const currentPage = window.location.pathname.split('/').pop();
    const links = document.querySelectorAll('.menu a');

    links.forEach(link => {
      const linkPage = link.getAttribute('href');
      if (linkPage === currentPage || (currentPage === '' && linkPage === 'dashboard.html')) {
        link.classList.add('active');
      } else {
        link.classList.remove('active');
      }
    });
  });
</script>

</body>
</html>