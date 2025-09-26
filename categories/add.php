<?php
session_start();
if (!defined('APP_INIT')) {
    define('APP_INIT', true);
}

// User authentication check
if (!isset($_SESSION['email'])) {
    header("Location: /Stock-tracker-new-ver/index.php");
    exit();
}

require_once '../connection.php';
require_once '../include/function.php';
require_once '../include/header.php';

$error = '';
$success = '';

// Handle category creation form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize_input($_POST['name']);
    $description = sanitize_input($_POST['description']);

    if (empty($name)) {
        $error = 'Category name is required.';
    } else {
        try {
            if (add_category($conn, $name, $description)) {
                $success = 'Category added successfully!';
                $name = $description = '';
            } else {
                $error = 'Failed to add category. Please try again.';
            }
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'duplicate key') !== false || strpos($e->getMessage(), 'unique') !== false) {
                $error = 'Category name already exists. Please use a different name.';
            } else {
                $error = 'Failed to add category. Please try again.';
            }
        }
    }
}
?>
<main>
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1>Add New Category</h1>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Categories
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Add Category Form -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <?php if ($error): ?>
                            <?php echo display_error($error); ?>
                        <?php endif; ?>
                        <?php if ($success): ?>
                            <?php echo display_success($success); ?>
                        <?php endif; ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label for="name" class="form-label">Category Name *</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="<?php echo htmlspecialchars($name ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($description ?? ''); ?></textarea>
                            </div>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="reset" class="btn btn-secondary">Reset</button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Add Category
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Category Guidelines Section -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Category Guidelines</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success"></i> Use clear, descriptive names</li>
                            <li><i class="fas fa-check text-success"></i> Keep categories organized</li>
                            <li><i class="fas fa-check text-success"></i> Add helpful descriptions</li>
                            <li><i class="fas fa-check text-success"></i> Avoid duplicate names</li>
                        </ul>
                        <hr>
                        <h6>Examples:</h6>
                        <ul class="list-unstyled small text-muted">
                            <li>• Electronics</li>
                            <li>• Office Supplies</li>
                            <li>• Warehouse Equipment</li>
                            <li>• Consumables</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php require_once '../include/footer.php'; ?>