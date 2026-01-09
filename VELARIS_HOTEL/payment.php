<?php
session_start();

/* WAJIB LOGIN */
if (!isset($_SESSION['customer_id'])) {
    header("Location: auth/login.php");
    exit;
}

/* DATA DARI RESERVASI */
$id_kamar    = $_POST['id_kamar']    ?? '';
$checkin     = $_POST['checkin']     ?? '';
$checkout    = $_POST['checkout']    ?? '';
$total_harga = $_POST['total_harga'] ?? '';

if (!$id_kamar || !$checkin || !$checkout || !$total_harga) {
    header("Location: booking.php");
    exit;
}

/* DATA USER */
$nama       = $_SESSION['customer_name']  ?? 'Guest';
$emailcust  = $_SESSION['customer_email'] ?? '-';

$kode_booking = $_POST['kode_booking'] ?? '';

if (!$kode_booking) {
    header("Location: booking.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Payment | Velaris Hotel</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600&family=Inter&display=swap" rel="stylesheet">

<style>
body{
    margin:0;
    font-family:'Inter',sans-serif;
    min-height:100vh;
    position:relative;
}
body::before{
    content:"";
    position:fixed;
    inset:0;
    background:url('uploads/experiences/pool.jpg') center/cover no-repeat;
    filter:blur(14px);
    transform:scale(1.1);
    z-index:-2;
}
body::after{
    content:"";
    position:fixed;
    inset:0;
    background:rgba(0,0,0,.25);
    z-index:-1;
}

/* HEADER */
.header{
    background:#fff;
    padding:20px 40px;
    border-bottom:1px solid #ddd;
    display:flex;
    justify-content:space-between;
    align-items:center;
}
.header h2{
    font-family:'Cinzel',serif;
    margin:0;
}
.back-btn{
    text-decoration:none;
    color:#555;
    font-size:.85rem;
}

/* CONTAINER */
.payment-container{
    max-width:1100px;
    margin:60px auto;
    display:grid;
    grid-template-columns:1.2fr .8fr;
    gap:40px;
    padding:0 20px;
}

/* CARD */
.card{
    background:#fff;
    border-radius:18px;
    padding:28px;
    box-shadow:0 20px 50px rgba(0,0,0,.2);
}

/* SUMMARY */
.summary table{
    width:100%;
    border-collapse:collapse;
}
.summary td{
    padding:12px 0;
}
.summary .total{
    border-top:1px solid #eee;
    font-weight:600;
    font-size:1.05rem;
}
.booking-code{
    margin:15px 0;
    padding:12px;
    border:1px dashed #d4af37;
    border-radius:12px;
    font-weight:600;
    text-align:center;
    color:#8a6d1d;
}

/* PAYMENT METHOD */
.method{
    display:flex;
    align-items:center;
    gap:10px;
    padding:14px;
    border:1px solid #ddd;
    border-radius:12px;
    margin-bottom:14px;
    cursor:pointer;
}
.method input{
    accent-color:#d4af37;
}

.payment-extra{
    margin:15px 0;
}
.payment-extra input{
    width:100%;
    padding:12px;
    border-radius:10px;
    border:1px solid #ccc;
}

/* BUTTON */
.pay-btn{
    width:100%;
    padding:15px;
    background:#d4af37;
    border:none;
    border-radius:30px;
    font-weight:600;
    cursor:pointer;
}

/* ALERT */
.alert-overlay{
    position:fixed;
    inset:0;
    background:rgba(0,0,0,.45);
    display:none;
    align-items:center;
    justify-content:center;
    z-index:999;
}
.alert-box{
    background:#fff;
    padding:28px;
    border-radius:20px;
    max-width:420px;
    text-align:center;
}
.alert-actions{
    margin-top:20px;
    display:flex;
    gap:15px;
    justify-content:center;
}
.btn-ok{
    background:#d4af37;
    border:none;
    padding:10px 24px;
    border-radius:25px;
}
.btn-cancel{
    background:#eee;
    border:none;
    padding:10px 24px;
    border-radius:25px;
}

/* FOOTER */
.footer{
    text-align:center;
    padding:30px;
    color:#eee;
    font-size:.8rem;
}

.btn-back{
    width:100%;
    margin-top:14px;
    padding:13px;
    background:#fff;
    border:1px solid #ddd;
    border-radius:30px;
    font-size:.8rem;
    letter-spacing:1px;
    cursor:pointer;
    transition:.3s;
}

.btn-back:hover{
    background:#000;
    color:#fff;
}

</style>
</head>

<body>

<header class="header">
    <h2>VELARIS HOTEL ★★★★</h2>
    <p>Secure Payment</p>
</header>

<section class="payment-container">

    <!-- SUMMARY -->
    <div class="card summary">
        <h3>Booking Summary</h3>

        <div class="booking-code">
    Booking Code<br>
    <?= htmlspecialchars($kode_booking) ?>
</div>


        <table>
            <tr><td>Guest Name</td><td><?= htmlspecialchars($nama) ?></td></tr>
            <tr><td>Email</td><td><?= htmlspecialchars($emailcust) ?></td></tr>
            <tr><td>Check-in</td><td><?= date('d M Y', strtotime($checkin)) ?></td></tr>
            <tr><td>Check-out</td><td><?= date('d M Y', strtotime($checkout)) ?></td></tr>
            <tr class="total">
                <td>Total Payment</td>
                <td>IDR <?= number_format($total_harga,0,',','.') ?></td>
            </tr>
        </table>
    </div>

    <!-- PAYMENT -->
    <div class="card">
        <h3>Select Payment Method</h3>

        <form id="paymentForm" action="payment_process.php" method="POST" enctype="multipart/form-data" onsubmit="return validatePayment();">

            <input type="hidden" name="id_kamar" value="<?= $id_kamar ?>">
            <input type="hidden" name="checkin" value="<?= $checkin ?>">
            <input type="hidden" name="checkout" value="<?= $checkout ?>">
            <input type="hidden" name="total_harga" value="<?= $total_harga ?>">
            <input type="hidden" name="kode_booking" value="<?= $kode_booking ?>">


            <label class="method">
                <input type="radio" name="payment_method" value="credit_card" onclick="showPaymentForm()">
                Credit Card
            </label>

            <label class="method">
                <input type="radio" name="payment_method" value="bank_transfer" onclick="showPaymentForm()">
                Bank Transfer
            </label>

            <div id="creditCardForm" class="payment-extra" style="display:none;">
                <label>Card Number</label>
                <input type="text" id="card_number" name="card_number" placeholder="XXXX XXXX XXXX XXXX">
            </div>

            <div id="bankTransferForm" class="payment-extra" style="display:none;">
                <label>Upload Payment Proof</label>
                <input type="file" id="payment_proof" name="payment_proof" accept="image/*">
            </div>

            <button class="pay-btn">Pay Now</button>
            <!-- BACK -->
    <button type="button" class="btn-back" onclick="history.back()">
        ← Back to Reservation
    </button>
        </form>
    </div>

</section>

<!-- CUSTOM ALERT -->
<div id="customAlert" class="alert-overlay">
    <div class="alert-box">
        <h3>Velaris Hotel</h3>
        <p id="alertMessage"></p>
        <div class="alert-actions">
            <button class="btn-cancel" onclick="closeAlert()">Cancel</button>
            <button class="btn-ok" onclick="confirmAlert()">Confirm</button>
        </div>
    </div>
</div>

<footer class="footer">
    &copy; <?= date('Y') ?> Velaris Hotel
</footer>

<script>
let confirmCallback = null;

function showPaymentForm(){
    const method = document.querySelector('input[name="payment_method"]:checked')?.value;
    document.getElementById('creditCardForm').style.display = method === 'credit_card' ? 'block' : 'none';
    document.getElementById('bankTransferForm').style.display = method === 'bank_transfer' ? 'block' : 'none';
}

function showAlert(message, callback = null){
    document.getElementById("alertMessage").innerText = message;
    document.getElementById("customAlert").style.display = "flex";
    confirmCallback = callback;
}

function closeAlert(){
    document.getElementById("customAlert").style.display = "none";
    confirmCallback = null;
}

function confirmAlert(){
    if(confirmCallback) confirmCallback();
    closeAlert();
}

function validatePayment(){
    const method = document.querySelector('input[name="payment_method"]:checked');

    if(!method){
        showAlert("Please select a payment method.");
        return false;
    }

    if(method.value === "credit_card" && document.getElementById("card_number").value.trim() === ""){
        showAlert("Card number is required.");
        return false;
    }

    if(method.value === "bank_transfer" && document.getElementById("payment_proof").value === ""){
        showAlert("Please upload payment proof.");
        return false;
    }

    showAlert(
        "Confirm payment of IDR <?= number_format($total_harga,0,',','.') ?> ?",
        () => document.getElementById("paymentForm").submit()
    );

    return false;
}
</script>

</body>
</html>
