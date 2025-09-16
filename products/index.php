<?php
session_start();
require_once '../connection.php';
require_once '../include/function.php';
require_once '../include/header.php';


$search = isset($_GET['search']) ? sanitize_input($_GET['search']) : '';
$category_id = isset($_GET['category']) ? intval($_GET['category']) : '';


<<<<<<< Updated upstream


=======
if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">'
        . htmlspecialchars($_SESSION['success']) .
        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
         </div>';
    unset($_SESSION['success']);
}

if (isset($_SESSION['deleted'])) {
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">'
        . htmlspecialchars($_SESSION['deleted']) .
        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
         </div>';
    unset($_SESSION['deleted']);
}
>>>>>>> Stashed changes


$products = get_all_products($conn, $search, $category_id);

?>
<main>
    <div class="container mt-4">

        <div class="row">
            <div class="col-12">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1>Products</h1>

                    <a href="add.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Product
                    </a>
                </div>
            </div>
        </div>



        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <form method="GET" class="row g-3">

                            <div class="col-md-6">
                                <label for="search" class="form-label">Search Products</label>
                                <input type="text" class="form-control" id="search" name="search"
                                    value="<?php echo htmlspecialchars($search); ?>"
                                    placeholder="Search by name, SKU, or description...">
                            </div>

                            <div class="col-md-4">
                                <label for="category" class="form-label">Category</label>
                                <select class="form-select" id="category" name="category">
                                    <option value="">All Categories</option>

                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>"
                                            <?php echo $category_id == $category['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-2">

                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Search
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>



        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <?php if (empty($products)): ?>

                            <div class="text-center py-4">
                                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                <h4>No Products Found</h4>
                                <p class="text-muted">No products match your search criteria.</p>
                                <a href="add.php" class="btn btn-primary">Add Your First Product</a>
                            </div>
                        <?php else: ?>

                            <div class="table-responsive">
                                <table class="table table-striped table-hover">

                                    <thead class="table-dark">
                                        <tr>
                                            <th>SKU</th>
                                            <th>Name</th>
                                            <th>Category</th>
                                            <th>Price</th>
                                            <th>Stock</th>
                                            <th>Min Stock</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php foreach ($products as $product): ?>
                                            <tr>

                                                <td><code><?php echo htmlspecialchars($product['st_p_sku']); ?></code></td>


                                                <td>
                                                    <strong><?php echo htmlspecialchars($product['st_p_name']); ?></strong>
                                                    <?php if ($product['st_p_description']): ?>
                                                        <br>
                                                        <small class="text-muted">
                                                            <?php echo htmlspecialchars(substr($product['st_p_description'], 0, 50)); ?>...
                                                        </small>
                                                    <?php endif; ?>
                                                </td>


                                                <td><?php echo htmlspecialchars($product['st_p_category_id'] ?? 'No Category'); ?></td>


                                                <td>$<?php echo number_format($product['st_price'], 2); ?></td>


                                                <td>
                                                    <span class="badge 
                                                    <?php echo $product['st_quantity'] <= $product['st_min_stock_level'] ? 'bg-danger' : 'bg-success'; ?>">
                                                        <?php echo $product['st_quantity']; ?>
                                                    </span>
                                                </td>


                                                <td><?php echo $product['st_min_stock_level']; ?></td>


                                                <td>
                                                    <?php if ($product['st_quantity'] <= $product['st_min_stock_level']): ?>
                                                        <span class="badge bg-warning">Low Stock</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-success">In Stock</span>
                                                    <?php endif; ?>
                                                </td>


                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <a href="edit.php?id=<?php echo $product['st_p_id']; ?>"
                                                            class="btn btn-outline-primary" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="delete.php?id=<?php echo $product['st_p_id']; ?>"
                                                            class="btn btn-outline-danger"
                                                            onclick="return confirm('Are you sure you want to delete this product?')"
                                                            title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
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