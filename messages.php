<?php
require_once 'config.php';
require_once 'includes/auth.php';
require_login();

$page_title = 'Internal Messages';
$conn = db_connect();
$msg  = '';

// ── SEND MESSAGE ─────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'send') {
    $sender_id = $_SESSION['user_id'];
    $subject   = $_POST['subject'];
    $body      = $_POST['body'];

    $sql = "INSERT INTO messages (sender_id, subject, body) VALUES ($sender_id,'$subject','$body')";
    if (mysqli_query($conn, $sql)) {
        $msg = "<div class='alert alert-success'>Message sent.</div>";
    } else {
        $msg = "<div class='alert alert-danger'>" . mysqli_error($conn) . "</div>";
    }
}

// ── DELETE MESSAGE ────────────────────────────────────────────
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM messages WHERE id=$id");
    header('Location: messages.php');
    exit;
}

// ── MARK READ ─────────────────────────────────────────────────
if (isset($_GET['read'])) {
    $id = $_GET['read'];
    mysqli_query($conn, "UPDATE messages SET is_read=1 WHERE id=$id");
}

// ── LIST MESSAGES ─────────────────────────────────────────────
$messages = mysqli_query($conn,
    "SELECT m.*, u.username AS sender_name
     FROM messages m
     LEFT JOIN users u ON m.sender_id = u.id
     ORDER BY m.created_at DESC"
);

require_once 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0 fw-bold"><i class="fas fa-envelope me-2"></i>Internal Messages</h5>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#composeModal">
        <i class="fas fa-pen me-2"></i>Compose
    </button>
</div>

<?php echo $msg; ?>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead><tr><th>From</th><th>Subject</th><th>Date</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
            <?php while ($m = mysqli_fetch_assoc($messages)): ?>
            <tr class="<?php echo !$m['is_read'] ? 'fw-bold' : ''; ?>">
                <td><?php echo $m['sender_name']; ?></td>
                <td>
                    <a href="messages.php?view=<?php echo $m['id']; ?>&read=<?php echo $m['id']; ?>">
                        <?php echo $m['subject']; ?>
                    </a>
                </td>
                <td><?php echo $m['created_at']; ?></td>
                <td><?php echo $m['is_read'] ? '<span class="text-muted">Read</span>' : '<span class="badge bg-primary">New</span>'; ?></td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <a href="messages.php?view=<?php echo $m['id']; ?>" class="btn btn-outline-info"><i class="fas fa-eye"></i></a>
                        <a href="messages.php?delete=<?php echo $m['id']; ?>" class="btn btn-outline-danger"
                           onclick="return confirm('Delete?')"><i class="fas fa-trash"></i></a>
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
$mid  = $_GET['view'];
$mdata = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT m.*, u.username AS sender_name FROM messages m LEFT JOIN users u ON m.sender_id=u.id WHERE m.id=$mid"
));
if ($mdata):
?>
<div class="card mt-4">
    <div class="card-header">
        <strong><?php echo $mdata['subject']; ?></strong>
        <span class="text-muted ms-3">From: <?php echo $mdata['sender_name']; ?></span>
        <span class="text-muted ms-2"><?php echo $mdata['created_at']; ?></span>
    </div>
    <div class="card-body">
        <?php echo $mdata['body']; ?>
    </div>
</div>
<?php endif; ?>
<?php endif; ?>

<!-- Compose Modal -->
<div class="modal fade" id="composeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Compose Message</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form method="POST">
                <input type="hidden" name="action" value="send">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <input type="text" name="subject" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message</label>
                        <textarea name="body" class="form-control" rows="6" placeholder="HTML is supported..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane me-2"></i>Send</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
mysqli_close($conn);
require_once 'includes/footer.php';
?>
