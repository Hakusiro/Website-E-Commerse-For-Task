<?php
include 'config/db.php';
include 'auth.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id    = $_SESSION['user']['user_id'];
    $product_id = $_POST['product_id'];
    $order_id   = $_POST['order_id'];
    $rating     = $_POST['rating'];
    $comment    = trim($_POST['comment']);

    // Cegah duplikat ulasan
    $cek = $conn->prepare("SELECT * FROM reviews WHERE user_id = ? AND product_id = ? AND order_id = ?");
    $cek->execute([$user_id, $product_id, $order_id]);

    if ($cek->rowCount() === 0) {
        $stmt = $conn->prepare("INSERT INTO reviews (user_id, product_id, order_id, rating, comment) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $product_id, $order_id, $rating, $comment]);
    }

    header("Location: order_detail.php?id=$order_id");
    exit;
}
?>
