<!-- index.php -->
<?php include("includes/navbar.php"); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>E-Commerce</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header style="text-align: center; padding: 40px; background-color: #d8d8d8;">
        <img src="images/cart.png" alt="Cart Icon" style="width: 80px;">
        <h1>ORDER NOW</h1>
    </header>

    <main style="display: flex; justify-content: center; margin: 40px;">
        <div style="width: 80%; display: flex; gap: 30px; background-color: white; padding: 30px; border-radius: 10px;">
            <!-- Sidebar Kategori -->
            <aside style="flex: 1;">
                <h2>Kategori</h2>
                <ul>
                    <li><a href="index.php?category=Elektronik">Elektronik</a></li>
                    <li><a href="index.php?category=Kesehatan & Kecantikan">Kesehatan & Kecantikan</a></li>
                    <li><a href="index.php?category=Makanan & Minuman">Makanan & Minuman</a></li>
                    <li><a href="index.php?category=Pakaian & Fashion">Pakaian & Fashion</a></li>
                    <li><a href="index.php">Tampilkan Semua</a></li>
                </ul>
            </aside>

            <!-- Featured Products -->
            <section style="flex: 3;">
                <h2>FEATURED PRODUCTS</h2>

                <div style="display: flex; gap: 20px;">
                    <!-- Produk 1 -->
                    <div style="border: 1px solid #ccc; padding: 10px; width: 45%;">
                        <img src="images/product1.jpg" alt="Product 1" style="width: 100%;">
                        <h3>Kesehatan & Kecantikan</h3>
                        <p>Rp. 123.000</p>
                        <button>Add to cart</button><br>
                        <span>★★★★★</span>
                    </div>

                    <!-- Produk 2 -->
                    <div style="border: 1px solid #ccc; padding: 10px; width: 45%;">
                        <img src="images/product2.jpg" alt="Product 2" style="width: 100%;">
                        <h3>Elektronik</h3>
                        <p>Rp. 123.000</p>
                        <button>Add to cart</button><br>
                        <span>★★★★★</span>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <footer style="text-align: center; padding: 20px; background-color: #2d4a4a; color: white;">
        © 2025 E-Commerce. All rights reserved.
    </footer>
</body>
</html>
