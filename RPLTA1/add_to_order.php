<?php
include 'config/db.php';
include 'auth.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$order_id = $_GET['order_id'] ?? null;
$user_id = $_SESSION['user']['user_id'];

if (!$order_id) {
    echo "Order ID tidak ditemukan.";
    exit;
}

// Cek apakah order milik user dan status diproses
$stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ? AND user_id = ? AND status = 'diproses'");
$stmt->execute([$order_id, $user_id]);
$order = $stmt->fetch();

if (!$order) {
    echo "Pesanan tidak ditemukan atau tidak bisa diedit.";
    exit;
}

// Ambil semua produk
$produk = $conn->query("SELECT * FROM products WHERE stock > 0")->fetchAll(PDO::FETCH_ASSOC);

// Proses penambahan barang
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $qty = intval($_POST['quantity']);

    $stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
    $stmt->execute([$product_id]);
    $item = $stmt->fetch();

    if (!$item || $qty > $item['stock']) {
        echo "Stok tidak mencukupi atau produk tidak valid.";
        exit;
    }

    // Cek apakah produk sudah ada di order_details
    $stmt = $conn->prepare("SELECT * FROM order_details WHERE order_id = ? AND product_id = ?");
    $stmt->execute([$order_id, $product_id]);
    $existing = $stmt->fetch();

    if ($existing) {
        // Update qty
        $stmt = $conn->prepare("UPDATE order_details SET quantity = quantity + ?, price = price + ? WHERE detail_id = ?");
        $stmt->execute([$qty, $item['price'] * $qty, $existing['detail_id']]);
    } else {
        // Insert baru
        $stmt = $conn->prepare("INSERT INTO order_details (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$order_id, $product_id, $qty, $item['price'] * $qty]);
    }

    // Update stok
    $conn->prepare("UPDATE products SET stock = stock - ? WHERE product_id = ?")->execute([$qty, $product_id]);

    // Update total harga
    $conn->prepare("UPDATE orders SET total_price = total_price + ? WHERE order_id = ?")
         ->execute([$item['price'] * $qty, $order_id]);

    header("Location: orders.php?update=1");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Barang ke Pesanan</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; padding: 20px; }
        .box { background: white; padding: 20px; max-width: 600px; margin: auto; border-radius: 8px; }
        form input, form select { width: 100%; padding: 10px; margin-bottom: 15px; }
        form button {
            padding: 10px 15px;
            background: #3b5998;
            color: white;
            border: none;
            border-radius: 5px;
        }
        a.cancel { display: inline-block; margin-top: 10px; text-decoration: none; color: #999; }
    </style>
</head>
<body>

<div class="box">
    <h2>Tambah Barang ke Pesanan #<?= $order_id ?></h2>
    <form method="POST">
        <label for="product_id">Pilih Produk:</label>
        <select name="product_id" required>
            <option value="">-- Pilih Produk --</option>
            <?php foreach ($produk as $p): ?>
                <option value="<?= $p['product_id'] ?>">
                    <?= htmlspecialchars($p['name']) ?> (Rp<?= number_format($p['price'], 0, ',', '.') ?>, Stok: <?= $p['stock'] ?>)
                </option>
            <?php endforeach; ?>
        </select>

        <label for="quantity">Jumlah:</label>
        <input type="number" name="quantity" min="1" required>

        <button type="submit">Tambah ke Pesanan</button>
    </form>

    <a href="orders.php" class="cancel">← Kembali ke Riwayat Pesanan</a>
</div>

</body>
</html>
