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

include '../connection.php';
include '../include/function.php';
include '../include/header.php';

// Retrieve products with low stock from the database
$low_stock_products = get_low_stock_products($conn);
?>
<main>
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Low Stock Report</h1>
                <div>
                    <a href="index.php" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-chart-bar"></i> Full Report
                    </a>
                    <button onclick="window.print()" class="btn btn-outline-primary">
                        <i class="fas fa-print"></i> Print Report
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-warning" role="alert">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Low Stock Alert!</strong> 
                The following products are at or below their minimum stock levels and need to be restocked.
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Products Requiring Attention (<?php echo count($low_stock_products); ?> items)</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($low_stock_products)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h4>All Products Well Stocked!</h4>
                            <p class="text-muted">No products are currently below their minimum stock levels.</p>
                            <a href="../products/" class="btn btn-primary">View All Products</a>
                        </div>
                    <?php else: ?>
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
                                        $shortage = $product['st_min_stock_level'] - $product['st_quantity'];
                                        $priority = $product['st_quantity'] == 0 ? 'critical' : ($shortage > 10 ? 'high' : 'medium');
                                        ?>
                                        <tr class="<?php echo $product['st_quantity'] == 0 ? 'table-danger' : 'table-warning'; ?>">
                                            <td>
                                                <?php if ($priority == 'critical'): ?>
                                                    <span class="badge bg-danger">Critical</span>
                                                <?php elseif ($priority == 'high'): ?>
                                                    <span class="badge bg-warning">High</span>
                                                <?php else: ?>
                                                    <span class="badge bg-info">Medium</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><code><?php echo htmlspecialchars($product['st_p_sku']); ?></code></td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($product['st_p_name']); ?></strong>
                                            </td>
                                            <td><?php echo htmlspecialchars($product['category_name'] ?? 'No Category'); ?></td>
                                            <td>
                                                <span class="badge <?php echo $product['st_quantity'] == 0 ? 'bg-danger' : 'bg-warning'; ?>">
                                                    <?php echo $product['st_quantity']; ?>
                                                </span>
                                            </td>
                                            <td><?php echo $product['st_min_stock_level']; ?></td>
                                            <td>
                                                <strong class="text-danger">
                                                    <?php echo $shortage > 0 ? $shortage : 0; ?> units
                                                </strong>
                                            </td>
                                            <td>
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

                        <div class="mt-3">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card text-center">
                                        <div class="card-body">
                                            <h5 class="card-title text-danger">Critical</h5>
                                            <h3><?php echo count(array_filter($low_stock_products, function($p) { return $p['st_quantity'] == 0; })); ?></h3>
                                            <p class="card-text">Out of Stock</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card text-center">
                                        <div class="card-body">
                                            <h5 class="card-title text-warning">High Priority</h5>
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
<style>
@media print {
    .btn, .navbar, .card-header {
        display: none !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    .table {
        font-size: 12px;
    }
    .alert {
        border: 1px solid #ccc !important;
        background-color: #f8f9fa !important;
    }
}
</style>

<?php require_once '../include/footer.php'; ?>