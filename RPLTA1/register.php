<?php
include 'config/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = isset($_POST['nama_lengkap']) ? trim($_POST['nama_lengkap']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $konfirmasi = isset($_POST['konfirmasi_password']) ? $_POST['konfirmasi_password'] : '';
    $alamat = isset($_POST['alamat']) ? trim($_POST['alamat']) : '';
    $no_hp = isset($_POST['no_hp']) ? trim($_POST['no_hp']) : '';
    $role     = $_POST['role']; // ✅ Ambil role dari form
    $created  = date('Y-m-d H:i:s');

    if ($password !== $konfirmasi) {
        $error = "Konfirmasi password tidak sesuai.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $error = "Email sudah terdaftar.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $insert = $conn->prepare("INSERT INTO users (nama_lengkap, email, password, alamat, no_hp, role, created_at) 
                                      VALUES (?, ?, ?, ?, ?, ?, ?)");
            $insert->execute([$nama_lengkap, $email, $hashedPassword, $alamat, $no_hp, $role, $created]);
            header("Location: login.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - E-Commerce</title>
    <style>
        body {
            background-color: #f5f5f5;
            font-family: Arial, sans-serif;
        }
        .register-container {
            max-width: 400px;
            margin: 80px auto;
            padding: 30px;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .register-container h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }
        .register-container input[type="text"],
        .register-container input[type="email"],
        .register-container input[type="password"],
        .register-container textarea,
        .register-container select {
            width: 100%;
            padding: 12px;
            margin-bottom: 16px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }
        .register-container button {
            width: 100%;
            padding: 12px;
            background-color: #3b5998;
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        .register-container button:hover {
            background-color: #2e467f;
        }
        .register-container .error {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }
        .register-container .login-link {
            text-align: center;
            margin-top: 15px;
        }
        .register-container .login-link a {
            color: #3b5998;
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="register-container">
    <h2>Register</h2>

    <?php if ($error): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="konfirmasi_password" placeholder="Konfirmasi Password" required>
        <textarea name="address" placeholder="Alamat" rows="3" required></textarea>
        <input type="text" name="phone" placeholder="Nomor HP" required>

        <!-- ✅ Dropdown untuk memilih role -->
        <select name="role" required>
            <option value="">Pilih Peran</option>
            <option value="customer">Pembeli</option>
            <option value="admin">Admin</option>
        </select>

        <button type="submit">Register</button>
    </form>

    <div class="login-link">
        Sudah punya akun? <a href="login.php">Login di sini</a>
    </div>
</div>

</body>
</html>
