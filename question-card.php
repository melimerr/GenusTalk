<?php
// components/question-card.php
if (!isset($row)) return;

$fotoProfilUser = !empty($row['foto_profil']) ? 'uploads/' . $row['foto_profil'] : 'assets/img/default-avatar.jpg';
?>
<div class="question-card">
  <div class="post">
    <div class="user-info">
      <img src="<?= htmlspecialchars($fotoProfilUser) ?>" alt="<?= htmlspecialchars($row['username']) ?>" class="avatar" />
      <div>
        <div class="username highlight-username"><?= htmlspecialchars($row['username']) ?></div>
        <div class="time"><?= timeAgo($row['created_at']) ?></div>
      </div>
    </div>
    <div class="question">
      <h3><strong><?= htmlspecialchars($row['title']) ?></strong></h3>
      <p><?= htmlspecialchars($row['description']) ?></p>
    </div>
    <div class="tags">
      <?php
        $tags = explode(' ', $row['tags']);
        foreach ($tags as $tag) {
          echo '<span>' . htmlspecialchars($tag) . '</span>';
        }
      ?>
    </div>
    <div class="post-actions">
      <span>🔍 <?= intval($row['jumlah_jawaban'] ?? 0) ?> Jawaban</span>
      <span>🔁 Repost</span>
    </div>
  </div>
</div>
