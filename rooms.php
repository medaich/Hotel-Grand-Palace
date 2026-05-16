<?php
require_once 'config.php';
require_once 'includes/auth.php';
require_login();

$page_title = 'Rooms Management';
$conn = db_connect();
$msg  = '';

// ── ADD ROOM ────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $room_number     = $_POST['room_number'];
    $room_type       = $_POST['room_type'];
    $floor           = $_POST['floor'];
    $capacity        = $_POST['capacity'];
    $price           = $_POST['price_per_night'];
    $description     = $_POST['description'];
    $amenities       = $_POST['amenities'];

    $image_path = '';
    if (isset($_FILES['room_image']) && $_FILES['room_image']['error'] === 0) {
        $upload_name = $_FILES['room_image']['name'];
        $tmp_path    = $_FILES['room_image']['tmp_name'];
        $dest        = UPLOAD_DIR . $upload_name;
        move_uploaded_file($tmp_path, $dest);
        $image_path  = 'uploads/' . $upload_name;
    }

    $sql = "INSERT INTO rooms (room_number, room_type, floor, capacity, price_per_night, description, amenities, image_path)
            VALUES ('$room_number','$room_type','$floor','$capacity','$price','$description','$amenities','$image_path')";
    if (mysqli_query($conn, $sql)) {
        $msg = "<div class='alert alert-success'>Room $room_number added successfully.</div>";
    } else {
        $msg = "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
    }
}

// ── DELETE ROOM ─────────────────────────────────────────────
if (isset($_GET['delete'])) {
    $id  = $_GET['delete'];
    $sql = "DELETE FROM rooms WHERE id = $id";
    mysqli_query($conn, $sql);
    header('Location: rooms.php?msg=deleted');
    exit;
}

// ── UPDATE STATUS ────────────────────────────────────────────
if (isset($_GET['status']) && isset($_GET['id'])) {
    $id     = $_GET['id'];
    $status = $_GET['status'];
    mysqli_query($conn, "UPDATE rooms SET status='$status' WHERE id=$id");
    header('Location: rooms.php');
    exit;
}

// ── EDIT ROOM POST — must run before any HTML output ─────────
if (isset($_GET['edit']) && $_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'edit_room') {
    $rid         = $_POST['room_id'];
    $room_number = $_POST['room_number'];
    $room_type   = $_POST['room_type'];
    $floor       = $_POST['floor'];
    $capacity    = $_POST['capacity'];
    $price       = $_POST['price_per_night'];
    $description = $_POST['description'];
    $amenities   = $_POST['amenities'];
    $status      = $_POST['status'];

    $sql = "UPDATE rooms SET room_number='$room_number',room_type='$room_type',floor='$floor',
            capacity='$capacity',price_per_night='$price',description='$description',
            amenities='$amenities',status='$status' WHERE id=$rid";
    if (mysqli_query($conn, $sql)) {
        header('Location: rooms.php?view=' . $rid . '&msg=Room+updated+successfully');
    } else {
        header('Location: rooms.php?edit=' . $rid . '&msg=' . urlencode(mysqli_error($conn)));
    }
    exit;
}

// ── FILTER / LIST ────────────────────────────────────────────
$filter_type   = $_GET['type']   ?? '';
$filter_status = $_GET['status'] ?? '';
$filter_floor  = $_GET['floor']  ?? '';

$where = "1=1";
if ($filter_type)   $where .= " AND room_type   = '$filter_type'";
if ($filter_status) $where .= " AND status      = '$filter_status'";
if ($filter_floor)  $where .= " AND floor       = '$filter_floor'";

$rooms = mysqli_query($conn, "SELECT * FROM rooms WHERE $where ORDER BY room_number");

require_once 'includes/header.php';
?>

<?php if (isset($_GET['msg'])): ?><div class="alert alert-info"><?php echo $_GET['msg']; ?></div><?php endif; ?>
<?php echo $msg; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0 fw-bold"><i class="fas fa-door-open me-2"></i>Rooms Management</h5>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoomModal">
        <i class="fas fa-plus me-2"></i>Add Room
    </button>
</div>
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-sm-3">
                <select name="type" class="form-select">
                    <option value="">All Types</option>
                    <?php foreach(['single','double','suite','penthouse'] as $t): ?>
                    <option value="<?php echo $t; ?>" <?php echo $filter_type===$t?'selected':''; ?>><?php echo ucfirst($t); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-sm-3">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <?php foreach(['available','occupied','maintenance'] as $s): ?>
                    <option value="<?php echo $s; ?>" <?php echo $filter_status===$s?'selected':''; ?>><?php echo ucfirst($s); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-sm-2">
                <input type="number" name="floor" class="form-control" placeholder="Floor"
                       value="<?php echo $filter_floor; ?>">
            </div>
            <div class="col-sm-2">
                <button type="submit" class="btn btn-outline-primary w-100">Filter</button>
            </div>
            <div class="col-sm-2">
                <a href="rooms.php" class="btn btn-outline-secondary w-100">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Rooms Table -->
<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead><tr>
                <th>Room #</th><th>Type</th><th>Floor</th><th>Capacity</th>
                <th>Price/Night</th><th>Status</th><th>Description</th><th>Actions</th>
            </tr></thead>
            <tbody>
            <?php while ($room = mysqli_fetch_assoc($rooms)): ?>
            <tr>
                <td><strong><?php echo $room['room_number']; ?></strong></td>
                <td><?php echo ucfirst($room['room_type']); ?></td>
                <td><?php echo $room['floor']; ?></td>
                <td><?php echo $room['capacity']; ?></td>
                <td>$<?php echo number_format($room['price_per_night'], 2); ?></td>
                <td>
                    <span class="status-<?php echo $room['status']; ?>">
                        <?php echo ucfirst($room['status']); ?>
                    </span>
                </td>
                <td><?php echo $room['description']; ?></td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <a href="rooms.php?view=<?php echo $room['id']; ?>" class="btn btn-outline-info">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="rooms.php?edit=<?php echo $room['id']; ?>" class="btn btn-outline-warning">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="rooms.php?delete=<?php echo $room['id']; ?>"
                           class="btn btn-outline-danger"
                           onclick="return confirm('Delete room?')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                    <div class="mt-1">
                        <select class="form-select form-select-sm" onchange="location='rooms.php?id=<?php echo $room['id']; ?>&status='+this.value">
                            <?php foreach(['available','occupied','maintenance'] as $s): ?>
                            <option value="<?php echo $s; ?>" <?php echo $room['status']===$s?'selected':''; ?>><?php echo ucfirst($s); ?></option>
                            <?php endforeach; ?>
                        </select>
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
$view_id   = $_GET['view'];
$room_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM rooms WHERE id = $view_id"));
if ($room_data): ?>
<div class="card mt-4">
    <div class="card-header">Room Details — #<?php echo $room_data['room_number']; ?></div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-sm">
                    <tr><th>Type</th><td><?php echo $room_data['room_type']; ?></td></tr>
                    <tr><th>Floor</th><td><?php echo $room_data['floor']; ?></td></tr>
                    <tr><th>Capacity</th><td><?php echo $room_data['capacity']; ?></td></tr>
                    <tr><th>Price/Night</th><td>$<?php echo $room_data['price_per_night']; ?></td></tr>
                    <tr><th>Status</th><td><?php echo $room_data['status']; ?></td></tr>
                    <tr><th>Amenities</th><td><?php echo $room_data['amenities']; ?></td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <p><?php echo $room_data['description']; ?></p>
                <?php if ($room_data['image_path']): ?>
                <img src="<?php echo $room_data['image_path']; ?>" class="img-fluid rounded" style="max-height:200px">
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<?php endif; ?>
<?php if (isset($_GET['edit'])): ?>
<?php
$edit_id   = $_GET['edit'];
$edit_room = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM rooms WHERE id = $edit_id"));
// POST is handled before header output (above); this block only displays the form.
if ($edit_room):
?>
<div class="card mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-edit me-2"></i>Edit Room #<?php echo $edit_room['room_number']; ?></span>
        <a href="rooms.php" class="btn btn-sm btn-outline-secondary">Cancel</a>
    </div>
    <div class="card-body">
        <form method="POST" action="rooms.php?edit=<?php echo $edit_id; ?>">
            <input type="hidden" name="action"  value="edit_room">
            <input type="hidden" name="room_id" value="<?php echo $edit_room['id']; ?>">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Room Number *</label>
                    <input type="text" name="room_number" class="form-control"
                           value="<?php echo $edit_room['room_number']; ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Type *</label>
                    <select name="room_type" class="form-select">
                        <?php foreach(['single','double','suite','penthouse'] as $t): ?>
                        <option value="<?php echo $t; ?>" <?php echo $edit_room['room_type']===$t?'selected':''; ?>>
                            <?php echo ucfirst($t); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Floor</label>
                    <input type="number" name="floor" class="form-control"
                           value="<?php echo $edit_room['floor']; ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Capacity</label>
                    <input type="number" name="capacity" class="form-control"
                           value="<?php echo $edit_room['capacity']; ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Price/Night ($)</label>
                    <input type="number" step="0.01" name="price_per_night" class="form-control"
                           value="<?php echo $edit_room['price_per_night']; ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <?php foreach(['available','occupied','maintenance'] as $s): ?>
                        <option value="<?php echo $s; ?>" <?php echo $edit_room['status']===$s?'selected':''; ?>>
                            <?php echo ucfirst($s); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-8">
                    <label class="form-label">Amenities</label>
                    <input type="text" name="amenities" class="form-control"
                           value="<?php echo $edit_room['amenities']; ?>">
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3"><?php echo $edit_room['description']; ?></textarea>
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Save Changes</button>
                <a href="rooms.php" class="btn btn-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>
<?php endif; ?>

<!-- Add Room Modal -->
<div class="modal fade" id="addRoomModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Room</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Room Number *</label>
                            <input type="text" name="room_number" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Type *</label>
                            <select name="room_type" class="form-select">
                                <option value="single">Single</option>
                                <option value="double">Double</option>
                                <option value="suite">Suite</option>
                                <option value="penthouse">Penthouse</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Floor</label>
                            <input type="number" name="floor" class="form-control" value="1">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Capacity</label>
                            <input type="number" name="capacity" class="form-control" value="2">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Price/Night ($) *</label>
                            <input type="number" step="0.01" name="price_per_night" class="form-control" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Amenities</label>
                            <input type="text" name="amenities" class="form-control" placeholder="WiFi, TV, AC, Mini-bar">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Room Image (any file)</label>
                            <input type="file" name="room_image" class="form-control">
                            <small class="text-muted">Upload room photo (JPG, PNG recommended)</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Room</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
mysqli_close($conn);
require_once 'includes/footer.php';
?>
