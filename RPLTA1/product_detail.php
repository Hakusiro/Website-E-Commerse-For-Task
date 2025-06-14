<?php
include 'config/db.php';
include 'auth.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    echo "Produk tidak ditemukan.";
    exit;
}

$product_id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    echo "Produk tidak ditemukan.";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Detail Produk</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; padding: 20px; }
        .container { background: white; max-width: 600px; margin: auto; padding: 20px; border-radius: 10px; }
        img { width: 100%; max-height: 300px; object-fit: contain; }
        .price { color: green; font-size: 24px; margin: 10px 0; }
        input[type=number] { width: 100%; padding: 8px; margin-top: 5px; }
        .btn { padding: 10px 15px; background: #3b5998; color: white; border: none; margin-top: 15px; cursor: pointer; }
        .btn.cancel { background: #ccc; color: #000; margin-left: 10px; }
        .total { font-weight: bold; margin-top: 10px; font-size: 18px; }
    </style>
</head>
<body>

<div class="container">
    <h2><?= htmlspecialchars($product['name']) ?></h2>
    <img src="assets/images/<?= htmlspecialchars($product['image_url']) ?>" alt="Produk">

    <p class="price">Rp <span id="unitPrice"><?= number_format($product['price'], 0, ',', '.') ?></span></p>
    <p><?= htmlspecialchars($product['description']) ?></p>

    <form method="POST" action="place_order.php">
        <label for="qty">Jumlah:</label>
        <input type="number" id="qty" name="quantity" min="1" max="<?= $product['stock'] ?>" value="1" required>

        <div class="total">
            Total Harga: Rp <span id="totalPrice"><?= number_format($product['price'], 0, ',', '.') ?></span>
        </div>

        <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
        <input type="hidden" name="unit_price" value="<?= $product['price'] ?>">

        <button class="btn" type="submit">Pesan Sekarang</button>
        <a href="dashboard.php" class="btn cancel">Batalkan</a>
    </form>
</div>

<script>
    const qtyInput = document.getElementById("qty");
    const unitPrice = <?= $product['price'] ?>;
    const totalPrice = document.getElementById("totalPrice");

    qtyInput.addEventListener("input", () => {
        const qty = parseInt(qtyInput.value || 1);
        const total = qty * unitPrice;
        totalPrice.textContent = total.toLocaleString('id-ID');
    });
</script>

</body>
</html>
