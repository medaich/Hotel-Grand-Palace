<?php
require_once 'config.php';
require_once 'includes/auth.php';
require_login();

$page_title = 'Bookings';
$conn = db_connect();
$msg  = '';

// ── CREATE BOOKING ───────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create') {
    $guest_id    = $_POST['guest_id'];
    $room_id     = $_POST['room_id'];
    $check_in    = $_POST['check_in'];
    $check_out   = $_POST['check_out'];
    $adults      = $_POST['adults'];
    $children    = $_POST['children'] ?? 0;
    $special_req = $_POST['special_requests'];
    $payment_m   = $_POST['payment_method'];
    $created_by  = $_SESSION['user_id'];
    $total_price = $_POST['total_price'];

    $ref = 'BK-' . date('Y') . '-' . rand(1000, 9999);
    $sql = "INSERT INTO bookings (booking_ref,guest_id,room_id,check_in,check_out,adults,children,total_price,special_requests,payment_method,created_by)
            VALUES ('$ref',$guest_id,$room_id,'$check_in','$check_out',$adults,$children,$total_price,'$special_req','$payment_m',$created_by)";
    if (mysqli_query($conn, $sql)) {
        mysqli_query($conn, "UPDATE rooms SET status='occupied' WHERE id=$room_id");
        $msg = "<div class='alert alert-success'>Booking created: <strong>$ref</strong></div>";
        log_action("Created booking $ref for guest $guest_id room $room_id");
    } else {
        $msg = "<div class='alert alert-danger'>" . mysqli_error($conn) . "</div>";
    }
}

// ── UPDATE STATUS ─────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update_status') {
    $bid    = $_POST['booking_id'];
    $status = $_POST['status'];
    $pay    = $_POST['payment_status'];
    mysqli_query($conn, "UPDATE bookings SET status='$status', payment_status='$pay' WHERE id=$bid");
    header('Location: bookings.php?updated=1');
    exit;
}

// ── DELETE BOOKING ────────────────────────────────────────────
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM bookings WHERE id=$id");
    header('Location: bookings.php?deleted=1');
    exit;
}

// ── LIST / FILTER ─────────────────────────────────────────────
$filter_status = $_GET['status']      ?? '';
$filter_date   = $_GET['date']        ?? '';
$search        = $_GET['search']      ?? '';
$sort          = $_GET['sort']        ?? 'created_at';
$order         = $_GET['order']       ?? 'DESC';

$base_sql = "SELECT b.*, g.first_name, g.last_name, g.email, r.room_number, r.room_type
             FROM bookings b
             JOIN guests g ON b.guest_id = g.id
             JOIN rooms  r ON b.room_id  = r.id
             WHERE 1=1";

if ($filter_status) $base_sql .= " AND b.status = '$filter_status'";
if ($filter_date)   $base_sql .= " AND b.check_in = '$filter_date'";
if ($search)        $base_sql .= " AND (g.first_name LIKE '%$search%' OR g.last_name LIKE '%$search%' OR b.booking_ref LIKE '%$search%')";

$base_sql .= " ORDER BY $sort $order";

$bookings = mysqli_query($conn, $base_sql);

// For dropdowns
$all_guests = mysqli_query($conn, "SELECT id, first_name, last_name FROM guests ORDER BY first_name");
$avail_rooms = mysqli_query($conn, "SELECT id, room_number, room_type, price_per_night FROM rooms WHERE status='available' ORDER BY room_number");

require_once 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0 fw-bold"><i class="fas fa-calendar-check me-2"></i>Bookings</h5>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newBookingModal">
        <i class="fas fa-plus me-2"></i>New Booking
    </button>
</div>

<?php echo $msg; ?>
<?php if (isset($_GET['updated'])): ?><div class="alert alert-success">Booking updated.</div><?php endif; ?>
<?php if (isset($_GET['deleted'])): ?><div class="alert alert-warning">Booking deleted.</div><?php endif; ?>

<!-- Filters — all params injected into SQL + reflected into HTML without encoding -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-sm-3">
                <input type="text" name="search" class="form-control" placeholder="Search guest / booking ref"
                       value="<?php echo $search; ?>">
            </div>
            <div class="col-sm-2">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <?php foreach(['pending','confirmed','checked_in','checked_out','cancelled'] as $s): ?>
                    <option value="<?php echo $s; ?>" <?php echo $filter_status===$s?'selected':''; ?>><?php echo ucfirst(str_replace('_',' ',$s)); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-sm-2">
                <input type="date" name="date" class="form-control" value="<?php echo $filter_date; ?>">
            </div>
            <div class="col-sm-2">
                <select name="sort" class="form-select">
                    <option value="created_at"  <?php echo $sort==='created_at' ?'selected':''; ?>>Sort: Created</option>
                    <option value="check_in"    <?php echo $sort==='check_in'   ?'selected':''; ?>>Sort: Check-in</option>
                    <option value="total_price" <?php echo $sort==='total_price'?'selected':''; ?>>Sort: Price</option>
                </select>
            </div>
            <div class="col-sm-1">
                <select name="order" class="form-select">
                    <option value="DESC" <?php echo $order==='DESC'?'selected':''; ?>>DESC</option>
                    <option value="ASC"  <?php echo $order==='ASC' ?'selected':''; ?>>ASC</option>
                </select>
            </div>
            <div class="col-sm-2">
                <button type="submit" class="btn btn-outline-primary w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead><tr>
                <th>Ref</th><th>Guest</th><th>Room</th><th>Check-in</th>
                <th>Check-out</th><th>Total</th><th>Status</th><th>Payment</th><th>Actions</th>
            </tr></thead>
            <tbody>
            <?php while ($b = mysqli_fetch_assoc($bookings)): ?>
            <tr>
                <td><strong><?php echo $b['booking_ref']; ?></strong></td>
                <td><?php echo $b['first_name'].' '.$b['last_name']; ?><br><small class="text-muted"><?php echo $b['email']; ?></small></td>
                <td><?php echo $b['room_number']; ?> <small class="text-muted">(<?php echo $b['room_type']; ?>)</small></td>
                <td><?php echo $b['check_in']; ?></td>
                <td><?php echo $b['check_out']; ?></td>
                <td><strong>$<?php echo number_format($b['total_price'],2); ?></strong></td>
                <td>
                    <?php
                    $sc = ['pending'=>'warning','confirmed'=>'primary','checked_in'=>'success','checked_out'=>'secondary','cancelled'=>'danger'];
                    $s  = $b['status'];
                    echo "<span class='badge bg-".($sc[$s]??'dark')."'>".ucfirst(str_replace('_',' ',$s))."</span>";
                    ?>
                </td>
                <td>
                    <?php
                    $pc = ['paid'=>'success','unpaid'=>'danger','partial'=>'warning'];
                    $p  = $b['payment_status'];
                    echo "<span class='badge bg-".($pc[$p]??'secondary')."'>".ucfirst($p)."</span>";
                    ?>
                </td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-info" data-bs-toggle="modal"
                                data-bs-target="#detailModal<?php echo $b['id']; ?>">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-outline-warning" data-bs-toggle="modal"
                                data-bs-target="#updateModal<?php echo $b['id']; ?>">
                            <i class="fas fa-edit"></i>
                        </button>
                        <a href="bookings.php?delete=<?php echo $b['id']; ?>"
                           class="btn btn-outline-danger"
                           onclick="return confirm('Delete this booking?')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </td>
            </tr>

            <!-- Detail Modal -->
            <div class="modal fade" id="detailModal<?php echo $b['id']; ?>" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Booking Details — <?php echo $b['booking_ref']; ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-sm">
                                        <tr><th>Guest</th><td><?php echo $b['first_name'].' '.$b['last_name']; ?></td></tr>
                                        <tr><th>Email</th><td><?php echo $b['email']; ?></td></tr>
                                        <tr><th>Room</th><td><?php echo $b['room_number'].' ('.ucfirst($b['room_type']).')'; ?></td></tr>
                                        <tr><th>Check-in</th><td><?php echo $b['check_in']; ?></td></tr>
                                        <tr><th>Check-out</th><td><?php echo $b['check_out']; ?></td></tr>
                                        <tr><th>Adults</th><td><?php echo $b['adults']; ?></td></tr>
                                        <tr><th>Children</th><td><?php echo $b['children']; ?></td></tr>
                                        <tr><th>Total</th><td><strong>$<?php echo number_format($b['total_price'],2); ?></strong></td></tr>
                                        <tr><th>Payment</th><td><?php echo $b['payment_method']; ?></td></tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6>Special Requests</h6>
                                    <p><?php echo $b['special_requests']; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="updateModal<?php echo $b['id']; ?>" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header"><h5 class="modal-title">Update Booking</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                        <form method="POST">
                            <div class="modal-body">
                                <input type="hidden" name="action"     value="update_status">
                                <input type="hidden" name="booking_id" value="<?php echo $b['id']; ?>">
                                <div class="mb-3">
                                    <label class="form-label">Booking Status</label>
                                    <select name="status" class="form-select">
                                        <?php foreach(['pending','confirmed','checked_in','checked_out','cancelled'] as $s): ?>
                                        <option value="<?php echo $s; ?>" <?php echo $b['status']===$s?'selected':''; ?>><?php echo ucfirst(str_replace('_',' ',$s)); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Payment Status</label>
                                    <select name="payment_status" class="form-select">
                                        <?php foreach(['unpaid','partial','paid'] as $p): ?>
                                        <option value="<?php echo $p; ?>" <?php echo $b['payment_status']===$p?'selected':''; ?>><?php echo ucfirst($p); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- New Booking Modal -->
<div class="modal fade" id="newBookingModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="create">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Guest *</label>
                            <select name="guest_id" class="form-select" required>
                                <option value="">Select Guest</option>
                                <?php
                                // Reset pointer
                                mysqli_data_seek($all_guests, 0);
                                while ($g = mysqli_fetch_assoc($all_guests)):
                                ?>
                                <option value="<?php echo $g['id']; ?>"><?php echo $g['first_name'].' '.$g['last_name']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Room *</label>
                            <select name="room_id" class="form-select" required id="roomSelect">
                                <option value="">Select Room</option>
                                <?php while ($r = mysqli_fetch_assoc($avail_rooms)): ?>
                                <option value="<?php echo $r['id']; ?>" data-price="<?php echo $r['price_per_night']; ?>">
                                    <?php echo $r['room_number'].' - '.ucfirst($r['room_type']).' ($'.$r['price_per_night'].'/night)'; ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-3"><label class="form-label">Check-in *</label><input type="date" name="check_in"  class="form-control" id="checkIn"  required></div>
                        <div class="col-md-3"><label class="form-label">Check-out *</label><input type="date" name="check_out" class="form-control" id="checkOut" required></div>
                        <div class="col-md-2"><label class="form-label">Adults</label><input type="number" name="adults"   class="form-control" value="1" min="1"></div>
                        <div class="col-md-2"><label class="form-label">Children</label><input type="number" name="children" class="form-control" value="0" min="0"></div>
                        <div class="col-md-2">
                            <label class="form-label">Total ($)</label>
                            <input type="number" step="0.01" name="total_price" class="form-control" id="totalPrice" value="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Payment Method</label>
                            <select name="payment_method" class="form-select">
                                <option value="cash">Cash</option>
                                <option value="credit_card">Credit Card</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="online">Online Payment</option>
                            </select>
                        </div>
                        <div class="col-12"><label class="form-label">Special Requests</label><textarea name="special_requests" class="form-control" rows="3"></textarea></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-calendar-plus me-2"></i>Create Booking</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function calcPrice() {
    var room = document.getElementById('roomSelect');
    var opt  = room.options[room.selectedIndex];
    var rate = parseFloat(opt.getAttribute('data-price')) || 0;
    var cin  = new Date(document.getElementById('checkIn').value);
    var cout = new Date(document.getElementById('checkOut').value);
    var days = Math.max(0, (cout - cin) / (1000*60*60*24));
    document.getElementById('totalPrice').value = (rate * days).toFixed(2);
}
document.getElementById('roomSelect').addEventListener('change', calcPrice);
document.getElementById('checkIn').addEventListener('change',   calcPrice);
document.getElementById('checkOut').addEventListener('change',  calcPrice);
</script>

<?php
mysqli_close($conn);
require_once 'includes/footer.php';
?>
