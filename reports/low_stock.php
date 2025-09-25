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
include '../connection.php';
include '../include/function.php';
include '../include/header.php';
// Call function to fetch low stock products from database
$low_stock_products = get_low_stock_products($conn);
?>
<main>
<!-- Main container with margin-top -->
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <!-- Header with title and action buttons -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Low Stock Report</h1>
                <div>
                    <!-- Button linking back to full report -->
                    <a href="index.php" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-chart-bar"></i> Full Report
                    </a>
                    <!-- Print button to trigger window.print() -->
                    <button onclick="window.print()" class="btn btn-outline-primary">
                        <i class="fas fa-print"></i> Print Report
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert section showing warning for low stock -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-warning" role="alert">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Low Stock Alert!</strong> 
                The following products are at or below their minimum stock levels and need to be restocked.
            </div>
        </div>
    </div>

    <!-- Main report card -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <!-- Show total count of low stock products -->
                    <h5>Products Requiring Attention (<?php echo count($low_stock_products); ?> items)</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($low_stock_products)): ?>
                        <!-- If no low stock products found -->
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h4>All Products Well Stocked!</h4>
                            <p class="text-muted">No products are currently below their minimum stock levels.</p>
                            <a href="../products/" class="btn btn-primary">View All Products</a>
                        </div>
                    <?php else: ?>
                        <!-- Table with low stock products -->
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Priority</th>
                                        <th>SKU</th>
                                        <th>Product Name</th>
                                        <th>Category</th>
                                        <th>Current Stock</th>
                                        <th>Min Stock</th>
                                        <th>Shortage</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($low_stock_products as $product): ?>
                                        <?php
                                        // Calculate shortage = min stock - current stock
                                        $shortage = $product['st_min_stock_level'] - $product['st_quantity'];
                                        // Assign priority based on shortage level
                                        $priority = $product['st_quantity'] == 0 ? 'critical' : ($shortage > 10 ? 'high' : 'medium');
                                        ?>
                                        <!-- Apply row color based on stock availability -->
                                        <tr class="<?php echo $product['st_quantity'] == 0 ? 'table-danger' : 'table-warning'; ?>">
                                            <td>
                                                <!-- Display priority badge -->
                                                <?php if ($priority == 'critical'): ?>
                                                    <span class="badge bg-danger">Critical</span>
                                                <?php elseif ($priority == 'high'): ?>
                                                    <span class="badge bg-warning">High</span>
                                                <?php else: ?>
                                                    <span class="badge bg-info">Medium</span>
                                                <?php endif; ?>
                                            </td>
                                            <!-- Show product details -->
                                            <td><code><?php echo htmlspecialchars($product['st_p_sku']); ?></code></td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($product['st_p_name']); ?></strong>
                                            </td>
                                            <td><?php echo htmlspecialchars($product['category_name'] ?? 'No Category'); ?></td>
                                            <td>
                                                <!-- Current stock with badge -->
                                                <span class="badge <?php echo $product['st_quantity'] == 0 ? 'bg-danger' : 'bg-warning'; ?>">
                                                    <?php echo $product['st_quantity']; ?>
                                                </span>
                                            </td>
                                            <td><?php echo $product['st_min_stock_level']; ?></td>
                                            <td>
                                                <!-- Show shortage units (if any) -->
                                                <strong class="text-danger">
                                                    <?php echo $shortage > 0 ? $shortage : 0; ?> units
                                                </strong>
                                            </td>
                                            <td>
                                                <!-- Action buttons for stock and editing -->
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="../stock/add_movement.php?product_id=<?php echo $product['st_p_id']; ?>" 
                                                       class="btn btn-outline-success" title="Add Stock">
                                                        <i class="fas fa-plus"></i>
                                                    </a>
                                                    <a href="../products/edit.php?id=<?php echo $product['st_p_id']; ?>" 
                                                       class="btn btn-outline-primary" title="Edit Product">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Summary cards showing counts by priority -->
                        <div class="mt-3">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card text-center">
                                        <div class="card-body">
                                            <h5 class="card-title text-danger">Critical</h5>
                                            <!-- Count products with 0 quantity -->
                                            <h3><?php echo count(array_filter($low_stock_products, function($p) { return $p['st_quantity'] == 0; })); ?></h3>
                                            <p class="card-text">Out of Stock</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card text-center">
                                        <div class="card-body">
                                            <h5 class="card-title text-warning">High Priority</h5>
                                            <!-- Count products with shortage > 10 -->
                                            <h3><?php echo count(array_filter($low_stock_products, function($p) { 
                                                return $p['st_quantity'] > 0 && ($p['st_min_stock_level'] - $p['st_quantity']) > 10; 
                                            })); ?></h3>
                                            <p class="card-text">Shortage > 10 units</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card text-center">
                                        <div class="card-body">
                                            <h5 class="card-title text-info">Medium Priority</h5>
                                            <!-- Count products with shortage <= 10 -->
                                            <h3><?php echo count(array_filter($low_stock_products, function($p) { 
                                                return $p['st_quantity'] > 0 && ($p['st_min_stock_level'] - $p['st_quantity']) <= 10; 
                                            })); ?></h3>
                                            <p class="card-text">Low but manageable</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</main>
<!-- Print-specific styles -->
<style>
@media print {
    .btn, .navbar, .card-header {
        display: none !important; /* Hide buttons and nav during print */
    }
    
    .card {
        border: none !important;
        box-shadow: none !important; /* Remove extra styling */
    }
    
    .table {
        font-size: 12px; /* Make table text smaller for print */
    }
    
    .alert {
        border: 1px solid #ccc !important;
        background-color: #f8f9fa !important; /* Lighten alerts for print */
    }
}
</style>

<?php require_once '../include/footer.php'; ?>