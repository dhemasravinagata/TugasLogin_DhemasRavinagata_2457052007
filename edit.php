<?php
session_start();
require 'koneksi.php';

// Pastikan admin login
if (!isset($_SESSION['nama']) || $_SESSION['nama'] !== 'admin1') {
    header("Location: dashboard.php");
    exit();
}

// Pastikan ada parameter id
if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$id = intval($_GET['id']);

// Ambil data user dari database
$stmt = $conn->prepare("SELECT nama FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($nama_lama);
if (!$stmt->fetch()) {
    $stmt->close();
    header("Location: dashboard.php");
    exit();
}
$stmt->close();

// Proses update
if (isset($_POST['update'])) {
    $nama_baru = trim($_POST['nama']);
    $password_baru = $_POST['password'];

    if (!empty($password_baru)) {
        $hashed_password = password_hash($password_baru, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE users SET nama=?, password=? WHERE id=?");
        $stmt->bind_param("ssi", $nama_baru, $hashed_password, $id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET nama=? WHERE id=?");
        $stmt->bind_param("si", $nama_baru, $id);
    }

    $stmt->execute();
    $stmt->close();
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Edit Data Pengguna</title>
</head>
<body>
<h2>Edit Data Pengguna</h2>
<form method="POST">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    Nama Pengguna:<br>
    <input type="text" name="nama" value="<?php echo htmlspecialchars($nama_lama); ?>" required><br>
    Password Baru:<br>
    <input type="password" name="password" placeholder="Kosongkan jika tidak diubah"><br><br>
    <button type="submit" name="update">Simpan Perubahan</button>
    <a href="dashboard.php"><button type="button">Batal</button></a>
</form>
</body>
</html>