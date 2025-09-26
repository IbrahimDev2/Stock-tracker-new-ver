<?php
session_start();
if (!defined('APP_INIT')) {
define('APP_INIT', true);
}

if (!isset($_SESSION['email'])) {
    header("Location: /Stock-tracker-new-ver/index.php");
    exit();
}
include '../connection.php';
include '../include/function.php';
include '../include/header.php';

$movements = get_stock_movements($conn);
?>

<main>
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Stock Movements</h1>
                <a href="add_movement.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Add Stock Movement
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <?php if (empty($movements)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-exchange-alt fa-3x text-muted mb-3"></i>
                            <h4>No Stock Movements Found</h4>
                            <p class="text-muted">Start tracking your inventory by adding stock movements.</p>
                            <a href="add_movement.php" class="btn btn-primary">Add Your First Movement</a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Date</th>
                                        <th>Product</th>
                                        <th>Type</th>
                                        <th>Quantity</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($movements as $movement): ?>
                                        <tr>
                                            <td>
                                                <?php
                                                echo date('M d, Y H:i', strtotime($movement['st_mt_created_at'])); ?>
                                            </td>
                                            <td>
                                                <strong>
                                                    <?php
                                                    echo htmlspecialchars($movement['st_mt_product_id']); ?>
                                                </strong>
                                            </td>
                                            <td>
                                                <?php if ($movement['st_mt_movement_type'] == 'in'): ?>
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-arrow-up"></i> Stock In
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-arrow-down"></i> Stock Out
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong>
                                                    <?php
                                                    echo $movement['st_mt_quantity']; ?>
                                                </strong>
                                            </td>
                                            <td>
                                                <?php
                                                echo htmlspecialchars($movement['st_mt_notes'] ?? ''); ?>
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
