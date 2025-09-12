<?php
if (!defined('APP_INIT')) {
    exit("No direct access allowed");
}

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
function get_all_products($conn, $search = '', $category_id = '') {

      // -------------------------------------------------------------------------
    // Step 1: Base SQL query
    // -------------------------------------------------------------------------
    // LEFT JOIN use kiya hai taake products without categories bhi fetch ho jaye
    // Logical thinking: Hum chahte hain ki main table (products) ke saare rows ho,
    // aur categories table se sirf matching rows. Agar match na ho → NULL.
    $query = "SELECT p.*, c.st_ct_name as category_name FROM products p 
              LEFT JOIN categories c ON p.st_p_category_id = c.st_ct_id";

    // -------------------------------------------------------------------------
    // Step 2: Prepare arrays for dynamic conditions and parameters
    // -------------------------------------------------------------------------
    // Logical thinking: 
    // - Conditions ko ek array me store karenge, taake multiple filters dynamically add ho sake
    // - Parameters ko ek separate array me store karenge taake prepared statement ke liye use ho
    $conditions = [];
    $params = [];
        // -------------------------------------------------------------------------
    // Step 3: Add search condition if search term is provided
    // -------------------------------------------------------------------------
    if (!empty($search)) {
        // Logical thinking:
        // - Cross-database compatibility (PostgreSQL ILIKE, MySQL LIKE)
        // - Search multiple columns (name, sku, description)
        global $conn;
      $search_operator = (isset($conn) && $conn instanceof PDO && strpos($conn->getAttribute(PDO::ATTR_DRIVER_NAME), 'pgsql') !== false) ? 'ILIKE' : 'LIKE';


        // Build condition string for multiple columns
        $conditions[] = "(p.st_p_name $search_operator ? OR p.st_p_sku $search_operator ? OR p.st_P_description $search_operator ?)";
        
        // Add wildcards for partial matching
        $search_param = '%' . $search . '%';

        // Add one parameter for each placeholder
        $params = array_merge($params, [$search_param, $search_param, $search_param]);

        // Future improvement / missing piece:
        // - Escape % and _ from user input to avoid unexpected matches
        // - Optional: Add full-text search for better performance on large datasets
    }
    
    // -------------------------------------------------------------------------
    // Step 4: Add category filter if specified
    // -------------------------------------------------------------------------
     if (!empty($category_id)) {
        $conditions[] = "p.st_p_category_id = ?";
        $params[] = $category_id;

        // Logical thinking:
        // - This is an optional filter, only applied if user provides category_id
        // Future improvement:
        // - Could allow multiple categories by using IN (?, ?, ?)
        // - Could validate category_id is numeric to prevent errors
    }
    
    // -------------------------------------------------------------------------
    // Step 5: Build final query by combining conditions
    // -------------------------------------------------------------------------
    if (!empty($conditions)) {
        // Logical thinking:
        // - Multiple conditions joined with AND
        // - OR conditions for search term already grouped with parentheses
        $query .= " WHERE " . implode(' AND ', $conditions);
    }

    // -------------------------------------------------------------------------
    // Step 6: Add ORDER BY for consistent display
    // -------------------------------------------------------------------------
    $query .= " ORDER BY p.st_p_name ASC";

    // Logical thinking:
    // - Sorting alphabetically ensures frontend displays results consistently
    // Future improvement:
    // - Could allow dynamic sorting by column and direction (ASC/DESC)
    
    // -------------------------------------------------------------------------
    // Step 7: Execute the prepared statement
    // -------------------------------------------------------------------------
    $stmt = $conn->prepare($query);
    $stmt->execute($params);

    // -------------------------------------------------------------------------
    // Step 8: Return all results as associative array
    // -------------------------------------------------------------------------
    // Logical thinking:
    // - fetchAll() gives a ready-to-use array for frontend, API, or further processing
    // Future improvement:
    // - Could add pagination using LIMIT and OFFSET
    // - Could return count for total matching rows if needed for frontend
    // - Could implement caching for frequently searched queries
    // 1. Get the mysqli_result object
$result = $stmt->get_result();

// 2. Fetch all rows from the result set
// You can specify the fetch mode, e.g., MYSQLI_ASSOC for associative arrays
$data = $result->fetch_all(MYSQLI_ASSOC);
    return $data;

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

function get_product_by_id($conn, $id) {
    $stmt = $conn->prepare("SELECT p.* FROM products p WHERE p.st_p_id = ?");
    $stmt->bind_param("i", $id);  // Bind integer parameter
    $stmt->execute();              // No arguments here
    $result = $stmt->get_result(); // Get result object
    return $result->fetch_assoc(); // Fetch row as associative array
}

    // Category filter
    if (!empty($category_id)) {
        $conditions[] = "st_p_category_id = ?";
        $params[] = $category_id;
        $types .= "i";
    }

function update_product($conn, $id, $name, $sku, $description,  $category_id, $price, $quantity, $min_stock_level) {
    $stmt = $conn->prepare("UPDATE products SET st_p_name = ?, st_p_sku = ?, st_p_description = ?, st_p_category_id = ?, st_price = ?, 
                           st_quantity = ?, st_min_stock_level = ? WHERE st_p_id = ?");
    return $stmt->execute([$name, $sku, $description, $category_id, $price, $quantity, $min_stock_level, $id]);
}


function delete_product($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM products WHERE st_p_id = ?");
    return $stmt->execute([$id]);
}
// Category-related functions
function get_all_categories($conn) {
    $stmt = $conn->prepare("SELECT * FROM categories ORDER BY st_ct_name ASC");
    $stmt->execute();      
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC); // ✅ saari rows ek array of arrays
}


function add_category($conn, $name, $description) {
    $stmt = $conn->prepare("INSERT INTO categories (st_ct_name, st_ct_description) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $description); 
    return $stmt->execute(); 
}
function get_category_by_id($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM categories WHERE st_ct_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function update_category($conn, $id, $name, $description) {
    $stmt = $conn->prepare("UPDATE categories SET st_ct_name = ?, st_ct_description = ? WHERE st_ct_id = ?");
    $stmt->bind_param("ssi", $name, $description, $id);
    return $stmt->execute();
}
function delete_category($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM categories WHERE st_ct_id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}
function add_stock_movement($conn, $product_id, $movement_type, $quantity, $notes = '') {
    try {
        // Start transaction so all steps succeed or fail together
            $conn->autocommit(FALSE);
        
        // Step 1: Record movement in stock_movements table
        $stmt = $conn->prepare("
            INSERT INTO stock_movements (st_mt_product_id, st_mt_movement_type, st_mt_quantity, st_mt_notes) 
            VALUES (?, ?, ?, ?)
        ");

        if (!$stmt) {
    // Agar prepare fail hua → MySQLi error dekho
    die("Prepare failed: " . $conn->error);
}
        $stmt->execute([$product_id, $movement_type, $quantity, $notes]);
        
        // Step 2: Decide how quantity should change
        // 'in' → add, 'out' → subtract
        $multiplier = ($movement_type == 'in') ? 1 : -1;
        $quantity_change = $quantity * $multiplier;
        
        // Step 3: Update the product quantity in products table
        $update_stmt = $conn->prepare("
            UPDATE products 
            SET st_quantity = st_quantity + ? 
            WHERE st_p_id = ?
        ");
        $update_stmt->execute([$quantity_change, $product_id]);
        
        // Step 4: Commit transaction, changes saved
        $conn->commit();
        return true;
        
    } catch (Exception $e) {
    $conn->rollback();
    die("Error in add_stock_movement: " . $e->getMessage());
}

}


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
    return $result->fetch_all(MYSQLI_ASSOC); // ✅ saari rows ek array of arrays
}
function get_low_stock_count($conn) {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM products WHERE st_quantity <= st_min_stock_level");
    $stmt->execute();
 // 3. Result lo
    $result = $stmt->get_result();

    // 4. Ek row fetch karo as associative array
    $row = $result->fetch_assoc();

    // 5. Sirf count value return karo
    return $row['count'];
}

function get_low_stock_products($conn) {
    $stmt = $conn->prepare("SELECT p.*, c.st_ct_name as category_name FROM products p 
                           LEFT JOIN categories c ON p.st_p_category_id = c.st_ct_id 
                           WHERE p.st_quantity <= p.st_min_stock_level 
                           ORDER BY p.st_quantity ASC");
   $stmt->execute();      
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC); // ✅ saari rows ek array of arrays
}

function get_total_products($conn) {
    // 1. Prepare karo query
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM products"); 

    // 2. Execute karo query
    $stmt->execute();      

    // 3. Result lo
    $result = $stmt->get_result();

    // 4. Ek row fetch karo as associative array
    $row = $result->fetch_assoc();

    // 5. Sirf count value return karo
    return $row['count'];
}


function get_total_categories($conn) {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM categories");
    $stmt->execute();
   // 3. Result lo
    $result = $stmt->get_result();

    // 4. Ek row fetch karo as associative array
    $row = $result->fetch_assoc();

    // 5. Sirf count value return karo
    return $row['count'];
}

function get_recent_movements($conn, $limit = 10) {
    return get_stock_movements($conn, $limit);
}
