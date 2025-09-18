<?php
session_start();
require_once '../connection.php';
require_once '../include/function.php';
require_once '../include/header.php';

$error = '';
$success = '';

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
                <!-- Page Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1>Add New Category</h1>
                    <!-- Back button to category list -->
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Categories
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- ==========================
             Left Side: Add Category Form
        ========================== -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">

                        <!-- Display error message if validation/database failed -->
                        <?php if ($error): ?>
                            <?php echo display_error($error); ?>
                        <?php endif; ?>

                        <!-- Display success message if category added -->
                        <?php if ($success): ?>
                            <?php echo display_success($success); ?>
                        <?php endif; ?>

                        <!-- Category Add Form -->
                        <form method="POST">
                            <!-- Category Name -->
                            <div class="mb-3">
                                <label for="name" class="form-label">Category Name *</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="<?php echo htmlspecialchars($name ?? ''); ?>">
                            </div>

                            <!-- Description -->
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($description ?? ''); ?></textarea>
                            </div>

                            <!-- Form Buttons -->
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <!-- Reset button clears form -->
                                <button type="reset" class="btn btn-secondary">Reset</button>
                                <!-- Submit button adds category -->
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Add Category
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- ==========================
             Right Side: Category Guidelines
        ========================== -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Category Guidelines</h5>
                    </div>
                    <div class="card-body">
                        <!-- Helpful tips for adding categories -->
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success"></i> Use clear, descriptive names</li>
                            <li><i class="fas fa-check text-success"></i> Keep categories organized</li>
                            <li><i class="fas fa-check text-success"></i> Add helpful descriptions</li>
                            <li><i class="fas fa-check text-success"></i> Avoid duplicate names</li>
                        </ul>

                        <hr>

                        <!-- Example category names -->
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