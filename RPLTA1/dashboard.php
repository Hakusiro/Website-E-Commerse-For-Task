<?php
include 'config/db.php';
include 'auth.php';

// Ambil data kategori dan produk
$kategori = $conn->query("SELECT * FROM kategori")->fetchAll(PDO::FETCH_ASSOC);
$produk = $conn->query("SELECT * FROM produk ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

// Ambil order terakhir dengan status 'diproses' jika user login
$lastOrder = null;
if (isLoggedIn()) {
    $user_id = $_SESSION['user']['id_user'];
    $lastOrder = $conn->query("SELECT * FROM pesanan WHERE id_user = $user_id AND status = 'diproses' ORDER BY id_pesanan DESC LIMIT 1")->fetch();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - E-Commerce</title>
    <style>
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
        <div><strong>E-COMMERCE</strong></div>
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
    <div>
        <div class="box">
            <h3>Kategori</h3>
            <ul>
                <?php foreach ($kategori as $k): ?>
                    <li><?= htmlspecialchars($k['nama_kategori']) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <?php if (isLoggedIn() && $lastOrder): ?>
            <div class="box">
                <form method="GET" action="order_item.php">
                    <input type="hidden" name="id_pesanan" value="<?= $lastOrder['id_pesanan'] ?>">
                    <button type="submit" class="order-button">Order</button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <!-- Main Content -->
    <div>
        <div class="banner">Selamat Berbelanja</div>

        <div class="box">
            <h3>Produk Terbaru</h3>
            <div class="product-grid">
                <?php foreach ($produk as $p): ?>
                    <div class="product-card">
                        <img src="assets/images/<?= htmlspecialchars($p['gambar_produk']) ?>" alt="<?= htmlspecialchars($p['nama_produk']) ?>">
                        <div><strong><?= htmlspecialchars($p['nama_produk']) ?></strong></div>
                        <div>Rp <?= number_format($p['harga'], 0, ',', '.') ?></div>

                        <?php if (isLoggedIn()): ?>
                            <form method="POST" action="place_order.php">
                                <input type="hidden" name="id_produk" value="<?= $p['id_produk'] ?>">
                                <input type="number" name="quantity" value="1" min="1" style="width: 60px;">
                                <button type="submit" class="add-btn">Beli</button>
                            </form>
                        <?php else: ?>
                            <a href="login.php"><button class="add-btn">Beli</button></a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

</body>
</html>
