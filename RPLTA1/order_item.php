<?php
include 'config/db.php';
include 'auth.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$order_id = $_GET['id'] ?? null;
$user_id = $_SESSION['user']['user_id'];

// Validasi order milik user
$stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ? AND user_id = ?");
$stmt->execute([$order_id, $user_id]);
$order = $stmt->fetch();

if (!$order) {
    die("Pesanan tidak ditemukan.");
}

// Batalkan pesanan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_order'])) {
    $conn->beginTransaction();
    $conn->prepare("DELETE FROM order_details WHERE order_id = ?")->execute([$order_id]);
    $conn->prepare("DELETE FROM orders WHERE order_id = ?")->execute([$order_id]);
    $conn->commit();
    header("Location: dashboard.php?cancelled=1");
    exit;
}

// Update jumlah pesanan
if (isset($_POST['update_qty']) && isset($_POST['detail_id'])) {
    $detail_id = $_POST['detail_id'];
    $qty = max(1, intval($_POST['quantity']));

    // Dapatkan harga satuan produk
    $stmt = $conn->prepare("
        SELECT od.product_id, p.price FROM order_details od 
        JOIN products p ON od.product_id = p.product_id 
        WHERE od.detail_id = ?
    ");
    $stmt->execute([$detail_id]);
    $data = $stmt->fetch();
    $unit_price = $data['price'];
    $subtotal = $qty * $unit_price;

    $conn->prepare("UPDATE order_details SET quantity = ?, price = ? WHERE detail_id = ?")
         ->execute([$qty, $subtotal, $detail_id]);

    // Update total di orders
    $conn->prepare("UPDATE orders SET total_price = (
        SELECT SUM(price) FROM order_details WHERE order_id = ?
    ) WHERE order_id = ?")->execute([$order_id, $order_id]);

    header("Location: order_item.php?id=$order_id");
    exit;
}

// Ambil detail produk dalam pesanan
$stmt = $conn->prepare("
    SELECT od.*, p.name, p.image_url, p.price AS unit_price FROM order_details od 
    JOIN products p ON od.product_id = p.product_id 
    WHERE od.order_id = ?
");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Detail Pesanan</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; padding: 20px; }
        .container { max-width: 800px; background: white; margin: auto; padding: 20px; border-radius: 10px; }
        h2 { margin-top: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background: #3b5998; color: white; }
        img { width: 60px; }
        input[type=number] { width: 60px; padding: 6px; }
        button { padding: 6px 12px; }
        .cancel-btn { background: none; border: none; color: red; cursor: pointer; font-weight: bold; }
        .modal {
            display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 999;
        }
        .modal-content {
            background: white; padding: 20px; border-radius: 8px; text-align: center;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Detail Pesanan #<?= $order['order_id'] ?></h2>
    <p>Status: <strong><?= $order['status'] ?></strong></p>
    <p>Total Harga: <strong>Rp<?= number_format($order['total_price'], 0, ',', '.') ?></strong></p>

    <form method="POST" onsubmit="return openCancelModal();">
        <button type="button" onclick="openCancelModal()" class="cancel-btn">❌ Batalkan Pesanan</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Produk</th>
                <th>Gambar</th>
                <th>Harga Satuan</th>
                <th>Jumlah</th>
                <th>Subtotal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td><img src="assets/images/<?= $item['image_url'] ?>" alt=""></td>
                    <td>Rp<?= number_format($item['unit_price'], 0, ',', '.') ?></td>
                    <td>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="detail_id" value="<?= $item['detail_id'] ?>">
                            <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1">
                            <button type="submit" name="update_qty">Update</button>
                        </form>
                    </td>
                    <td>Rp<?= number_format($item['price'], 0, ',', '.') ?></td>
                    <td>-</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal Batalkan -->
<div id="cancelModal" class="modal">
    <div class="modal-content">
        <p>Apakah kamu yakin ingin membatalkan pesanan?</p>
        <form method="POST">
            <button type="submit" name="cancel_order">Ya</button>
            <button type="button" onclick="closeCancelModal()">Cancel</button>
        </form>
    </div>
</div>

<script>
    function openCancelModal() {
        document.getElementById('cancelModal').style.display = 'flex';
        return false;
    }
    function closeCancelModal() {
        document.getElementById('cancelModal').style.display = 'none';
    }
</script>

</body>
</html>
