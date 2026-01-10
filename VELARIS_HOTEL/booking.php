<?php
session_start();
require_once "config/database.php";
require_once "config/functions.php";

$page_title = 'Booking';

$db = new Koneksi();
$conn = $db->getKoneksi();

/**
 * GET PARAMETER
 */
$checkin = $_GET['checkin'] ?? '';
$checkout = $_GET['checkout'] ?? '';

/**
 * AMBIL DATA KAMAR
 */
$rooms = $conn->query("SELECT * FROM kamar");

$today = date('Y-m-d');

if ($checkin && $checkin < $today) {
    header("Location: booking.php?error=invalid_date");
    exit;
}

if ($checkin && $checkout && $checkout <= $checkin) {
    header("Location: booking.php?error=invalid_range");
    exit;
}

/* HEADER GLOBAL */
require_once "components/header.php";
?>

<!-- SweetAlert2 CSS & JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* OFFSET KARENA HEADER GLOBAL FIXED */
    .page-after-header {
        margin-top: 90px;
        background: #f6f6f6;
    }

    /* BOOKING HEADER */
    .booking-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 22px 40px;
        background: #fff;
        border-bottom: 1px solid #eee;
    }

    .booking-header h3 {
        font-family: 'Cinzel', serif;
        margin: 0;
    }

    .booking-header p {
        margin: 2px 0 0;
        font-size: .8rem;
        color: #666;
    }

    /* USER SECTION */
    .user-section {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 18px;
        background: #f8f8f8;
        border-radius: 25px;
        border: 1px solid #e0e0e0;
    }

    .user-info:hover {
        background: #f1f1f1;
    }

    .user-avatar {
        width: 32px;
        height: 32px;
        background: linear-gradient(135deg, #d4af37 0%, #b8860b 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #000;
        font-weight: 700;
        font-size: .85rem;
    }

    .user-name {
        font-size: .85rem;
        color: #333;
        font-weight: 500;
    }

    .login-btn,
    .logout-btn {
        padding: 8px 22px;
        border: 1px solid #000;
        border-radius: 25px;
        text-decoration: none;
        font-size: .8rem;
        color: #000;
        background: #fff;
        cursor: pointer;
        transition: all .3s ease;
        font-family: inherit;
    }

    .login-btn:hover,
    .logout-btn:hover {
        background: #000;
        color: #fff;
    }

    /* HERO */
    .booking-hero img {
        width: 100%;
        height: 420px;
        object-fit: cover;
        display: block;
    }

    /* SEARCH BAR */
    .search-bar {
        background: #fff;
        padding: 26px 34px;
        max-width: 1100px;
        margin: 40px auto 60px;
        border-radius: 18px;
        box-shadow: 0 15px 40px rgba(0, 0, 0, .15);
    }

    .search-bar form {
        display: flex;
        gap: 24px;
        align-items: flex-end;
        flex-wrap: wrap;
    }

    .search-bar .field {
        flex: 1;
        min-width: 220px;
    }

    .search-bar label {
        display: block;
        font-size: .75rem;
        letter-spacing: 1px;
        margin-bottom: 6px;
        font-weight: 600;
        color: #333;
    }

    .search-bar input {
        width: 100%;
        padding: 12px 14px;
        border: 1px solid #ccc;
        border-radius: 10px;
        font-size: .9rem;
    }

    .search-btn {
        height: 46px;
        padding: 0 36px;
        border: none;
        background: linear-gradient(135deg, #d4af37 0%, #b8860b 100%);
        color: #000;
        border-radius: 30px;
        font-size: .85rem;
        font-weight: 600;
        cursor: pointer;
        transition: all .3s ease;
        box-shadow: 0 4px 12px rgba(212, 175, 55, .3);
    }

    .search-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(212, 175, 55, .4);
    }

    /* CONTENT */
    .booking-content {
        max-width: 1200px;
        margin: auto;
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 40px;
        padding: 0 20px 100px;
    }

    /* SIDEBAR SUMMARY */
    .booking-summary {
        position: sticky;
        top: 110px;
        height: fit-content;
    }

    .summary-card {
        background: #fff;
        border-radius: 18px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .08);
        border: 2px solid #f5f5f5;
    }

    .summary-card h4 {
        font-family: 'Cinzel', serif;
        font-size: 1.3rem;
        margin: 0 0 20px;
        color: #333;
    }

    .summary-dates {
        background: linear-gradient(135deg, #faf7ef 0%, #fff 100%);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        border: 1px dashed #d4af37;
    }

    .summary-dates .date-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }

    .summary-dates .date-item:last-child {
        margin-bottom: 0;
    }

    .summary-dates label {
        font-size: .75rem;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .summary-dates .date-value {
        font-size: .9rem;
        font-weight: 600;
        color: #000;
    }

    .summary-info {
        padding: 15px 0;
        border-top: 1px solid #eee;
        margin-top: 15px;
    }

    .summary-info p {
        display: flex;
        justify-content: space-between;
        margin: 8px 0;
        font-size: .85rem;
        color: #666;
    }

    .summary-info p strong {
        color: #000;
    }

    .summary-note {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 10px;
        font-size: .8rem;
        color: #666;
        margin-top: 20px;
        line-height: 1.6;
    }

    .summary-note strong {
        display: block;
        color: #d4af37;
        margin-bottom: 8px;
    }

    .summary-cta {
        margin-top: 25px;
        padding: 15px;
        background: linear-gradient(135deg, #d4af37 0%, #b8860b 100%);
        border-radius: 14px;
        text-align: center;
        color: #000;
        font-weight: 600;
        font-size: .9rem;
        cursor: pointer;
        transition: .3s;
        box-shadow: 0 8px 20px rgba(212, 175, 55, .3);
    }

    .summary-cta:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(212, 175, 55, .4);
    }

    /* ROOM CARD */
    .room-card {
        background: #fff;
        border-radius: 16px;
        overflow: hidden;
        margin-bottom: 30px;
        display: flex;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .08);
        transition: .3s;
    }

    .room-card:hover {
        box-shadow: 0 15px 40px rgba(0, 0, 0, .12);
    }

    .room-card img {
        width: 240px;
        object-fit: cover;
    }

    .room-info {
        padding: 22px;
        flex: 1;
    }

    .room-info h3 {
        font-family: 'Cinzel', serif;
        margin-bottom: 6px;
    }

    .room-info small {
        color: #999;
        font-size: .75rem;
    }

    .room-info p {
        font-size: .85rem;
        line-height: 1.6;
    }

    .availability {
        font-size: .8rem;
        color: green;
    }

    .unavailable {
        font-size: .8rem;
        color: #a00;
    }

    /* ACTION */
    .room-action {
        padding: 22px;
        border-left: 1px solid #eee;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        gap: 14px;
    }

    .price {
        font-weight: 600;
        font-size: 1.1rem;
    }

    .btn {
        padding: 10px 24px;
        border-radius: 25px;
        border: none;
        background: linear-gradient(135deg, #d4af37 0%, #b8860b 100%);
        text-decoration: none;
        font-size: .8rem;
        color: #000;
        font-weight: 600;
        transition: all .3s ease;
        box-shadow: 0 4px 12px rgba(212, 175, 55, .2);
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(212, 175, 55, .3);
    }

    .btn.disabled {
        opacity: .5;
        pointer-events: none;
        background: #ccc;
        box-shadow: none;
    }

    /* USER DROPDOWN */
    .user-dropdown {
        position: relative;
    }

    .user-info {
        cursor: pointer;
    }

    .user-info .arrow {
        font-size: .65rem;
        margin-left: 4px;
        color: #666;
    }

    .dropdown-menu {
        position: absolute;
        top: 115%;
        right: 0;
        width: 220px;
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, .18);
        display: none;
        overflow: hidden;
        z-index: 1000;
        animation: dropdownFade .25s ease;
    }

    @keyframes dropdownFade {
        from {
            opacity: 0;
            transform: translateY(-6px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .dropdown-menu a {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 20px;
        font-size: .8rem;
        color: #333;
        text-decoration: none;
        transition: all .25s ease;
    }

    .dropdown-menu a:hover {
        background: #faf7ef;
    }

    .dropdown-menu a.logout {
        color: #8b0000;
    }

    .dropdown-menu a.logout:hover {
        background: #fff1f1;
    }

    .dropdown-menu hr {
        border: none;
        height: 1px;
        background: #eee;
        margin: 4px 0;
    }

    .dropdown-header {
        padding: 16px 20px 12px;
        background: linear-gradient(135deg, #faf7ef, #fff);
        border-bottom: 1px solid #eee;
    }

    .dropdown-header strong {
        font-family: 'Cinzel', serif;
        font-size: .85rem;
        color: #333;
        display: block;
    }

    .dropdown-header small {
        font-size: .7rem;
        color: #777;
    }

    .dropdown-menu a.active {
        background: #faf7ef;
        font-weight: 500;
    }

    /* SWEETALERT */
    .swal2-popup {
        font-family: 'Poppins', sans-serif;
        border-radius: 20px;
    }

    .swal2-title {
        font-family: 'Cinzel', serif;
        color: #333;
    }

    .swal2-confirm {
        background: linear-gradient(135deg, #d4af37 0%, #b8860b 100%) !important;
        border-radius: 25px;
        padding: 10px 30px !important;
        color: #000 !important;
        font-weight: 600 !important;
    }

    .swal2-cancel {
        border-radius: 25px;
        padding: 10px 30px !important;
    }

    /* ============================================
       RESPONSIVE DESIGN
       ============================================ */

    /* TABLET (768px - 992px) */
    @media (max-width: 992px) {
        .booking-header {
            padding: 18px 30px;
        }

        .booking-header h3 {
            font-size: 1.3rem;
        }

        .booking-hero img {
            height: 350px;
        }

        .search-bar {
            padding: 22px 28px;
            margin: 30px 20px 50px;
        }

        .booking-content {
            grid-template-columns: 1fr;
            padding: 0 20px 80px;
        }

        .booking-summary {
            display: none;
        }

        .room-card img {
            width: 200px;
        }

        .room-info {
            padding: 18px;
        }

        .room-action {
            padding: 18px;
        }
    }

    /* MOBILE (< 768px) */
    @media (max-width: 768px) {
        .page-after-header {
            margin-top: 70px;
        }

        /* HEADER MOBILE */
        .booking-header {
            flex-direction: column;
            align-items: flex-start;
            padding: 15px 20px;
            gap: 12px;
        }

        .booking-header h3 {
            font-size: 1.1rem;
        }

        .booking-header p {
            font-size: .7rem;
        }

        .user-section {
            width: 100%;
            justify-content: flex-end;
        }

        .user-info {
            padding: 6px 14px;
        }

        .user-avatar {
            width: 28px;
            height: 28px;
            font-size: .75rem;
        }

        .user-name {
            font-size: .8rem;
        }

        .login-btn {
            padding: 6px 18px;
            font-size: .75rem;
        }

        /* HERO MOBILE */
        .booking-hero img {
            height: 250px;
        }

        /* SEARCH BAR MOBILE */
        .search-bar {
            padding: 20px;
            margin: 20px 15px 40px;
        }

        .search-bar form {
            gap: 15px;
        }

        .search-bar .field {
            min-width: 100%;
            flex: none;
        }

        .search-bar label {
            font-size: .7rem;
        }

        .search-bar input {
            padding: 10px 12px;
            font-size: .85rem;
        }

        .search-btn {
            width: 100%;
            height: 44px;
            font-size: .85rem;
        }

        /* CONTENT MOBILE */
        .booking-content {
            padding: 0 15px 60px;
            gap: 0;
        }

        .booking-summary {
            display: none;
        }

        /* ROOM CARD MOBILE - STACK VERTICAL */
        .room-card {
            flex-direction: column;
            margin-bottom: 20px;
        }

        .room-card img {
            width: 100%;
            height: 200px;
        }

        .room-info {
            padding: 15px;
        }

        .room-info h3 {
            font-size: 1.1rem;
        }

        .room-info p {
            font-size: .8rem;
        }

        .room-action {
            flex-direction: row;
            justify-content: space-between;
            padding: 15px;
            border-left: none;
            border-top: 1px solid #eee;
        }

        .price {
            font-size: 1rem;
        }

        .btn {
            padding: 8px 20px;
            font-size: .75rem;
        }

        /* DROPDOWN MOBILE */
        .dropdown-menu {
            width: 200px;
        }

        .dropdown-menu a {
            padding: 12px 18px;
            font-size: .75rem;
        }

        .dropdown-header {
            padding: 14px 18px 10px;
        }

        .dropdown-header strong {
            font-size: .8rem;
        }
    }

    /* SMALL MOBILE (< 480px) */
    @media (max-width: 480px) {
        .booking-header {
            padding: 12px 15px;
        }

        .booking-header h3 {
            font-size: 1rem;
        }

        .booking-header p {
            font-size: .65rem;
            line-height: 1.4;
        }

        .booking-hero img {
            height: 200px;
        }

        .search-bar {
            padding: 15px;
            margin: 15px 10px 30px;
            border-radius: 14px;
        }

        .search-bar label {
            font-size: .65rem;
        }

        .search-bar input {
            padding: 10px;
            font-size: .8rem;
        }

        .search-btn {
            height: 42px;
            font-size: .8rem;
            padding: 0 24px;
        }

        .booking-content {
            padding: 0 10px 50px;
        }

        .room-card {
            border-radius: 12px;
            margin-bottom: 15px;
        }

        .room-card img {
            height: 180px;
        }

        .room-info {
            padding: 12px;
        }

        .room-info h3 {
            font-size: 1rem;
        }

        .room-info small {
            font-size: .7rem;
        }

        .room-info p {
            font-size: .75rem;
        }

        .room-action {
            padding: 12px;
        }

        .price {
            font-size: .9rem;
        }

        .btn {
            padding: 8px 18px;
            font-size: .7rem;
        }

        .user-info {
            padding: 5px 12px;
        }

        .user-avatar {
            width: 26px;
            height: 26px;
            font-size: .7rem;
        }

        .user-name {
            font-size: .75rem;
        }

        .dropdown-menu {
            width: 180px;
        }

        .dropdown-menu a {
            padding: 10px 15px;
            font-size: .7rem;
        }
    }

    /* LANDSCAPE MOBILE */
    @media (max-width: 768px) and (orientation: landscape) {
        .booking-hero img {
            height: 180px;
        }
    }
</style>

<div class="page-after-header">

    <!-- BOOKING HEADER -->
    <div class="booking-header">
        <div>
            <h3>Velaris Hotel ★★★★★</h3>
            <p>Jl. Slamet Riyadi No.233, Purwosari, Kec. Laweyan, Surakarta, Jawa Tengah 57141</p>
        </div>

        <div class="user-section">
            <?php if (isset($_SESSION['customer_id'])): ?>
                <div class="user-dropdown">
                    <div class="user-info" onclick="toggleUserMenu()">
                        <div class="user-avatar">
                            <?= strtoupper(substr($_SESSION['customer_name'], 0, 1)); ?>
                        </div>
                        <span class="user-name"><?= htmlspecialchars($_SESSION['customer_name']); ?></span>
                        <span class="arrow">▾</span>
                    </div>

                    <div class="dropdown-menu" id="userMenu">
                        <div class="dropdown-header">
                            <strong><?= htmlspecialchars($_SESSION['customer_name']); ?></strong>
                            <small>Velaris Guest</small>
                        </div>

                        <a href="guest_profile.php"
                            class="<?= basename($_SERVER['PHP_SELF']) == 'guest_profile.php' ? 'active' : '' ?>">
                            Guest Profile
                        </a>

                        <hr>

                        <a href="javascript:void(0)" class="logout" onclick="confirmLogout()">
                            Logout
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <a href="auth/login.php" class="login-btn">Login</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- HERO -->
    <section class="booking-hero">
        <img src="uploads/experiences/pool.jpg" alt="Velaris Hotel">
    </section>

    <!-- SEARCH -->
    <section class="search-bar">
        <form method="GET">
            <div class="field">
                <label>CHECK-IN</label>
                <input type="date" id="check_in" name="checkin" value="<?= htmlspecialchars($checkin); ?>" required>
            </div>
            <div class="field">
                <label>CHECK-OUT</label>
                <input type="date" id="check_out" name="checkout" value="<?= htmlspecialchars($checkout); ?>" required>
            </div>
            <button type="submit" class="search-btn">Search Rooms</button>
        </form>
    </section>

    <!-- CONTENT -->
    <section class="booking-content">

        <!-- LEFT -->
        <div>
            <?php while ($r = $rooms->fetch_assoc()): ?>
                <div class="room-card">
                    <img src="uploads/kamar/<?= htmlspecialchars($r['foto_kamar']) ?>">

                    <div class="room-info">
                        <h3><?= htmlspecialchars($r['nama_kamar']) ?></h3>
                        <small><?= htmlspecialchars($r['tipe_kamar']) ?></small>
                        <p><?= substr(strip_tags($r['deskripsi']), 0, 140) ?>...</p>

                        <?php if ($r['stok'] > 0): ?>
                            <p class="availability">✔ Free cancellation <br> ✔ Book now, pay later</p>
                        <?php else: ?>
                            <p class="unavailable">Selected dates are unavailable</p>
                        <?php endif; ?>
                    </div>

                    <div class="room-action">
                        <p class="price">IDR <?= number_format($r['harga'], 0, ',', '.') ?></p>

                        <?php if ($checkin && $checkout && $r['stok'] > 0): ?>
                            <?php if (!isset($_SESSION['customer_id'])): ?>
                                <a href="auth/login.php" class="btn">Select</a>
                            <?php else: ?>
                                <a href="reservasi.php?id_kamar=<?= $r['id_kamar'] ?>&checkin=<?= $checkin ?>&checkout=<?= $checkout ?>"
                                    class="btn">Select</a>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="btn disabled">
                                <?= $r['stok'] > 0 ? 'Select Dates' : 'Unavailable' ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- RIGHT SUMMARY -->
        <div class="booking-summary">
            <div class="summary-card">
                <h4>Your Booking</h4>

                <?php if($checkin && $checkout): 
                    $checkin_date = new DateTime($checkin);
                    $checkout_date = new DateTime($checkout);
                    $nights = $checkout_date->diff($checkin_date)->days;
                ?>
                    <div class="summary-dates">
                        <div class="date-item">
                            <label>Check-in</label>
                            <span class="date-value"><?= date('d M Y', strtotime($checkin)) ?></span>
                        </div>
                        <div class="date-item">
                            <label>Check-out</label>
                            <span class="date-value"><?= date('d M Y', strtotime($checkout)) ?></span>
                        </div>
                        <div class="date-item" style="border-top:1px solid rgba(212,175,55,.3);padding-top:12px;margin-top:12px;">
                            <label>Total Nights</label>
                            <span class="date-value"><?= $nights ?> <?= $nights > 1 ? 'nights' : 'night' ?></span>
                        </div>
                    </div>

                    <div class="summary-info">
                        <p>
                            <span>Guests</span>
                            <strong>2 Adults</strong>
                        </p>
                        <p>
                            <span>Rooms</span>
                            <strong>1 Room</strong>
                        </p>
                    </div>

                    <div class="summary-note">
                        <strong>✓ Best Price Guarantee</strong>
                        Click the button below to choose from our room recommendations. Affordable, elegant, and comfortable.
                    </div>

                    <div class="summary-cta" onclick="document.querySelector('.room-card .btn:not(.disabled)')?.click()">
                        Select Your Room
                    </div>
                <?php else: ?>
                    <div class="summary-dates">
                        <div class="date-item">
                            <label>Check-in</label>
                            <span class="date-value" style="color:#999;">Select date</span>
                        </div>
                        <div class="date-item">
                            <label>Check-out</label>
                            <span class="date-value" style="color:#999;">Select date</span>
                        </div>
                    </div>

                    <div class="summary-note">
                        <strong>💡 Getting Started</strong>
                        Select your check-in and check-out dates above to see available rooms and prices.
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </section>

</div>

<script>
    function confirmLogout() {
        Swal.fire({
            title: 'Confirm Logout',
            text: 'Are you sure you want to logout from your account?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#d4af37',
            cancelButtonColor: '#999',
            confirmButtonText: 'Yes, Logout',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'auth/logout.php';
            }
        });
    }

    <?php if (isset($_GET['logout']) && $_GET['logout'] == 'success'): ?>
        Swal.fire({
            title: 'Logged Out Successfully',
            text: 'Thank you for visiting. We hope to see you again soon!',
            icon: 'success',
            confirmButtonColor: '#d4af37',
            confirmButtonText: 'OK',
            timer: 3000,
            timerProgressBar: true
        });
        window.history.replaceState({}, document.title, window.location.pathname);
    <?php endif; ?>

    document.addEventListener('DOMContentLoaded', () => {
        const today = new Date().toISOString().split('T')[0];
        const checkIn = document.getElementById('check_in');
        const checkOut = document.getElementById('check_out');

        checkIn.min = today;
        checkOut.min = today;

        checkIn.addEventListener('change', () => {
            checkOut.min = checkIn.value;
            if (checkOut.value < checkIn.value) {
                checkOut.value = '';
            }
        });
    });

    <?php if (isset($_GET['error']) && $_GET['error'] === 'invalid_range'): ?>
        Swal.fire({
            icon: 'error',
            title: 'Invalid Date Range',
            text: 'Check-out date must be after check-in date.',
            confirmButtonColor: '#d4af37'
        });
    <?php endif; ?>

    function toggleUserMenu() {
        const menu = document.getElementById('userMenu');
        menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
    }

    document.addEventListener('click', function (e) {
        const dropdown = document.querySelector('.user-dropdown');
        if (dropdown && !dropdown.contains(e.target)) {
            document.getElementById('userMenu').style.display = 'none';
        }
    });
</script>

<?php require_once "components/footer.php"; ?>