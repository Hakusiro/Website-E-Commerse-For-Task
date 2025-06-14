<?php
include 'config/db.php';
include 'auth.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id_user'];

// Ubah jumlah
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_qty'])) {
    $detail_id = $_POST['id_order_detail'];
    $quantity = max(1, intval($_POST['quantity']));

    // Ambil harga per unit
    $stmt = $conn->prepare("SELECT id_produk FROM order_detail WHERE id_order_detail = ?");
    $stmt->execute([$detail_id]);
    $product = $stmt->fetch();

    $getPrice = $conn->prepare("SELECT harga FROM produk WHERE id_produk = ?");
    $getPrice->execute([$product['id_produk']]);
    $unitPrice = $getPrice->fetchColumn();

    $total = $quantity * $unitPrice;

    // Update detail dan total
    $conn->prepare("UPDATE order_detail SET quantity = ?, harga = ? WHERE id_order_detail = ?")
        ->execute([$quantity, $total, $detail_id]);

    $conn->prepare("UPDATE pesanan SET total_price = (
        SELECT SUM(harga) FROM order_detail WHERE id_pesanan = (
            SELECT id_pesanan FROM order_detail WHERE id_order_detail = ?
        )
    ) WHERE id_pesanan = (SELECT id_pesanan FROM order_detail WHERE id_order_detail = ?)")
        ->execute([$detail_id, $detail_id]);

    header("Location: my_orders.php");
    exit;
}

// Batalkan pesanan
if (isset($_POST['cancel_order']) && isset($_POST['id_pesanan'])) {
    $order_id = $_POST['id_pesanan'];

    $conn->beginTransaction();
    $conn->prepare("DELETE FROM order_detail WHERE id_pesanan = ?")->execute([$order_id]);
    $conn->prepare("DELETE FROM pesanan WHERE id_pesanan = ?")->execute([$order_id]);
    $conn->commit();

    header("Location: my_orders.php?cancelled=1");
    exit;
}

// Ambil semua pesanan aktif
$stmt = $conn->prepare("SELECT * FROM pesanan WHERE id_user = ? AND status = 'diproses' ORDER BY order_date DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pesanan Saya</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; padding: 20px; }
        .box { max-width: 900px; background: white; padding: 20px; margin: auto; border-radius: 8px; }
        h2 { margin-top: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background: #3b5998; color: white; }
        input[type=number] { width: 60px; padding: 5px; }
        button { padding: 5px 10px; }
        .cancel-btn { background: none; color: red; border: none; cursor: pointer; }
        .alert { background: #d4edda; padding: 10px; text-align: center; color: #155724; margin-bottom: 10px; }
        .modal {
            display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 999;
        }
        .modal-content {
            background: white; padding: 20px; border-radius: 8px; text-align: center;
        }
        .modal-content button { margin: 10px; }
    </style>
</head>
<body>

<div class="box">
    <h2>Pesanan Aktif</h2>

    <?php if (isset($_GET['cancelled'])): ?>
        <div class="alert">Pesanan berhasil dibatalkan.</div>
    <?php endif; ?>

    <?php if (count($orders) == 0): ?>
        <p>Pesanan anda sedang di proses.</p>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <h3>Order #<?= $order['id_pesanan'] ?> | Total: Rp<?= number_format($order['total_price'], 0, ',', '.') ?></h3>
            <form method="POST" onsubmit="return openCancelModal(<?= $order['id_pesanan'] ?>);">
                <button type="button" onclick="openCancelModal(<?= $order['id_pesanan'] ?>)" class="cancel-btn">❌ Batalkan Pesanan</button>
            </form>

            <?php
            $detailStmt = $conn->prepare("
                SELECT od.*, p.nama_produk, p.gambar_produk FROM order_detail od
                JOIN produk p ON od.id_produk = p.id_produk
                WHERE od.id_pesanan = ?
            ");
            $detailStmt->execute([$order['id_pesanan']]);
            $items = $detailStmt->fetchAll(PDO::FETCH_ASSOC);
            ?>

            <table>
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Gambar</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Subtotal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['nama_produk']) ?></td>
                            <td><img src="assets/images/<?= $item['gambar_produk'] ?>" alt="" width="60"></td>
                            <td>Rp<?= number_format($item['harga_satuan'] / $item['quantity'], 0, ',', '.') ?></td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="id_order_detail" value="<?= $item['id_order_detail'] ?>">
                                    <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1">
                                    <button type="submit" name="update_qty">Ubah</button>
                                </form>
                            </td>
                            <td>Rp<?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                            <td>-</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Modal Batalkan -->
<div id="cancelModal" class="modal">
    <div class="modal-content">
        <p>Apakah Anda yakin ingin membatalkan pesanan ini?</p>
        <form method="POST">
            <input type="hidden" id="orderToCancel" name="order_id">
            <button type="submit" name="cancel_order">Ya</button>
            <button type="button" onclick="closeCancelModal()">Cancel</button>
        </form>
    </div>
</div>

<script>
function openCancelModal(orderId) {
    document.getElementById("orderToCancel").value = orderId;
    document.getElementById("cancelModal").style.display = "flex";
    return false;
}

function closeCancelModal() {
    document.getElementById("cancelModal").style.display = "none";
}
</script>

</body>
</html>
