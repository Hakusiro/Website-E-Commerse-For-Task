<?php
include 'config/db.php';
include 'auth.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id_user'];

// Ambil produk
$products = $conn->query("SELECT * FROM produk WHERE stok > 0")->fetchAll(PDO::FETCH_ASSOC);

// Proses form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['id_produk'];
    $quantity = intval($_POST['quantity']);

    // Ambil harga & stok produk
    $stmt = $conn->prepare("SELECT * FROM produk WHERE id_produk = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();

    if (!$product || $quantity < 1 || $quantity > $product['stok']) {
        die("Jumlah tidak valid atau stok habis.");
    }

    $total_price = $quantity * $product['harga'];

    // Insert ke orders
    $orderStmt = $conn->prepare("INSERT INTO pesanan (id_user, total_price, status, order_date) VALUES (?, ?, 'diproses', NOW())");
    $orderStmt->execute([$user_id, $total_price]);
    $order_id = $conn->lastInsertId();

    // Insert ke order_details
    $detailStmt = $conn->prepare("INSERT INTO order_detail (id_pesanan, id_produk, quantity, harga_satuan) VALUES (?, ?, ?, ?)");
    $detailStmt->execute([$order_id, $product_id, $quantity, $total_price]);

    // Update stok produk
    $conn->prepare("UPDATE produk SET stok = stok - ? WHERE id_produk = ?")
         ->execute([$quantity, $product_id]);

    header("Location: my_orders.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Form Pemesanan</title>
    <style>
        body { font-family: Arial; background: #f0f0f0; padding: 30px; }
        .box { background: white; max-width: 400px; margin: auto; padding: 20px; border-radius: 8px; }
        label { display: block; margin-bottom: 10px; }
        select, input[type=number] {
            width: 100%; padding: 10px; margin-bottom: 15px;
        }
        button {
            padding: 10px 15px;
            background: #3b5998;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
        }
    </style>
</head>
<body>

<div class="box">
    <h2>Form Pemesanan</h2>
    <form method="POST">
        <label for="product_id">Pilih Produk</label>
        <select name="product_id" required>
            <option value="">-- Pilih --</option>
            <?php foreach ($products as $p): ?>
                <option value="<?= $p['product_id'] ?>">
                    <?= htmlspecialchars($p['name']) ?> (Stok: <?= $p['stock'] ?>, Rp<?= number_format($p['price'], 0, ',', '.') ?>)
                </option>
            <?php endforeach; ?>
        </select>

        <label for="quantity">Jumlah</label>
        <input type="number" name="quantity" min="1" value="1" required>

        <button type="submit">Pesan Sekarang</button>
    </form>
</div>

</body>
</html>
