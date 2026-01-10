<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/functions.php';

require_staff();

$page_title = 'Cancellation Requests';

/* DATA */
$cancellations = fetch_all("
    SELECT 
        p.*,
        r.tgl_checkin,
        r.kode_booking,
        r.total_harga,
        u.nama_lengkap,
        u.email,
        k.nama_kamar
    FROM pembatalan p
    JOIN reservasi r ON p.id_reservasi = r.id_reservasi
    JOIN users u ON r.id_user = u.id_user
    JOIN kamar k ON r.id_kamar = k.id_kamar
    ORDER BY p.tgl_pengajuan DESC
");

$pending_count = 0;
$approved_count = 0;
$rejected_count = 0;

foreach ($cancellations as $c) {
    if ($c['status_pengajuan'] === 'pending') $pending_count++;
    if ($c['status_pengajuan'] === 'disetujui') $approved_count++;
    if ($c['status_pengajuan'] === 'ditolak') $rejected_count++;
}

require_once __DIR__ . '/../includes/header.php';
?>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<style>
.page-wrapper{
    padding:160px 20px 120px;
    background:linear-gradient(180deg,#ffffff 0%,#f6f6f6 100%);
}

.lux-container{
    max-width:1400px;
    margin:auto;
}

.lux-card{
    background:#fff;
    border-radius:26px;
    box-shadow:0 25px 60px rgba(0,0,0,.08);
    border:0;
}

.lux-header{
    padding:34px 40px;
    font-family:'Cinzel',serif;
    font-size:1.8rem;
    letter-spacing:2px;
    border-bottom:1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.lux-body{
    padding:40px;
}

/* STAT BOXES */
.stat-box{
    background:#fff;
    border-radius:22px;
    padding:32px;
    text-align:center;
    box-shadow:0 20px 45px rgba(0,0,0,.08);
    transition: transform 0.3s ease;
}

.stat-box:hover{
    transform: translateY(-5px);
}

.stat-box i{
    font-size:42px;
    margin-bottom: 12px;
}

.stat-box.pending i{
    color: #ffc107;
}

.stat-box.approved i{
    color: #28a745;
}

.stat-box.rejected i{
    color: #dc3545;
}

.stat-box h2{
    font-size: 2.5rem;
    font-weight: 700;
    margin: 10px 0;
}

.stat-box p{
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #888;
}

/* TABLE */
.lux-table thead th{
    text-transform:uppercase;
    font-size:.75rem;
    letter-spacing:1px;
    border-bottom:2px solid #eee;
    padding: 16px 12px;
    font-weight: 600;
    color: #555;
    background: #f8f9fa;
}

.lux-table td{
    vertical-align:middle;
    padding: 16px 12px;
    font-size: 0.9rem;
}

.lux-table tbody tr{
    transition: background 0.2s ease;
}

.lux-table tbody tr:hover{
    background: #f8f9fa;
}

/* BADGES */
.badge{
    padding:8px 16px;
    border-radius:50px;
    font-size:.75rem;
    font-weight: 600;
    letter-spacing: 0.5px;
}

/* BUTTONS */
.btn-action{
    padding: 6px 12px;
    border-radius: 8px;
    margin: 0 2px;
    transition: all 0.2s ease;
}

.btn-action:hover{
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

/* ID STYLING */
.cancellation-id {
    font-weight: 700;
    color: #d4af37;
    font-size: 1rem;
}

/* BOOKING CODE */
.booking-code {
    font-family: 'Courier New', monospace;
    font-weight: 600;
    letter-spacing: 1px;
    color: #333;
}

/* DATATABLES CUSTOM */
.dataTables_wrapper .dataTables_length select,
.dataTables_wrapper .dataTables_filter input {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 6px 12px;
}

.dataTables_wrapper .dataTables_paginate .paginate_button {
    border-radius: 8px;
    margin: 0 2px;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: #d4af37 !important;
    border-color: #d4af37 !important;
    color: white !important;
}

.dataTables_wrapper .dataTables_info {
    padding-top: 1rem;
    font-size: 0.9rem;
    color: #666;
}

/* SWAL CUSTOM */
.swal2-popup {
    border-radius: 20px !important;
    padding: 30px !important;
}

.swal2-title {
    font-family: 'Cinzel', serif;
    font-size: 1.8rem;
}
</style>

<div class="page-wrapper">
    <div class="lux-container">

        <!-- STATISTICS -->
        <div class="row mb-5 g-4">
            <div class="col-md-4">
                <div class="stat-box pending">
                    <i class="fas fa-clock"></i>
                    <h2><?= $pending_count ?></h2>
                    <p class="mb-0">Pending Requests</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-box approved">
                    <i class="fas fa-check-circle"></i>
                    <h2><?= $approved_count ?></h2>
                    <p class="mb-0">Approved</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-box rejected">
                    <i class="fas fa-times-circle"></i>
                    <h2><?= $rejected_count ?></h2>
                    <p class="mb-0">Rejected</p>
                </div>
            </div>
        </div>

        <!-- MAIN CARD -->
        <div class="lux-card">
            <div class="lux-header">
                <span>Cancellation Requests</span>
                <span class="badge bg-primary"><?= count($cancellations) ?> Total</span>
            </div>

            <div class="lux-body">
                <div class="table-responsive">
                    <table id="cancellationsTable" class="table lux-table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Reservation</th>
                                <th>Guest</th>
                                <th>Room</th>
                                <th>Check-in</th>
                                <th>Total Amount</th>
                                <th>Requested Date</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($cancellations as $c): ?>
                            <tr>
                                <td class="cancellation-id">
                                    #<?= str_pad($c['id_batal'], 4, '0', STR_PAD_LEFT) ?>
                                </td>
                                <td>
                                    <span class="booking-code"><?= htmlspecialchars($c['kode_booking']) ?></span>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($c['nama_lengkap']) ?></strong><br>
                                    <small class="text-muted"><?= htmlspecialchars($c['email']) ?></small>
                                </td>
                                <td><?= htmlspecialchars($c['nama_kamar']) ?></td>
                                <td><?= format_tanggal($c['tgl_checkin'], 'd M Y') ?></td>
                                <td><strong><?= format_rupiah($c['total_harga']) ?></strong></td>
                                <td><?= format_tanggal($c['tgl_pengajuan'], 'd M Y H:i') ?></td>
                                <td>
                                    <span class="badge bg-<?=
                                        $c['status_pengajuan']=='pending'?'warning':
                                        ($c['status_pengajuan']=='disetujui'?'success':'danger')
                                    ?>">
                                        <?= ucfirst($c['status_pengajuan']) ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <!-- VIEW DETAIL -->
                                    <a href="detail.php?id=<?= $c['id_batal'] ?>"
                                       class="btn btn-sm btn-outline-dark btn-action"
                                       title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <?php if ($c['status_pengajuan'] === 'pending'): ?>

                                    <button class="btn btn-sm btn-success btn-action btn-approve"
                                            data-id="<?= $c['id_batal'] ?>"
                                            data-guest="<?= htmlspecialchars($c['nama_lengkap']) ?>"
                                            data-booking="<?= htmlspecialchars($c['kode_booking']) ?>"
                                            title="Approve">
                                        <i class="fas fa-check"></i>
                                    </button>

                                    <button class="btn btn-sm btn-danger btn-action btn-reject"
                                            data-id="<?= $c['id_batal'] ?>"
                                            data-guest="<?= htmlspecialchars($c['nama_lengkap']) ?>"
                                            data-booking="<?= htmlspecialchars($c['kode_booking']) ?>"
                                            title="Reject">
                                        <i class="fas fa-times"></i>
                                    </button>

                                    <?php endif; ?>

                                </td>
                            </tr>
                        <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    $('#cancellationsTable').DataTable({
        responsive: true,
        order: [[0, 'desc']],
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        language: {
            search: "Search:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "Showing 0 to 0 of 0 entries",
            infoFiltered: "(filtered from _MAX_ total entries)",
            zeroRecords: "No matching records found",
            emptyTable: "No data available in table",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        },
        columnDefs: [
            { orderable: false, targets: -1 } // Disable sorting on Actions column
        ]
    });
});
</script>

<script>
// APPROVE
$(document).on('click', '.btn-approve', function () {
    const id = $(this).data('id');
    const guest = $(this).data('guest');
    const booking = $(this).data('booking');

    Swal.fire({
        title: 'Approve Cancellation?',
        html: `
            <p>You are about to approve the cancellation request for:</p>
            <strong>Guest:</strong> ${guest}<br>
            <strong>Booking Code:</strong> ${booking}
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Approve',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Processing...',
                text: 'Please wait',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: 'approve.php',
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function (res) {
                    if (res.success) {
                        Swal.fire({
                            title: 'Approved!',
                            text: res.message || 'Cancellation request has been approved',
                            icon: 'success',
                            confirmButtonColor: '#28a745'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Failed!',
                            text: res.message || 'Failed to approve cancellation',
                            icon: 'error',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                },
                error: function () {
                    Swal.fire({
                        title: 'Error!',
                        text: 'An error occurred while processing your request',
                        icon: 'error',
                        confirmButtonColor: '#dc3545'
                    });
                }
            });
        }
    });
});

// REJECT
$(document).on('click', '.btn-reject', function () {
    const id = $(this).data('id');
    const guest = $(this).data('guest');
    const booking = $(this).data('booking');

    Swal.fire({
        title: 'Reject Cancellation',
        html: `
            <p>You are about to reject the cancellation request for:</p>
            <strong>Guest:</strong> ${guest}<br>
            <strong>Booking Code:</strong> ${booking}<br><br>
            <label for="reject-reason" style="display:block;text-align:left;margin-bottom:8px;font-weight:600;">Reason for Rejection:</label>
        `,
        input: 'textarea',
        inputPlaceholder: 'Enter the reason for rejection...',
        inputAttributes: {
            id: 'reject-reason',
            'aria-label': 'Rejection reason',
            style: 'min-height: 100px;'
        },
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Reject',
        cancelButtonText: 'Cancel',
        inputValidator: (value) => {
            if (!value || value.trim() === '') {
                return 'Please provide a reason for rejection!'
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const reason = result.value;

            // Show loading
            Swal.fire({
                title: 'Processing...',
                text: 'Please wait',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: 'reject.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    id: id,
                    catatan_admin: reason
                },
                success: function (res) {
                    if (res.success) {
                        Swal.fire({
                            title: 'Rejected!',
                            text: res.message || 'Cancellation request has been rejected',
                            icon: 'success',
                            confirmButtonColor: '#28a745'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Failed!',
                            text: res.message || 'Failed to reject cancellation',
                            icon: 'error',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                },
                error: function () {
                    Swal.fire({
                        title: 'Error!',
                        text: 'An error occurred while processing your request',
                        icon: 'error',
                        confirmButtonColor: '#dc3545'
                    });
                }
            });
        }
    });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>