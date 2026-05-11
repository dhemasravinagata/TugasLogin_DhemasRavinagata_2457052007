<?php
session_start();
require 'koneksi.php';

// Pastikan sudah login
if (!isset($_SESSION['nama'])) {
    header("Location: auth.php");
    exit();
}

$nama_user = $_SESSION['nama'];

// Proses delete user
if (isset($_GET['delete']) && $nama_user === 'admin1') {
    $id_delete = intval($_GET['delete']);
    $conn->query("DELETE FROM users WHERE id = $id_delete");
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard Admin</title>
<style>
table { border-collapse: collapse; width: 400px; margin-bottom: 20px; }
th, td { border: 1px solid #000; padding: 5px; text-align: left; }
button { margin: 2px; }
</style>
</head>
<body>
<h2>Selamat Datang, <?php echo htmlspecialchars($nama_user); ?>!</h2>
<a href="logout.php"><button>Logout</button></a>

<?php if ($nama_user === 'admin1'): ?>
    <h3>Menu Admin: Kelola User</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Aksi</th>
        </tr>
        <?php
        $result = $conn->query("SELECT id, nama FROM users ORDER BY id DESC");
        while ($row = $result->fetch_assoc()):
        ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['nama']); ?></td>
            <td>
                <a href="edit.php?id=<?php echo $row['id']; ?>"><button>Edit</button></a>
                <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Yakin ingin dihapus?')"><button>Hapus</button></a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p>Dashboard user biasa, tidak ada akses tabel.</p>
<?php endif; ?>
</body>
</html>