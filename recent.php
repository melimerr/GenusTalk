<?php
session_start();
date_default_timezone_set('Asia/Jakarta');
include 'koneksi.php';


if (!isset($_SESSION['id']) || !isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$id = $_SESSION['id'];

// Ambil data user termasuk foto profil
$query = "SELECT foto_profil FROM users WHERE id = $id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);
$foto = !empty($user['foto_profil']) ? 'uploads/' . $user['foto_profil'] : 'assets/img/default-avatar.jpg';

// Ambil pertanyaan terbaru dari database
$questionQuery = "
  SELECT q.id, q.title, q.description, q.tags, q.created_at, u.username 
  FROM questions q
  JOIN users u ON q.user_id = u.id
  ORDER BY q.created_at DESC
  LIMIT 10
";
$questionResult = mysqli_query($conn, $questionQuery);
if (!$questionResult) {
    die("Query error: " . mysqli_error($conn));
}

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
  <title>GenusTalks - Recent Questions</title>
  <link rel="stylesheet" href="dashboard.css" />
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
        <a href="categories.php"><i data-feather="book-open"></i><span>Categories</span></a>
        <a href="recent.php"><i data-feather="clock"></i><span>Recent</span></a>
      </nav>
    </div>
    <div class="sidebar-footer">
      <button class="signout-btn" onclick="showLogoutModal()">Logout</button>
    </div>
  </aside>

  <!-- Main Content -->
  <main class="main">
    <div class="topbar">
      <div class="topbar-left">
        <button class="toggle-sidebar icon-btn" id="toggleSidebarBtn">☰</button>
      </div>
      <div class="topbar-center">
        <input type="text" id="searchInput" class="search-bar" placeholder="Cari di Categories..." />
        <button id="searchBtn" class="icon-btn" aria-label="Search">
          <i data-feather="search"></i>
        </button>
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

    <!-- Recent Questions -->
    <section class="recent-questions">
      <h2>Pertanyaan Terbaru</h2>
      <?php while ($row = mysqli_fetch_assoc($questionResult)):
    // Ambil foto profil dari username yang memposting pertanyaan
    $userQuery = "SELECT foto_profil FROM users WHERE username = '" . mysqli_real_escape_string($conn, $row['username']) . "'";
    $userResult = mysqli_query($conn, $userQuery);
    $userRow = mysqli_fetch_assoc($userResult);
    $fotoProfil = !empty($userRow['foto_profil']) ? 'uploads/' . $userRow['foto_profil'] : 'assets/img/default-avatar.jpg';
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

        <?php
          $question_id = $row['id'];
          $queryJawaban = "SELECT a.*, u.username, u.foto_profil 
                 FROM answers a 
                 JOIN users u ON a.user_id = u.id 
                 WHERE a.question_id = $question_id 
                 ORDER BY a.created_at ASC";
          $resultJawaban = mysqli_query($conn, $queryJawaban);

          if (mysqli_num_rows($resultJawaban) > 0) {
              echo '<div class="answers-list"><h4>Komentar:</h4>';
              while ($jawaban = mysqli_fetch_assoc($resultJawaban)) {
                $jawabanFoto = !empty($jawaban['foto_profil']) ? 'uploads/' . $jawaban['foto_profil'] : 'assets/img/default-avatar.jpg';
                $waktuJawaban = timeAgo($jawaban['created_at']);
                  echo '<div class="answer-item">';
                  echo '<div class="user-info">';
                  echo '<img src="' . htmlspecialchars($jawabanFoto) . '" alt="' . htmlspecialchars($jawaban['username']) . '" class="avatar">';
                  echo '<div class="user-meta">'; // Tambahkan pembungkus
                  echo '<div class="username">' . htmlspecialchars($jawaban['username']) . '</div>';
                  echo '<div class="waktu-jawab">' . $waktuJawaban . '</div>';
                  echo '</div>'; // Tutup user-meta
                  echo '</div>';
                  echo '<div class="text">' . htmlspecialchars($jawaban['answer_text']) . '</div>';
                  echo '</div>';
              }
              echo '</div>';
          } else {
              echo '<p style="color:#666; margin-top:10px;">Belum ada komentar. Jadilah yang pertama!</p>';
          }
          ?>

          <?php
  // Hitung jumlah like
  $qId = $row['id'];
  $likeResult = mysqli_query($conn, "SELECT COUNT(*) as total FROM likes WHERE question_id = $qId");
  $likeData = mysqli_fetch_assoc($likeResult);

  // Cek apakah user sudah like
  $liked = false;
  $checkLike = mysqli_query($conn, "SELECT * FROM likes WHERE user_id = $id AND question_id = $qId");
  if (mysqli_num_rows($checkLike) > 0) {
      $liked = true;
  }
?>
<form action="like.php" method="POST" style="margin-top:10px;">
  <input type="hidden" name="question_id" value="<?= $qId ?>">
  <button type="submit" name="like" class="like-btn" style="background:none; border:none; cursor:pointer; color:<?= $liked ? '#e0245e' : '#555'; ?>">
    ❤️ <?= $likeData['total'] ?>
  </button>
</form>

<!-- FORM -->
        </div>
        <!-- FORM -->
        <div class="answer-form">
  <form action="post_answer.php" method="POST" class="comment-form">
    <input type="hidden" name="pertanyaan_id" value="<?= $row['id'] ?>">
    <textarea name="jawaban" placeholder="Tulis komentar..." required></textarea>
    <div class="form-actions">
      <button type="submit">💬 Kirim Komentar</button>
    </div>
  </form>
</div>
            </div>
<?php endwhile; ?>
      <p id="noResults" style="display: none; color: #888; text-align: center; margin-top: 1rem;">
        Tidak ada pertanyaan yang cocok dengan pencarian Anda.
      </p>
    </div>
    </section>
  </main>
</div>

<footer class="main-footer">
  <p>&copy; <?= date('Y') ?> GenusTalks. All rights reserved.</p>
</footer>

<!-- JavaScript Section -->
<script src="https://unpkg.com/feather-icons"></script>
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

</body>
</html>
