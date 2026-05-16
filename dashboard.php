<?php
require_once 'config.php';
require_once 'includes/auth.php';
require_login();

$page_title = 'Dashboard';
$conn = db_connect();

// Stats queries — raw, no parameterization (not injected here, but pattern is unsafe)
$total_rooms     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM rooms"))['c'];
$available_rooms = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM rooms WHERE status='available'"))['c'];
$occupied_rooms  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM rooms WHERE status='occupied'"))['c'];
$total_guests    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM guests"))['c'];
$total_bookings  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM bookings"))['c'];
$pending_bookings= mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM bookings WHERE status='pending'"))['c'];

$revenue_row = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT SUM(total_price) AS rev FROM bookings WHERE payment_status='paid'"
));
$total_revenue = $revenue_row['rev'] ?? 0;

$search = $_GET['search'] ?? '';
if ($search) {
    $recent_bookings_sql = "SELECT b.*, g.first_name, g.last_name, r.room_number
                            FROM bookings b
                            JOIN guests g ON b.guest_id = g.id
                            JOIN rooms r  ON b.room_id  = r.id
                            WHERE g.first_name LIKE '%$search%'
                               OR g.last_name  LIKE '%$search%'
                               OR b.booking_ref LIKE '%$search%'
                            ORDER BY b.created_at DESC LIMIT 10";
} else {
    $recent_bookings_sql = "SELECT b.*, g.first_name, g.last_name, r.room_number
                            FROM bookings b
                            JOIN guests g ON b.guest_id = g.id
                            JOIN rooms r  ON b.room_id  = r.id
                            ORDER BY b.created_at DESC LIMIT 10";
}
$recent_bookings = mysqli_query($conn, $recent_bookings_sql);

// Today's check-ins and check-outs
$todays_checkins  = mysqli_query($conn, "SELECT b.*, g.first_name, g.last_name, r.room_number FROM bookings b JOIN guests g ON b.guest_id=g.id JOIN rooms r ON b.room_id=r.id WHERE b.check_in = CURDATE()");
$todays_checkouts = mysqli_query($conn, "SELECT b.*, g.first_name, g.last_name, r.room_number FROM bookings b JOIN guests g ON b.guest_id=g.id JOIN rooms r ON b.room_id=r.id WHERE b.check_out = CURDATE()");

require_once 'includes/header.php';
?>

<?php if ($search): ?>
<div class="alert alert-info">
    <i class="fas fa-search me-2"></i>Search results for: <strong><?php echo $search; ?></strong>
</div>
<?php endif; ?>

<!-- Stats Row -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#0f3460,#533483)">
            <div class="d-flex justify-content-between align-items-center">
                <div><div class="text-white-50 small mb-1">Total Rooms</div><h3 class="mb-0"><?php echo $total_rooms; ?></h3></div>
                <i class="fas fa-door-open fa-2x opacity-50"></i>
            </div>
            <div class="mt-2 small text-white-50">
                <?php echo $available_rooms; ?> available &bull; <?php echo $occupied_rooms; ?> occupied
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#198754,#20c997)">
            <div class="d-flex justify-content-between align-items-center">
                <div><div class="text-white-50 small mb-1">Total Guests</div><h3 class="mb-0"><?php echo $total_guests; ?></h3></div>
                <i class="fas fa-users fa-2x opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#0dcaf0,#0d6efd)">
            <div class="d-flex justify-content-between align-items-center">
                <div><div class="text-white-50 small mb-1">Bookings</div><h3 class="mb-0"><?php echo $total_bookings; ?></h3></div>
                <i class="fas fa-calendar-check fa-2x opacity-50"></i>
            </div>
            <div class="mt-2 small text-white-50"><?php echo $pending_bookings; ?> pending</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#e2b96f,#dc3545)">
            <div class="d-flex justify-content-between align-items-center">
                <div><div class="text-white-50 small mb-1">Revenue</div><h3 class="mb-0">$<?php echo number_format($total_revenue,2); ?></h3></div>
                <i class="fas fa-dollar-sign fa-2x opacity-50"></i>
            </div>
        </div>
    </div>
</div>

<!-- Search Bar -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="d-flex gap-2">
            <input type="text" name="search" class="form-control" placeholder="Search bookings by guest name or booking ref..."
                   value="<?php echo $_GET['search'] ?? '';  ?>">
            <button type="submit" class="btn btn-primary px-4"><i class="fas fa-search"></i></button>
            <a href="dashboard.php" class="btn btn-outline-secondary">Clear</a>
        </form>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Bookings -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-calendar-check me-2"></i>Recent Bookings</span>
                <a href="bookings.php" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead><tr>
                        <th>Ref</th><th>Guest</th><th>Room</th>
                        <th>Check-in</th><th>Check-out</th><th>Status</th><th>Action</th>
                    </tr></thead>
                    <tbody>
                    <?php while ($b = mysqli_fetch_assoc($recent_bookings)): ?>
                    <tr>
                        <td><a href="bookings.php?id=<?php echo $b['id']; ?>"><?php echo $b['booking_ref']; ?></a></td>
                        <td><?php echo $b['first_name'] . ' ' . $b['last_name']; ?></td>
                        <td><?php echo $b['room_number']; ?></td>
                        <td><?php echo $b['check_in']; ?></td>
                        <td><?php echo $b['check_out']; ?></td>
                        <td>
                            <?php
                            $sc = ['pending'=>'warning','confirmed'=>'primary','checked_in'=>'success','checked_out'=>'secondary','cancelled'=>'danger'];
                            $s  = $b['status'];
                            echo "<span class='badge bg-".($sc[$s]??'dark')."'>".ucfirst(str_replace('_',' ',$s))."</span>";
                            ?>
                        </td>
                        <td>
                            <a href="bookings.php?id=<?php echo $b['id']; ?>" class="btn btn-sm btn-outline-primary">View</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Today panel -->
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header"><i class="fas fa-sign-in-alt me-2 text-success"></i>Today's Check-ins</div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                <?php
                $ci_count = 0;
                while ($ci = mysqli_fetch_assoc($todays_checkins)):
                    $ci_count++;
                ?>
                    <li class="list-group-item d-flex justify-content-between small py-2">
                        <span><?php echo $ci['first_name'].' '.$ci['last_name']; ?></span>
                        <span class="text-muted">Rm <?php echo $ci['room_number']; ?></span>
                    </li>
                <?php endwhile; ?>
                <?php if (!$ci_count): ?>
                    <li class="list-group-item text-muted small py-2">No check-ins today</li>
                <?php endif; ?>
                </ul>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><i class="fas fa-sign-out-alt me-2 text-danger"></i>Today's Check-outs</div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                <?php
                $co_count = 0;
                while ($co = mysqli_fetch_assoc($todays_checkouts)):
                    $co_count++;
                ?>
                    <li class="list-group-item d-flex justify-content-between small py-2">
                        <span><?php echo $co['first_name'].' '.$co['last_name']; ?></span>
                        <span class="text-muted">Rm <?php echo $co['room_number']; ?></span>
                    </li>
                <?php endwhile; ?>
                <?php if (!$co_count): ?>
                    <li class="list-group-item text-muted small py-2">No check-outs today</li>
                <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php
mysqli_close($conn);
require_once 'includes/footer.php';
?>
