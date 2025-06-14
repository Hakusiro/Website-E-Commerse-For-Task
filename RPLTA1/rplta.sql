-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 14 Jun 2025 pada 08.30
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rplta`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `nama_admin` varchar(100) NOT NULL,
  `email_admin` varchar(100) NOT NULL,
  `password_admin` varchar(255) NOT NULL,
  `level` enum('super','staff') DEFAULT 'staff'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id_admin`, `nama_admin`, `email_admin`, `password_admin`, `level`) VALUES
(1, 'Admin Utama', 'admin@rplta.com', 'hashed_admin_pass', 'super');

-- --------------------------------------------------------

--
-- Struktur dari tabel `diskon`
--

CREATE TABLE `diskon` (
  `id_diskon` int(11) NOT NULL,
  `kode_diskon` varchar(50) DEFAULT NULL,
  `nama_diskon` varchar(100) DEFAULT NULL,
  `jenis_diskon` enum('persen','potongan') DEFAULT 'persen',
  `nilai_diskon` int(11) DEFAULT NULL,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_berakhir` date DEFAULT NULL,
  `batas_penggunaan` int(11) DEFAULT NULL,
  `status_aktif` tinyint(1) DEFAULT 1,
  `create_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `feedback`
--

CREATE TABLE `feedback` (
  `id_feedback` int(11) NOT NULL,
  `nama_pengguna` varchar(100) NOT NULL,
  `email_pengguna` varchar(100) NOT NULL,
  `pesan_feedback` text NOT NULL,
  `rating` int(1) DEFAULT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `tanggal_feedback` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `feedback`
--

INSERT INTO `feedback` (`id_feedback`, `nama_pengguna`, `email_pengguna`, `pesan_feedback`, `rating`, `tanggal_feedback`) VALUES
(1, 'Dewi', 'dewi@mail.com', 'Sangat puas dengan pelayanan.', 5, '2025-06-13 10:47:17');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `nama_kategori`, `deskripsi`) VALUES
(1, 'Elektronik', 'Produk-produk elektronik seperti laptop, gadget, dan aksesoris'),
(2, 'Fashion', 'Pakaian, sepatu, dan aksesoris fashion'),
(3, 'Peralatan Rumah Tangga', 'Produk untuk keperluan rumah tangga');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kontak`
--

CREATE TABLE `kontak` (
  `id_kontak` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `pesan` text NOT NULL,
  `tanggal_kirim` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kontak`
--

INSERT INTO `kontak` (`id_kontak`, `nama`, `email`, `pesan`, `tanggal_kirim`) VALUES
(1, 'Budi', 'budi@mail.com', 'Saya ingin tahu lebih lanjut tentang produk.', '2025-06-13 10:47:17');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kurir`
--

CREATE TABLE `kurir` (
  `id_kurir` int(11) NOT NULL,
  `nama_kurir` varchar(100) NOT NULL,
  `layanan` varchar(100) DEFAULT NULL,
  `estimasi_pengiriman` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kurir`
--

INSERT INTO `kurir` (`id_kurir`, `nama_kurir`, `layanan`, `estimasi_pengiriman`) VALUES
(1, 'JNE', 'Reguler', '2-3 hari'),
(2, 'SiCepat', 'Best', '1-2 hari');

-- --------------------------------------------------------

--
-- Struktur dari tabel `notifikasi`
--

CREATE TABLE `notifikasi` (
  `id_notifikasi` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `pesan` text NOT NULL,
  `tanggal_dibuat` timestamp NOT NULL DEFAULT current_timestamp(),
  `sudah_dibaca` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `notifikasi`
--

INSERT INTO `notifikasi` (`id_notifikasi`, `id_user`, `pesan`, `tanggal_dibuat`, `sudah_dibaca`) VALUES
(1, 1, 'Pesanan Anda sedang dikemas.', '2025-06-13 10:47:17', 0),
(2, 2, 'Pembayaran Anda sedang diverifikasi.', '2025-06-13 10:47:17', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `order_detail`
--

CREATE TABLE `order_detail` (
  `id_order_detail` int(11) NOT NULL,
  `id_pesanan` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `harga_satuan` decimal(10,2) NOT NULL,
  `subtotal` decimal(12,2) DEFAULT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `order_detail`
--

INSERT INTO `order_detail` (`id_order_detail`, `id_pesanan`, `id_produk`, `jumlah`, `harga_satuan`, `subtotal`, `quantity`) VALUES
(1, 1, 1, 1, 350000.00, 350000.00, 0),
(2, 1, 2, 1, 150000.00, 150000.00, 0),
(3, 2, 2, 1, 150000.00, 150000.00, 0),
(4, 14, 4, 0, 200000.00, NULL, 1),
(5, 15, 4, 0, 200000.00, NULL, 1),
(6, 16, 4, 0, 200000.00, NULL, 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id_pembayaran` int(11) NOT NULL,
  `id_pesanan` int(11) NOT NULL,
  `metode_pembayaran` varchar(50) DEFAULT NULL,
  `status_pembayaran` enum('belum bayar','menunggu verifikasi','lunas','gagal') DEFAULT 'belum bayar',
  `tanggal_pembayaran` datetime DEFAULT NULL,
  `total_bayar` decimal(12,2) DEFAULT NULL,
  `bukti_transfer` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pembayaran`
--

INSERT INTO `pembayaran` (`id_pembayaran`, `id_pesanan`, `metode_pembayaran`, `status_pembayaran`, `tanggal_pembayaran`, `total_bayar`, `bukti_transfer`) VALUES
(1, 1, 'Transfer Bank', 'lunas', '2025-06-13 17:47:17', 500000.00, 'bukti1.jpg'),
(2, 2, 'E-Wallet', 'menunggu verifikasi', '2025-06-13 17:47:17', 150000.00, 'bukti2.jpg');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengiriman`
--

CREATE TABLE `pengiriman` (
  `id_pengiriman` int(11) NOT NULL,
  `id_pesanan` int(11) DEFAULT NULL,
  `id_kurir` int(11) DEFAULT NULL,
  `alamat_pengiriman` text DEFAULT NULL,
  `tanggal_kirim` datetime DEFAULT NULL,
  `estimasi_tiba` date DEFAULT NULL,
  `tanggal_terima` date DEFAULT NULL,
  `ongkir_kirim` int(11) DEFAULT NULL,
  `status_pengiriman` enum('belum dikirim','dikirim','diterima') DEFAULT 'belum dikirim',
  `nomor_resi` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pesanan`
--

CREATE TABLE `pesanan` (
  `id_pesanan` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_kurir` int(11) DEFAULT NULL,
  `tanggal_pesanan` datetime DEFAULT current_timestamp(),
  `status_pesanan` enum('pending','diproses','dikirim','selesai','dibatalkan') DEFAULT 'pending',
  `alamat_pengiriman` text DEFAULT NULL,
  `total_harga` decimal(12,2) DEFAULT NULL,
  `status` enum('diproses','dikirim','selesai','dibatalkan') DEFAULT 'diproses',
  `total_price` int(11) DEFAULT 0,
  `order_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pesanan`
--

INSERT INTO `pesanan` (`id_pesanan`, `id_user`, `id_kurir`, `tanggal_pesanan`, `status_pesanan`, `alamat_pengiriman`, `total_harga`, `status`, `total_price`, `order_date`) VALUES
(1, 1, 1, '2025-06-13 17:47:17', 'pending', 'Jl. Mawar No. 5, Bandung', 500000.00, 'diproses', 0, '2025-06-14 12:46:18'),
(2, 2, 2, '2025-06-13 17:47:17', 'diproses', 'Jl. Melati No. 10, Jakarta', 150000.00, 'diproses', 0, '2025-06-14 12:46:18'),
(3, 3, NULL, '2025-06-14 12:46:21', 'pending', NULL, NULL, 'diproses', 350000, '2025-06-14 12:46:21'),
(4, 3, NULL, '2025-06-14 12:46:47', 'pending', NULL, NULL, 'diproses', 350000, '2025-06-14 12:46:47'),
(5, 3, NULL, '2025-06-14 12:47:03', 'pending', NULL, NULL, 'diproses', 350000, '2025-06-14 12:47:03'),
(6, 3, NULL, '2025-06-14 12:47:11', 'pending', NULL, NULL, 'diproses', 350000, '2025-06-14 12:47:11'),
(7, 3, NULL, '2025-06-14 12:48:13', 'pending', NULL, NULL, 'diproses', 350000, '2025-06-14 12:48:13'),
(8, 3, NULL, '2025-06-14 12:48:21', 'pending', NULL, NULL, 'diproses', 350000, '2025-06-14 12:48:21'),
(9, 3, NULL, '2025-06-14 12:48:36', 'pending', NULL, NULL, 'diproses', 350000, '2025-06-14 12:48:36'),
(10, 3, NULL, '2025-06-14 12:49:38', 'pending', NULL, NULL, 'diproses', 350000, '2025-06-14 12:49:38'),
(11, 3, NULL, '2025-06-14 12:49:43', 'pending', NULL, NULL, 'diproses', 350000, '2025-06-14 12:49:43'),
(12, 3, NULL, '2025-06-14 12:49:46', 'pending', NULL, NULL, 'diproses', 250000, '2025-06-14 12:49:46'),
(13, 3, NULL, '2025-06-14 12:57:34', 'pending', NULL, NULL, 'diproses', 200000, '2025-06-14 12:57:34'),
(14, 3, NULL, '2025-06-14 12:59:47', 'pending', NULL, NULL, 'diproses', 200000, '2025-06-14 12:59:47'),
(15, 3, NULL, '2025-06-14 13:00:06', 'pending', NULL, NULL, 'diproses', 200000, '2025-06-14 13:00:06'),
(16, 3, NULL, '2025-06-14 13:25:58', 'pending', NULL, NULL, 'diproses', 200000, '2025-06-14 13:25:58');

-- --------------------------------------------------------

--
-- Struktur dari tabel `produk`
--

CREATE TABLE `produk` (
  `id_produk` int(11) NOT NULL,
  `nama_produk` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `harga` decimal(10,2) NOT NULL,
  `stok` int(11) NOT NULL,
  `gambar_produk` varchar(255) DEFAULT NULL,
  `id_kategori` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `produk`
--

INSERT INTO `produk` (`id_produk`, `nama_produk`, `deskripsi`, `harga`, `stok`, `gambar_produk`, `id_kategori`, `created_at`) VALUES
(1, 'Keyboard Mechanical', 'Keyboard RGB dengan switch biru', 350000.00, 50, 'keyboard.jpg', 1, '2025-06-13 10:52:29'),
(2, 'Mouse Gaming', 'Mouse dengan DPI hingga 16000', 250000.00, 75, 'mouse.jpg', 1, '2025-06-13 10:52:29'),
(3, 'Kaos Polos', 'Kaos bahan cotton combed 30s', 80000.00, 100, 'kaos.jpg', 2, '2025-06-13 10:52:29'),
(4, 'Makanan', NULL, 200000.00, 198, '.jpg', NULL, '2025-06-14 05:54:20');

-- --------------------------------------------------------

--
-- Struktur dari tabel `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `comment` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `alamat` text DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `role` enum('customer','admin') DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id_user`, `nama_lengkap`, `email`, `password`, `alamat`, `no_hp`, `role`, `created_at`) VALUES
(1, 'Andi Wijaya', 'andi@gmail.com', 'hashed_password_1', 'Jl. Mawar No. 5, Bandung', '081234567890', 'customer', '2025-06-14 05:05:20'),
(2, 'Siti Rahma', 'siti@gmail.com', 'hashed_password_2', 'Jl. Melati No. 10, Jakarta', '082345678901', 'customer', '2025-06-14 05:05:20'),
(3, '', 'apa@gmail.com', '$2y$10$uGy34t6wpNcdU6tLpmp2QuE0ruOs6AcrT9.XWDcHWojfTHZQZRMjC', '', '', 'customer', '2025-06-14 00:21:40'),
(4, '', 'aa@gmail.com', '$2y$10$Wtn74/WxHU/I4slV/Qx0EugCYnni2G8hOhV5QdXHZeiOxS4yzMM5W', '', '', 'admin', '2025-06-14 00:50:02');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `email_admin` (`email_admin`);

--
-- Indeks untuk tabel `diskon`
--
ALTER TABLE `diskon`
  ADD PRIMARY KEY (`id_diskon`);

--
-- Indeks untuk tabel `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id_feedback`);

--
-- Indeks untuk tabel `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indeks untuk tabel `kontak`
--
ALTER TABLE `kontak`
  ADD PRIMARY KEY (`id_kontak`);

--
-- Indeks untuk tabel `kurir`
--
ALTER TABLE `kurir`
  ADD PRIMARY KEY (`id_kurir`);

--
-- Indeks untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD PRIMARY KEY (`id_notifikasi`),
  ADD KEY `id_user` (`id_user`);

--
-- Indeks untuk tabel `order_detail`
--
ALTER TABLE `order_detail`
  ADD PRIMARY KEY (`id_order_detail`),
  ADD KEY `id_pesanan` (`id_pesanan`),
  ADD KEY `id_produk` (`id_produk`);

--
-- Indeks untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD KEY `id_pesanan` (`id_pesanan`);

--
-- Indeks untuk tabel `pengiriman`
--
ALTER TABLE `pengiriman`
  ADD PRIMARY KEY (`id_pengiriman`),
  ADD KEY `id_pesanan` (`id_pesanan`),
  ADD KEY `id_kurir` (`id_kurir`);

--
-- Indeks untuk tabel `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id_pesanan`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_kurir` (`id_kurir`);

--
-- Indeks untuk tabel `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id_produk`),
  ADD KEY `id_kategori` (`id_kategori`);

--
-- Indeks untuk tabel `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `diskon`
--
ALTER TABLE `diskon`
  MODIFY `id_diskon` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id_feedback` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `kontak`
--
ALTER TABLE `kontak`
  MODIFY `id_kontak` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `kurir`
--
ALTER TABLE `kurir`
  MODIFY `id_kurir` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  MODIFY `id_notifikasi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `order_detail`
--
ALTER TABLE `order_detail`
  MODIFY `id_order_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `pengiriman`
--
ALTER TABLE `pengiriman`
  MODIFY `id_pengiriman` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id_pesanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT untuk tabel `produk`
--
ALTER TABLE `produk`
  MODIFY `id_produk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD CONSTRAINT `notifikasi_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `order_detail`
--
ALTER TABLE `order_detail`
  ADD CONSTRAINT `order_detail_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_detail_ibfk_2` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pesanan`
--
ALTER TABLE `pesanan`
  ADD CONSTRAINT `pesanan_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `pesanan_ibfk_2` FOREIGN KEY (`id_kurir`) REFERENCES `kurir` (`id_kurir`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `produk`
--
ALTER TABLE `produk`
  ADD CONSTRAINT `produk_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
