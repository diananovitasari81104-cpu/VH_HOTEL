<?php
require_once __DIR__ . '/../../config/functions.php';
require_staff();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $page_title ?? 'Velaris Hotel' ?></title>

<!-- GOOGLE FONTS -->
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

<!-- BOOTSTRAP CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- SWEETALERT -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<style>
:root{
    --gold:#d4af37;
    --header-h:64px;
}

body{
    margin:0;
    font-family:'Inter',sans-serif;
}

/* ================= HEADER ================= */
.header{
    position:fixed;
    inset:0 0 auto 0;
    height:var(--header-h);
    z-index:1000;

    background:rgba(140,150,160,.35);
    backdrop-filter:blur(6px);
    -webkit-backdrop-filter:blur(6px);

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
    text-shadow:0 3px 6px rgba(0,0,0,.35);
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

.nav-menu a:hover{
    color:var(--gold);
}

/* ADMIN BUTTON */
.user-btn{
    font-size:.75rem;
    letter-spacing:1px;
    border:1px solid #fff;
    padding:6px 18px;
    border-radius:20px;
    color:#fff;
    background:transparent;
    cursor:pointer;
}

.user-btn::after{
    content:"▼";
    font-size:.6rem;
    margin-left:8px;
}

/* DROPDOWN (CUSTOM) */
.dropdown{
    position:relative;
}

.dropdown-menu{
    display:none;
    position:absolute;
    right:0;
    top:calc(100% + 10px);
    min-width:auto;
    padding:8px;
    border-radius:22px;
    border:none;
    box-shadow:0 14px 28px rgba(0,0,0,.18);
    background:#fff;
    z-index:2000;
}

.dropdown.show .dropdown-menu{
    display:block;
}

.dropdown-menu .dropdown-item{
    font-size:.78rem;
    padding:8px 22px;
    border-radius:22px;
    text-align:center;
    color:#000;
    line-height:1.2;
    white-space:nowrap;
}

.dropdown-menu .dropdown-item:hover{
    background:var(--gold);
    color:#000;
}

/*  HAMBURGER  */
.menu-toggle{
    display:none;
    background:none;
    border:none;
    font-size:1.6rem;
    color:#fff;
    margin-left:12px;
    cursor:pointer;
}

/*  MOBILE MENU  */
.mobile-menu{
    position:fixed;
    top:var(--header-h);
    left:0;
    right:0;
    background:rgba(120,130,140,.98);
    backdrop-filter:blur(6px);
    display:none;
    flex-direction:column;
    padding:24px;
    gap:18px;
    z-index:999;
}

.mobile-menu a{
    font-size:.8rem;
    letter-spacing:2px;
    color:#fff;
    text-decoration:none;
}

/*  RESPONSIVE  */
@media(max-width:992px){
    .nav-menu{display:none}
    .menu-toggle{display:block}
    .header .wrap{padding:0 24px}
}

/* SWEETALERT HOTEL THEME */
.swal2-popup{
    border-radius:24px !important;
    font-family:'Inter',sans-serif;
}

.swal2-title{
    font-family:'Cinzel',serif;
    letter-spacing:2px;
}

.swal2-confirm{
    background:#000 !important;
    border-radius:24px !important;
    padding:10px 32px !important;
}

.swal2-cancel{
    border-radius:24px !important;
    padding:10px 32px !important;
}

/* ================= LUXURY DROPDOWN ================= */
.dropdown-menu{
    display:none;
    position:absolute;
    right:0;
    top:calc(100% + 14px);

    min-width:220px;
    padding:14px;
    border-radius:18px;
    border:1px solid rgba(212,175,55,.35);

    background:linear-gradient(
        180deg,
        #ffffff 0%,
        #f7f5f1 100%
    );

    box-shadow:
        0 18px 40px rgba(0,0,0,.18),
        inset 0 1px 0 rgba(255,255,255,.7);

    z-index:2000;
}

/* ARROW */
.dropdown-menu::before{
    content:"";
    position:absolute;
    top:-8px;
    right:26px;
    width:16px;
    height:16px;
    background:#fff;
    transform:rotate(45deg);
    border-left:1px solid rgba(212,175,55,.35);
    border-top:1px solid rgba(212,175,55,.35);
}

/* SHOW */
.dropdown.show .dropdown-menu{
    display:block;
}

/* ITEM */
.dropdown-menu .dropdown-item{
    font-size:.78rem;
    padding:10px 18px;
    border-radius:14px;
    text-align:left;
    color:#222;
    letter-spacing:1px;
    display:flex;
    align-items:center;
    gap:10px;
    transition:.25s ease;
}

/* ICON STYLE (optional if pakai icon) */
.dropdown-menu .dropdown-item i{
    font-size:.8rem;
    color:var(--gold);
}

/* HOVER EFFECT */
.dropdown-menu .dropdown-item:hover{
    background:linear-gradient(
        90deg,
        rgba(212,175,55,.15),
        rgba(212,175,55,.05)
    );
    color:#000;
    transform:translateX(4px);
}

/* DIVIDER */
.dropdown-divider{
    height:1px;
    background:rgba(0,0,0,.08);
    margin:8px 0;
}

</style>
</head>

<body>

<header class="header" id="siteHeader">
    <div class="wrap">

        <div class="brand">VELARIS HOTEL</div>

        <!-- DESKTOP MENU -->
        <nav class="nav-menu">
            <a href="/uaspemweb/VELARIS_HOTEL/admin/index.php">HOME</a>
            <a href="/uaspemweb/VELARIS_HOTEL/admin/kamar/index.php#rooms">ROOM</a>
            <a href="/uaspemweb/VELARIS_HOTEL/admin/experiences/index.php#experience">EXPERIENCES</a>
            <a href="/uaspemweb/VELARIS_HOTEL/admin/blog/index.php#gallery">BLOG</a>
        </nav>

        <!-- RIGHT -->
        <div class="d-flex align-items-center gap-2">

            <!-- ADMIN DROPDOWN (CUSTOM JS) -->
            <div class="dropdown" id="adminDropdown">
                <button class="user-btn" id="adminToggle">
                    <?= htmlspecialchars($_SESSION['nama_lengkap']) ?>
                </button>

                <ul class="dropdown-menu">

    <li>
        <span class="dropdown-item" style="cursor:default;font-weight:600;">
            <?= htmlspecialchars($_SESSION['nama_lengkap']) ?>
        </span>
    </li>

    <li><div class="dropdown-divider"></div></li>

    <li>
        <a class="dropdown-item" href="/uaspemweb/VELARIS_HOTEL/admin/reservasi/index.php">
            Reservations
        </a>
    </li>

    <li>
        <a class="dropdown-item" href="/uaspemweb/VELARIS_HOTEL/admin/pembatalan/index.php">
            Cancellation Requests
        </a>
    </li>

    <?php if (is_admin()): ?>
        <li><div class="dropdown-divider"></div></li>

        <li>
            <a class="dropdown-item" href="/uaspemweb/VELARIS_HOTEL/admin/users/index.php">
                Manage Users
            </a>
        </li>

        <li>
            <a class="dropdown-item" href="/uaspemweb/VELARIS_HOTEL/admin/log/index.php">
                Log Activity
            </a>
        </li>
    <?php endif; ?>

    <li><div class="dropdown-divider"></div></li>

    <li>
        <a class="dropdown-item text-danger"
           href="javascript:void(0)"
           onclick="confirmAdminLogout()">
            Logout
        </a>
    </li>

</ul>

            </div>

            <!-- HAMBURGER -->
            <button class="menu-toggle" id="menuToggle">☰</button>
        </div>

    </div>
</header>

<!-- MOBILE MENU -->
<div class="mobile-menu" id="mobileMenu">
    <a href="/uaspemweb/VELARIS_HOTEL/admin/index.php">HOME</a>
    <a href="/uaspemweb/VELARIS_HOTEL/admin/kamar/index.php#rooms">ROOM</a>
    <a href="/uaspemweb/VELARIS_HOTEL/admin/experiences/index.php#experience">EXPERIENCES</a>
    <a href="/uaspemweb/VELARIS_HOTEL/admin/blog/index.php#gallery">BLOG</a>
</div>

<script>
/* HEADER SCROLL */
const header = document.getElementById('siteHeader');
window.addEventListener('scroll', () => {
    header.classList.toggle('scrolled', window.scrollY > 10);
});

/* HAMBURGER */
const toggle = document.getElementById('menuToggle');
const mobileMenu = document.getElementById('mobileMenu');

toggle.addEventListener('click', (e) => {
    e.stopPropagation();
    mobileMenu.style.display =
        mobileMenu.style.display === 'flex' ? 'none' : 'flex';
});

/* ADMIN DROPDOWN  */
const adminToggle = document.getElementById('adminToggle');
const adminDropdown = document.getElementById('adminDropdown');

adminToggle.addEventListener('click', (e) => {
    e.stopPropagation();
    adminDropdown.classList.toggle('show');
});

/* close dropdown if click outside */
document.addEventListener('click', () => {
    adminDropdown.classList.remove('show');
});

function confirmAdminLogout(){
    Swal.fire({
        title: 'Confirm Logout',
        text: 'Are you sure you want to logout from admin panel?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Logout',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#000',
        cancelButtonColor: '#aaa',
        reverseButtons: true,
        customClass: {
            popup: 'swal-hotel',
            title: 'swal-title'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '/uaspemweb/VELARIS_HOTEL/admin/logout.php';
        }
    });
}
</script>
