<?php
session_start();
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: dashboard.php");
            exit();
        } else {
            echo "<script>alert('Password salah!'); window.location.href='login.html';</script>";
        }
    } else {
        echo "<script>alert('Username tidak ditemukan!'); window.location.href='login.html';</script>";
    }
} else {
    header("Location: login.html");
    exit();
}
?>
