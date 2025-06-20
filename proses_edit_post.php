<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = intval($_POST['id']);
    $user_id = $_SESSION['id'];
    $judul = $_POST['judul'] ?? '';
    $isi = $_POST['isi'] ?? '';
    $tags = $_POST['tags'] ?? '';
    $gambar_lama = $_POST['gambar_lama'] ?? '';
    $hapus_gambar = isset($_POST['hapus_gambar']);
    $gambar_baru = $gambar_lama;

    // Validasi
    if (empty($judul) || empty($isi)) {
        echo "Judul dan isi tidak boleh kosong.";
        exit();
    }

    // Proses hapus gambar jika dicentang
    if ($hapus_gambar && !empty($gambar_lama)) {
        $file_path = 'uploads/' . $gambar_lama;
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        $gambar_baru = '';
    }

    // Proses upload gambar baru jika ada
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $new_filename = uniqid('img_') . '.' . strtolower($ext);
        $upload_path = 'uploads/' . $new_filename;

        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_path)) {
            // Hapus gambar lama jika ada
            if (!empty($gambar_lama) && file_exists("uploads/$gambar_lama")) {
                unlink("uploads/$gambar_lama");
            }
            $gambar_baru = $new_filename;
        } else {
            echo "❗ Upload gambar gagal.";
            exit();
        }
    }

    // Update query
    $stmt = $conn->prepare("UPDATE questions SET title = ?, description = ?, tags = ?, image = ? WHERE id = ? AND user_id = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ssssii", $judul, $isi, $tags, $gambar_baru, $post_id, $user_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        header("Location: dashboard.php?edit=success");
        exit();
    } else {
        echo "❗ Tidak ada perubahan atau Anda tidak memiliki akses.";
        echo "<br>Debug: Post ID = $post_id, User ID = $user_id";
    }
}
?>
