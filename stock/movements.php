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

$movements = get_stock_movements($conn);
?>
<!-- Start of HTML layout -->
<main>
<div class="container mt-4"> <!-- container class centers and adds margin-top -->
    <div class="row"> <!-- row for bootstrap grid -->
        <div class="col-12"> <!-- full-width column -->
            <div class="d-flex justify-content-between align-items-center mb-4"> <!-- flex container with space between items -->
                <h1>Stock Movements</h1> <!-- Page Title -->
                <a href="add_movement.php" class="btn btn-primary"> <!-- Link styled as button -->
                    <i class="fas fa-plus"></i> <!-- plus icon from font-awesome -->
                    Add Stock Movement <!-- button text -->
                </a>
            </div>
        </div>
    </div>

    <div class="row"> <!-- another row -->
        <div class="col-12"> <!-- full width column -->
            <div class="card"> <!-- bootstrap card -->
                <div class="card-body"> <!-- card body where content goes -->
                    <?php if (empty($movements)): ?> <!-- If no movements exist -->
                        <div class="text-center py-4"> <!-- center aligned, padding-y=4 -->
                            <i class="fas fa-exchange-alt fa-3x text-muted mb-3"></i> <!-- icon of exchange arrows, size 3x -->
                            <h4>No Stock Movements Found</h4> <!-- message -->
                            <p class="text-muted">Start tracking your inventory by adding stock movements.</p> <!-- instructions -->
                            <a href="add_movement.php" class="btn btn-primary">Add Your First Movement</a> <!-- button link -->
                        </div>
                    <?php else: ?> <!-- Else if we have movements -->
                        <div class="table-responsive"> <!-- Responsive table wrapper -->
                            <table class="table table-striped table-hover"> <!-- table with stripes and hover effect -->
                                <thead class="table-dark"> <!-- table header dark background -->
                                    <tr> <!-- table header row -->
                                        <th>Date</th> <!-- Column: Date -->
                                        <th>Product</th> <!-- Column: Product -->
                                        <th>Type</th> <!-- Column: Movement type (in/out) -->
                                        <th>Quantity</th> <!-- Column: Quantity -->
                                        <th>Notes</th> <!-- Column: Notes -->
                                    </tr>
                                </thead>
                                <tbody> <!-- Table body -->
                                    <?php foreach ($movements as $movement): ?> <!-- Loop through each stock movement record -->
                                        <tr> <!-- Row for each movement -->
                                            <td>
                                                <?php // format the created_at date from database into readable form
                                                echo date('M d, Y H:i', strtotime($movement['st_mt_created_at'])); ?>
                                            </td>
                                            <td>
                                                <strong>
                                                    <?php // print product name safely with htmlspecialchars
                                                    echo htmlspecialchars($movement['st_mt_product_id']); ?>
                                                </strong>
                                            </td>
                                            <td>
                                                <?php if ($movement['st_mt_movement_type'] == 'in'): ?> <!-- If movement is stock in -->
                                                    <span class="badge bg-success"> <!-- green badge -->
                                                        <i class="fas fa-arrow-up"></i> Stock In <!-- up arrow with text -->
                                                    </span>
                                                <?php else: ?> <!-- Else it's stock out -->
                                                    <span class="badge bg-danger"> <!-- red badge -->
                                                        <i class="fas fa-arrow-down"></i> Stock Out <!-- down arrow with text -->
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong>
                                                    <?php // display the quantity of movement
                                                    echo $movement['st_mt_quantity']; ?>
                                                </strong>
                                            </td>
                                            <td>
                                                <?php // display notes if available, else empty string
                                                echo htmlspecialchars($movement['st_mt_notes'] ?? ''); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?> <!-- end foreach -->
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
<?php // include footer file (closing html, scripts, etc.)
require_once '../include/footer.php';

?>
