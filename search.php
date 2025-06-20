<?php
date_default_timezone_set('Asia/Jakarta');
include 'koneksi.php';

$keyword = isset($_GET['q']) ? trim($_GET['q']) : '';

if ($keyword === '') {
    echo ''; // kosongkan jika tidak ada keyword
    exit;
}

$keyword = "%" . $conn->real_escape_string($keyword) . "%";

$query = "SELECT q.*, u.username, u.foto_profil, 
                 (SELECT COUNT(*) FROM answers a WHERE a.question_id = q.id) AS jumlah_jawaban
          FROM questions q 
          JOIN users u ON q.user_id = u.id 
          WHERE q.title LIKE ? OR q.description LIKE ? OR q.tags LIKE ?
          ORDER BY q.created_at DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("sss", $keyword, $keyword, $keyword);
$stmt->execute();
$result = $stmt->get_result();

function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;

    if ($diff < 60) return 'Baru saja';
    elseif ($diff < 3600) return floor($diff / 60) . ' menit yang lalu';
    elseif ($diff < 86400) return floor($diff / 3600) . ' jam yang lalu';
    elseif ($diff < 2592000) return floor($diff / 86400) . ' hari yang lalu';
    else return date('j F Y', $timestamp);
}

if ($result->num_rows === 0) {
    echo '<p class="no-result" style="padding: 16px; background: #fff3f3; border: 1px solid #ffbdbd; border-radius: 8px; text-align: center; color: #b30000;">Tidak ada hasil ditemukan untuk kata kunci tersebut.</p>';
} else {

    function highlightKeyword($text, $keyword) {
    if (empty($keyword)) return $text;
    return preg_replace("/(" . preg_quote($keyword) . ")/i", '<span class="highlight">$1</span>', $text);
}

    while ($row = $result->fetch_assoc()) {
        $foto = !empty($row['foto_profil']) ? 'uploads/' . $row['foto_profil'] : 'assets/img/default-avatar.jpg';
        $tags = explode(' ', $row['tags']);

        echo '<div class="question-card">
            <div class="post">
                <div class="user-info">
                    <img src="' . htmlspecialchars($foto) . '" class="avatar" alt="' . htmlspecialchars($row['username']) . '" />
                    <div>
                        <div class="username highlight-username">' . htmlspecialchars($row['username']) . '</div>
                        <div class="time">' . timeAgo($row['created_at']) . '</div>
                    </div>
                </div>
                <div class="question">
                    <h3><strong>' . highlightKeyword(htmlspecialchars($row['title']), $_GET['q']) . '</strong></h3>
                    <p>' . highlightKeyword(htmlspecialchars($row['description']), $_GET['q']) . '</p>
                </div>
                <div class="tags">';
                    foreach ($tags as $tag) {
                        echo '<span>' . htmlspecialchars($tag) . '</span>';
                    }
        echo '</div>
                <div class="post-actions">
                    <span>🔍 ' . intval($row['jumlah_jawaban'] ?? 0) . ' Jawaban</span>
                    <span>🔁 Repost</span>
                </div>
            </div>
        </div>';
    }
}

$stmt->close();
?>
