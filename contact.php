<?php
session_start();
require_once "config/database.php";
require_once "config/functions.php";

$page_title = 'Contact';
require_once "components/header.php";
?>

<!-- HERO -->
<section style="height:100vh; position:relative;">
    <img src="uploads/contactus.jpg"
         alt="Contact Velaris Hotel"
         style="width:100%; height:100%; object-fit:cover;">
</section>

<style>
/* ===== CONTACT PAGE ===== */
.contact-section{
    padding:100px 20px 80px;
    background:#fff;
}

.contact-title{
    text-align:center;
    font-family:'Cinzel',serif;
    font-size:2.6rem;
    letter-spacing:2px;
    margin-bottom:80px;
}

/* GRID */
.contact-wrap{
    max-width:1200px;
    margin:auto;
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:80px;
}

/* LEFT */
.contact-info h4{
    font-family:'Cinzel',serif;
    font-size:1.4rem;
    margin-bottom:28px;
}

.contact-info p{
    font-size:.95rem;
    line-height:1.7;
    color:#444;
    margin-bottom:14px;
}

/* MAP */
.contact-map{
    margin-top:30px;
    border-radius:8px;
    overflow:hidden;
}
.contact-map iframe{
    width:100%;
    height:260px;
    border:0;
}

/* RIGHT FORM */
.contact-form label{
    font-size:.75rem;
    letter-spacing:1px;
    margin-bottom:6px;
    display:block;
}

.contact-form input,
.contact-form textarea{
    width:100%;
    border:none;
    border-bottom:1px solid #ccc;
    padding:8px 4px;
    margin-bottom:26px;
    font-size:.9rem;
}

.contact-form textarea{
    min-height:100px;
    resize:none;
}

.contact-form button{
    border:1px solid #000;
    background:none;
    padding:8px 26px;
    font-size:.75rem;
    letter-spacing:1px;
}
.contact-form button:hover{
    background:#000;
    color:#fff;
}

/* RESPONSIVE */
@media(max-width:992px){
    .contact-wrap{
        grid-template-columns:1fr;
        gap:50px;
    }
}
</style>

<section class="contact-section">

    <h2 class="contact-title">CONTACT</h2>

    <div class="contact-wrap">

        <!-- LEFT -->
        <div class="contact-info">
            <h4>GET IN TOUCH</h4>

            <p><strong>VELARIS HOTEL – SOLO</strong></p>

            <p>
                Jl. Slamet Riyadi No.233 Purwosari, Kec. Laweyan, Surakarta, Jawa Tengah 57141
            </p>

            <p>
                +62 361 846 4618 (hunting)<br>
                +62 361 846 4718 (fax)<br>
                +62 831 3714 8108 (WhatsApp)
            </p>

            <div class="contact-map">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3955.151348705097!2d110.77188207500271!3d-7.558471592455391!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a14450edd210d%3A0xa6ea1f9494841e84!2sFKIP%20JPTK%20UNS%20Kampus%20V!5e0!3m2!1sen!2sid!4v1766893524239!5m2!1sen!2sid"
                    loading="lazy">
                </iframe>
            </div>
        </div>

        <!-- RIGHT -->
        <div class="contact-form">
            <form method="post" action="">
                <label>NAME</label>
                <input type="text" name="name" placeholder="your name">

                <label>E-MAIL</label>
                <input type="email" name="email" placeholder="your e-mail address">

                <label>REASON FOR CONTACT</label>
                <input type="text" name="reason" placeholder="e.g. reservation, complaint, partnership">

                <label>MESSAGE</label>
                <textarea name="message" placeholder="write your message here..."></textarea>

                <button type="submit">Send Message</button>
            </form>
        </div>

    </div>

</section>

<?php require_once "components/footer.php"; ?>
