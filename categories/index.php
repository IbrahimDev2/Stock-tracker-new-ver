<?php
session_start();
if (!defined('APP_INIT')) {
    define('APP_INIT', true);
}

// Redirect to login if user session is not set
if (!isset($_SESSION['email'])) {
    header("Location: /Stock-tracker-new-ver/index.php");
    exit();
}

require_once '../connection.php';
require_once '../include/function.php';
require_once '../include/header.php';

// Fetch all categories from the database
$categories = get_all_categories($conn);
?>
<main>
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1>Categories</h1>
                    <a href="add.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Category
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <?php if (empty($categories)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                                <h4>No Categories Found</h4>
                                <p class="text-muted">Start by creating your first product category.</p>
                                <a href="add.php" class="btn btn-primary">Add Your First Category</a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Name</th>
                                            <th>Description</th>
                                            <th>Products Count</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($categories as $category): ?>
                                            <?php
                                            // Get product count for each category
                                            $count_stmt = $conn->prepare("SELECT COUNT(*) as count FROM products WHERE st_p_category_id = ?");
                                            $count_stmt->bind_param("i", $category['st_ct_id']);
                                            $count_stmt->execute();
                                            $result = $count_stmt->get_result();
                                            $count_row = $result->fetch_assoc();
                                            $product_count = $count_row['count'] ?? 0;
                                            ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($category['st_ct_name']); ?></strong>
                                                </td>
                                                <td><?php echo htmlspecialchars($category['st_ct_description'] ?? ''); ?></td>
                                                <td>
                                                    <span class="badge bg-info"><?php echo $product_count; ?> products</span>
                                                </td>
                                                <td><?php echo date('M d, Y', strtotime($category['st_ct_created_at'])); ?></td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <a href="edit.php?id=<?php echo $category['st_ct_id']; ?>" class="btn btn-outline-primary" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <?php if ($product_count == 0): ?>
                                                            <a href="delete.php?id=<?php echo $category['st_ct_id']; ?>" class="btn btn-outline-danger"
                                                            onclick="return confirm('Are you sure you want to delete this category?')" title="Delete">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        <?php else: ?>
                                                            <button class="btn btn-outline-secondary" disabled title="Cannot delete - has products">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php
require_once '../include/footer.php';
?>