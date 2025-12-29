-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Waktu pembuatan: 21 Des 2025 pada 14.50
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
-- Database: `db_velarishotel`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `blog`
--

CREATE TABLE `blog` (
  `id_blog` int(11) NOT NULL,
  `judul` varchar(200) NOT NULL,
  `isi_konten` text NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `penulis` varchar(100) NOT NULL,
  `tgl_posting` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `blog`
--

INSERT INTO `blog` (`id_blog`, `judul`, `isi_konten`, `gambar`, `penulis`, `tgl_posting`) VALUES
(1, 'Top 10 Things to Do in Surakarta', 'Surakarta, also known as Solo, is the cultural heart of Java. Here are 10 must-visit destinations:\r\n  \r\n  1. Keraton Kasunanan - The magnificent royal palace\r\n  2. Pasar Klewer - Southeast Asia\'s largest batik market\r\n  3. Pura Mangkunegaran - Stunning palace with Javanese-European architecture\r\n  4. Taman Balekambang - Historic park with beautiful gardens\r\n  5. Radya Pustaka Museum - Indonesia\'s oldest museum\r\n  6. Solo Grand Mall - Modern shopping center\r\n  7. Galabo Night Market - Amazing street food experience\r\n  8. Kampung Batik Laweyan - Traditional batik village\r\n  9. Ngarsopuro Night Market - Vintage market with antiques\r\n  10. Mount Lawu - Sacred mountain for sunrise trekking\r\n  \r\n  Stay at Velaris Hotel for easy access to all these attractions!', 'YMPDN0XG5VeIlH7tSWeq.jpg', 'Paulo Dyana Beckham', '2025-12-21 08:52:32'),
(2, 'Solo Food Guide: Must-Try Dishes', 'Surakarta cuisine is unique and delicious. Don\'t miss these iconic dishes:\r\n  \r\n  NASI LIWET - Fragrant rice cooked in coconut milk, served with chicken and vegetables. The most famous Solo dish!\r\n  \r\n  SELAT SOLO - Javanese interpretation of European steak. Unique sweet-savory flavor that you won\'t find elsewhere.\r\n  \r\n  SERABI NOTOSUMAN - Thick coconut pancakes with various toppings. Try the chocolate or classic kinca (brown sugar syrup).\r\n  \r\n  TENGKLENG - Spicy goat meat soup with rich broth. Perfect comfort food on rainy days.\r\n  \r\n  SATE BUNTEL - Minced lamb satay wrapped in lamb fat. Juicy and incredibly flavorful.\r\n  \r\n  Visit our concierge for restaurant recommendations and food tour bookings!', 'RxJBzMNah55an0bZN29H.jpg', 'Andien Elinor Westwood', '2025-12-21 08:54:24'),
(3, 'Why Velaris Hotel is Perfect for Business Travelers', 'Business travel requires comfort, efficiency, and reliability. Here\'s why corporate guests choose Velaris Hotel:\r\n  \r\n  STRATEGIC LOCATION - Only 20 minutes from Adisumarmo Airport and walking distance to business districts.\r\n  \r\n  HIGH-SPEED WIFI - Fiber optic internet throughout the property ensures you stay connected.\r\n  \r\n  MEETING FACILITIES - Fully equipped meeting rooms with modern AV equipment and professional catering.\r\n  \r\n  BUSINESS LOUNGE - 24/7 co-working space with complimentary coffee and printing services.\r\n  \r\n  FLEXIBLE CHECK-IN - Mobile check-in via WhatsApp. No waiting in line.\r\n  \r\n  AIRPORT TRANSFER - Reliable shuttle service ensures you never miss a flight.\r\n  \r\n  Contact our corporate sales team for special business rates and long-term packages!', 'y43MLhOk4qj3DbhGwnAl.jpg', 'Silvera Claire Whitmore', '2025-12-21 08:56:06');

-- --------------------------------------------------------

--
-- Struktur dari tabel `experiences`
--

CREATE TABLE `experiences` (
  `id_experience` int(11) NOT NULL,
  `nama_aktivitas` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `harga` decimal(10,2) NOT NULL DEFAULT 0.00,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `experiences`
--

INSERT INTO `experiences` (`id_experience`, `nama_aktivitas`, `deskripsi`, `harga`, `foto`) VALUES
(1, 'Sunrise Yoga &amp; Meditation', 'Start your day with 75-minute guided yoga session on our rooftop garden. All levels welcome.', 125000.00, 'TUB53zGe9PkiisZx72C1.png'),
(2, 'Traditional Javanese Spa', 'Authentic 120-minute spa treatment with lulur scrub, flower bath, and traditional massage.', 450000.00, 'lcJGOwUpfyHxvTfTQ5Rz.jpg'),
(3, 'Airport Transfer Service', 'Comfortable pickup/drop-off service to Adisumarmo Airport with professional driver.', 175000.00, 'pEYuAV3Pak0ABTABIUVS.jpg'),
(4, 'Welcome Drink', 'Complimentary refreshing welcome drink for all guests upon arrival at lobby lounge.', 0.00, 'fHvBQ4k2umgtYLJlIaom.png');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kamar`
--

CREATE TABLE `kamar` (
  `id_kamar` int(11) NOT NULL,
  `nama_kamar` varchar(100) NOT NULL,
  `tipe_kamar` varchar(50) NOT NULL,
  `harga` decimal(10,2) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `foto_kamar` varchar(255) DEFAULT NULL,
  `stok` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kamar`
--

INSERT INTO `kamar` (`id_kamar`, `nama_kamar`, `tipe_kamar`, `harga`, `deskripsi`, `foto_kamar`, `stok`) VALUES
(1, 'Lawu Standard 101', 'Standard', 320000.00, 'Comfortable standard room with AC, 32&quot; LED TV, free WiFi, and private bathroom. Perfect for budget travelers.', '4bVqrwvyvawuOjEDhNK6.png', 5),
(2, 'Merapi Deluxe 201', 'Deluxe', 520000.00, 'Spacious deluxe room with work desk, minibar, and city view. Ideal for business travelers.', 'RWKA1W7pZuAgCYb9X9GD.png', 3),
(3, 'Sindoro VIP 401', 'VIP', 990000.00, 'Luxurious VIP suite with jacuzzi, private terrace, and butler service. Ultimate comfort experience.', 'J0FVyrgDbw73W7brtbX0.png', 2);

-- --------------------------------------------------------

--
-- Struktur dari tabel `log_aktivitas`
--

CREATE TABLE `log_aktivitas` (
  `id_log` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `aksi` varchar(255) NOT NULL,
  `waktu` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `log_aktivitas`
--

INSERT INTO `log_aktivitas` (`id_log`, `id_user`, `aksi`, `waktu`) VALUES
(1, 1, 'Added new user: Paulo Dyana Beckham (staff)', '2025-12-21 08:15:05'),
(2, 1, 'Added new user: Andien Elinor Westwood (staff)', '2025-12-21 08:21:55'),
(3, 1, 'Added new user: Silvera Claire Whitmore (staff)', '2025-12-21 08:24:40'),
(4, 1, 'Added new user: Issa Olivia Ravenscroft (staff)', '2025-12-21 08:33:58'),
(5, 1, 'Updated user: Issa Olivia Ravenscroft (ID: 5)', '2025-12-21 08:34:18'),
(6, 1, 'Updated user: Issa Olivia Ravenscroft (ID: 5)', '2025-12-21 08:34:23'),
(7, 1, 'Added new room: Lawu Standard 101', '2025-12-21 08:35:58'),
(8, 1, 'Added new room: Merapi Deluxe 201', '2025-12-21 08:36:54'),
(9, 1, 'Added new room: Sindoro VIP 401', '2025-12-21 08:37:40'),
(10, 1, 'Updated room: Sindoro VIP 401 (ID: 3)', '2025-12-21 08:38:00'),
(11, 1, 'Added new experience: Sunrise Yoga &amp; Meditation', '2025-12-21 08:38:58'),
(12, 1, 'Added new experience: Traditional Javanese Spa', '2025-12-21 08:40:50'),
(13, 1, 'Added new experience: Airport Transfer Service', '2025-12-21 08:45:50'),
(14, 1, 'Added new experience: Welcome Drink', '2025-12-21 08:49:07'),
(15, 1, 'Logout from admin panel', '2025-12-21 08:50:27'),
(16, 2, 'Login to admin panel', '2025-12-21 08:51:19'),
(17, 2, 'Added new blog article: Top 10 Things to Do in Surakarta', '2025-12-21 08:52:32'),
(18, 2, 'Updated blog article: Top 10 Things to Do in Surakarta (ID: 1)', '2025-12-21 08:52:42'),
(19, 2, 'Logout from admin panel', '2025-12-21 08:52:58'),
(20, 3, 'Login to admin panel', '2025-12-21 08:53:12'),
(21, 3, 'Added new blog article: Solo Food Guide: Must-Try Dishes', '2025-12-21 08:54:24'),
(22, 3, 'Logout from admin panel', '2025-12-21 08:54:39'),
(23, 4, 'Login to admin panel', '2025-12-21 08:54:53'),
(24, 4, 'Added new blog article: Why Velaris Hotel is Perfect for Business Travelers', '2025-12-21 08:56:06'),
(25, 4, 'Logout from admin panel', '2025-12-21 08:56:48'),
(26, 1, 'Login to admin panel', '2025-12-21 08:56:56'),
(27, 1, 'Logout from admin panel', '2025-12-21 13:10:52'),
(28, 1, 'Login to admin panel', '2025-12-21 13:11:02'),
(29, 1, 'Logout from admin panel', '2025-12-21 13:11:06'),
(30, 1, 'Login to admin panel', '2025-12-21 13:11:15');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pembatalan`
--

CREATE TABLE `pembatalan` (
  `id_batal` int(11) NOT NULL,
  `id_reservasi` int(11) NOT NULL,
  `tgl_pengajuan` timestamp NOT NULL DEFAULT current_timestamp(),
  `tgl_diproses` datetime DEFAULT NULL,
  `alasan` text NOT NULL,
  `nama_bank` varchar(50) NOT NULL,
  `no_rekening` varchar(50) NOT NULL,
  `nama_pemilik` varchar(100) NOT NULL,
  `status_pengajuan` enum('pending','disetujui','ditolak') DEFAULT 'pending',
  `catatan_admin` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `reservasi`
--

CREATE TABLE `reservasi` (
  `id_reservasi` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_kamar` int(11) NOT NULL,
  `tgl_checkin` date NOT NULL,
  `tgl_checkout` date NOT NULL,
  `jumlah_kamar` int(11) NOT NULL DEFAULT 1,
  `total_harga` decimal(10,2) NOT NULL,
  `bukti_bayar` varchar(255) DEFAULT NULL,
  `status` enum('menunggu_bayar','menunggu_verifikasi','lunas','batal','selesai') DEFAULT 'menunggu_bayar',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `reservasi_experience`
--

CREATE TABLE `reservasi_experience` (
  `id` int(11) NOT NULL,
  `id_reservasi` int(11) NOT NULL,
  `id_experience` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `role` enum('admin','staff','user') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id_user`, `nama_lengkap`, `email`, `password`, `no_hp`, `role`, `created_at`) VALUES
(1, 'Admin Velaris', 'admin@velaris.com', '$2y$10$Ftmpc5VrpGVPhbjntp/s7.IdGWyghvN5j.MfDV2wlmEDwYuQnz/V6', '081234567890', 'admin', '2025-12-20 05:05:12'),
(2, 'Paulo Dyana Beckham', 'diana@velaris.com', '$2y$10$msFX62h0XBgTJp4vX7B7zu/smZnbIPqdTmvFV2HWQLxeU0NcfPCfy', '081234567891', 'staff', '2025-12-21 08:15:05'),
(3, 'Andien Elinor Westwood', 'andien@velaris.com', '$2y$10$DYFhWgOiTvpKnAnIHZb70ekOQQFNASoB3kEEsyAiFjaEfW/pRi8d2', '081234567892', 'staff', '2025-12-21 08:21:55'),
(4, 'Silvera Claire Whitmore', 'silvi@velaris.com', '$2y$10$D/4saY9sW52Ycm8sev/IrOIq7K/12ehbLPY4wbL7wK3jz29fyli0i', '081234567893', 'staff', '2025-12-21 08:24:40'),
(5, 'Issa Olivia Ravenscroft', 'nisa@velaris.com', '$2y$10$e3E3narAOBnwGZ8FIDCsGuO6vM88aCkIDExk/k6PgjWI/VJkZXG/C', '081234567894', 'staff', '2025-12-21 08:33:58');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `blog`
--
ALTER TABLE `blog`
  ADD PRIMARY KEY (`id_blog`);

--
-- Indeks untuk tabel `experiences`
--
ALTER TABLE `experiences`
  ADD PRIMARY KEY (`id_experience`);

--
-- Indeks untuk tabel `kamar`
--
ALTER TABLE `kamar`
  ADD PRIMARY KEY (`id_kamar`);

--
-- Indeks untuk tabel `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD PRIMARY KEY (`id_log`),
  ADD KEY `id_user` (`id_user`);

--
-- Indeks untuk tabel `pembatalan`
--
ALTER TABLE `pembatalan`
  ADD PRIMARY KEY (`id_batal`),
  ADD KEY `id_reservasi` (`id_reservasi`);

--
-- Indeks untuk tabel `reservasi`
--
ALTER TABLE `reservasi`
  ADD PRIMARY KEY (`id_reservasi`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_kamar` (`id_kamar`);

--
-- Indeks untuk tabel `reservasi_experience`
--
ALTER TABLE `reservasi_experience`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_reservasi` (`id_reservasi`),
  ADD KEY `id_experience` (`id_experience`);

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
-- AUTO_INCREMENT untuk tabel `blog`
--
ALTER TABLE `blog`
  MODIFY `id_blog` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `experiences`
--
ALTER TABLE `experiences`
  MODIFY `id_experience` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `kamar`
--
ALTER TABLE `kamar`
  MODIFY `id_kamar` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT untuk tabel `pembatalan`
--
ALTER TABLE `pembatalan`
  MODIFY `id_batal` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `reservasi`
--
ALTER TABLE `reservasi`
  MODIFY `id_reservasi` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `reservasi_experience`
--
ALTER TABLE `reservasi_experience`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD CONSTRAINT `log_aktivitas_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pembatalan`
--
ALTER TABLE `pembatalan`
  ADD CONSTRAINT `pembatalan_ibfk_1` FOREIGN KEY (`id_reservasi`) REFERENCES `reservasi` (`id_reservasi`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `reservasi`
--
ALTER TABLE `reservasi`
  ADD CONSTRAINT `reservasi_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservasi_ibfk_2` FOREIGN KEY (`id_kamar`) REFERENCES `kamar` (`id_kamar`);

--
-- Ketidakleluasaan untuk tabel `reservasi_experience`
--
ALTER TABLE `reservasi_experience`
  ADD CONSTRAINT `reservasi_experience_ibfk_1` FOREIGN KEY (`id_reservasi`) REFERENCES `reservasi` (`id_reservasi`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservasi_experience_ibfk_2` FOREIGN KEY (`id_experience`) REFERENCES `experiences` (`id_experience`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
