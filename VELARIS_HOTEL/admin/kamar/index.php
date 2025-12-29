<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';

require_staff();

$page_title = 'Rooms';
require_once '../includes/header.php';

$rooms = fetch_all("SELECT * FROM kamar ORDER BY id_kamar DESC");
?>

<!-- hero kek di figma-->
<section style="
    height:100vh;
    position:relative;
">
    <img src="/uaspemweb/VELARIS_HOTEL/uploads/kamar/executivesuite.jpg"
         style="
            width:100%;
            height:100%;
            object-fit:cover;
         ">
</section>

<style>
/*  ROOMS GRID STYLE  */
.rooms-wrapper{
    max-width:1200px;
    margin:auto;
}

.room-card{
    background:#fff;
    border-radius:16px;
    overflow:hidden;
    box-shadow:0 8px 30px rgba(0,0,0,.06);
    transition:.3s;
    height:100%;
}

.room-card:hover{
    transform:translateY(-6px);
    box-shadow:0 14px 40px rgba(0,0,0,.1);
}

.room-image{
    height:260px;
    overflow:hidden;
}

.room-image img{
    width:100%;
    height:100%;
    object-fit:cover;
}

.room-body{
    padding:18px 20px 22px;
}

.room-title{
    font-family:'Cinzel', serif;
    font-size:1.25rem;
    font-weight:600;
    color:#d4af37;
    margin-bottom:8px;
}

.room-desc{
    font-size:.9rem;
    color:#555;
    line-height:1.6;
    min-height:48px;
}

.room-price{
    font-weight:600;
    margin:12px 0;
}

.room-meta{
    display:flex;
    gap:8px;
    flex-wrap:wrap;
    margin-bottom:14px;
}

.badge-room{
    font-size:.7rem;
    padding:6px 14px;
    border-radius:20px;
    background:#f0f0f0;
}

.room-actions{
    display:flex;
    gap:10px;
}

.room-actions a,
.room-actions button{
    font-size:.75rem;
    padding:6px 14px;
    border-radius:20px;
    border:1px solid #d4af37;
    background:#fff;
    color:#d4af37;
    text-decoration:none;
    transition:.2s;
}

.room-actions a:hover,
.room-actions button:hover{
    background:#d4af37;
    color:#000;
}


</style>

<div class="rooms-wrapper mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Rooms</h3>
        <a href="create.php" class="btn btn-dark btn-sm">+ Add New Room</a>
    </div>

    <div class="row g-4">
        <?php foreach($rooms as $r): ?>
        <div class="col-md-6 col-lg-6">
            <div class="room-card">

                <!-- IMAGE -->
                <div class="room-image">
                    <?php if($r['foto_kamar']): ?>
                        <img src="../../uploads/kamar/<?= $r['foto_kamar']; ?>">
                    <?php else: ?>
                        <img src="https://via.placeholder.com/600x400?text=No+Image">
                    <?php endif; ?>
                </div>

                <!-- BODY -->
                <div class="room-body">
                    <div class="room-title">
                        <?= htmlspecialchars($r['nama_kamar']); ?>
                    </div>

                    <div class="room-desc">
                        <?= substr(strip_tags($r['deskripsi'] ?? 'Luxury room with premium facilities.'),0,120); ?>...
                    </div>

                    <div class="room-price">
                        <?= format_rupiah($r['harga']); ?> / night
                    </div>

                    <div class="room-meta">
                        <span class="badge-room"><?= htmlspecialchars($r['tipe_kamar']); ?></span>
                        <span class="badge-room"><?= $r['stok']; ?> units</span>
                    </div>

                    <div class="room-actions">
                        <a href="edit.php?id=<?= $r['id_kamar']; ?>">Edit</a>
                        <button onclick="deleteRoom(<?= $r['id_kamar']; ?>)">Delete</button>
                    </div>
                </div>

            </div>
        </div>
        <?php endforeach; ?>
    </div>

</div>

<script>
function deleteRoom(id){
    if(confirm('Delete this room?')){
        fetch('delete.php',{
            method:'POST',
            headers:{'Content-Type':'application/x-www-form-urlencoded'},
            body:'id='+id
        })
        .then(res=>res.json())
        .then(res=>{
            alert(res.message);
            if(res.success) location.reload();
        });
    }
}
</script>

<?php require_once '../includes/footer.php'; ?>