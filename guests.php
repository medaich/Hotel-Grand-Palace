<?php
require_once 'config.php';
require_once 'includes/auth.php';
require_login();

$page_title = 'Guest Management';
$conn = db_connect();
$msg  = '';

// ── ADD GUEST ────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    $fn    = $_POST['first_name'];
    $ln    = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $id_no = $_POST['id_number'];
    $nat   = $_POST['nationality'];
    $addr  = $_POST['address'];
    $dob   = $_POST['date_of_birth'];
    $notes = $_POST['notes'];
    $by    = $_SESSION['user_id'];

    $sql = "INSERT INTO guests (first_name,last_name,email,phone,id_number,nationality,address,date_of_birth,notes,created_by)
            VALUES ('$fn','$ln','$email','$phone','$id_no','$nat','$addr','$dob','$notes',$by)";
    if (mysqli_query($conn, $sql)) {
        $msg = "<div class='alert alert-success'>Guest <strong>$fn $ln</strong> registered.</div>";
        log_action("Added guest: $fn $ln ($email)");
    } else {
        $msg = "<div class='alert alert-danger'>" . mysqli_error($conn) . "</div>";
    }
}

// ── DELETE GUEST ─────────────────────────────────────────────
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM guests WHERE id = $id");
    header('Location: guests.php?deleted=1');
    exit;
}

// ── EDIT GUEST POST — must run before any HTML output ────────
if (isset($_GET['edit']) && $_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'edit') {
    $fn    = $_POST['first_name'];
    $ln    = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $id_no = $_POST['id_number'];
    $nat   = $_POST['nationality'];
    $addr  = $_POST['address'];
    $notes = $_POST['notes'];
    $gid2  = $_POST['guest_id'];

    mysqli_query($conn, "UPDATE guests SET first_name='$fn',last_name='$ln',email='$email',phone='$phone',id_number='$id_no',nationality='$nat',address='$addr',notes='$notes' WHERE id=$gid2");
    header('Location: guests.php?view=' . $gid2);
    exit;
}

// ── SEARCH / LIST ─────────────────────────────────────────────
$search = $_GET['q'] ?? '';
$sql = "SELECT * FROM guests";
if ($search) {
    $sql .= " WHERE first_name LIKE '%$search%' OR last_name LIKE '%$search%'
              OR email LIKE '%$search%' OR id_number LIKE '%$search%'
              OR nationality LIKE '%$search%'";
}
$sql    .= " ORDER BY id DESC";
$guests  = mysqli_query($conn, $sql);

require_once 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0 fw-bold"><i class="fas fa-users me-2"></i>Guest Management</h5>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addGuestModal">
        <i class="fas fa-user-plus me-2"></i>Register Guest
    </button>
</div>

<?php echo $msg; ?>
<?php if (isset($_GET['deleted'])): ?><div class="alert alert-warning">Guest record deleted.</div><?php endif; ?>
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="d-flex gap-2">
            <input type="text" name="q" class="form-control" placeholder="Search by name, email, ID number, nationality..."
                   value="<?php echo $search;  ?>">
            <button type="submit" class="btn btn-primary px-4"><i class="fas fa-search"></i></button>
        </form>
        <?php if ($search): ?>
        <small class="text-muted mt-2 d-block">Showing results for: <?php echo $search; ?></small>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead><tr>
                <th>#</th><th>Name</th><th>Email</th><th>Phone</th>
                <th>Nationality</th><th>ID Number</th><th>Notes</th><th>Actions</th>
            </tr></thead>
            <tbody>
            <?php while ($g = mysqli_fetch_assoc($guests)): ?>
            <tr>
                <td><?php echo $g['id']; ?></td>
                <td><?php echo $g['first_name'] . ' ' . $g['last_name']; ?></td>
                <td><?php echo $g['email']; ?></td>
                <td><?php echo $g['phone']; ?></td>
                <td><?php echo $g['nationality']; ?></td>
                <td><?php echo $g['id_number']; ?></td>
                <td><?php echo $g['notes']; ?></td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <a href="guests.php?view=<?php echo $g['id']; ?>" class="btn btn-outline-info"><i class="fas fa-eye"></i></a>
                        <a href="guests.php?edit=<?php echo $g['id']; ?>" class="btn btn-outline-warning"><i class="fas fa-edit"></i></a>
                        <a href="guests.php?delete=<?php echo $g['id']; ?>" class="btn btn-outline-danger"
                           onclick="return confirm('Delete this guest?')"><i class="fas fa-trash"></i></a>
                    </div>
                </td>
            </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<?php if (isset($_GET['view'])): ?>
<?php
$gid    = $_GET['view'];
$gdata  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM guests WHERE id = $gid"));
if ($gdata):
    $gbookings = mysqli_query($conn, "SELECT b.*, r.room_number FROM bookings b JOIN rooms r ON b.room_id=r.id WHERE b.guest_id = $gid");
?>
<div class="card mt-4">
    <div class="card-header">Guest Profile — <?php echo $gdata['first_name'].' '.$gdata['last_name']; ?></div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-sm">
                    <tr><th>Full Name</th><td><?php echo $gdata['first_name'].' '.$gdata['last_name']; ?></td></tr>
                    <tr><th>Email</th>    <td><?php echo $gdata['email']; ?></td></tr>
                    <tr><th>Phone</th>    <td><?php echo $gdata['phone']; ?></td></tr>
                    <tr><th>ID Number</th><td><?php echo $gdata['id_number']; ?></td></tr>
                    <tr><th>Nationality</th><td><?php echo $gdata['nationality']; ?></td></tr>
                    <tr><th>Date of Birth</th><td><?php echo $gdata['date_of_birth']; ?></td></tr>
                    <tr><th>Address</th> <td><?php echo $gdata['address']; ?></td></tr>
                    <tr><th>Notes</th>   <td><?php echo $gdata['notes']; ?></td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>Booking History</h6>
                <table class="table table-sm">
                    <thead><tr><th>Ref</th><th>Room</th><th>Check-in</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php while ($bk = mysqli_fetch_assoc($gbookings)): ?>
                    <tr>
                        <td><?php echo $bk['booking_ref']; ?></td>
                        <td><?php echo $bk['room_number']; ?></td>
                        <td><?php echo $bk['check_in']; ?></td>
                        <td><?php echo $bk['status']; ?></td>
                    </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<?php endif; ?>
<?php if (isset($_GET['edit'])): ?>
<?php
$eid   = $_GET['edit'];
$edata = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM guests WHERE id = $eid"));
// POST is handled before header output (above); this block only displays the form.
if ($edata):
?>
<div class="card mt-4">
    <div class="card-header">Edit Guest</div>
    <div class="card-body">
        <form method="POST">
            <input type="hidden" name="action"   value="edit">
            <input type="hidden" name="guest_id" value="<?php echo $edata['id']; ?>">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">First Name</label>
                    <input type="text" name="first_name" class="form-control" value="<?php echo $edata['first_name']; ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="last_name" class="form-control" value="<?php echo $edata['last_name']; ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?php echo $edata['email']; ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" value="<?php echo $edata['phone']; ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">ID Number</label>
                    <input type="text" name="id_number" class="form-control" value="<?php echo $edata['id_number']; ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nationality</label>
                    <input type="text" name="nationality" class="form-control" value="<?php echo $edata['nationality']; ?>">
                </div>
                <div class="col-12">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control"><?php echo $edata['address']; ?></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control"><?php echo $edata['notes']; ?></textarea>
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="guests.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>
<?php endif; ?>

<!-- Add Guest Modal -->
<div class="modal fade" id="addGuestModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Register New Guest</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">First Name *</label><input type="text" name="first_name" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">Last Name *</label><input type="text" name="last_name" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control"></div>
                        <div class="col-md-6"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control"></div>
                        <div class="col-md-6"><label class="form-label">ID / Passport Number</label><input type="text" name="id_number" class="form-control"></div>
                        <div class="col-md-6"><label class="form-label">Nationality</label><input type="text" name="nationality" class="form-control"></div>
                        <div class="col-md-6"><label class="form-label">Date of Birth</label><input type="date" name="date_of_birth" class="form-control"></div>
                        <div class="col-12"><label class="form-label">Address</label><textarea name="address" class="form-control" rows="2"></textarea></div>
                        <div class="col-12"><label class="form-label">Notes (internal)</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-user-plus me-2"></i>Register Guest</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
mysqli_close($conn);
require_once 'includes/footer.php';
?>
