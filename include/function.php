<?php
// =============================================================================
// UTILITY FUNCTIONS
// =============================================================================

/**
 * sanitize_input
 * Clean user input: trim spaces, remove slashes, convert special chars
 */
function sanitize_input($data)
{
    return htmlspecialchars(stripslashes(trim($data)));
}

/**
 * display_error
 * Show error messages in red alert box
 */
function display_error($message)
{
    return "<div class='alert alert-danger' role='alert'>" . htmlspecialchars($message) . "</div>";
}

/**
 * display_success
 * Show success messages in green alert box
 */
function display_success($message)
{
    return "<div class='alert alert-success' role='alert'>" . htmlspecialchars($message) . "</div>";
}

// =============================================================================
// PRODUCT FUNCTIONS (MySQLi version)
// =============================================================================

/**
 * add_product
 * Insert a new product into the database
 * @param mysqli $conn MySQLi connection object
 */
function add_product($conn, $name, $sku, $description, $category_id, $price, $quantity, $min_stock_level)
{
    $stmt = $conn->prepare(
        "INSERT INTO products 
        (st_p_name, st_p_sku, st_p_description, st_p_category_id, st_price, st_quantity, st_min_stock_level) 
        VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param("sssiddi", $name, $sku, $description, $category_id, $price, $quantity, $min_stock_level);
    return $stmt->execute();
}

/**
 * get_all_products
 * Fetch all products with optional search and category filter (MySQLi)
 */
function get_all_products($conn, $search = '', $category_id = '')
{
    $query = "SELECT * FROM products";
    $conditions = [];
    $params = [];
    $types = "";

    // Search filter
    if (!empty($search)) {
        $conditions[] = "(st_p_name LIKE ? OR st_p_sku LIKE ? OR st_p_description LIKE ?)";
        $search_param = '%' . $search . '%';
        $params[] = $search_param;
        $params[] = $search_param;
        $params[] = $search_param;
        $types .= "sss";
    }

    // Category filter
    if (!empty($category_id)) {
        $conditions[] = "st_p_category_id = ?";
        $params[] = $category_id;
        $types .= "i";
    }

    // Combine conditions
    if (!empty($conditions)) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }

    $query .= " ORDER BY st_p_name ASC";

    // Prepare statement
    $stmt = $conn->prepare($query);

    // Bind params dynamically if needed
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    return $result->fetch_all(MYSQLI_ASSOC);
}
