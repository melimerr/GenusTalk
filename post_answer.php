<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = intval($_SESSION['id']);
    $pertanyaan_id = intval($_POST['pertanyaan_id']);
    $jawaban = trim($_POST['jawaban']);

    if (!empty($jawaban)) {
        $stmt = $conn->prepare("INSERT INTO answers (question_id, user_id, answer_text, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iis", $pertanyaan_id, $user_id, $jawaban);

        if ($stmt->execute()) {
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Gagal menyimpan jawaban: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Jawaban tidak boleh kosong.";
    }
} else {
    echo "Akses tidak sah.";
}
?>
