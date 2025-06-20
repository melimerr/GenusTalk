<?php
include 'koneksi.php';
session_start();

if (isset($_SESSION['reset_user']) && isset($_POST['password_baru'])) {
    $username = $_SESSION['reset_user'];
    $password_baru = password_hash($_POST['password_baru'], PASSWORD_DEFAULT);

    $query = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
    $query->bind_param("ss", $password_baru, $username);
    
    if ($query->execute()) {
        unset($_SESSION['reset_user']);
        echo "Password berhasil diubah. <a href='login.php'>Login sekarang</a>";
    } else {
        echo "Gagal mengubah password.";
    }
}
?>
