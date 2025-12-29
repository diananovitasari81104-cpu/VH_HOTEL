</div> <!-- end .main-content -->

<style>
.footer-velaris{
    background:linear-gradient(135deg,#1b1b1b,#2b2b2b);
    color:#ddd;
    padding:70px 0 30px;
    font-size:.9rem;
}

/* ===== HARD RESET BOOTSTRAP GRID ===== */
.footer-velaris *{ box-sizing:border-box }

.footer-velaris .container{
    max-width:1320px !important;
    margin-left:auto !important;
    margin-right:auto !important;
    padding-left:15px !important;
    padding-right:15px !important;
}

.footer-velaris .row{
    margin-left:-15px;
    margin-right:-15px;
}

.footer-velaris [class^="col-"]{
    padding-left:15px;
    padding-right:15px;
}

/* ===== TEXT ===== */
.footer-velaris h6{
    color:#fff;
    font-weight:600;
    margin-bottom:18px;
    letter-spacing:1px;
}

.footer-velaris .brand{
    font-family:'Cinzel',serif;
    color:#d4af37;
    font-size:1.5rem;
    letter-spacing:3px;
    font-weight:600;
}

.footer-velaris a{
    color:#ddd;
    text-decoration:none;
    display:block;
    margin-bottom:6px;
}

.footer-velaris a:hover{
    color:#d4af37;
}

/* DIVIDER */
.footer-divider{
    border-top:1px solid rgba(255,255,255,.15);
    margin:35px 0 25px;
    position:relative;
}

.footer-divider::after{
    content:"❖";
    color:#d4af37;
    background:#1b1b1b;
    padding:0 12px;
    position:absolute;
    left:50%;
    top:-12px;
    transform:translateX(-50%);
}

.footer-social a{
    display:inline-block;
    font-size:1.2rem;
    margin-right:12px;
}

.footer-bottom{
    text-align:center;
    color:#aaa;
    font-size:.8rem;
}
</style>

<footer class="footer-velaris">
    <div class="container">
        <div class="row">

            <div class="col-md-3 mb-4">
                <div class="brand mb-3">VELARIS HOTEL</div>
            </div>

            <div class="col-md-3 mb-4">
                <h6>VELARIS HOTEL - INDONESIA</h6>
                <p class="mb-1">Jl. Slamet Riyadi No.233</p>
                <p class="mb-1">Purwosari, Kec. Laweyan</p>
                <p>Surakarta, Jawa Tengah 57141</p>
            </div>

            <div class="col-md-3 mb-4">
                <h6>CONTACT</h6>
                <p class="mb-1">+62 361 846 4618 (hunting)</p>
                <p class="mb-1">+62 361 846 4718 (fax)</p>

                <h6 class="mt-3">FIND US ON</h6>
                <div class="footer-social">
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-whatsapp"></i></a>
                    <a href="#"><i class="fab fa-facebook"></i></a>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <h6>MENU</h6>
                <a href="index.php">Home</a>
                <a href="rooms.php">Rooms</a>
                <a href="experience.php">Experience</a>
                <a href="contact.php">Contact</a>
            </div>

        </div>

        <div class="footer-divider"></div>

        <div class="footer-bottom">
            © <?= date('Y') ?> Velaris Hotel. All rights reserved.
        </div>
    </div>
</footer>

<!-- Font Awesome -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
