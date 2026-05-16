<?php
require_once 'config.php';
require_once 'includes/auth.php';
require_login();

$page_title = 'Reports';
$conn = db_connect();

$date_from = $_GET['date_from'] ?? date('Y-m-01');
$date_to   = $_GET['date_to']   ?? date('Y-m-t');
$report    = $_GET['report']    ?? 'revenue';

$revenue_data = mysqli_query($conn,
    "SELECT DATE(created_at) AS day, SUM(total_price) AS revenue, COUNT(*) AS bookings
     FROM bookings
     WHERE DATE(created_at) BETWEEN '$date_from' AND '$date_to'
     AND payment_status = 'paid'
     GROUP BY DATE(created_at)
     ORDER BY day"
);

$occupancy_data = mysqli_query($conn,
    "SELECT r.room_type, COUNT(b.id) AS bookings,
            SUM(DATEDIFF(b.check_out, b.check_in)) AS total_nights
     FROM rooms r
     LEFT JOIN bookings b ON r.id = b.room_id
         AND b.check_in BETWEEN '$date_from' AND '$date_to'
     GROUP BY r.room_type"
);

$guest_data = mysqli_query($conn,
    "SELECT nationality, COUNT(*) AS guests
     FROM guests
     WHERE DATE(created_at) BETWEEN '$date_from' AND '$date_to'
     GROUP BY nationality ORDER BY guests DESC LIMIT 10"
);

if (isset($_GET['export'])) {
    $format = $_GET['format'] ?? 'csv';
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="report_' . $_GET['format'] . '_' . date('Ymd') . '.csv"');
    $export_q = mysqli_query($conn,
        "SELECT b.booking_ref, g.first_name, g.last_name, r.room_number,
                b.check_in, b.check_out, b.total_price, b.status
         FROM bookings b
         JOIN guests g ON b.guest_id = g.id
         JOIN rooms  r ON b.room_id  = r.id
         WHERE DATE(b.created_at) BETWEEN '$date_from' AND '$date_to'"
    );
    echo "Ref,First Name,Last Name,Room,Check-in,Check-out,Total,Status\n";
    while ($row = mysqli_fetch_assoc($export_q)) {
        echo implode(',', array_values($row)) . "\n";
    }
    exit;
}

require_once 'includes/header.php';
?>

<h5 class="mb-4 fw-bold"><i class="fas fa-chart-bar me-2"></i>Reports</h5>
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-sm-3">
                <label class="form-label">From Date</label>
                <input type="date" name="date_from" class="form-control" value="<?php echo $date_from; ?>">
            </div>
            <div class="col-sm-3">
                <label class="form-label">To Date</label>
                <input type="date" name="date_to" class="form-control" value="<?php echo $date_to; ?>">
            </div>
            <div class="col-sm-3">
                <label class="form-label">Report Type</label>
                <select name="report" class="form-select">
                    <option value="revenue"   <?php echo $report==='revenue'   ?'selected':''; ?>>Revenue</option>
                    <option value="occupancy" <?php echo $report==='occupancy' ?'selected':''; ?>>Occupancy</option>
                    <option value="guests"    <?php echo $report==='guests'    ?'selected':''; ?>>Guests</option>
                </select>
            </div>
            <div class="col-sm-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill">Generate</button>
                <a href="?date_from=<?php echo $date_from; ?>&date_to=<?php echo $date_to; ?>&export=1&format=csv"
                   class="btn btn-outline-success">Export CSV</a>
            </div>
        </form>
        <small class="text-muted mt-2 d-block">
            Showing report for: <?php echo $date_from; ?> to <?php echo $date_to; ?>
        </small>
    </div>
</div>

<div class="row g-4">
    <!-- Revenue Table -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><i class="fas fa-dollar-sign me-2"></i>Daily Revenue</div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Date</th><th>Bookings</th><th>Revenue</th></tr></thead>
                    <tbody>
                    <?php
                    $total_rev = 0;
                    while ($r = mysqli_fetch_assoc($revenue_data)):
                        $total_rev += $r['revenue'];
                    ?>
                    <tr>
                        <td><?php echo $r['day']; ?></td>
                        <td><?php echo $r['bookings']; ?></td>
                        <td><strong>$<?php echo number_format($r['revenue'],2); ?></strong></td>
                    </tr>
                    <?php endwhile; ?>
                    <tr class="table-active fw-bold">
                        <td colspan="2">TOTAL</td>
                        <td>$<?php echo number_format($total_rev,2); ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Occupancy -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><i class="fas fa-bed me-2"></i>Occupancy by Room Type</div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Room Type</th><th>Bookings</th><th>Total Nights</th></tr></thead>
                    <tbody>
                    <?php while ($o = mysqli_fetch_assoc($occupancy_data)): ?>
                    <tr>
                        <td><?php echo ucfirst($o['room_type']); ?></td>
                        <td><?php echo $o['bookings']; ?></td>
                        <td><?php echo $o['total_nights'] ?? 0; ?></td>
                    </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Top Guest Nationalities -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><i class="fas fa-globe me-2"></i>Top Guest Nationalities</div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Nationality</th><th>Guests</th></tr></thead>
                    <tbody>
                    <?php while ($gn = mysqli_fetch_assoc($guest_data)): ?>
                    <tr>
                        <td><?php echo $gn['nationality']; ?></td>
                        <td><?php echo $gn['guests']; ?></td>
                    </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php if (DEBUG_MODE): ?>
    <div class="col-12">
        <div class="alert alert-warning">
            <strong>Debug — Last SQL:</strong><br>
            <code>
            SELECT DATE(created_at) AS day, SUM(total_price) AS revenue, COUNT(*) AS bookings
            FROM bookings WHERE DATE(created_at) BETWEEN '<?php echo $date_from; ?>' AND '<?php echo $date_to; ?>'
            </code>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php
mysqli_close($conn);
require_once 'includes/footer.php';
?>
