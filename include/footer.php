<footer class="footer text-light text-center py-3 mt-4">
    <div class="container">
        &copy; <?= date('Y'); ?> My Dashboard. All rights reserved.
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<?php
// Calculate base path for asset linking based on script location
$current_dir = dirname($_SERVER['PHP_SELF']);
$depth = substr_count($current_dir, '/') - 1;
$base_path = str_repeat('../', max(0, $depth));
if ($depth <= 0) $base_path = './';
?>

<script src="<?= $base_path ?>js/main.js"></script>
</body>
</html>
