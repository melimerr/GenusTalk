<?php
session_start();
include 'koneksi.php';

// Pastikan user sudah login
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$id_user = $_SESSION['id'];

// Pastikan form dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $foto_profil = isset($_FILES['foto']['name']) ? $_FILES['foto']['name'] : '';
    $foto_tmp = isset($_FILES['foto']['tmp_name']) ? $_FILES['foto']['tmp_name'] : '';
    $upload_dir = "uploads/";

    if (!empty($foto_profil)) {
        $ekstensi = pathinfo($foto_profil, PATHINFO_EXTENSION);
        $nama_baru = "profil_" . $id_user . "_" . time() . "." . $ekstensi;
        $path_file = $upload_dir . $nama_baru;

        if (move_uploaded_file($foto_tmp, $path_file)) {
            $query = "UPDATE users SET fullname = ?, foto_profil = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssi", $nama, $nama_baru, $id_user);
        } else {
            echo "Gagal mengupload foto.";
            exit();
        }
    } else {
        $query = "UPDATE users SET fullname = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $nama, $id_user);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Profil berhasil diperbarui!'); window.location.href='dashboard.php';</script>";
    } else {
        echo "Gagal update: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Akses tidak sah.";
}
