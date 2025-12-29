<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Pembayaran Gagal | Velaris Hotel</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

<style>
*{
    box-sizing:border-box;
    font-family:'Inter',sans-serif;
}

body{
    margin:0;
    min-height:100vh;

    /* BACKGROUND FOTO + DARK OVERLAY */
    background: linear-gradient(rgba(0,0,0,.55), rgba(0,0,0,.55)), url('uploads/experiences/pool.jpg') center/cover no-repeat;
    display:flex;
    align-items:center;
    justify-content:center;
}

/* CARD */
.error-card{
    background:rgba(255,255,255,.96);
    backdrop-filter: blur(6px);
    border-radius:18px;
    padding:42px 36px;
    width:100%;
    max-width:540px;
    text-align:center;
    box-shadow:0 20px 50px rgba(0,0,0,.3);
}

/* ICON */
.error-icon{
    width:64px;
    height:64px;
    background:#e74c3c;
    border-radius:16px;
    display:flex;
    align-items:center;
    justify-content:center;
    color:#fff;
    font-size:34px;
    margin:0 auto 20px;
}

/* TEXT */
.error-card h1{
    margin:10px 0 12px;
    font-size:26px;
}

.error-card p{
    color:#444;
    line-height:1.6;
    font-size:.95rem;
}

/* LIST */
.error-list{
    margin:16px 0 6px;
    padding:0;
    list-style:none;
    font-size:.9rem;
    color:#555;
}

.error-list li{
    margin-bottom:6px;
}

/* BUTTON */
.error-card a{
    display:inline-block;
    margin-top:26px;
    padding:14px 36px;
    background:#e74c3c;
    color:#fff;
    text-decoration:none;
    border-radius:30px;
    font-weight:600;
    transition:.25s ease;
}

.error-card a:hover{
    background:#c0392b;
}
</style>
</head>

<body>

<div class="error-card">

    <div class="error-icon">✕</div>

    <h1>Payment Failed</h1>

    <p>
        Sorry, your payment could not be processed at this time.
    </p>

    <ul class="error-list">
        <li>• Please make sure the card number is entered correctly</li>
        <li>• Please ensure the transfer receipt has been uploaded</li>
    </ul>

    <p style="font-size:.85rem;color:#777;">
        Please check your information and try the payment process again.
    </p>

    <a href="booking.php">
        Try Again
    </a>

</div>

</body>
</html>
