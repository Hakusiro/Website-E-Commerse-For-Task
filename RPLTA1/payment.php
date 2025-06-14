<?php
include 'config/db.php';
include 'auth.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$pesanan_id = $_GET['id'] ?? null;
$user_id = $_SESSION['user']['id_user'];

// Cek apakah pesanan valid
$stmt = $conn->prepare("SELECT * FROM pesanan WHERE id_pesanan = ? AND id_user = ?");
$stmt->execute([$pesanan_id, $user_id]);
$pesanan = $stmt->fetch();

if (!$pesanan) {
    die("Pesanan tidak ditemukan atau bukan milik Anda.");
}

$error = "";
$success = "";

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $metode = $_POST['metode'];
    $jumlah = intval($_POST['jumlah_bayar']);
    $tanggal = date('Y-m-d H:i:s');
    $kode = 'TRX' . strtoupper(uniqid());

    if ($jumlah < $pesanan['total_harga']) {
        $error = "Jumlah bayar tidak boleh kurang dari total pesanan.";
    } else {
        // Masukkan ke tabel pembayaran
        $stmt = $conn->prepare("INSERT INTO pembayaran (id_pesanan, kode_transaksi, status_pembayaran, tanggal_bayar, metode_pembayaran, jumlah_bayar)
                                VALUES (?, ?, 'lunas', ?, ?, ?)");
        $stmt->execute([$pesanan_id, $kode, $tanggal, $metode, $jumlah]);

        // Update status pesanan (opsional)
        $conn->prepare("UPDATE pesanan SET status_pesanan = 'menunggu konfirmasi' WHERE id_pesanan = ?")->execute([$pesanan_id]);

        $success = "Pembayaran berhasil!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Form Pembayaran</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; padding: 20px; }
        .container { max-width: 600px; margin: auto; background: white; padding: 20px; border-radius: 8px; }
        label { display: block; margin-top: 10px; }
        input, select { width: 100%; padding: 10px; margin-top: 5px; }
        button { margin-top: 20px; padding: 10px 20px; background: #3b5998; color: white; border: none; border-radius: 6px; cursor: pointer; }
        .alert { padding: 10px; margin-top: 10px; border-radius: 5px; }
        .error { background: #fdd; color: red; }
        .success { background: #dfd; color: green; }
    </style>
</head>
<body>

<div class="container">
    <h2>Konfirmasi Pembayaran</h2>

    <p><strong>ID Pesanan:</strong> <?= $pesanan['id_pesanan'] ?></p>
    <p><strong>Tanggal Pesanan:</strong> <?= $pesanan['tanggal_pesanan'] ?></p>
    <p><strong>Total Harga:</strong> Rp <?= number_format($pesanan['total_harga'], 0, ',', '.') ?></p>

    <?php if ($error): ?>
        <div class="alert error"><?= $error ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert success"><?= $success ?></div>
    <?php else: ?>
        <form method="POST">
            <label for="metode">Metode Pembayaran</label>
            <select name="metode" required>
                <option value="">-- Pilih Metode --</option>
                <option value="Transfer Bank">Transfer Bank</option>
                <option value="E-Wallet">E-Wallet</option>
                <option value="COD">COD (Bayar di Tempat)</option>
            </select>

            <label for="jumlah_bayar">Jumlah Bayar</label>
            <input type="number" name="jumlah_bayar" min="<?= $pesanan['total_harga'] ?>" required>

            <a href="payment.php?id=<?= $order['id_pesanan'] ?>">Bayar Sekarang</a>
        </form>
    <?php endif; ?>
</div>

</body>
</html>
