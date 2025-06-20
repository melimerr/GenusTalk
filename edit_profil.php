<?php
session_start();
include 'koneksi.php';

// Cek login
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$id_user = $_SESSION['id'];

// Ambil data user
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Proses form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];

    // Update data dasar
    $query = "UPDATE users SET fullname = ?, username = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $fullname, $username, $id_user);
    $stmt->execute();

    // Hapus foto profil jika diminta
    if (isset($_POST['hapus_foto']) && !empty($user['foto_profil'])) {
        $file_path = 'uploads/' . $user['foto_profil'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        $stmt = $conn->prepare("UPDATE users SET foto_profil = NULL WHERE id = ?");
        $stmt->bind_param("i", $id_user);
        $stmt->execute();
    }

    // Upload foto baru jika ada
    if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] === UPLOAD_ERR_OK) {
        $foto_name = basename($_FILES['foto_profil']['name']);
        $foto_tmp = $_FILES['foto_profil']['tmp_name'];
        $target_dir = "uploads/";
        $target_file = $target_dir . time() . '_' . $foto_name;

        // Pindahkan file ke folder uploads/
        if (move_uploaded_file($foto_tmp, $target_file)) {
            $filename_only = basename($target_file);
            $stmt = $conn->prepare("UPDATE users SET foto_profil = ? WHERE id = ?");
            $stmt->bind_param("si", $filename_only, $id_user);
            $stmt->execute();
        }
    }

    // Redirect ke dashboard setelah simpan
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Profil</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        h2 {
            color: #7c0022;
            text-align: center;
        }

        form {
            max-width: 500px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        img {
            max-width: 100%;
            margin-top: 10px;
            border-radius: 8px;
        }

        input[type="checkbox"] {
            margin-top: 10px;
        }

        button {
            width: 100%;
            padding: 12px;
            margin-top: 20px;
            background-color: #7c0022;
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background-color: #5a0019;
        }

        a {
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
            color: #7c0022;
        }
    </style>
</head>
<body>
    <h2>Edit Profil</h2>
    <form action="edit_profil.php" method="POST" enctype="multipart/form-data">
        <label for="fullname">Nama Lengkap</label>
        <input type="text" name="fullname" id="fullname" value="<?= htmlspecialchars($user['fullname']) ?>" required>

        <label for="username">Username</label>
        <input type="text" name="username" id="username" value="<?= htmlspecialchars($user['username']) ?>" required>

        <label>Foto Profil Saat Ini:</label>
        <?php if (!empty($user['foto_profil'])): ?>
            <img src="uploads/<?= htmlspecialchars($user['foto_profil']) ?>" alt="Foto Profil">
            <br>
            <input type="checkbox" name="hapus_foto" id="hapus_foto">
            <label for="hapus_foto">Hapus gambar ini</label>
        <?php else: ?>
            <p><i>Tidak ada foto profil</i></p>
        <?php endif; ?>

        <label for="foto_profil">Ganti Gambar (opsional):</label>
        <input type="file" name="foto_profil" id="foto_profil" accept="image/*">

        <button type="submit" name="simpan">Simpan Perubahan</button>
        <a href="dashboard.php">← Kembali ke Dashboard</a>
    </form>
</body>
</html>
