<?php
session_start();
if (!defined('APP_INIT')) {
define('APP_INIT', true);
}
// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    // If session does not exist, redirect to login page
    header("Location: /Stock-tracker-new-ver/index.php");
    exit();
}
require_once '../connection.php';
require_once '../include/function.php';
require_once '../include/header.php';

$error = '';   
$success = '';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    // If no valid ID provided, redirect back to index
    header('Location: index.php');
    exit;
}
$category = get_category_by_id($conn, $id);

// If category doesn't exist, redirect back
if (!$category) {
    header('Location: index.php');
    exit;
}



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize user input to avoid XSS/SQL injection
    $name = sanitize_input($_POST['name']);
    $description = sanitize_input($_POST['description']);

    // ----------------------------
    // Step 6: Validate Form Input
    // ----------------------------
    if (empty($name)) {
        $error = 'Category name is required.';
    } else {
        // ----------------------------
        // Step 7: Try Updating Category
        // ----------------------------
        try {
            if (update_category($conn, $id, $name, $description)) {
                // Success → update confirmation
                $success = 'Category updated successfully!';
                // Refresh category data from DB
                $category = get_category_by_id($conn, $id);
            } else {
                // DB update failed
                $error = 'Failed to update category. Please try again.';
            }
        } catch (PDOException $e) {
            // Handle duplicate name or DB errors
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
            <!-- Page Heading + Back Button -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Edit Category</h1>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Categories
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- ============================
             Left Side: Edit Form
        =============================== -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">

                    <!-- Display error message if any -->
                    <?php if ($error): ?>
                        <?php echo display_error($error); ?>
                    <?php endif; ?>
                    
                    <!-- Display success message if update succeeded -->
                    <?php if ($success): ?>
                        <?php echo display_success($success); ?>
                    <?php endif; ?>

                    <!-- Form for editing category -->
                    <form method="POST">
                        <!-- Category Name (Required) -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Category Name *</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?php echo htmlspecialchars($category['st_ct_name']); ?>" required>
                        </div>

                        <!-- Category Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($category['st_ct_description']); ?></textarea>
                        </div>

                        <!-- Action Buttons (Cancel + Update) -->
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
        
        <!-- ============================
             Right Side: Category Info
        =============================== -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5>Category Info</h5>
                </div>
                <div class="card-body">
                    <?php
                    // Get number of products in this category
                    $count_stmt = $conn->prepare("SELECT COUNT(*) as count FROM products WHERE st_p_category_id = ?");
                    $count_stmt->bind_param("i", $category['st_ct_id']);
                    $count_stmt->execute();
                    $count_row = $count_stmt->get_result()->fetch_assoc();
                    $product_count = $count_row['count'];
                    ?>
                    
                    <!-- Show category statistics -->
                    <p><strong>Products in this category:</strong> <?php echo $product_count; ?></p>
                    <p><strong>Created:</strong> <?php echo date('M d, Y', strtotime($category['st_ct_created_at'])); ?></p>
                    <p><strong>Last Updated:</strong> <?php echo date('M d, Y', strtotime($category['st_ct_updated_at'])); ?></p>
                    
                    <!-- If products exist, show a button to view them -->
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

