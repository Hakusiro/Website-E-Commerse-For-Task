<?php
include 'config/db.php';
include 'auth.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['user_id'];

// Batalkan pesanan
if (isset($_GET['cancel']) && is_numeric($_GET['cancel'])) {
    $order_id = $_GET['cancel'];

    // Pastikan hanya membatalkan pesanan milik sendiri yang masih diproses
    $cek = $conn->prepare("SELECT * FROM orders WHERE order_id = ? AND user_id = ? AND status = 'diproses'");
    $cek->execute([$order_id, $user_id]);

    if ($cek->rowCount() > 0) {
        $conn->beginTransaction();
        $conn->prepare("DELETE FROM order_details WHERE order_id = ?")->execute([$order_id]);
        $conn->prepare("DELETE FROM orders WHERE order_id = ?")->execute([$order_id]);
        $conn->commit();
        header("Location: orders.php?batal=1");
        exit;
    }
}

// Tampilkan semua pesanan
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Riwayat Pesanan</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; padding: 20px; }
        .box { background: white; padding: 20px; max-width: 800px; margin: auto; border-radius: 8px; }
        .alert { padding: 10px; background: #d4edda; color: #155724; margin-bottom: 20px; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #3b5998; color: white; }
        a.cancel { color: red; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

<div class="box">
    <h2>Riwayat Pesanan</h2>

    <?php if (isset($_GET['batal'])): ?>
        <div class="alert">Pesanan berhasil dibatalkan.</div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Total</th>
                <th>Status</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
            <tr>
                <td>#<?= $order['order_id'] ?></td>
                <td>Rp<?= number_format($order['total_price'], 0, ',', '.') ?></td>
                <td><?= $order['status'] ?></td>
                <td><?= $order['order_date'] ?></td>
                <td>
                    <?php if ($order['status'] === 'diproses'): ?>
                        <a href="add_to_order.php?order_id=<?= $order['order_id'] ?>">Tambah Barang</a> |
                        <a href="orders.php?cancel=<?= $order['order_id'] ?>" class="cancel" onclick="return confirm('Yakin ingin membatalkan pesanan ini?')">Batalkan</a>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
