<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id'];
$question_id = intval($_POST['question_id']);

// Cek apakah sudah like
$cek = mysqli_query($conn, "SELECT * FROM likes WHERE user_id = $user_id AND question_id = $question_id");

if (mysqli_num_rows($cek) > 0) {
    // Jika sudah like, hapus like
    mysqli_query($conn, "DELETE FROM likes WHERE user_id = $user_id AND question_id = $question_id");
} else {
    // Jika belum, tambahkan like
    mysqli_query($conn, "INSERT INTO likes (user_id, question_id) VALUES ($user_id, $question_id)");
}

header("Location: dashboard.php");
exit();
?>
