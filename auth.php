<?php
session_start();
require 'koneksi.php';

$pesan = "";

if (isset($_POST['register'])) {
    $nama = trim($_POST['nama']);
    $password = $_POST['password'];

    if (empty($nama) || empty($password)) {
        $pesan = "Nama dan password wajib diisi.";
    } elseif (strlen($password) < 6) {
        $pesan = "Password minimal 6 karakter.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE nama = ?");
        $stmt->bind_param("s", $nama);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $pesan = "Registrasi gagal: Nama sudah terdaftar.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("INSERT INTO users (nama, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $nama, $hashed_password);
            if ($stmt->execute()) {
                $pesan = "Registrasi berhasil. Silakan login.";
            } else {
                $pesan = "Kesalahan server: " . $stmt->error;
            }
        }
        $stmt->close();
    }
}

if (isset($_POST['login'])) {
    $nama = trim($_POST['nama']);
    $password = $_POST['password'];

    if (empty($nama) || empty($password)) {
        $pesan = "Nama dan password wajib diisi.";
    } else {
        $stmt = $conn->prepare("SELECT password FROM users WHERE nama = ?");
        $stmt->bind_param("s", $nama);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($hashed_password);
        if ($stmt->num_rows > 0) {
            $stmt->fetch();
            if (password_verify($password, $hashed_password)) {
                $_SESSION['nama'] = $nama;
                header("Location: dashboard.php");
                exit();
            } else {
                $pesan = "Login gagal: Password salah.";
            }
        } else {
            $pesan = "Login gagal: Pengguna tidak ditemukan.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Login & Register</title>
</head>
<body>
<?php if ($pesan != "") echo "<h3>$pesan</h3>"; ?>

<h2>Registrasi</h2>
<form method="POST" action="">
    <input type="text" name="nama" placeholder="Nama Pengguna" required><br>
    <input type="password" name="password" placeholder="Password (min. 6 karakter)" required><br>
    <button type="submit" name="register">Register</button>
</form>

<h2>Login</h2>
<form method="POST" action="">
    <input type="text" name="nama" placeholder="Nama Pengguna" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit" name="login">Login</button>
</form>
</body>
</html>
