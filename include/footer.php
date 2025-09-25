<footer class="footer text-light text-center py-3 mt-4">
    <div class="container">
        &copy; <?= date('Y'); ?> My Dashboard. All rights reserved.
    </div>
</footer>
  <!-- Bootstrap JavaScript (for responsive components like navbar, modals, etc.) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <?php
    // -----------------------------
    // Dynamically calculate base path for assets
    // -----------------------------

    // Get the current directory path of the script
    $current_dir = dirname($_SERVER['PHP_SELF']);

    // Count how many slashes are in the path to find depth level
    $depth = substr_count($current_dir, '/') - 1;

    // Generate "../" for each level up if depth > 0
    $base_path = str_repeat('../', max(0, $depth));

    // If we are in root folder, base path should be "./"
    if ($depth <= 0) $base_path = './';
    ?>

    <!-- Link to custom JavaScript file (path will adjust dynamically) -->
    <script src="<?= $base_path ?>js/main.js"></script>
</body>
</html>
