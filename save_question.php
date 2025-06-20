<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id'];
$title = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';
$tags = $_POST['tags'] ?? '';
$image = ''; // default kosong

// Validasi minimal
if (empty($title) || empty($description)) {
    echo "Judul dan deskripsi harus diisi.";
    exit;
}

// Upload gambar jika ada
if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
    $namaFile = basename($_FILES['gambar']['name']);
    $ext = pathinfo($namaFile, PATHINFO_EXTENSION);
    $namaBaru = uniqid() . '.' . $ext;
    $pathSimpan = 'uploads/' . $namaBaru;

    if (move_uploaded_file($_FILES['gambar']['tmp_name'], $pathSimpan)) {
        $image = $namaBaru;
    } else {
        echo "Upload gambar gagal.";
        exit;
    }
}

// Simpan ke database
$stmt = $conn->prepare("INSERT INTO questions (user_id, title, description, tags, image) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("issss", $user_id, $title, $description, $tags, $image);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    header("Location: dashboard.php?status=sukses");
    exit;
} else {
    echo "❗ Gagal menyimpan pertanyaan.";
}
?>
