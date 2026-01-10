-- phpMyAdmin SQL Dump
-- version 6.0.0-dev+20260106.f3f3d53389
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 10, 2026 at 05:46 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.28

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
-- Table structure for table `blog`
--

CREATE TABLE `blog` (
  `id_blog` int NOT NULL,
  `judul` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `isi_konten` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `gambar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `penulis` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tgl_posting` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `blog`
--

INSERT INTO `blog` (`id_blog`, `judul`, `isi_konten`, `gambar`, `penulis`, `tgl_posting`) VALUES
(1, 'Top 10 Things to Do in Surakarta', 'Surakarta, also known as Solo, is the cultural heart of Java. Here are 10 must-visit destinations:\r\n  \r\n  1. Keraton Kasunanan - The magnificent royal palace\r\n  2. Pasar Klewer - Southeast Asia\'s largest batik market\r\n  3. Pura Mangkunegaran - Stunning palace with Javanese-European architecture\r\n  4. Taman Balekambang - Historic park with beautiful gardens\r\n  5. Radya Pustaka Museum - Indonesia\'s oldest museum\r\n  6. Solo Grand Mall - Modern shopping center\r\n  7. Galabo Night Market - Amazing street food experience\r\n  8. Kampung Batik Laweyan - Traditional batik village\r\n  9. Ngarsopuro Night Market - Vintage market with antiques\r\n  10. Mount Lawu - Sacred mountain for sunrise trekking\r\n  \r\n  Stay at Velaris Hotel for easy access to all these attractions!', 'YMPDN0XG5VeIlH7tSWeq.jpg', 'Paulo Dyana Beckham', '2025-12-21 08:52:32'),
(2, 'Solo Food Guide: Must-Try Dishes', 'Surakarta cuisine is unique and delicious. Don\'t miss these iconic dishes:\r\n  \r\n  NASI LIWET - Fragrant rice cooked in coconut milk, served with chicken and vegetables. The most famous Solo dish!\r\n  \r\n  SELAT SOLO - Javanese interpretation of European steak. Unique sweet-savory flavor that you won\'t find elsewhere.\r\n  \r\n  SERABI NOTOSUMAN - Thick coconut pancakes with various toppings. Try the chocolate or classic kinca (brown sugar syrup).\r\n  \r\n  TENGKLENG - Spicy goat meat soup with rich broth. Perfect comfort food on rainy days.\r\n  \r\n  SATE BUNTEL - Minced lamb satay wrapped in lamb fat. Juicy and incredibly flavorful.\r\n  \r\n  Visit our concierge for restaurant recommendations and food tour bookings!', 'RxJBzMNah55an0bZN29H.jpg', 'Andien Elinor Westwood', '2025-12-21 08:54:24'),
(3, 'Why Velaris Hotel is Perfect for Business Travelers', 'Business travel requires comfort, efficiency, and reliability. Here\'s why corporate guests choose Velaris Hotel:\r\n  \r\n  STRATEGIC LOCATION - Only 20 minutes from Adisumarmo Airport and walking distance to business districts.\r\n  \r\n  HIGH-SPEED WIFI - Fiber optic internet throughout the property ensures you stay connected.\r\n  \r\n  MEETING FACILITIES - Fully equipped meeting rooms with modern AV equipment and professional catering.\r\n  \r\n  BUSINESS LOUNGE - 24/7 co-working space with complimentary coffee and printing services.\r\n  \r\n  FLEXIBLE CHECK-IN - Mobile check-in via WhatsApp. No waiting in line.\r\n  \r\n  AIRPORT TRANSFER - Reliable shuttle service ensures you never miss a flight.\r\n  \r\n  Contact our corporate sales team for special business rates and long-term packages!', 'y43MLhOk4qj3DbhGwnAl.jpg', 'Silvera Claire Whitmore', '2025-12-21 08:56:06');

-- --------------------------------------------------------

--
-- Table structure for table `experiences`
--

CREATE TABLE `experiences` (
  `id_experience` int NOT NULL,
  `nama_aktivitas` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `harga` decimal(10,2) NOT NULL DEFAULT '0.00',
  `foto` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `experiences`
--

INSERT INTO `experiences` (`id_experience`, `nama_aktivitas`, `deskripsi`, `harga`, `foto`) VALUES
(1, 'Sunrise Yoga &amp; Meditation', 'Start your day with 75-minute guided yoga session on our rooftop garden. All levels welcome.', 125000.00, 'TUB53zGe9PkiisZx72C1.png'),
(2, 'Traditional Javanese Spa', 'Authentic 120-minute spa treatment with lulur scrub, flower bath, and traditional massage.', 450000.00, 'lcJGOwUpfyHxvTfTQ5Rz.jpg'),
(3, 'Airport Transfer Service', 'Comfortable pickup/drop-off service to Adisumarmo Airport with professional driver.', 175000.00, 'pEYuAV3Pak0ABTABIUVS.jpg'),
(4, 'Welcome Drink', 'Complimentary refreshing welcome drink for all guests upon arrival at lobby lounge.', 0.00, 'fHvBQ4k2umgtYLJlIaom.png'),
(5, 'Batik Making Workshop', 'Learn the ancient art of batik from master craftsmen. 3-hour hands-on workshop includes all materials, traditional canting tools, and you take home your own batik creation. Cultural immersion at its best.', 280000.00, 'D2XrTtPQGbXQUgfioOpA.jpg'),
(6, 'Keraton Solo Cultural Tour', 'Explore the magnificent Kasunanan Palace with expert guide. Half-day tour includes palace entrance, batik museum visit, and traditional gamelan music demonstration. Discover Surakarta rich heritage.', 350000.00, 'jb3UJjeEn2NewLXguffo.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `kamar`
--

CREATE TABLE `kamar` (
  `id_kamar` int NOT NULL,
  `nama_kamar` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipe_kamar` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `harga` decimal(10,2) NOT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `foto_kamar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stok` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kamar`
--

INSERT INTO `kamar` (`id_kamar`, `nama_kamar`, `tipe_kamar`, `harga`, `deskripsi`, `foto_kamar`, `stok`) VALUES
(1, 'Lawu Standard 101', 'Standard', 320000.00, 'Comfortable standard room with AC, 32&quot; LED TV, free WiFi, and private bathroom. Perfect for budget travelers.', '4bVqrwvyvawuOjEDhNK6.png', 5),
(2, 'Merapi Deluxe 201', 'Deluxe', 520000.00, 'Spacious deluxe room with work desk, minibar, and city view. Ideal for business travelers.', 'RWKA1W7pZuAgCYb9X9GD.png', 3),
(3, 'Sindoro VIP 401', 'VIP', 990000.00, 'Luxurious VIP suite with jacuzzi, private terrace, and butler service. Ultimate comfort experience.', 'J0FVyrgDbw73W7brtbX0.png', 2),
(5, 'Lawu Standard 102', 'Standard', 320000.00, 'Twin bed standard room with modern amenities. Perfect for friends or colleagues. Includes AC, cable TV, complimentary WiFi, work desk, and daily housekeeping service.', 'Buf4jhXROkyTFZWNsVz7.png', 8),
(7, 'Merapi Deluxe 202', 'Deluxe', 520000.00, 'Elegant deluxe accommodation with contemporary design. Equipped with work station, sofa seating area, complimentary breakfast, and access to executive lounge. Perfect for extended stays.', 'VZsoBFXFxmjmMD5Avqgt.png', 4),
(8, 'Merbabu Suite 301', 'Suite', 720000.00, 'Premium family suite with two bedrooms and living area. Features kitchenette, dining table, 2 bathrooms, washing machine, and separate children playing corner. Ideal for families with kids.', '7pTDFWsouXCpiCV1otwq.png', 3),
(11, 'Merbabu Suite 302', 'Suite', 720000.00, 'Luxurious suite with panoramic city views. Includes master bedroom, guest room, living room with 55&quot; TV, full kitchen, and access to private lounge with complimentary snacks.', 'gu5d9l0OfNrQfWk1mhEK.jpg', 2),
(12, 'Sumbing VIP 402', 'VIP', 950000.00, 'Executive VIP suite with modern sophistication. Includes home theater system, wine cellar, private gym equipment, and personalized concierge service. Perfect for business executives and VIP guests.', 'O4S0l2X3mOlnxJWrXw7Q.jpg', 6);

-- --------------------------------------------------------

--
-- Table structure for table `log_aktivitas`
--

CREATE TABLE `log_aktivitas` (
  `id_log` int NOT NULL,
  `id_user` int NOT NULL,
  `aksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `waktu` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `log_aktivitas`
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
(30, 1, 'Login to admin panel', '2025-12-21 13:11:15'),
(31, 1, 'Login to admin panel', '2025-12-28 13:11:01'),
(32, 1, 'Login to admin panel', '2025-12-29 00:51:36'),
(33, 1, 'Logout from admin panel', '2025-12-29 03:22:41'),
(34, 2, 'Login to admin panel', '2025-12-29 03:23:20'),
(35, 2, 'Logout from admin panel', '2025-12-29 04:11:35'),
(36, 1, 'Login to admin panel', '2025-12-29 04:11:43'),
(37, 1, 'Deleted room: Lawu Standard 102 (ID: 6)', '2025-12-29 07:01:16'),
(38, 1, 'Deleted room: Lawu Standard 102 (ID: 4)', '2025-12-29 07:01:25'),
(39, 1, 'Deleted room: Merbabu Suite 301 (ID: 10)', '2025-12-29 07:35:02'),
(40, 1, 'Deleted room: Merbabu Suite 301 (ID: 9)', '2025-12-29 07:35:08'),
(41, 1, 'Added experience: Batik Making Workshop', '2025-12-29 07:49:55'),
(42, 1, 'Added experience: Keraton Solo Cultural Tour', '2025-12-29 07:50:58'),
(43, 1, 'Logout from admin panel', '2025-12-29 07:51:22'),
(44, 5, 'Login to admin panel', '2025-12-29 07:51:52'),
(45, 5, 'Added blog article: Why Velaris Hotel is Perfect for Business Travelers', '2025-12-29 07:53:41'),
(46, 5, 'Deleted blog article: Why Velaris Hotel is Perfect for Business Travelers (ID: 4)', '2025-12-29 07:53:47'),
(47, 5, 'Logout from admin panel', '2025-12-29 07:54:14'),
(48, 1, 'Login to admin panel', '2025-12-29 08:20:42'),
(49, 1, 'Login to admin panel', '2026-01-09 15:16:15'),
(50, 1, 'Login to admin panel', '2026-01-09 21:59:58'),
(51, 1, 'Login to admin panel', '2026-01-09 22:55:22'),
(52, 1, 'Login to admin panel', '2026-01-10 03:39:11'),
(53, 1, 'Login to admin panel', '2026-01-10 04:41:58'),
(54, 1, 'Admin logout', '2026-01-10 05:16:50'),
(55, 1, 'Login to admin panel', '2026-01-10 05:17:54'),
(56, 1, 'Admin logout', '2026-01-10 05:32:06'),
(57, 1, 'Login to admin panel', '2026-01-10 05:32:16'),
(58, 1, 'Admin logout', '2026-01-10 05:44:15'),
(59, 1, 'Login to admin panel', '2026-01-10 05:44:25');

-- --------------------------------------------------------

--
-- Table structure for table `pembatalan`
--

CREATE TABLE `pembatalan` (
  `id_batal` int NOT NULL,
  `id_reservasi` int NOT NULL,
  `tgl_pengajuan` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tgl_diproses` datetime DEFAULT NULL,
  `alasan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_bank` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_rekening` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_pemilik` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_pengajuan` enum('pending','disetujui','ditolak') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `catatan_admin` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pembatalan`
--

INSERT INTO `pembatalan` (`id_batal`, `id_reservasi`, `tgl_pengajuan`, `tgl_diproses`, `alasan`, `nama_bank`, `no_rekening`, `nama_pemilik`, `status_pengajuan`, `catatan_admin`) VALUES
(1, 2, '2025-12-29 08:18:39', '2025-10-16 00:00:00', 'Perubahan jadwal mendadak', 'BCA', '1234567890', 'Alya Putri', 'disetujui', NULL),
(2, 6, '2025-12-29 08:19:28', NULL, 'Kondisi kesehatan', 'Mandiri', '9876543210', 'Nabila Rahman', 'pending', NULL),
(3, 9, '2026-01-09 08:19:12', NULL, 'ganti tanggal', 'BCA', '123123', 'COKI', 'pending', NULL),
(4, 10, '2026-01-09 08:23:08', '2026-01-09 22:24:45', 'ganti tanggal kak', 'BCA', '123123', 'COKI', 'disetujui', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `reservasi`
--

CREATE TABLE `reservasi` (
  `id_reservasi` int NOT NULL,
  `id_user` int NOT NULL,
  `id_kamar` int NOT NULL,
  `kode_booking` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tgl_checkin` date NOT NULL,
  `tgl_checkout` date NOT NULL,
  `jumlah_kamar` int NOT NULL DEFAULT '1',
  `total_harga` decimal(10,2) NOT NULL,
  `bukti_bayar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('menunggu_bayar','menunggu_verifikasi','lunas','pembatalan_diajukan','batal','selesai','checkin') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'menunggu_bayar',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reservasi`
--

INSERT INTO `reservasi` (`id_reservasi`, `id_user`, `id_kamar`, `kode_booking`, `tgl_checkin`, `tgl_checkout`, `jumlah_kamar`, `total_harga`, `bukti_bayar`, `status`, `created_at`) VALUES
(1, 6, 1, '', '2025-10-10', '2025-10-12', 1, 640000.00, NULL, 'selesai', '2025-09-30 17:00:00'),
(2, 6, 2, '', '2025-10-20', '2025-10-22', 1, 1040000.00, NULL, 'batal', '2025-10-14 17:00:00'),
(3, 7, 3, '', '2025-11-05', '2025-11-07', 1, 1980000.00, NULL, 'lunas', '2025-10-31 17:00:00'),
(4, 7, 5, '', '2025-11-18', '2025-11-20', 2, 1280000.00, NULL, 'selesai', '2025-11-09 17:00:00'),
(5, 8, 8, '', '2025-12-10', '2025-12-13', 1, 2160000.00, NULL, 'menunggu_verifikasi', '2025-11-30 17:00:00'),
(6, 8, 12, '', '2025-12-20', '2025-12-23', 1, 2850000.00, NULL, 'batal', '2025-12-04 17:00:00'),
(7, 7, 1, 'VLR-20260109-745F', '2026-01-09', '2026-01-10', 1, 320000.00, '1767971338_Screenshot (687).png', 'lunas', '2026-01-09 15:08:58'),
(8, 9, 2, 'VLR-20260109-5CFA', '2026-01-09', '2026-01-10', 1, 520000.00, '1767971818_Screenshot (687).png', 'lunas', '2026-01-09 15:16:58'),
(9, 9, 1, 'VLR-20260109-AC61', '2026-01-09', '2026-01-10', 1, 320000.00, '1767971926_Screenshot (687).png', 'checkin', '2026-01-09 15:18:46'),
(10, 9, 1, 'VLR-20260109-BA58', '2026-01-09', '2026-01-10', 1, 320000.00, '1767972174_Screenshot (687).png', 'batal', '2026-01-09 15:22:54'),
(11, 9, 3, 'VLR-20260109-67CC', '2026-01-10', '2026-01-11', 1, 990000.00, '1767997417_Screenshot 2026-01-09 123138.png', 'lunas', '2026-01-09 22:23:37'),
(12, 9, 8, 'VLR-20260109-8899', '2026-01-10', '2026-01-11', 1, 720000.00, NULL, 'checkin', '2026-01-09 22:37:05');

-- --------------------------------------------------------

--
-- Table structure for table `reservasi_experience`
--

CREATE TABLE `reservasi_experience` (
  `id` int NOT NULL,
  `id_reservasi` int NOT NULL,
  `id_experience` int NOT NULL,
  `jumlah` int NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int NOT NULL,
  `nama_lengkap` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_hp` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','staff','user') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `nama_lengkap`, `email`, `password`, `no_hp`, `role`, `created_at`) VALUES
(1, 'Admin Velaris', 'admin@velaris.com', '$2y$10$KeHCNy8eEumQYDHjbOZ0Uuje1lDwOMDirwLVRmyp3b2DqWs6fL38q', '081234567890', 'admin', '2025-12-20 05:05:12'),
(2, 'Paulo Dyana Beckham', 'diana@velaris.com', '$2y$10$msFX62h0XBgTJp4vX7B7zu/smZnbIPqdTmvFV2HWQLxeU0NcfPCfy', '081234567891', 'staff', '2025-12-21 08:15:05'),
(3, 'Andien Elinor Westwood', 'andien@velaris.com', '$2y$10$DYFhWgOiTvpKnAnIHZb70ekOQQFNASoB3kEEsyAiFjaEfW/pRi8d2', '081234567892', 'staff', '2025-12-21 08:21:55'),
(4, 'Silvera Claire Whitmore', 'silvi@velaris.com', '$2y$10$D/4saY9sW52Ycm8sev/IrOIq7K/12ehbLPY4wbL7wK3jz29fyli0i', '081234567893', 'staff', '2025-12-21 08:24:40'),
(5, 'Issa Olivia Ravenscroft', 'nisa@velaris.com', '$2y$10$e3E3narAOBnwGZ8FIDCsGuO6vM88aCkIDExk/k6PgjWI/VJkZXG/C', '081234567894', 'staff', '2025-12-21 08:33:58'),
(6, 'Alya Putri', 'alya@gmail.com', '$2y$10$dummyhashalya', '081300000001', 'user', '2025-10-04 17:00:00'),
(7, 'Rizky Pratama', 'rizky@gmail.com', '$2y$10$DHTBBrFc0F/7tKyNSEADMeuQrCt620wcqg7NvHzibPjHgI.ilFJdC', '081300000002', 'user', '2025-11-09 17:00:00'),
(8, 'Nabila Rahman', 'nabila@gmail.com', '$2y$10$dummynabila', '081300000003', 'user', '2025-12-01 17:00:00'),
(9, 'coki', 'cokipardede@gmail.com', '$2y$10$eZ2UBT3ptf22jS1/LXzb4Opp/pdLPr7zGT5TLkCKhR6QewH1WEONq', '123123123', 'user', '2026-01-09 15:13:31'),
(10, 'lala', 'lala@gmail.com', '$2y$10$A44UrQ6HL77lyRXRDj5KDuDUHFwpjm9532L7Vkj1eFLL36V4RSd4e', '085123456789', 'user', '2026-01-09 22:53:28');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blog`
--
ALTER TABLE `blog`
  ADD PRIMARY KEY (`id_blog`);

--
-- Indexes for table `experiences`
--
ALTER TABLE `experiences`
  ADD PRIMARY KEY (`id_experience`);

--
-- Indexes for table `kamar`
--
ALTER TABLE `kamar`
  ADD PRIMARY KEY (`id_kamar`);

--
-- Indexes for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD PRIMARY KEY (`id_log`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `pembatalan`
--
ALTER TABLE `pembatalan`
  ADD PRIMARY KEY (`id_batal`),
  ADD KEY `id_reservasi` (`id_reservasi`);

--
-- Indexes for table `reservasi`
--
ALTER TABLE `reservasi`
  ADD PRIMARY KEY (`id_reservasi`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_kamar` (`id_kamar`);

--
-- Indexes for table `reservasi_experience`
--
ALTER TABLE `reservasi_experience`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_reservasi` (`id_reservasi`),
  ADD KEY `id_experience` (`id_experience`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blog`
--
ALTER TABLE `blog`
  MODIFY `id_blog` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `experiences`
--
ALTER TABLE `experiences`
  MODIFY `id_experience` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `kamar`
--
ALTER TABLE `kamar`
  MODIFY `id_kamar` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  MODIFY `id_log` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `pembatalan`
--
ALTER TABLE `pembatalan`
  MODIFY `id_batal` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `reservasi`
--
ALTER TABLE `reservasi`
  MODIFY `id_reservasi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `reservasi_experience`
--
ALTER TABLE `reservasi_experience`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD CONSTRAINT `log_aktivitas_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `pembatalan`
--
ALTER TABLE `pembatalan`
  ADD CONSTRAINT `pembatalan_ibfk_1` FOREIGN KEY (`id_reservasi`) REFERENCES `reservasi` (`id_reservasi`) ON DELETE CASCADE;

--
-- Constraints for table `reservasi`
--
ALTER TABLE `reservasi`
  ADD CONSTRAINT `reservasi_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservasi_ibfk_2` FOREIGN KEY (`id_kamar`) REFERENCES `kamar` (`id_kamar`);

--
-- Constraints for table `reservasi_experience`
--
ALTER TABLE `reservasi_experience`
  ADD CONSTRAINT `reservasi_experience_ibfk_1` FOREIGN KEY (`id_reservasi`) REFERENCES `reservasi` (`id_reservasi`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservasi_experience_ibfk_2` FOREIGN KEY (`id_experience`) REFERENCES `experiences` (`id_experience`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
