<?php
session_start();

define('APP_INIT', true);

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    // If session does not exist, redirect to login page
    header("Location: /Stock-tracker-new-ver/index.php");
    exit();
}
require_once('connection.php');
require_once('include/function.php');
include 'include/header.php';


$total_products   = get_total_products($conn);
$low_stock_count  = get_low_stock_count($conn);
$total_categories = get_total_categories($conn);
$recent_movements = get_recent_movements($conn, 5);
?>

<main class="container mt-4">
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>


    <div class="container mt-4">
        <div class="row">

            <div class="col-12">
                <h1 class="mb-4">Inventory Management Dashboard</h1>
                <!-- Heading for the page -->
            </div>

        </div>
        <div class="row mb-4">
            <div class="col-md-3 d-flex">
                <div class="card bg-primary text-white flex-fill">
                <div class="card-body">
                    <h5 class="card-title">Total Products</h5>
                   <h2><?php echo $total_products; ?></h2>
                </div>
                </div>
            </div>

            <div class="col-md-3 d-flex">
                <div class="card bg-warning text-white flex-fill">
                <div class="card-body">
                    <h5 class="card-title">Low Stock Items</h5>
                    <h2><?php echo $low_stock_count; ?></h2>
                </div>
                </div>
            </div>

            <div class="col-md-3 d-flex">
                <div class="card bg-success text-white flex-fill">
                <div class="card-body">
                    <h5 class="card-title">Categories</h5>
                     <h2><?php echo $total_categories; ?></h2>
                </div>
                </div>
            </div>

            <div class="col-md-3 d-flex">
                <div class="card bg-info text-white flex-fill">
                    <div class="card-body">
                        <h5 class="card-title">Quick Actions</h5>
                        <a href="products/add.php" class="btn btn-light btn-sm">Add Product</a>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- =================== RECENT STOCK MOVEMENTS (TABLE) =================== -->
    <div class="row">
        <div class="col-12">
            <div class="card">

                <!-- Card Header -->
                <div class="card-header">
                    <h5>Recent Stock Movements</h5>
                </div>

                <!-- Card Body -->
                <div class="card-body">
                    <?php if (empty($recent_movements)): ?>
                        <!-- If no data available -->
                        <p class="text-muted">No recent stock movements found.</p>
                    <?php else: ?>

                        <div class="table-responsive">


                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Type</th>
                                        <th>Quantity</th>
                                        <th>Date</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_movements as $movement): ?>
                                        <tr>
                                            <!-- Product Name -->
                                            <td><?php echo htmlspecialchars($movement['product_name']); ?></td>

                                            <!-- Type: IN or OUT -->
                                            <td style="color:black;">
                                                <!-- Badge color depends on type -->
                                                <span class="badge 
                                                    <?php echo $movement['st_mt_movement_type'] == 'in' 
                                                        ? 'badge-success' 
                                                        : 'badge-danger'; ?>" style="background-color: <?php echo $movement['st_mt_movement_type'] == 'in' ? '#28a745' : '#dc3545'; ?>; color: white; padding: 5px 10px; border-radius: 5px;">
                                                    <?php echo ucfirst($movement['st_mt_movement_type']); ?>
                                                </span>
                                            </td>

                                            <!-- Quantity -->
                                            <td><?php echo $movement['st_mt_quantity']; ?></td>

                                            <!-- Date formatted -->
                                            <td><?php echo date('M d, Y H:i', strtotime($movement['st_mt_created_at'])); ?></td>

                                            <!-- Notes -->
                                            <td><?php echo htmlspecialchars($movement['st_mt_notes'] ?? ''); ?></td>
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
</main>

<?php
include 'include/footer.php';
?>