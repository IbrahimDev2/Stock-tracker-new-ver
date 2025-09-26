<?php
if (!defined('APP_INIT')) {
    exit("No direct access allowed");
}

/**
 * Sanitize user input for safe usage.
 */
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Display an error message in a styled alert box.
 */
function display_error($message) {
    return "<div class='alert alert-danger' role='alert'>" . htmlspecialchars($message) . "</div>";
}

/**
 * Display a success message in a styled alert box.
 */
function display_success($message) {
    return "<div class='alert alert-success' role='alert'>" . htmlspecialchars($message) . "</div>";
}

/**
 * Add a new product to the database.
 */
function add_product($conn, $name, $sku, $description, $category_id, $price, $quantity, $min_stock_level) {
    $stmt = $conn->prepare("INSERT INTO products (st_p_name, st_p_sku, st_p_description, st_p_category_id, st_price, st_quantity, st_min_stock_level) 
                           VALUES (?, ?, ?, ?, ?, ?, ?)");
    return $stmt->execute([$name, $sku, $description, $category_id, $price, $quantity, $min_stock_level]);
}

/**
 * Retrieve all products with optional search and category filtering.
 */
function get_all_products($conn, $search = '', $category_id = '') {
    $query = "SELECT p.*, c.st_ct_name as category_name FROM products p 
              LEFT JOIN categories c ON p.st_p_category_id = c.st_ct_id";
    $conditions = [];
    $params = [];
    if (!empty($search)) {
        global $conn;
        $search_operator = (isset($conn) && $conn instanceof PDO && strpos($conn->getAttribute(PDO::ATTR_DRIVER_NAME), 'pgsql') !== false) ? 'ILIKE' : 'LIKE';
        $conditions[] = "(p.st_p_name $search_operator ? OR p.st_p_sku $search_operator ? OR p.st_P_description $search_operator ?)";
        $search_param = '%' . $search . '%';
        $params = array_merge($params, [$search_param, $search_param, $search_param]);
    }
    if (!empty($category_id)) {
        $conditions[] = "p.st_p_category_id = ?";
        $params[] = $category_id;
    }
    if (!empty($conditions)) {
        $query .= " WHERE " . implode(' AND ', $conditions);
    }
    $query .= " ORDER BY p.st_p_name ASC";
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $result = $stmt->get_result();
    $data = $result->fetch_all(MYSQLI_ASSOC);
    return $data;
}

/**
 * Retrieve a single product by its ID.
 */
function get_product_by_id($conn, $id) {
    $stmt = $conn->prepare("SELECT p.* FROM products p WHERE p.st_p_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

/**
 * Update product details.
 */
function update_product($conn, $id, $name, $sku, $description,  $category_id, $price, $quantity, $min_stock_level) {
    $stmt = $conn->prepare("UPDATE products SET st_p_name = ?, st_p_sku = ?, st_p_description = ?, st_p_category_id = ?, st_price = ?, 
                           st_quantity = ?, st_min_stock_level = ? WHERE st_p_id = ?");
    return $stmt->execute([$name, $sku, $description, $category_id, $price, $quantity, $min_stock_level, $id]);
}

/**
 * Delete a product by its ID.
 */
function delete_product($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM products WHERE st_p_id = ?");
    return $stmt->execute([$id]);
}

/**
 * Retrieve all categories.
 */
function get_all_categories($conn) {
    $stmt = $conn->prepare("SELECT * FROM categories ORDER BY st_ct_name ASC");
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Add a new category.
 */
function add_category($conn, $name, $description) {
    $stmt = $conn->prepare("INSERT INTO categories (st_ct_name, st_ct_description) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $description);
    return $stmt->execute();
}

/**
 * Retrieve a category by its ID.
 */
function get_category_by_id($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM categories WHERE st_ct_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

/**
 * Update category details.
 */
function update_category($conn, $id, $name, $description) {
    $stmt = $conn->prepare("UPDATE categories SET st_ct_name = ?, st_ct_description = ? WHERE st_ct_id = ?");
    $stmt->bind_param("ssi", $name, $description, $id);
    return $stmt->execute();
}

/**
 * Delete a category by its ID.
 */
function delete_category($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM categories WHERE st_ct_id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

/**
 * Add a stock movement and update product quantity accordingly.
 */
function add_stock_movement($conn, $product_id, $movement_type, $quantity, $notes = '') {
    try {
        $conn->autocommit(FALSE);
        $stmt = $conn->prepare("
            INSERT INTO stock_movements (st_mt_product_id, st_mt_movement_type, st_mt_quantity, st_mt_notes) 
            VALUES (?, ?, ?, ?)
        ");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->execute([$product_id, $movement_type, $quantity, $notes]);
        $multiplier = ($movement_type == 'in') ? 1 : -1;
        $quantity_change = $quantity * $multiplier;
        $update_stmt = $conn->prepare("
            UPDATE products 
            SET st_quantity = st_quantity + ? 
            WHERE st_p_id = ?
        ");
        $update_stmt->execute([$quantity_change, $product_id]);
        $conn->commit();
        return true;
    } catch (Exception $e) {
        $conn->rollback();
        die("Error in add_stock_movement: " . $e->getMessage());
    }
}

/**
 * Retrieve stock movement records, optionally limited by count.
 */
function get_stock_movements($conn, $limit = null) {
    $query = "SELECT sm.*, p.st_p_name as product_name FROM stock_movements sm 
              JOIN products p ON sm.st_mt_product_id = p.st_p_id 
              ORDER BY sm.st_mt_created_at DESC";
    if ($limit) {
        $query .= " LIMIT " . intval($limit);
    }
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get the count of products with low stock.
 */
function get_low_stock_count($conn) {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM products WHERE st_quantity <= st_min_stock_level");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['count'];
}

/**
 * Retrieve all products with low stock.
 */
function get_low_stock_products($conn) {
    $stmt = $conn->prepare("SELECT p.*, c.st_ct_name as category_name FROM products p 
                           LEFT JOIN categories c ON p.st_p_category_id = c.st_ct_id 
                           WHERE p.st_quantity <= p.st_min_stock_level 
                           ORDER BY p.st_quantity ASC");
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get the total number of products.
 */
function get_total_products($conn) {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM products");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['count'];
}

/**
 * Get the total number of categories.
 */
function get_total_categories($conn) {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM categories");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['count'];
}

/**
 * Retrieve recent stock movements.
 */
function get_recent_movements($conn, $limit = 10) {
    return get_stock_movements($conn, $limit);
}
