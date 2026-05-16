<?php
require_once 'config.php';
require_once 'includes/auth.php';
require_login();

$page_title = 'Services';
$conn = db_connect();
$msg  = '';

// ── ADD SERVICE CHARGE ────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    $booking_id   = $_POST['booking_id'];
    $service_name = $_POST['service_name'];
    $quantity     = $_POST['quantity'];
    $unit_price   = $_POST['unit_price'];
    $total        = $quantity * $unit_price;
    $by           = $_SESSION['user_id'];

    $sql = "INSERT INTO services (booking_id, service_name, quantity, unit_price, total, added_by)
            VALUES ($booking_id,'$service_name',$quantity,$unit_price,$total,$by)";
    if (mysqli_query($conn, $sql)) {
        $msg = "<div class='alert alert-success'>Service charge added successfully.</div>";
        log_action("Added service '$service_name' to booking $booking_id");
    } else {
        $msg = "<div class='alert alert-danger'>" . mysqli_error($conn) . "</div>";
    }
}

// ── DELETE SERVICE ─────────────────────────────────────────────
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM services WHERE id = $id");
    header('Location: services.php?deleted=1');
    exit;
}

// ── LIST SERVICES ─────────────────────────────────────────────
$filter_booking = $_GET['booking_id'] ?? '';
$sql = "SELECT s.*, b.booking_ref, g.first_name, g.last_name, u.username AS added_by_user
        FROM services s
        JOIN bookings b ON s.booking_id = b.id
        JOIN guests  g ON b.guest_id   = g.id
        JOIN users   u ON s.added_by   = u.id
        WHERE 1=1";
if ($filter_booking) {
    $sql .= " AND s.booking_id = '$filter_booking'";
}
$sql .= " ORDER BY s.added_at DESC";
$services = mysqli_query($conn, $sql);

// Bookings dropdown
$bookings_list = mysqli_query($conn, "SELECT b.id, b.booking_ref, g.first_name, g.last_name
                                      FROM bookings b JOIN guests g ON b.guest_id = g.id
                                      ORDER BY b.booking_ref");

require_once 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0 fw-bold"><i class="fas fa-concierge-bell me-2"></i>Services & Charges</h5>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addServiceModal">
        <i class="fas fa-plus me-2"></i>Add Charge
    </button>
</div>

<?php echo $msg; ?>
<?php if (isset($_GET['deleted'])): ?><div class="alert alert-warning">Service charge deleted.</div><?php endif; ?>
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="d-flex gap-2">
            <select name="booking_id" class="form-select">
                <option value="">All Bookings</option>
                <?php
                mysqli_data_seek($bookings_list, 0);
                while ($bl = mysqli_fetch_assoc($bookings_list)):
                ?>
                <option value="<?php echo $bl['id']; ?>" <?php echo $filter_booking == $bl['id'] ? 'selected' : ''; ?>>
                    <?php echo $bl['booking_ref'] . ' — ' . $bl['first_name'] . ' ' . $bl['last_name']; ?>
                </option>
                <?php endwhile; ?>
            </select>
            <button type="submit" class="btn btn-outline-primary px-4">Filter</button>
            <a href="services.php" class="btn btn-outline-secondary">Reset</a>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead><tr>
                <th>Booking Ref</th><th>Guest</th><th>Service</th>
                <th>Qty</th><th>Unit Price</th><th>Total</th><th>Added By</th><th>Date</th><th>Actions</th>
            </tr></thead>
            <tbody>
            <?php while ($s = mysqli_fetch_assoc($services)): ?>
            <tr>
                <td><a href="bookings.php?id=<?php echo $s['booking_id']; ?>"><?php echo $s['booking_ref']; ?></a></td>
                <td><?php echo $s['first_name'] . ' ' . $s['last_name']; ?></td>
                <td><?php echo $s['service_name']; ?></td>
                <td><?php echo $s['quantity']; ?></td>
                <td>$<?php echo number_format($s['unit_price'], 2); ?></td>
                <td><strong>$<?php echo number_format($s['total'], 2); ?></strong></td>
                <td><?php echo $s['added_by_user']; ?></td>
                <td><?php echo $s['added_at']; ?></td>
                <td>
                    <a href="services.php?delete=<?php echo $s['id']; ?>"
                       class="btn btn-sm btn-outline-danger"
                       onclick="return confirm('Delete this charge?')">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Service Modal -->
<div class="modal fade" id="addServiceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Service Charge</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Booking *</label>
                        <select name="booking_id" class="form-select" required>
                            <option value="">Select Booking</option>
                            <?php
                            mysqli_data_seek($bookings_list, 0);
                            while ($bl = mysqli_fetch_assoc($bookings_list)):
                            ?>
                            <option value="<?php echo $bl['id']; ?>">
                                <?php echo $bl['booking_ref'] . ' — ' . $bl['first_name'] . ' ' . $bl['last_name']; ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Service Name *</label>
                        <input type="text" name="service_name" class="form-control" required
                               placeholder="e.g. Room Service, Spa, Laundry">
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label">Quantity</label>
                            <input type="number" name="quantity" class="form-control" value="1" min="1" id="svcQty">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Unit Price ($)</label>
                            <input type="number" step="0.01" name="unit_price" class="form-control" value="0" id="svcPrice">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Add Charge</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
mysqli_close($conn);
require_once 'includes/footer.php';
?>
