<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id']) || !isset($_POST['id'])) {
    header("Location: my_questions.php");
    exit();
}

$id_user = $_SESSION['id'];
$id_post = intval($_POST['id']);

// Pastikan user adalah pemilik postingan
$checkQuery = "SELECT * FROM questions WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($checkQuery);
$stmt->bind_param("ii", $id_post, $id_user);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: my_questions.php?error=notauthorized");
    exit();
}

// Hapus gambar jika ada
$row = $result->fetch_assoc();
if (!empty($row['image']) && file_exists('uploads/' . $row['image'])) {
    unlink('uploads/' . $row['image']);
}

// Hapus komentar dan likes
$conn->query("DELETE FROM answers WHERE question_id = $id_post");
$conn->query("DELETE FROM likes WHERE question_id = $id_post");

// Hapus pertanyaan
$conn->query("DELETE FROM questions WHERE id = $id_post");

header("Location: my_questions.php?deleted=success");
exit();
?>
