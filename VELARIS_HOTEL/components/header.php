<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $page_title ?? 'Velaris Hotel' ?></title>

<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
:root{
    --gold:#d4af37;
    --header-h:64px;
}

body{
    margin:0;
    font-family:'Inter',sans-serif;
}

/* HEADER  */
.header{
    position:fixed;
    top:0; left:0; right:0;
    height:var(--header-h);
    z-index:1000;
    background:rgba(140,150,160,.35);
    backdrop-filter:blur(6px);
    display:flex;
    align-items:center;
    transition:.3s;
}

.header.scrolled{
    background:#9fa4a8;
    backdrop-filter:none;
}

.header .wrap{
    width:100%;
    padding:0 80px;
    display:flex;
    align-items:center;
    justify-content:space-between;
}

/* BRAND */
.brand{
    font-family:'Cinzel',serif;
    color:var(--gold);
    font-size:1.45rem;
    letter-spacing:4px;
    font-weight:700;
}

/* DESKTOP MENU */
.nav-menu{
    display:flex;
    gap:56px;
}

.nav-menu a{
    font-size:.78rem;
    letter-spacing:2px;
    font-weight:500;
    color:#fff;
    text-decoration:none;
}

.nav-menu a:hover{color:var(--gold);}

/* BOOKING BUTTON */
.btn-booking-now{
    padding:8px 22px;
    border-radius:30px;
    border:1px solid #fff;
    color:#fff;
    text-decoration:none;
    font-size:.75rem;
    letter-spacing:1px;
    white-space:nowrap;
}

.btn-booking-now:hover{
    background:var(--gold);
    color:#000;
}

/* HAMBURGER */
.menu-toggle{
    display:none;
    background:none;
    border:none;
    font-size:1.8rem;
    color:#fff;
    cursor:pointer;
}

/* MOBILE MENU */
.mobile-menu{
    position:fixed;
    top:var(--header-h);
    left:0; right:0;
    background:rgba(120,130,140,.97);
    backdrop-filter:blur(6px);
    display:none;
    flex-direction:column;
    padding:24px;
    gap:20px;
    z-index:999;
}

.mobile-menu a{
    color:#fff;
    text-decoration:none;
    font-size:.85rem;
    letter-spacing:2px;
}

.mobile-menu .btn-booking-now{
    margin-top:10px;
    text-align:center;
}

/* RESPONSIVE */
@media (max-width: 992px){
    .nav-menu{display:none}
    .btn-booking-now.desktop{display:none}
    .menu-toggle{display:block}
    .header .wrap{padding:0 24px}
}

.nav-menu a.active{
    color: var(--gold);
    position: relative;
}

.nav-menu a.active::after{
    content:'';
    position:absolute;
    left:0;
    bottom:-6px;
    width:100%;
    height:1px;
    background:var(--gold);
}

</style>
</head>

<body>

<header class="header" id="siteHeader">
    <div class="wrap">

        <div class="brand">VELARIS HOTEL</div>

        <!-- DESKTOP MENU -->
        <nav class="nav-menu">
    <a href="index.php" class="<?= $currentPage=='index.php'?'active':'' ?>">HOME</a>
    <a href="rooms.php" class="<?= $currentPage=='rooms.php'?'active':'' ?>">ROOM</a>
    <a href="experience.php" class="<?= $currentPage=='experience.php'?'active':'' ?>">EXPERIENCES</a>
    <a href="contact.php" class="<?= $currentPage=='contact.php'?'active':'' ?>">CONTACT</a>

    <?php if (isset($_SESSION['customer_id'])): ?>
        <a href="checkin_online.php" class="<?= $currentPage=='checkin_online.php'?'active':'' ?>">
            ONLINE CHECK-IN
        </a>
    <?php else: ?>
        <a href="auth/login.php">
            ONLINE CHECK-IN
        </a>
    <?php endif; ?>
</nav>


        <div class="d-flex align-items-center gap-3">
            <!-- BOOKING DESKTOP -->
            <a href="booking.php" class="btn-booking-now desktop">
                Booking Now
            </a>

            <!-- HAMBURGER -->
            <button class="menu-toggle" id="menuToggle">
                ☰
            </button>
        </div>

    </div>
</header>

<!-- MOBILE MENU -->
<div class="mobile-menu" id="mobileMenu">
    <a href="index.php">HOME</a>
    <a href="rooms.php#rooms">ROOM</a>
    <a href="experience.php#experience">EXPERIENCES</a>
    <a href="contact.php#gallery">CONTACT</a>

    <?php if (isset($_SESSION['customer_id'])): ?>
    <a href="checkin_online.php">ONLINE CHECK-IN</a>
<?php else: ?>
    <a href="auth/login.php">ONLINE CHECK-IN</a>
<?php endif; ?>

    <a href="booking.php" class="btn-booking-now">
        Booking Now
    </a>
</div>

<script>
const header = document.getElementById('siteHeader');
window.addEventListener('scroll', () => {
    header.classList.toggle('scrolled', window.scrollY > 10);
});

const toggle = document.getElementById('menuToggle');
const mobileMenu = document.getElementById('mobileMenu');

toggle.addEventListener('click', e => {
    e.stopPropagation();
    mobileMenu.style.display =
        mobileMenu.style.display === 'flex' ? 'none' : 'flex';
});

document.addEventListener('click', () => {
    mobileMenu.style.display = 'none';
});
</script>

</body>
</html>
