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

    .user-avatar {
        width: 32px;
        height: 32px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-weight: 600;
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
    }

    .search-bar input {
        width: 100%;
        padding: 12px 14px;
        border: 1px solid #ccc;
        border-radius: 10px;
    }

    .search-btn {
        height: 46px;
        padding: 0 36px;
        border: none;
        background: #000;
        color: #fff;
        border-radius: 30px;
        font-size: .85rem;
        cursor: pointer;
        transition: all .3s ease;
    }

    .search-btn:hover {
        background: #333;
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

    /* ROOM CARD */
    .room-card {
        background: #fff;
        border-radius: 16px;
        overflow: hidden;
        margin-bottom: 30px;
        display: flex;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .08);
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
    }

    .btn {
        padding: 8px 22px;
        border-radius: 20px;
        border: 1px solid #000;
        text-decoration: none;
        font-size: .75rem;
        color: #000;
        transition: all .3s ease;
    }

    .btn:hover {
        background: #000;
        color: #fff;
    }

    .btn.disabled {
        opacity: .5;
        pointer-events: none;
    }

    /* Custom SweetAlert Styling */
    .swal2-popup {
        font-family: 'Poppins', sans-serif;
        border-radius: 20px;
    }

    .swal2-title {
        font-family: 'Cinzel', serif;
        color: #333;
    }

    .swal2-confirm {
        background: #000 !important;
        border-radius: 25px;
        padding: 10px 30px !important;
    }

    .swal2-cancel {
        border-radius: 25px;
        padding: 10px 30px !important;
    }

    /* ===== USER DROPDOWN ELEGANT HOTEL ===== */
    .user-dropdown {
        position: relative;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 18px;
        background: #f8f8f8;
        border-radius: 30px;
        border: 1px solid #e5e5e5;
        cursor: pointer;
        transition: all .3s ease;
    }

    .user-info:hover {
        background: #f1f1f1;
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

    /* Header kecil dalam dropdown */
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
</style>

<div class="page-after-header">

    <!-- BOOKING HEADER -->
    <div class="booking-header">
        <div>
            <h3>Hotel ★★★★★</h3>
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
                <label>CHECK IN</label>
                <input type="date" id="check_in" name="checkin" value="<?= htmlspecialchars($checkin); ?>" required>
            </div>
            <div class="field">
                <input type="date" id="check_out" name="checkout" value="<?= htmlspecialchars($checkout); ?>" required>
            </div>
            <button type="submit" class="search-btn">Search</button>
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
                                <?= $r['stok'] > 0 ? 'Select' : 'Find available dates' ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- RIGHT SUMMARY -->
        <div>
            <div class="room-card">
                <div class="room-info">
                    <strong><?= $checkin ?: 'Check-in date' ?> – <?= $checkout ?: 'Check-out date' ?></strong>
                    <p>1 room, 2 guests</p>
                </div>
                <div class="room-action">
                    <span class="btn disabled">Book</span>
                </div>
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
            confirmButtonColor: '#000',
            cancelButtonColor: '#999',
            confirmButtonText: 'Yes, Logout',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            customClass: {
                popup: 'swal-elegant'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect ke auth/logout.php (yang akan redirect ke api/auth/logout.php)
                window.location.href = 'auth/logout.php';
            }
        });
    }

    // Check if logout was successful (from URL parameter)
    <?php if (isset($_GET['logout']) && $_GET['logout'] == 'success'): ?>
        Swal.fire({
            title: 'Logged Out Successfully',
            text: 'Thank you for visiting. We hope to see you again soon!',
            icon: 'success',
            confirmButtonColor: '#000',
            confirmButtonText: 'OK',
            timer: 3000,
            timerProgressBar: true
        });
        // Remove the parameter from URL
        window.history.replaceState({}, document.title, window.location.pathname);
    <?php endif; ?>

    document.addEventListener('DOMContentLoaded', () => {
        const today = new Date().toISOString().split('T')[0];

        const checkIn = document.getElementById('check_in');
        const checkOut = document.getElementById('check_out');

        // Tidak boleh pilih tanggal sebelum hari ini
        checkIn.min = today;
        checkOut.min = today;

        // Check-out tidak boleh lebih awal dari check-in
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

    // Tutup dropdown kalau klik di luar
    document.addEventListener('click', function (e) {
        const dropdown = document.querySelector('.user-dropdown');
        if (dropdown && !dropdown.contains(e.target)) {
            document.getElementById('userMenu').style.display = 'none';
        }
    });


</script>

<?php require_once "components/footer.php"; ?>