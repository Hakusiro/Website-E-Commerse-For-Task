<?php
include 'config/db.php';
include 'auth.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$order_id = $_GET['id'] ?? null;
$user_id = $_SESSION['user']['user_id'];

if (!$order_id) {
    echo "ID pesanan tidak ditemukan.";
    exit;
}

// Validasi kepemilikan pesanan
$stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ? AND user_id = ?");
$stmt->execute([$order_id, $user_id]);
$order = $stmt->fetch();

if (!$order) {
    echo "Pesanan tidak ditemukan.";
    exit;
}

// Ambil rincian produk dalam pesanan
$stmt = $conn->prepare("
    SELECT od.*, p.name, p.image_url 
    FROM order_details od 
    JOIN products p ON od.product_id = p.product_id 
    WHERE od.order_id = ?
");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Siapkan query cek review
$reviewStmt = $conn->prepare("SELECT * FROM reviews WHERE order_id = ? AND product_id = ?");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Detail Pesanan</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; padding: 20px; }
        .box { background: white; padding: 20px; max-width: 800px; margin: auto; border-radius: 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background: #3b5998; color: white; }
        img { width: 60px; }
        textarea { width: 100%; padding: 8px; margin-top: 5px; }
        select { padding: 6px; }
        .back-link { margin-top: 15px; display: inline-block; text-decoration: none; color: #3b5998; }
    </style>
</head>
<body>

<div class="box">
    <h2>Detail Pesanan #<?= $order['order_id'] ?></h2>
    <p>Status: <strong><?= $order['status'] ?></strong></p>
    <p>Tanggal: <?= $order['order_date'] ?></p>

    <table>
        <thead>
            <tr>
                <th>Gambar</th>
                <th>Produk</th>
                <th>Jumlah</th>
                <th>Harga Satuan</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td><img src="assets/images/<?= $item['image_url'] ?>" alt="<?= $item['name'] ?>"></td>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td><?= $item['quantity'] ?></td>
                <td>Rp<?= number_format($item['price'] / $item['quantity'], 0, ',', '.') ?></td>
                <td>Rp<?= number_format($item['price'], 0, ',', '.') ?></td>
            </tr>
            <tr>
                <td colspan="5">
                    <?php
                    $reviewStmt->execute([$order['order_id'], $item['product_id']]);
                    $review = $reviewStmt->fetch();
                    ?>
                    <?php if ($order['status'] === 'selesai'): ?>
                        <?php if ($review): ?>
                            <strong>Ulasan Anda:</strong> ⭐<?= $review['rating'] ?> - <?= htmlspecialchars($review['comment']) ?>
                        <?php else: ?>
                            <form method="POST" action="submit_review.php" style="margin-top: 10px;">
                                <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                                <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                                <label>Rating:
                                    <select name="rating" required>
                                        <option value="">-</option>
                                        <option value="5">⭐ 5</option>
                                        <option value="4">⭐ 4</option>
                                        <option value="3">⭐ 3</option>
                                        <option value="2">⭐ 2</option>
                                        <option value="1">⭐ 1</option>
                                    </select>
                                </label>
                                <br>
                                <textarea name="comment" rows="2" placeholder="Tulis ulasan..." required></textarea><br>
                                <button type="submit">Kirim Ulasan</button>
                            </form>
                        <?php endif; ?>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h3>Total: Rp<?= number_format($order['total_price'], 0, ',', '.') ?></h3>
    <a href="orders.php" class="back-link">← Kembali ke Riwayat</a>
</div>

</body>
</html>
