<?php
include 'config/db.php';
include 'auth.php';

if (!isLoggedIn() || $_SESSION['user']['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

$page = $_GET['page'] ?? 'summary';

// =================== RINGKASAN ===================
$totalProduk = $conn->query("SELECT COUNT(*) FROM produk")->fetchColumn();
$produkTerjual = $conn->query("SELECT SUM(quantity) FROM order_detail")->fetchColumn();
$totalPenjualan = $conn->query("SELECT SUM(total_price) FROM pesanan WHERE status = 'selesai'")->fetchColumn();

// =================== CRUD PRODUK ===================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_produk'])) {
    $name  = $_POST['name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $image = $_POST['image'];

    $conn->prepare("INSERT INTO produk (nama_produk, harga, stok, gambar_produk) VALUES (?, ?, ?, ?)")
         ->execute([$name, $price, $stock, $image]);

    header("Location: admin_dashboard.php?page=produk");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_produk'])) {
    $id    = $_POST['product_id'];
    $name  = $_POST['name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $image = $_POST['image'];

    $conn->prepare("UPDATE products SET name=?, price=?, stock=?, image_url=? WHERE product_id=?")
         ->execute([$name, $price, $stock, $image, $id]);

    header("Location: admin_dashboard.php?page=produk");
    exit;
}

if (isset($_GET['delete_product'])) {
    $id = $_GET['delete_product'];
    $conn->prepare("DELETE FROM products WHERE product_id = ?")->execute([$id]);
    header("Location: admin_dashboard.php?page=produk");
    exit;
}

$produk = $conn->query("SELECT * FROM produk ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
$pesanan = $conn->query("SELECT * FROM pesanan ORDER BY order_date DESC")->fetchAll(PDO::FETCH_ASSOC);

// =================== LAPORAN FILTER ===================
$tgl_awal  = $_GET['start_date'] ?? null;
$tgl_akhir = $_GET['end_date'] ?? null;
$filter = "";
$params = [];

if ($tgl_awal && $tgl_akhir) {
    $filter = "AND order_date BETWEEN ? AND ?";
    $params = [$tgl_awal, $tgl_akhir];
}

$stmt1 = $conn->prepare("SELECT SUM(quantity) FROM order_detail od JOIN pesanan o ON od.id_pesanan = o.id_pesanan WHERE o.status = 'selesai' $filter");
$stmt1->execute($params);
$produkTerjualFiltered = $stmt1->fetchColumn();

$stmt2 = $conn->prepare("SELECT SUM(total_price) FROM pesanan WHERE status = 'selesai' $filter");
$stmt2->execute($params);
$totalPenjualanFiltered = $stmt2->fetchColumn();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
        body { font-family: Arial; margin: 0; background: #f4f4f4; }
        header {
            background: #1e1e2f; color: white; padding: 15px 20px;
            display: flex; justify-content: space-between; align-items: center;
        }
        .logout-btn {
            background: #007bff; color: white; padding: 6px 12px;
            text-decoration: none; border-radius: 4px;
        }
        nav {
            background: #f1f1f1; padding: 10px 20px; display: flex; gap: 10px;
        }
        nav a {
            text-decoration: none; background: #ddd; padding: 8px 12px;
            border-radius: 5px; color: #000;
        }
        nav a.active {
            background: #3b5998; color: white;
        }
        .container { padding: 20px; }
        .summary { display: flex; gap: 20px; margin-top: 20px; }
        .box { background: white; padding: 15px; border-radius: 8px; flex: 1; text-align: center; }

        table {
            width: 100%; border-collapse: collapse; background: white;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ccc; padding: 10px; text-align: left;
        }
        th { background: #3b5998; color: white; }
        form input, form button {
            padding: 6px; margin: 5px 0; width: 100%;
        }
        form.edit-form input {
            width: 90%;
        }
        .btn-danger {
            background: red; color: white; padding: 6px 8px; border: none; cursor: pointer;
        }
    </style>
</head>
<body>

<header>
    <h2>Dashboard</h2>
    <div>
        <span>Admin</span>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</header>

<nav>
    <a href="admin_dashboard.php?page=summary" class="<?= $page === 'summary' ? 'active' : '' ?>">Ringkasan</a>
    <a href="admin_dashboard.php?page=produk" class="<?= $page === 'produk' ? 'active' : '' ?>">Daftar Produk</a>
    <a href="admin_dashboard.php?page=pesanan" class="<?= $page === 'pesanan' ? 'active' : '' ?>">Pesanan</a>
    <a href="admin_dashboard.php?page=laporan" class="<?= $page === 'laporan' ? 'active' : '' ?>">Laporan</a>
</nav>

<div class="container">
    <?php if ($page === 'summary'): ?>
        <h3>Ringkasan</h3>
        <div class="summary">
            <div class="box">
                <h4>Total Produk</h4>
                <p><?= $totalProduk ?></p>
            </div>
            <div class="box">
                <h4>Produk Terjual</h4>
                <p><?= $produkTerjual ?? 0 ?></p>
            </div>
            <div class="box">
                <h4>Total Penjualan</h4>
                <p>Rp<?= number_format($totalPenjualan ?? 0, 0, ',', '.') ?></p>
            </div>
        </div>

    <?php elseif ($page === 'produk'): ?>
        <h3>Daftar Produk</h3>
        <form method="POST">
            <input type="text" name="name" placeholder="Nama Produk" required>
            <input type="number" name="price" placeholder="Harga" required>
            <input type="number" name="stock" placeholder="Stok" required>
            <input type="text" name="image" placeholder="Nama File Gambar" required>
            <button type="submit" name="tambah_produk">Tambah Produk</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Gambar</th>
                    <th>Nama</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produk as $p): ?>
                <tr>
                    <form method="POST" class="edit-form">
                        <input type="hidden" name="id_produk" value="<?= $p['id_produk'] ?>">
                        <td><img src="assets/images/<?= $p['gambar_produk'] ?>" width="60"></td>
                        <td><input type="text" name="nama_produk" value="<?= htmlspecialchars($p['nama_produk']) ?>"></td>
                        <td><input type="number" name="harga" value="<?= $p['harga'] ?>"></td>
                        <td><input type="number" name="stok" value="<?= $p['stok'] ?>"></td>
                        <td>
                            <input type="text" name="gambar_produk" value="<?= htmlspecialchars($p['gambar_produk']) ?>" placeholder="image.jpg"><br>
                            <button type="submit" name="edit_produk">Simpan</button>
                            <a href="admin_dashboard.php?page=produk&delete_product=<?= $p['id_produk'] ?>" onclick="return confirm('Hapus produk ini?')" class="btn-danger">Hapus</a>
                        </td>
                    </form>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <?php elseif ($page === 'pesanan'): ?>
        <h3>Daftar Pesanan</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User ID</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pesanan as $o): ?>
                <tr>
                    <td>#<?= $o['id_pesanan'] ?></td>
                    <td><?= $o['id_user'] ?></td>
                    <td><?= $o['status'] ?></td>
                    <td>Rp<?= number_format($o['total_price'], 0, ',', '.') ?></td>
                    <td><?= $o['order_date'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <?php elseif ($page === 'laporan'): ?>
        <h3>Laporan Penjualan</h3>
        <form method="GET">
            <input type="hidden" name="page" value="laporan">
            <label>Dari Tanggal: <input type="date" name="start_date" value="<?= $tgl_awal ?>"></label>
            <label>Sampai Tanggal: <input type="date" name="end_date" value="<?= $tgl_akhir ?>"></label>
            <button type="submit">Filter</button>
        </form>

        <p><strong>Total Produk Terjual:</strong> <?= $produkTerjualFiltered ?? 0 ?></p>
        <p><strong>Total Pendapatan:</strong> Rp<?= number_format($totalPenjualanFiltered ?? 0, 0, ',', '.') ?></p>
    <?php endif; ?>
</div>

</body>
</html>
