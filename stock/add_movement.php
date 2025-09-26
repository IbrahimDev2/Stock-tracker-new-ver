<?php
session_start();
if (!defined('APP_INIT')) {
define('APP_INIT', true);
}
if (!isset($_SESSION['email'])) {
    header("Location: /Stock-tracker-new-ver/index.php");
    exit();
}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('../connection.php');
require_once('../include/function.php');
require_once('../include/header.php');

$error = '';
$success = '';

$selected_product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

$products = get_all_products($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = intval($_POST['product_id']);
    $movement_type = sanitize_input($_POST['movement_type']);
    $quantity = intval($_POST['quantity']);
    $notes = sanitize_input($_POST['notes']);

    if ($product_id <= 0) {
        $error = 'Please select a product.';
    } elseif (!in_array($movement_type, ['in', 'out'])) {
        $error = 'Invalid movement type.';
    } elseif ($quantity <= 0) {
        $error = 'Quantity must be greater than 0.';
    } else {
        if ($movement_type == 'out') {
            $product = get_product_by_id($conn, $product_id);
            if ($product && $quantity > $product['st_quantity']) {
                $error = 'Cannot remove more stock than available. Current stock: ' . $product['st_quantity'];
            }
        }
        if (empty($error)) {
            if (add_stock_movement($conn, $product_id, $movement_type, $quantity, $notes)) {
                $success = 'Stock movement added successfully!';
                $product_id = $quantity = 0;
                $movement_type = $notes = '';
            } else {
                $error = 'Failed to add stock movement. Please try again.';
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
                <h1>Add Stock Movement</h1>
                <a href="movements.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Movements
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <?php if ($error): ?>
                        <?php echo display_error($error); ?>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <?php echo display_success($success); ?>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="product_id" class="form-label">Product *</label>
                                    <select class="form-select" id="product_id" name="product_id" required onchange="updateProductInfo()">
                                        <option value="select_product">Select Product</option>
                                        <?php foreach ($products as $product): ?>
                                            <option value="<?php echo $product['st_p_id']; ?>" 
                                                    data-quantity="<?php echo $product['st_quantity']; ?>"
                                                    data-name="<?php echo htmlspecialchars($product['st_p_name']); ?>"
                                                    <?php echo ($selected_product_id == $product['st_p_id'] || ($product_id ?? '') == $product['st_p_id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($product['st_p_name'] . ' (' . $product['st_p_sku'] . ')'); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="movement_type" class="form-label">Movement Type *</label>
                                    <select class="form-select" id="movement_type" name="movement_type" required>
                                        <option value="">Select Type</option>
                                        <option value="in" <?php echo ($movement_type ?? '') == 'in' ? 'selected' : ''; ?>>Stock In</option>
                                        <option value="out" <?php echo ($movement_type ?? '') == 'out' ? 'selected' : ''; ?>>Stock Out</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="quantity" class="form-label">Quantity *</label>
                                    <input type="number" class="form-control" id="quantity" name="quantity" 
                                           min="1" value="<?php echo $quantity ?? ''; ?>" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Current Stock</label>
                                    <div class="form-control-plaintext" id="current-stock">
                                        Select a product to see current stock
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" 
                                      placeholder="Optional notes about this movement..."><?php echo htmlspecialchars($notes ?? ''); ?></textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="reset" class="btn btn-secondary">Reset</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Add Movement
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5>Movement Types</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-success">
                            <i class="fas fa-arrow-up"></i> Stock In
                        </h6>
                        <p class="small text-muted">
                            Increases inventory quantity. Use for:
                        </p>
                        <ul class="small">
                            <li>New purchases</li>
                            <li>Returns from customers</li>
                            <li>Production completion</li>
                        </ul>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="text-danger">
                            <i class="fas fa-arrow-down"></i> Stock Out
                        </h6>
                        <p class="small text-muted">
                            Decreases inventory quantity. Use for:
                        </p>
                        <ul class="small">
                            <li>Sales to customers</li>
                            <li>Damaged goods</li>
                            <li>Returns to suppliers</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</main>
<script>
function updateProductInfo() {
    const select = document.getElementById('product_id');
    const currentStock = document.getElementById('current-stock');
    if (select.value) {
        const selectedOption = select.options[select.selectedIndex];
        const quantity = selectedOption.getAttribute('data-quantity');
        const name = selectedOption.getAttribute('data-name');
        currentStock.innerHTML = `<strong>${quantity}</strong> units`;
        const movementType = document.getElementById('movement_type');
        const quantityInput = document.getElementById('quantity');
        if (movementType.value === 'out') {
            quantityInput.max = quantity;
        } else {
            quantityInput.removeAttribute('max');
        }
    } else {
        currentStock.innerHTML = 'Select a product to see current stock';
    }
}

document.getElementById('movement_type').addEventListener('change', function() {
    updateProductInfo();
});

document.addEventListener('DOMContentLoaded', function() {
    updateProductInfo();
});
</script>

<?php require_once '../include/footer.php'; ?>
