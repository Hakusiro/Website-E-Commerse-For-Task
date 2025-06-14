<?php
include 'config/db.php';
include 'auth.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];

// Ambil riwayat pesanan user
$stmt = $conn->prepare("SELECT * FROM pesanan WHERE id_user = ? ORDER BY order_date DESC");
$stmt->execute([$user['id_user']]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ambil rekomendasi produk (sederhana: semua produk)
$rekomendasi = $conn->query("SELECT * FROM produk ORDER BY created_at DESC LIMIT 4")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Akun Pelanggan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: #f4f4f4;
        }

        header {
            background: #3b5998;
            color: white;
            padding: 10px 20px;
        }

        .container {
            display: grid;
            grid-template-columns: 220px 1fr;
            gap: 20px;
            padding: 20px;
        }

        .sidebar {
            background: white;
            border-radius: 8px;
            padding: 20px;
        }

        .sidebar h3 {
            margin-top: 0;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            margin: 10px 0;
        }

        .sidebar ul li a {
            text-decoration: none;
            color: #3b5998;
            font-weight: bold;
        }

        .main-content {
            background: white;
            border-radius: 8px;
            padding: 20px;
        }

        .section {
            margin-bottom: 30px;
        }

        .section h3 {
            border-bottom: 1px solid #ccc;
            padding-bottom: 8px;
        }

        .order {
            margin-bottom: 10px;
            padding: 10px;
            background: #f8f8f8;
            border-left: 4px solid #3b5998;
        }

        .product-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .product-card {
            width: 180px;
            background: #f9f9f9;
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 6px;
            text-align: center;
        }

        .product-card img {
            width: 100%;
            height: 120px;
            object-fit: cover;
        }

        .add-btn {
            background-color: #3b5998;
            color: white;
            border: none;
            padding: 6px 10px;
            margin-top: 8px;
            border-radius: 4px;
            cursor: pointer;
        }

        .add-btn:hover {
            background-color: #2e467f;
        }
        body { margin: 0; font-family: Arial, sans-serif; background: #f4f4f4; }
        header { background-color: #3b5998; padding: 10px 20px; color: white; }
        nav { display: flex; justify-content: space-between; align-items: center; }
        nav ul { list-style: none; display: flex; gap: 20px; margin: 0; padding: 0; }
        nav ul li a { color: white; text-decoration: none; font-weight: bold; }
        .container { display: grid; grid-template-columns: 220px 1fr; gap: 20px; padding: 20px; }
        .box { background: white; border-radius: 8px; padding: 15px; margin-bottom: 20px; }
        .banner { height: 180px; background: #ddd; border-radius: 8px; display: flex; justify-content: center; align-items: center; font-size: 28px; font-weight: bold; color: #333; }
        .product-grid { display: flex; flex-wrap: wrap; gap: 15px; }
        .product-card { width: 200px; background: #f9f9f9; padding: 10px; border: 1px solid #ccc; border-radius: 6px; text-align: center; }
        .product-card img { width: 100%; height: 120px; object-fit: cover; }
        .add-btn { background-color: #3b5998; color: white; border: none; padding: 8px; margin-top: 8px; border-radius: 4px; cursor: pointer; width: 100%; }
        .add-btn:hover { background-color: #2e467f; }
        .order-button { width: 100%; padding: 10px; background: #3b5998; color: white; border: none; border-radius: 5px; margin-top: 10px; cursor: pointer; }
        .order-button:hover { background: #2e467f; }
    </style>
</head>
<body>

<header>
    <nav>
        <h2>Akun Pelanggan: <?= htmlspecialchars($user['nama_lengkap']) ?></h2>
            <ul>
                <li><a href="dashboard.php">Home</a></li>
                <?php if (isLoggedIn()): ?>
                    <li><a href="account.php">Account</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
    </nav>
</header>

<div class="container">
    <!-- Sidebar -->
    <div class="sidebar">
        <h3>Menu Akun</h3>
        <ul>
            <li><a href="#">Riwayat Pesanan</a></li>
            <li><a href="submit_review.php">Ulasan</a></li>
            <li><a href="#">Pengaturan Akun</a></li>
            <li><a href="payment.php">Pembayaran</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Status Pemesanan -->
        <div class="section">
            <h3>Status Pemesanan</h3>
            <?php if (!empty($orders)): ?>
                <?php foreach ($orders as $order): ?>
                    <div class="order">
                        <strong>Order #<?= $order['id_pesanan'] ?></strong> - Status: <?= $order['status'] ?> <br>
                        Total: Rp <?= number_format($order['total_price'], 0, ',', '.') ?> <br>
                        Tanggal: <?= $order['order_date'] ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Belum ada pesanan.</p>
            <?php endif; ?>
        </div>

        <!-- Rekomendasi Produk -->
        <div class="section">
            <h3>Rekomendasi Produk</h3>
            <div class="product-grid">
                <?php foreach ($rekomendasi as $p): ?>
                    <div class="product-card">
                        <img src="assets/images/<?= htmlspecialchars($p['gambar_produk']) ?>" alt="<?= htmlspecialchars($p['nama_produk']) ?>">
                        <div><strong><?= htmlspecialchars($p['nama_produk']) ?></strong></div>
                        <div>Rp <?= number_format($p['harga'], 0, ',', '.') ?></div>
                        <form method="post" action="add_to_cart.php">
                            <input type="hidden" name="stok" value="<?= $p['stok'] ?>">
                            <button type="submit" class="add-btn">Tambah ke Keranjang</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

</body>
</html>
