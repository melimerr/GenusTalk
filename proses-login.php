<?php
session_start();
include 'koneksi.php'; // sambungkan ke database

// Ambil data dari form login
$username = $_POST['username'];
$password = $_POST['password'];

// Cek apakah user ada di database
$query = "SELECT * FROM users WHERE username = ? OR email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $username, $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // Cek password
    if (password_verify($password, $user['password'])) {
        // Set session
        $_SESSION['id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

        // Redirect ke dashboard
        header("Location: dashboard.php");
        exit();
    } else {
        // Password salah
        echo "<script>alert('Password salah!'); window.location.href='login.html';</script>";
    }
} else {
    // User tidak ditemukan
    echo "<script>alert('Username atau email tidak ditemukan!'); window.location.href='login.html';</script>";
}
?>
