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
$nama  = $_SESSION['customer_name'] ?? 'Guest';
$email = $_SESSION['customer_email'] ?? '-';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Payment | Velaris Hotel</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600&family=Inter&display=swap" rel="stylesheet">

<style>
/* BACKGROUND BLUR */
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
}
.header h2{
    font-family:'Cinzel',serif;
    margin:0;
}
.header p{
    margin:4px 0 0;
    color:#666;
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
    font-size:.95rem;
    cursor:pointer;
}

/* FOOTER  */
.footer{
    text-align:center;
    padding:30px;
    color:#eee;
    font-size:.8rem;
}
</style>
</head>

<body>

<!-- HEADER -->
<header class="header">
    <h2>VELARIS HOTEL ★★★★</h2>
    <p>Secure Payment</p>
</header>

<!-- CONTENT -->
<section class="payment-container">

    <!-- LEFT : SUMMARY -->
    <div class="card summary">
        <h3>Booking Summary</h3>
        <table>
            <tr>
                <td>Guest Name</td>
                <td><?= htmlspecialchars($nama) ?></td>
            </tr>
            <tr>
                <td>Email</td>
                <td><?= htmlspecialchars($email) ?></td>
            </tr>
            <tr>
                <td>Check-in</td>
                <td><?= date('d M Y', strtotime($checkin)) ?></td>
            </tr>
            <tr>
                <td>Check-out</td>
                <td><?= date('d M Y', strtotime($checkout)) ?></td>
            </tr>
            <tr class="total">
                <td>Total Payment</td>
                <td>IDR <?= number_format($total_harga,0,',','.') ?></td>
            </tr>
        </table>
    </div>

    <!-- RIGHT : PAYMENT -->
    <div class="card">
        <h3>Select Payment Method</h3>

        <form action="payment_process.php" method="POST" enctype="multipart/form-data">

            <input type="hidden" name="id_kamar" value="<?= $id_kamar ?>">
            <input type="hidden" name="checkin" value="<?= $checkin ?>">
            <input type="hidden" name="checkout" value="<?= $checkout ?>">
            <input type="hidden" name="total_harga" value="<?= $total_harga ?>">

            <label class="method">
                <input type="radio" name="payment_method" value="credit_card" onclick="showPaymentForm()" required>
                Credit Card
            </label>

            <label class="method">
                <input type="radio" name="payment_method" value="bank_transfer" onclick="showPaymentForm()">
                Bank Transfer
            </label>

            <div id="creditCardForm" class="payment-extra" style="display:none;">
                <label>Card Number</label>
                <input type="text" name="card_number" placeholder="XXXX XXXX XXXX XXXX">
            </div>

            <div id="bankTransferForm" class="payment-extra" style="display:none;">
                <label>Upload Payment Proof</label>
                <input type="file" name="payment_proof" accept="image/*">
            </div>

            <button class="pay-btn">Pay Now</button>
        </form>
    </div>

</section>

<footer class="footer">
    &copy; <?= date('Y') ?> Velaris Hotel
</footer>

<script>
function showPaymentForm(){
    const method = document.querySelector('input[name="payment_method"]:checked').value;
    document.getElementById('creditCardForm').style.display =
        method === 'credit_card' ? 'block' : 'none';
    document.getElementById('bankTransferForm').style.display =
        method === 'bank_transfer' ? 'block' : 'none';
}
</script>

</body>
</html>
