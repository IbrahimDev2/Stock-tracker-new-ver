<?php
ob_start(); // sabse pehle
session_start();
require_once '../connection.php';  
require_once '../include/function.php'; 
require_once '../include/header.php';    


$error = '';
$success = '';

$categories = get_all_categories($conn);
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

$product = get_product_by_id($conn, $id);


if (!$product) {
    header('Location: index.php');
    exit;
}


// STEP 5: Form submit hone ke baad (POST request handle karna)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Input fields ko sanitize karna (security ke liye)
    $name = sanitize_input($_POST['name']);
    $sku = sanitize_input($_POST['sku']);
    $description = sanitize_input($_POST['description']);
    $category_id = !empty($_POST['category_id']) ? intval($_POST['category_id']) : null;
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);
    $min_stock_level = intval($_POST['min_stock_level']);

    // STEP 6: Validation (thinking: galat data to DB me nahi jana chahiye)
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
    } else {
        // STEP 7: Database update try karna
        try {
            if (update_product($conn, $id, $name, $sku, $description,  $category_id, $price, $quantity, $min_stock_level)) {
                $_SESSION['success'] = 'Product updated successfully!';
                // Data refresh kar lo taake form updated values dikha sake
                $product = get_product_by_id($conn, $id);
                header("Location: index.php");
            exit();
            } else {
                $error = 'Failed to update product. Please try again.';
            }
        } catch (PDOException $e) {
            // Duplicate SKU error handle karna (unique constraint)
            if (strpos($e->getMessage(), 'duplicate key') !== false || strpos($e->getMessage(), 'unique') !== false) {
                $error = 'SKU already exists. Please use a different SKU.';
            } else {
                $error = 'Failed to update product. Please try again.';
            }
        }
    }
}


?>
<main class="container mt-4">
<div class="container mt-4">
    <!-- Page Title + Back Button -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Edit Product</h1>
                <!-- Back button -->
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Products
                </a>
            </div>
        </div>
    </div>

    <!-- Main Row: Form (left) + Product Info (right) -->
    <div class="row">
        <!-- Left side = Product Edit Form -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">

                    <!-- Error message (if validation failed) -->
                    <?php if ($error): ?>
                        <?php echo display_error($error); ?>
                    <?php endif; ?>
                    
                    <!-- Success message (if update successful) -->
                    <?php if ($success): ?>
                        <?php echo display_success($success); ?>
                    <?php endif; ?>

                    <!-- Form to update product -->
                    <form method="POST">
                        <div class="row">
                            <!-- Product Name -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Product Name *</label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?php if (is_array($product) && isset($product['st_p_name'])) {
    echo htmlspecialchars($product['st_p_name']);
} ?>" required>
                                </div>
                            </div>
                            <!-- SKU -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sku" class="form-label">SKU *</label>
                                    <input type="number" class="form-control" id="sku" name="sku" 
                                           value="<?php echo htmlspecialchars($product['st_p_sku']); ?>" required>
                                    <div class="form-text">Stock Keeping Unit - must be unique</div>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($product['st_p_description']); ?></textarea>
                        </div>

                        <div class="row">
                            <!-- Category Dropdown -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category_id" class="form-label">Category</label>
                                  <select class="form-select" id="category_id" name="category_id">
                                        <option value="">Select Category</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo $category['st_ct_id']; ?>" 
                                                    <?php echo ($category_id ?? '') == $category['st_ct_id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($category['st_ct_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <!-- Price -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="price" class="form-label">Price ($) *</label>
                                    <input type="number" class="form-control" id="price" name="price" 
                                           step="0.01" min="0" value="<?php echo $product['st_price']; ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Quantity -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="quantity" class="form-label">Current Quantity *</label>
                                    <input type="number" class="form-control" id="quantity" name="quantity" 
                                           min="0" value="<?php echo $product['st_quantity']; ?>" required>
                                    <div class="form-text">Use stock movements for better tracking</div>
                                </div>
                            </div>
                            <!-- Minimum Stock -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="min_stock_level" class="form-label">Minimum Stock Level *</label>
                                    <input type="number" class="form-control" id="min_stock_level" name="min_stock_level" 
                                           min="0" value="<?php echo $product['st_min_stock_level']; ?>" required>
                                    <div class="form-text">Alert when stock falls below this level</div>
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="index.php" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Product
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        
    </div>
</div>
</main>
<?php require_once '../include/footer.php'; ?>
