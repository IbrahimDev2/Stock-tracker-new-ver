<?php
session_start();
include '../connection.php';
include '../include/header.php';
include '../include/function.php';


$error = '';     
$success = '';   



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
<<<<<<< Updated upstream
    // =============================================================================
    // GET AND SANITIZE INPUT DATA
    // =============================================================================

    /**
     * Extract and clean all form data
     * 
     * Key principles demonstrated:
     * - Always sanitize user input (prevent XSS attacks)
     * - Handle optional fields properly (category_id can be empty)
     * - Convert data to appropriate types (int, float)
     * - Use null for optional database fields
     */
=======
 
>>>>>>> Stashed changes
    $name = sanitize_input($_POST['name']);
    $sku = sanitize_input($_POST['sku']);
    $description = sanitize_input($_POST['description']);
    $category_id = !empty($_POST['category_id']) ? intval($_POST['category_id']) : null;
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);
    $min_stock_level = intval($_POST['min_stock_level']);

<<<<<<< Updated upstream
    // =============================================================================
    // INPUT VALIDATION
    // =============================================================================

    /**
     * Validate all input according to business rules
     * 
     * Validation principles:
     * - Check required fields first
     * - Validate data types and ranges
     * - Provide clear, user-friendly error messages
     * - Stop at first error (don't overwhelm user)
     */
=======
   
>>>>>>> Stashed changes
    if (empty($name)) {
        $error = 'Product name is required.';
    } elseif (empty($sku)) {
        $error = 'SKU is required.';
    } elseif ($price < 0) {
        $error = 'Price cannot be negative.';
    } elseif ($quantity < 0) {
        $error = 'Quantity cannot be negative.';
    } elseif ($min_stock_level < 0) {
        $error = 'Minimum stock level cannot be negative.';
    }


    if (add_product($conn, $name, $sku, $description, $category_id, $price, $quantity, $min_stock_level)) {
        $success = 'Product added successfully!';

<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
        // Clear form data on success (reset for next entry)
        // This is good UX - user can immediately add another product
=======

>>>>>>> Stashed changes
=======
        // Clear form data on success (reset for next entry)
        // This is good UX - user can immediately add another product
>>>>>>> Stashed changes
=======
        // Clear form data on success (reset for next entry)
        // This is good UX - user can immediately add another product
>>>>>>> Stashed changes
        $name = $sku = $description = '';
        $category_id = $price = $quantity = $min_stock_level = 0;
    } else {
        $error = 'Failed to add product. Please try again.';
    }
}
?>
<main>
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1>Add New Product</h1>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Products
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
                                        <label for="name" class="form-label">Product Name *</label>
                                        <input type="text" class="form-control" id="name" name="name"
                                            value="">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="sku" class="form-label">SKU *</label>
                                        <input type="text" class="form-control" id="sku" name="sku"
                                            value="">
                                        <div class="form-text">Stock Keeping Unit - must be unique</div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="category_id" class="form-label">Category</label>
                                        <select class="form-select" id="category_id" name="category_id">
                                            <option value="1">Select Category</option>
                                            <option value="2">Category</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="price" class="form-label">Price ($) *</label>
                                        <input type="number" class="form-control" id="price" name="price"
                                            step="0.01" min="0" value="">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="quantity" class="form-label">Initial Quantity *</label>
                                        <input type="number" class="form-control" id="quantity" name="quantity"
                                            min="0" value="">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="min_stock_level" class="form-label">Minimum Stock Level *</label>
                                        <input type="number" class="form-control" id="min_stock_level" name="min_stock_level"
                                            min="0" value="">
                                        <div class="form-text">Alert when stock falls below this level</div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="reset" class="btn btn-secondary">Reset</button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Add Product
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Quick Tips</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success"></i> Use descriptive product names</li>
                            <li><i class="fas fa-check text-success"></i> SKU should be unique and meaningful</li>
                            <li><i class="fas fa-check text-success"></i> Set appropriate minimum stock levels</li>
                            <li><i class="fas fa-check text-success"></i> Select the correct category</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php
include '../include/footer.php';
?>