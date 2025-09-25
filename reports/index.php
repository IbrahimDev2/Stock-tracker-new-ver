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
require_once('../connection.php');



require_once('../include/function.php');
require_once('../include/header.php');

// Get all products from the database using a function
$products = get_all_products($conn);
?>
<main>
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <!-- Page Title -->
                <h1>Product Report</h1>
                <!-- Buttons: Low stock report and print report -->
                <div>
                    <a href="low_stock.php" class="btn btn-warning me-2">
                        <i class="fas fa-exclamation-triangle"></i> Low Stock Report
                    </a>
                    <!-- Print report triggers browser print dialog -->
                    <button onclick="window.print()" class="btn btn-outline-primary">
                        <i class="fas fa-print"></i> Print Report
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row text-center">
                        <!-- Total Products -->
                        <div class="col-md-3">
                            <h4><?php echo count($products); ?></h4>
                            <p class="text-muted">Total Products</p>
                        </div>

                        <!-- Total Inventory Value -->
                        <div class="col-md-3">
                            <?php
                            $total_value = 0; // Initialize total value
                            foreach ($products as $product) {
                                // Add (price * quantity) for each product
                                $total_value += $product['st_price'] * $product['st_quantity'];
                            }
                            ?>
                            <h4>$<?php echo number_format($total_value, 2); ?></h4>
                            <p class="text-muted">Total Inventory Value</p>
                        </div>

                        <!-- Total Units -->
                        <div class="col-md-3">
                            <?php
                            $total_quantity = 0; // Initialize total quantity
                            foreach ($products as $product) {
                                // Add each product's quantity
                                $total_quantity += $product['st_quantity'];
                            }
                            ?>
                            <h4><?php echo number_format($total_quantity); ?></h4>
                            <p class="text-muted">Total Units</p>
                        </div>

                        <!-- Low Stock Items -->
                        <div class="col-md-3">
                            <?php
                            $low_stock_count = 0; // Initialize low stock count
                            foreach ($products as $product) {
                                // Check if product quantity is less than or equal to min stock
                                if ($product['st_quantity'] <= $product['st_min_stock_level']) {
                                    $low_stock_count++;
                                }
                            }
                            ?>
                            <!-- Show number of low stock items -->
                            <h4 class="<?php echo $low_stock_count > 0 ? 'text-danger' : 'text-success'; ?>">
                                <?php echo $low_stock_count; ?>
                            </h4>
                            <p class="text-muted">Low Stock Items</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Complete Product List Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Complete Product List</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($products)): ?>
                        <!-- No products found case -->
                        <div class="text-center py-4">
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <h4>No Products Found</h4>
                            <p class="text-muted">Add products to generate inventory reports.</p>
                            <a href="../products/add.php" class="btn btn-primary">Add Your First Product</a>
                        </div>
                    <?php else: ?>
                        <!-- Products table -->
                        <div class="table-responsive">
                            <table class="table table-striped table-sm">
                                <thead class="table-dark">
                                    <tr>
                                        <th>SKU</th>
                                        <th>Product Name</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Min Stock</th>
                                        <th>Value</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($products as $product): ?>
                                        <!-- Highlight row if product is at or below min stock -->
                                        <tr class="<?php echo $product['st_quantity'] <= $product['st_min_stock_level'] ? 'table-warning' : ''; ?>">
                                            <!-- SKU code -->
                                            <td><code><?php echo htmlspecialchars($product['st_p_sku']); ?></code></td>
                                            <!-- Product name -->
                                            <td><?php echo htmlspecialchars($product['st_p_name']); ?></td>
                                            <!-- Category name or default -->
                                            <td><?php echo htmlspecialchars($product['st_p_category_name'] ?? 'No Category'); ?></td>
                                            <!-- Price formatted -->
                                            <td>$<?php echo number_format($product['st_price'], 2); ?></td>
                                            <!-- Quantity -->
                                            <td><?php echo $product['st_quantity']; ?></td>
                                            <!-- Minimum stock level -->
                                            <td><?php echo $product['st_min_stock_level']; ?></td>
                                            <!-- Total value per product (price * quantity) -->
                                            <td>$<?php echo number_format($product['st_price'] * $product['st_quantity'], 2); ?></td>
                                            <!-- Stock status -->
                                            <td>
                                                <?php if ($product['st_quantity'] <= $product['st_min_stock_level']): ?>
                                                    <span class="badge bg-warning">Low Stock</span>
                                                <?php elseif ($product['st_quantity'] == 0): ?>
                                                    <span class="badge bg-danger">Out of Stock</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success">In Stock</span>
                                                <?php endif; ?>
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
<!-- Print-specific CSS rules -->
<style>
@media print {
    /* Hide buttons, navbar, and card headers while printing */
    .btn, .navbar, .card-header {
        display: none !important;
    }
    
    /* Remove borders and shadows for cleaner print */
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    
    /* Adjust table font size for print */
    .table {
        font-size: 12px;
    }
}
</style>

<?php // Include footer layout file
require_once '../include/footer.php'; ?>