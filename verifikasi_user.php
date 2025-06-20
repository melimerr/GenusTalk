<?php
include 'koneksi.php';

if (isset($_POST['username'])) {
    $username = $_POST['username'];
    
    $query = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $query->bind_param("s", $username);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        session_start();
        $_SESSION['reset_user'] = $username;
        header("Location: reset_password.php");
        exit();
    } else {
        echo "User tidak ditemukan!";
    }
}
?>
