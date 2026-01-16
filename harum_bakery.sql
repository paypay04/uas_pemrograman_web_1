-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 16 Jan 2026 pada 18.19
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
-- Database: `harum_bakery`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `icon`, `created_at`) VALUES
(1, 'Cupcakes', 'cupcakes', 'Berbagai macam cupcake dengan topping yang menarik', 'fas fa-cupcake', '2026-01-15 15:08:37'),
(2, 'Bread', 'bread', 'Roti segar dipanggang setiap hari', 'fas fa-bread-slice', '2026-01-15 15:08:37'),
(3, 'Cakes', 'cakes', 'Kue ulang tahun dan kue spesial lainnya', 'fas fa-birthday-cake', '2026-01-15 15:08:37'),
(4, 'Cookies', 'cookies', 'Kue kering berbagai rasa', 'fas fa-cookie-bite', '2026-01-15 15:08:37'),
(5, 'Donuts', 'donuts', 'Donut dengan berbagai topping', 'fas fa-donut', '2026-01-15 15:08:37'),
(6, 'Pastries', 'pastries', 'Pastry renyah dan lezat', 'fas fa-pie', '2026-01-15 15:08:37'),
(7, 'Traditional', 'traditional', 'Kue tradisional Indonesia', 'fas fa-home', '2026-01-15 15:08:37'),
(8, 'Healthy', 'healthy', 'Produk rendah gula dan sehat', 'fas fa-heart', '2026-01-15 15:08:37');

-- --------------------------------------------------------

--
-- Struktur dari tabel `favorites`
--

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `shipping_address` text NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `order_number`, `total_amount`, `status`, `shipping_address`, `payment_method`, `notes`, `created_at`) VALUES
(1, 2, 'ORD-20240101-001', 125000.00, 'delivered', 'Jl. Mawar No. 12, Bandung', 'bank_transfer', 'Tolong dibungkus rapi ya', '2026-01-15 15:08:37'),
(2, 3, 'ORD-20240102-002', 85000.00, 'shipped', 'Jl. Melati No. 8, Surabaya', 'credit_card', 'Kirim pagi hari', '2026-01-15 15:08:37'),
(3, 4, 'ORD-20240103-003', 220000.00, 'processing', 'Jl. Kenanga No. 5, Yogyakarta', 'bank_transfer', 'Kue ulang tahun, tolong hiasannya', '2026-01-15 15:08:37'),
(4, 5, 'ORD-20240104-004', 75000.00, 'pending', 'Jl. Tulip No. 3, Semarang', 'cash_on_delivery', '', '2026-01-15 15:08:37'),
(5, 2, 'ORD-20240110-010', 65000.00, 'delivered', 'Jl. Mawar No. 12, Bandung', 'credit_card', 'Untuk sarapan', '2026-01-15 15:08:37'),
(6, 3, 'ORD-20240111-011', 280000.00, 'cancelled', 'Jl. Melati No. 8, Surabaya', 'bank_transfer', 'Cancel karena acara batal', '2026-01-15 15:08:37'),
(7, 4, 'ORD-20240112-012', 90000.00, 'delivered', 'Jl. Kenanga No. 5, Yogyakarta', 'credit_card', 'Tambah krim', '2026-01-15 15:08:37'),
(8, 5, 'ORD-20240113-013', 150000.00, 'shipped', 'Jl. Tulip No. 3, Semarang', 'bank_transfer', '', '2026-01-15 15:08:37');

-- --------------------------------------------------------

--
-- Struktur dari tabel `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `discount_price` decimal(10,2) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `is_featured` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `products`
--

INSERT INTO `products` (`id`, `name`, `slug`, `description`, `price`, `discount_price`, `category_id`, `image_url`, `stock`, `is_featured`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Basque Cheesecake', 'basque-cheesecake', 'Cheesecake Basque klasik dengan pinggiran gosong yang khas dan bagian dalam yang lembut dan creamy.', 85000.00, 75000.00, 3, '1.jpg', 15, 1, 1, '2026-01-15 18:11:49', '2026-01-15 18:20:09'),
(2, 'Croissant', 'croissant', 'Croissant klasik Prancis dengan tekstur berlapis dan mentega yang harum.', 28000.00, NULL, 6, '12.jpg', 40, 0, 1, '2026-01-15 18:11:49', '2026-01-15 18:20:10'),
(3, 'Choco Croissant', 'choco-croissant', 'Croissant dengan isian cokelat yang lumer saat dipanaskan.', 35000.00, 30000.00, 6, '13.jpg', 22, 1, 1, '2026-01-15 18:11:49', '2026-01-15 18:20:10'),
(4, 'Strawberry Croissant', 'strawberry-croissant', 'Croissant dengan isian selai stroberi segar dan taburan gula halus.', 32000.00, 28000.00, 6, '4.jpg', 25, 1, 1, '2026-01-15 18:11:49', '2026-01-15 18:20:10'),
(5, 'Dark Choco Cookies', 'dark-choco-cookies', 'Cookies cokelat hitam dengan potongan cokelat yang melimpah dan rasa yang kaya.', 25000.00, NULL, 4, '3.jpg', 50, 0, 1, '2026-01-15 18:11:49', '2026-01-15 18:20:10'),
(6, 'Matcha Chocolate Chip Cookies', 'matcha-chocolate-chip-cookies', 'Cookies matcha dengan potongan cokelat chip, perpaduan rasa unik.', 30000.00, NULL, 4, '10.jpg', 45, 0, 1, '2026-01-15 18:11:49', '2026-01-15 18:20:10'),
(7, 'Red Velvet Cookies', 'red-velvet-cookies', 'Cookies red velvet dengan cream cheese frosting dan taburan cokelat putih.', 35000.00, 32000.00, 4, '11.jpg', 28, 1, 1, '2026-01-15 18:11:49', '2026-01-15 18:20:10'),
(8, 'Biskuit Lotus Biscoff', 'biskuit-lotus-biscoff', 'Biskuit karamel dengan rasa spesial khas Belgia.', 22000.00, 20000.00, 4, '8.jpg', 40, 0, 1, '2026-01-15 18:11:49', '2026-01-15 18:20:10'),
(9, 'Roti Sourdough', 'roti-sourdough', 'Roti sourdough tradisional dengan rasa asam yang khas dan tekstur kenyal.', 40000.00, NULL, 2, '5.jpg', 20, 0, 1, '2026-01-15 18:11:49', '2026-01-15 18:20:10'),
(10, 'Choco Cromboloni', 'choco-cromboloni', 'Pastri Italia dengan isian cokelat yang melimpah dan taburan gula bubuk.', 38000.00, 33000.00, 6, '6.jpg', 18, 1, 1, '2026-01-15 18:11:49', '2026-01-15 18:20:10'),
(11, 'Pistachio Cromboloni', 'pistachio-cromboloni', 'Cromboloni dengan isian krim pistachio dan taburan pistachio cincang.', 42000.00, 37000.00, 6, '9.jpg', 12, 1, 1, '2026-01-15 18:11:49', '2026-01-15 18:20:10'),
(12, 'Danish Pastri Krim Keju Bluberi', 'danish-pastri-krim-keju-bluberi', 'Pastri Denmark dengan krim keju dan topping blueberry segar.', 45000.00, 40000.00, 6, '14.jpg', 15, 1, 1, '2026-01-15 18:11:49', '2026-01-15 18:24:28'),
(13, 'Strawberry Danish', 'strawberry-danish', 'Danish pastri dengan isian stroberi segar dan glaze yang manis.', 40000.00, 35000.00, 6, '15.jpg', 20, 0, 1, '2026-01-15 18:11:49', '2026-01-15 18:25:05'),
(14, 'Choux Pastry', 'choux-pastry', 'Pastri choux klasik yang bisa diisi dengan berbagai macam krim.', 32000.00, NULL, 6, '16.jpg', 30, 0, 1, '2026-01-15 18:11:49', '2026-01-15 18:25:05'),
(15, 'Croffel', 'croffel', 'Perpaduan sempurna antara croissant dan waffle dengan tekstur renyah di luar dan lembut di dalam.', 35000.00, 30000.00, 5, '2.jpg', 30, 1, 1, '2026-01-15 18:11:49', '2026-01-15 18:25:05'),
(16, 'Morning Buns', 'morning-buns', 'Roti manis dengan rasa kayu manis dan gula yang cocok untuk sarapan.', 28000.00, NULL, 5, '7.jpg', 35, 0, 1, '2026-01-15 18:11:49', '2026-01-15 18:25:05');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `profile_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `phone`, `address`, `role`, `profile_image`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@harumbakery.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin Harum', '081234567890', 'Jl. Bakery No. 1, Jakarta', 'admin', 'admin.jpg', '2026-01-15 15:08:37', '2026-01-15 15:08:37'),
(2, 'sari_dewi', 'sari.dewi@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sari Dewi', '081122334455', 'Jl. Mawar No. 12, Bandung', 'user', 'sari.jpg', '2026-01-15 15:08:37', '2026-01-15 15:08:37'),
(3, 'melati', 'melati.kania@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Melati Kania', '081233445566', 'Jl. Melati No. 8, Surabaya', 'user', 'melati.jpg', '2026-01-15 15:08:37', '2026-01-15 15:08:37'),
(4, 'rina_shop', 'rina.shopaholic@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Rina Santoso', '081344556677', 'Jl. Kenanga No. 5, Yogyakarta', 'user', 'rina.jpg', '2026-01-15 15:08:37', '2026-01-15 15:08:37'),
(5, 'dina_cake', 'dina.cakelover@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dina Putri', '081455667788', 'Jl. Tulip No. 3, Semarang', 'user', 'dina.jpg', '2026-01-15 15:08:37', '2026-01-15 15:08:37');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_cart_item` (`user_id`,`product_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_product_id` (`product_id`);

--
-- Indeks untuk tabel `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indeks untuk tabel `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_favorite` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indeks untuk tabel `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indeks untuk tabel `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `category_id` (`category_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
