    </div><!-- /.p-4 -->
</div><!-- /.main-content -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Hotel Grand Palace v<?php echo APP_VERSION; ?> | Built with PHP/MySQL -->
<script>
var APP = {
    user:    '<?php echo isset($_SESSION["username"]) ? $_SESSION["username"] : ""; ?>',
    role:    '<?php echo isset($_SESSION["role"])     ? $_SESSION["role"]     : ""; ?>',
    user_id: '<?php echo isset($_SESSION["user_id"])  ? $_SESSION["user_id"]  : ""; ?>',
    base:    '<?php echo BASE_URL; ?>'
};
</script>
</body>
</html>
