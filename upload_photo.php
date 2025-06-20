<?php
session_start();

if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit;
}

$username = $_SESSION['username'];

// Koneksi ke database
$conn = mysqli_connect("localhost", "root", "", "genustalks");

if (!$conn) {
  die("Koneksi gagal: " . mysqli_connect_error());
}

if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
  $fileTmp = $_FILES['photo']['tmp_name'];
  $fileName = basename($_FILES['photo']['name']);
  $targetDir = "uploads/";
  $targetPath = $targetDir . uniqid() . "_" . $fileName;

  if (!is_dir($targetDir)) {
    mkdir($targetDir, 0777, true);
  }

  if (move_uploaded_file($fileTmp, $targetPath)) {
    $stmt = $conn->prepare("UPDATE users SET photo = ? WHERE username = ?");
    if ($stmt) {
      $stmt->bind_param("ss", $targetPath, $username);
      $stmt->execute();
      echo "Foto berhasil diupload dan disimpan ke database.";
      $stmt->close();
    } else {
      echo "Prepare gagal: " . $conn->error;
    }
  } else {
    echo "Gagal memindahkan file.";
  }
} else {
  echo "File tidak tersedia atau error upload.";
}

$conn->close();
?>
<br><a href="dashboard.php">Kembali ke Dashboard</a>
