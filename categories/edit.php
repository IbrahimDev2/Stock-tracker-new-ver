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

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Validate category ID
if ($id <= 0) {
    header('Location: index.php');
    exit;
}
$category = get_category_by_id($conn, $id);

// Redirect if category does not exist
if (!$category) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize_input($_POST['name']);
    $description = sanitize_input($_POST['description']);

    // Validate form input
    if (empty($name)) {
        $error = 'Category name is required.';
    } else {
        try {
            if (update_category($conn, $id, $name, $description)) {
                $success = 'Category updated successfully!';
                $category = get_category_by_id($conn, $id);
            } else {
                $error = 'Failed to update category. Please try again.';
            }
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'duplicate key') !== false || 
                strpos($e->getMessage(), 'unique') !== false) {
                $error = 'Category name already exists. Please use a different name.';
            } else {
                $error = 'Failed to update category. Please try again.';
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
                <h1>Edit Category</h1>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Categories
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Edit form section -->
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
                                   value="<?php echo htmlspecialchars($category['st_ct_name']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($category['st_ct_description']); ?></textarea>
                        </div>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="index.php" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Category
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Category info section -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5>Category Info</h5>
                </div>
                <div class="card-body">
                    <?php
                    $count_stmt = $conn->prepare("SELECT COUNT(*) as count FROM products WHERE st_p_category_id = ?");
                    $count_stmt->bind_param("i", $category['st_ct_id']);
                    $count_stmt->execute();
                    $count_row = $count_stmt->get_result()->fetch_assoc();
                    $product_count = $count_row['count'];
                    ?>
                    <p><strong>Products in this category:</strong> <?php echo $product_count; ?></p>
                    <p><strong>Created:</strong> <?php echo date('M d, Y', strtotime($category['st_ct_created_at'])); ?></p>
                    <p><strong>Last Updated:</strong> <?php echo date('M d, Y', strtotime($category['st_ct_updated_at'])); ?></p>
                    <?php if ($product_count > 0): ?>
                        <hr>
                        <div class="d-grid">
                            <a href="../products/index.php?category=<?php echo $category['st_ct_id']; ?>" class="btn btn-outline-primary">
                                <i class="fas fa-box"></i> View Products in Category
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>
<?php require_once '../include/footer.php'; ?>

