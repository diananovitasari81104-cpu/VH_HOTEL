<?php
require_once 'config/database.php';
require_once 'config/functions.php';


$page_title = 'Home';
require_once 'components/header.php';
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Allura&display=swap" rel="stylesheet">

<!-- hero kek di figma-->
<section style="
    height:100vh;
    position:relative;
">
    <img src="uploads/velaris_hotel.png" alt="Velaris Hotel"
         style="
            width:100%;
            height:100%;
            object-fit:cover;
         ">
</section>


<!-- ABOUT -->
<style>
.about-wrap{
    position:relative;
    padding:80px 40px;
}

.about-box{
    max-width:1100px;
    margin:auto;
    display:grid;
    grid-template-columns:1fr 1px 1fr;
    gap:60px;
    align-items:flex-start;
}

/* garis tengah */
.about-divider{
    width:1px;
    background:#cfcfcf;
    height:100%;
}

/* aksen emas */
.about-wrap::before,
.about-wrap::after{
    content:'';
    position:absolute;
    width:40px;
    height:40px;
    border:2px solid #d4af37;
}
.about-wrap::before{
    top:40px; left:40px;
    border-right:none;
    border-bottom:none;
}
.about-wrap::after{
    bottom:40px; right:40px;
    border-left:none;
    border-top:none;
}

/* LEFT */
.about-left h4{
    font-family:'Cinzel',serif;
    color:#d4af37;
    letter-spacing:2px;
    font-size:1.1rem;
    margin-bottom:18px;
}
.about-left p{
    max-width:420px;
    line-height:1.7;
    font-size:.95rem;
    margin-bottom:22px;
}
.about-left .btn{
    border-radius:0;
    padding:8px 22px;
    letter-spacing:1px;
}

/* RIGHT */
.about-right h5{
    font-family:'Cinzel',serif;
    color:#d4af37;
    letter-spacing:2px;
    margin-bottom:24px;
}
.about-info{
    list-style:none;
    padding:0;
}
.about-info li{
    display:flex;
    gap:14px;
    margin-bottom:16px;
    font-size:.9rem;
}
.about-info i{
    color:#d4af37;
    font-size:1.1rem;
}
</style>

<section class="about-wrap">

    <div class="about-box">

        <!-- LEFT -->
        <div class="about-left">
            <h4>VELARIS HOTEL – INDONESIA</h4>
            <p>
                We are back with new management, more facilities,
                completed with consistent warm hospitality.
                A 4-star hotel will provide you with the ultimate
                comfort and relaxation during your stay in Solo.
            </p>

            <a href="#" class="btn btn-outline-dark">
                HOTEL BLOG →
            </a>
        </div>

        <!-- DIVIDER -->
        <div class="about-divider"></div>

        <!-- RIGHT -->
        <div class="about-right">
            <h5>VELARIS<br>HOTEL</h5>

            <ul class="about-info">
                <li>
                    <i class="bi bi-envelope"></i>
                    booking@velarishotel.com
                </li>
                <li>
                    <i class="bi bi-telephone"></i>
                    +62 361 846 4719
                </li>
                <li>
                    <i class="bi bi-geo-alt"></i>
                    Jl. Slamet Riyadi No.233, Surakarta
                </li>
            </ul>
        </div>

    </div>

</section>


<!-- ROOMS -->
<style>
.room-hero{
    position:relative;
    height:420px;
    border-radius:28px;
    overflow:hidden;
    margin-bottom:48px;
}
.room-hero img{
    width:100%;
    height:100%;
    object-fit:cover;
}
.room-overlay{
    position:absolute;
    inset:0;
    background:rgba(0,0,0,.35);
    display:flex;
    flex-direction:column;
    justify-content:center;
    align-items:center;
    text-align:center;
    color:#fff;
    padding:40px;
}
.room-overlay h2{
    font-family:'Playfair Display',serif;
    font-size:2.6rem;
    margin-bottom:10px;
}
.room-overlay p{
    max-width:520px;
    font-size:.95rem;
    line-height:1.6;
    opacity:.9;
    margin-bottom:22px;
}
.room-overlay a{
    padding:10px 30px;
    border:1px solid #fff;
    border-radius:30px;
    color:#fff;
    text-decoration:none;
    letter-spacing:1px;
}
.room-overlay a:hover{
    background:#d4af37;
    border-color:#d4af37;
    color:#000;
}
</style>

<section class="container py-5">

    <div class="room-hero">
        <img src="uploads/kamar/RWKA1W7pZuAgCYb9X9GD.png" alt="Rooms">
        <div class="room-overlay">
            <h2>Rooms</h2>
            <p>
                Where every detail is crafted for comfort,
                offering a serene blend of culture, luxury,
                and heartfelt hospitality.
            </p>
            <a href="booking.php">DISCOVER MORE</a>
        </div>
    </div>

</section>

<!-- ROOMS CARD -->
<style>
.room-card img{
    transition:.4s ease;
}
.room-card:hover img{
    transform:scale(1.05);
}
</style>

<section id="rooms-card" class="container pb-5">
    <div class="row g-4 justify-content-center">

    <?php
$rooms = fetch_all("SELECT * FROM kamar LIMIT 3");
foreach ($rooms as $r):
?>
    <div class="col-md-4">
        <div class="card room-card border-0 shadow-sm rounded-4 overflow-hidden">

            <img
                src="uploads/kamar/<?= htmlspecialchars($r['foto_kamar']) ?>"
                alt="<?= htmlspecialchars($r['nama_kamar']) ?>"
                style="height:260px;object-fit:cover"
            >

            <div class="card-body text-center">
                <h5 class="mb-1">
                    <?= htmlspecialchars($r['nama_kamar']) ?>
                </h5>
                <p class="text-muted mb-0">
                    <?= format_rupiah($r['harga']) ?>/night
                </p>
            </div>

        </div>
    </div>
<?php endforeach; ?>


    </div>
</section>

<!-- RESTAURANTS (FIGMA STYLE) -->
<style>
.rest-section{
    padding:80px 0;
}

.rest-wrap{
    max-width:1100px;
    margin:auto;
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:80px;
    align-items:center;
}

/* LEFT IMAGES */
.rest-images{
    position:relative;
    display:flex;
    flex-direction:column;
    gap:24px;
}

.rest-images img{
    width:260px;
    height:180px;
    object-fit:cover;
    border-radius:18px;
}

/* offset images */
.rest-images img:nth-child(1){
    align-self:flex-start;
}
.rest-images img:nth-child(2){
    align-self:center;
}
.rest-images img:nth-child(3){
    align-self:flex-end;
}

/* RIGHT CONTENT */
.rest-content h2{
    font-family:'Playfair Display',serif;
    font-size:2.2rem;
    color:#1f3c88;
    margin-bottom:22px;
}

.rest-content p{
    font-size:.95rem;
    line-height:1.7;
    max-width:420px;
    margin-bottom:18px;
}

.rest-content a{
    text-decoration:none;
    font-weight:500;
    color:#1f3c88;
    letter-spacing:.5px;
}

.rest-content a:hover{
    text-decoration:underline;
}

/* responsive */
@media(max-width:992px){
    .rest-wrap{
        grid-template-columns:1fr;
        gap:40px;
    }
    .rest-images{
        align-items:center;
    }
    .rest-images img{
        width:100%;
        max-width:340px;
    }
}
</style>

<section class="rest-section">

    <div class="rest-wrap">

        <!-- LEFT IMAGES -->
        <div class="rest-images">
            <img src="uploads/restaurant1.jpg" alt="Restaurant">
            <img src="uploads/restaurant2.jpg" alt="Restaurant">
            <img src="uploads/restaurant3.jpg" alt="Restaurant">
        </div>

        <!-- RIGHT CONTENT -->
        <div class="rest-content">
            <h2>Restaurants</h2>
            <p>
                Our hotel offers a diverse and delectable dining
                experience at our on-site restaurant. Our restaurant
                features a menu of delicious dishes made with fresh
                and locally sourced ingredients, as well as international
                cuisine to cater to a variety of tastes.
            </p>
            <p>
                We take pride in our exceptional service and strive
                to provide a welcoming atmosphere that enhances
                your dining experience.
            </p>

           
        </div>

    </div>

</section>



<!-- EXPERIENCE -->
<!-- EXPERIENCE SLIDER -->
<style>
.experience-section{
    padding:100px 0;
}

.experience-wrap{
    max-width:1200px;
    margin:auto;
    position:relative;
}

.experience-title{
    text-align:center;
    font-family:'Cinzel',serif;
    font-size:2.4rem;
    color:#1f3c88;
    margin-bottom:60px;
}

/* SLIDER */
.exp-slider-wrapper{
    position:relative;
    overflow:hidden;
}

.exp-slider{
    display:flex;
    gap:24px;
    transition:transform .5s ease;
}

.exp-slide{
    flex:0 0 300px;
}

.exp-slide img{
    width:100%;
    height:380px;
    object-fit:cover;
    border-radius:22px;
}

/* ARROWS */
.exp-arrow{
    position:absolute;
    top:50%;
    transform:translateY(-50%);
    width:56px;
    height:56px;
    border-radius:50%;
    background:rgba(255, 215, 0, 0.9);
    border:none;
    font-size:28px;
    font-weight:bold;
    cursor:pointer;
    z-index:10;
    color:#000;
    display:flex;
    align-items:center;
    justify-content:center;
    box-shadow:0 10px 30px rgba(0,0,0,.25);
    transition:.3s ease;
}

.exp-arrow:hover{
    background:#000;
    color:#FFD700;
    transform:translateY(-50%) scale(1.1);
}


.exp-arrow.left{
    left:-10px;
}

.exp-arrow.right{
    right:-10px;
}

.exp-card{
    position:relative;
    overflow:hidden;
    border-radius:22px;
}

/* overlay desc */
.exp-desc{
    position:absolute;
    left:0;
    right:0;
    bottom:0;
    padding:20px;
    background:linear-gradient(to top, rgba(0,0,0,.75), rgba(0,0,0,0));
    color:#fff;
    transform:translateY(100%);
    transition:.35s ease;
}

/* hover effect */
.exp-card:hover .exp-desc{
    transform:translateY(0);
}

/* text style */
.exp-desc h4{
    font-size:1rem;
    margin-bottom:6px;
    font-weight:600;
}

.exp-desc p{
    font-size:.8rem;
    line-height:1.5;
    margin-bottom:6px;
    opacity:.9;
}

.exp-price{
    font-size:.8rem;
    font-weight:600;
    color:#FFD700;
}


/* responsive */
@media(max-width:768px){
    .exp-slide{
        flex:0 0 220px;
    }
    .exp-slide img{
        height:280px;
    }
}
</style>

<section id="experience" class="experience-section">

    <div class="experience-wrap">

        <h2 class="experience-title">Experience</h2>

        <?php
        $experiences = fetch_all("SELECT * FROM experiences ORDER BY id_experience ASC");
        ?>

        <div class="exp-slider-wrapper">

            <button class="exp-arrow left" id="expPrev">&#10094;</button>

            <div class="exp-slider" id="expSlider">
                <?php foreach ($experiences as $e): ?>
                    <div class="exp-slide">
                        <div class="exp-card">

                            <img
                                src="uploads/experiences/<?= htmlspecialchars($e['foto']) ?>"
                                alt="<?= htmlspecialchars($e['nama_aktivitas']) ?>"
                            >

                            <div class="exp-desc">
                                <h4><?= htmlspecialchars($e['nama_aktivitas']) ?></h4>
                                <p><?= htmlspecialchars($e['deskripsi']) ?></p>

                            </div>

                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <button class="exp-arrow right" id="expNext">&#10095;</button>

        </div>

    </div>

</section>


<script>
const slider = document.getElementById('expSlider');
const prev = document.getElementById('expPrev');
const next = document.getElementById('expNext');

let offset = 0;
const slideWidth = 324; // 300 + gap

next.addEventListener('click', () => {
    if (offset > -(slider.scrollWidth - slider.clientWidth)) {
        offset -= slideWidth;
        slider.style.transform = `translateX(${offset}px)`;
    }
});

prev.addEventListener('click', () => {
    if (offset < 0) {
        offset += slideWidth;
        slider.style.transform = `translateX(${offset}px)`;
    }
});
</script>

<?php require_once 'components/footer.php'; ?>
