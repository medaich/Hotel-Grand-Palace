<?php
require_once 'config.php';
require_once 'includes/auth.php';
require_login();
$page_title = 'Maintenance';
$conn = db_connect();
$msg  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_id   = $_POST['room_id'];
    $issue     = $_POST['issue'];
    $priority  = $_POST['priority'];
    $by        = $_SESSION['user_id'];
    mysqli_query($conn, "INSERT INTO maintenance (room_id,reported_by,issue,priority) VALUES ($room_id,'$by','$issue','$priority')");
    $msg = "<div class='alert alert-success'>Maintenance request submitted.</div>";
}

if (isset($_GET['resolve'])) {
    $id = $_GET['resolve'];
    mysqli_query($conn, "UPDATE maintenance SET status='resolved',resolved_at=NOW() WHERE id=$id");
    header('Location: maintenance.php');
    exit;
}

$requests = mysqli_query($conn,
    "SELECT m.*, r.room_number, u.username AS reporter
     FROM maintenance m
     JOIN rooms r ON m.room_id = r.id
     JOIN users u ON m.reported_by = u.id
     ORDER BY m.reported_at DESC"
);
$rooms = mysqli_query($conn, "SELECT id, room_number FROM rooms ORDER BY room_number");
require_once 'includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0 fw-bold"><i class="fas fa-tools me-2"></i>Maintenance Requests</h5>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMaintModal">
        <i class="fas fa-plus me-2"></i>Report Issue
    </button>
</div>
<?php echo $msg; ?>
<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead><tr><th>Room</th><th>Issue</th><th>Priority</th><th>Reporter</th><th>Date</th><th>Status</th><th>Action</th></tr></thead>
            <tbody>
            <?php while ($r = mysqli_fetch_assoc($requests)): ?>
            <tr>
                <td><?php echo $r['room_number']; ?></td>
                <td><?php echo $r['issue']; ?></td>
                <td>
                    <?php
                    $pc = ['low'=>'success','normal'=>'warning','high'=>'danger','critical'=>'dark'];
                    $p  = $r['priority'];
                    echo "<span class='badge bg-".($pc[$p]??'secondary')."'>".ucfirst($p)."</span>";
                    ?>
                </td>
                <td><?php echo $r['reporter']; ?></td>
                <td><?php echo $r['reported_at']; ?></td>
                <td><?php echo $r['status'] === 'resolved' ? '<span class="text-success">Resolved</span>' : '<span class="text-warning">Open</span>'; ?></td>
                <td>
                    <?php if ($r['status'] !== 'resolved'): ?>
                    <a href="maintenance.php?resolve=<?php echo $r['id']; ?>" class="btn btn-sm btn-success">Resolve</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="modal fade" id="addMaintModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Report Maintenance Issue</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <form method="POST">
            <div class="modal-body">
                <div class="mb-3"><label class="form-label">Room</label>
                    <select name="room_id" class="form-select">
                        <?php while ($rm = mysqli_fetch_assoc($rooms)): ?>
                        <option value="<?php echo $rm['id']; ?>"><?php echo $rm['room_number']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3"><label class="form-label">Issue Description</label>
                    <textarea name="issue" class="form-control" rows="3" required></textarea></div>
                <div class="mb-3"><label class="form-label">Priority</label>
                    <select name="priority" class="form-select">
                        <option value="low">Low</option><option value="normal" selected>Normal</option>
                        <option value="high">High</option><option value="critical">Critical</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div></div>
</div>
<?php mysqli_close($conn); require_once 'includes/footer.php'; ?>
