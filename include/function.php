<?php
// =============================================================================
// UTILITY FUNCTIONS FOR DATA PROCESSING & UI 🍰
// =============================================================================

/**
 * sanitize_input($data)
 * 
 * Easy Terms:
 * - User se aayi hui input ko **safe aur clean banata hai**
 * - Example: Extra spaces, backslashes, harmful scripts → remove kar deta hai
 *
 * Mental Model / Logic:
 * 1. Problem: User input messy aur dangerous ho sakta hai
 * 2. Break into steps:
 *    - trim() → extra spaces remove
 *    - stripslashes() → backslashes remove
 *    - htmlspecialchars() → special characters safe
 * 3. Variable handling: $data ko modify karke return
 * 4. Reusability: Har form/input me call kar sakte ho
 * 5. Practice: Try "<script>", "  hello  ", "O\'Reilly", etc
 */
function sanitize_input($data) {
    $data = trim($data);             // Extra spaces remove
    $data = stripslashes($data);     // Backslashes remove
    $data = htmlspecialchars($data); // Special characters safe
    return $data;                    // Clean data return
}


/**
 * display_error($message)
 * 
 * Easy Terms:
 * - Error message ko **red box** me user ko show karo
 */
function display_error($message) {
    return "<div class='alert alert-danger' role='alert'>" . htmlspecialchars($message) . "</div>";
}

/**
 * display_success($message)
 * 
 * Easy Terms:
 * - Success message ko **green box** me user ko show karo
 */
function display_success($message) {
    return "<div class='alert alert-success' role='alert'>" . htmlspecialchars($message) . "</div>";
}



function add_product($conn, $name, $sku, $description, $category_id, $price, $quantity, $min_stock_level) {
    $stmt = $conn->prepare("INSERT INTO products (st_p_name, st_p_sku, st_p_description, st_p_category_id, st_price, st_quantity, st_min_stock_level) 
                           VALUES (?, ?, ?, ?, ?, ?, ?)");
    return $stmt->execute([$name, $sku, $description, $category_id, $price, $quantity, $min_stock_level]);
}







// =============================================================================
// PRODUCT MANAGEMENT FUNCTIONS
// =============================================================================

/**
 * Retrieve all products with optional search and filtering
 * 
 * This function demonstrates:
 * - Dynamic SQL query building based on user input
 * - JOIN operations to combine data from multiple tables
 * - Parameter binding for security (prevents SQL injection)
 * - Database-agnostic case-insensitive search (ILIKE for PostgreSQL, LIKE for MySQL)
 * 
 * @param PDO $conn Database connection object
 * @param string $search Search term for product name, SKU, or description
 * @param string $category_id Filter by specific category ID
 * @return array Array of products with category information
 */
function get_all_products($conn, $search = '', $category_id = '') {

      // -------------------------------------------------------------------------
    // Step 1: Base SQL query
    // -------------------------------------------------------------------------
    // LEFT JOIN use kiya hai taake products without categories bhi fetch ho jaye
    // Logical thinking: Hum chahte hain ki main table (products) ke saare rows ho,
    // aur categories table se sirf matching rows. Agar match na ho → NULL.
    $query = "SELECT p.* FROM products p";

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


function get_product_by_id($conn, $id) {
    $stmt = $conn->prepare("SELECT p.* FROM products p WHERE p.st_p_id = ?");
    $stmt->bind_param("i", $id);  // Bind integer parameter
    $stmt->execute();              // No arguments here
    $result = $stmt->get_result(); // Get result object
    return $result->fetch_assoc(); // Fetch row as associative array
}


function update_product($conn, $id, $name, $sku, $description, $price, $quantity, $min_stock_level) {
    $stmt = $conn->prepare("UPDATE products SET st_p_name = ?, st_p_sku = ?, st_p_description = ?, st_price = ?, 
                           st_quantity = ?, st_min_stock_level = ? WHERE st_p_id = ?");
    return $stmt->execute([$name, $sku, $description, $price, $quantity, $min_stock_level, $id]);
}


function delete_product($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM products WHERE st_p_id = ?");
    return $stmt->execute([$id]);
}
// Category-related functions
function get_all_categories($conn) {
    $stmt = $conn->prepare("SELECT * FROM categories ORDER BY name ASC");
    $stmt->execute();
    return $stmt->fetchAll();
}