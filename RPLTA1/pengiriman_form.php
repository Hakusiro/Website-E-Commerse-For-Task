<?php
include 'config/db.php';
include 'auth.php';

// Hanya admin
if (!isLoggedIn() || $_SESSION['user']['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

$pesanan = $conn->query("SELECT * FROM pesanan WHERE status_pesanan = 'menunggu konfirmasi'")->fetchAll(PDO::FETCH_ASSOC);
$kurir = $conn->query("SELECT * FROM kurir WHERE status_aktif = 1")->fetchAll(PDO::FETCH_ASSOC);

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pesanan = $_POST['id_pesanan'];
    $id_kurir = $_POST['id_kurir'];
    $alamat = $_POST['alamat_pengiriman'];
    $tanggal_kirim = $_POST['tanggal_kirim'];
    $estimasi_tiba = $_POST['estimasi_tiba'];
    $ongkir = intval($_POST['ongkir_kirim']);
    $resi = $_POST['nomor_resi'];

    $stmt = $conn->prepare("INSERT INTO pengiriman 
        (id_pesanan, id_kurir, alamat_pengiriman, tanggal_kirim, estimasi_tiba, ongkir_kirim, status_pengiriman, nomor_resi)
        VALUES (?, ?, ?, ?, ?, ?, 'dikirim', ?)");
    $stmt->execute([$id_pesanan, $id_kurir, $alamat, $tanggal_kirim, $estimasi_tiba, $ongkir, $resi]);

    $conn->prepare("UPDATE pesanan SET status_pesanan = 'dikirim' WHERE id_pesanan = ?")->execute([$id_pesanan]);

    $success = "Data pengiriman berhasil disimpan.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Input Pengiriman</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; padding: 30px; }
        .box { background: white; padding: 20px; border-radius: 8px; max-width: 600px; margin: auto; }
        label { display: block; margin-top: 10px; }
        input, select, textarea { width: 100%; padding: 10px; margin-top: 5px; }
        button { padding: 10px 20px; background: #3b5998; color: white; border: none; border-radius: 6px; margin-top: 20px; }
        .success { background: #dfd; padding: 10px; margin-top: 10px; border-radius: 5px; }
    </style>
</head>
<body>
<div class="box">
    <h2>Input Data Pengiriman</h2>

    <?php if ($success): ?>
        <div class="success"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Pesanan</label>
        <select name="id_pesanan" required>
            <option value="">-- Pilih --</option>
            <?php foreach ($pesanan as $p): ?>
                <option value="<?= $p['id_pesanan'] ?>">#<?= $p['id_pesanan'] ?> - Rp<?= number_format($p['total_harga'], 0, ',', '.') ?></option>
            <?php endforeach; ?>
        </select>

        <label>Kurir</label>
        <select name="id_kurir" required>
            <option value="">-- Pilih --</option>
            <?php foreach ($kurir as $k): ?>
                <option value="<?= $k['id_kurir'] ?>"><?= htmlspecialchars($k['nama_kurir']) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Alamat Pengiriman</label>
        <textarea name="alamat_pengiriman" rows="3" required></textarea>

        <label>Nomor Resi</label>
        <input type="text" name="nomor_resi" required>

        <label>Ongkir</label>
        <input type="number" name="ongkir_kirim" required>

        <label>Tanggal Kirim</label>
        <input type="datetime-local" name="tanggal_kirim" required>

        <label>Estimasi Tiba</label>
        <input type="date" name="estimasi_tiba" required>

        <button type="submit">Simpan Pengiriman</button>
    </form>
</div>
</body>
</html>
