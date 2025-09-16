<?php

function sanitize_input($data) {
    $data = trim($data);             
    $data = stripslashes($data);     
    $data = htmlspecialchars($data); 
    return $data;                    
}



function display_error($message) {
    return "<div class='alert alert-danger' role='alert'>" . htmlspecialchars($message) . "</div>";
}


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


function get_all_products($conn, $search = '', $category_id = '') {


    $query = "SELECT p.* FROM products p";


    $conditions = [];
    $params = [];

    if (!empty($search)) {

        global $conn;
      $search_operator = (isset($conn) && $conn instanceof PDO && strpos($conn->getAttribute(PDO::ATTR_DRIVER_NAME), 'pgsql') !== false) ? 'ILIKE' : 'LIKE';



        $conditions[] = "(p.st_p_name $search_operator ? OR p.st_p_sku $search_operator ? OR p.st_P_description $search_operator ?)";
        

        $search_param = '%' . $search . '%';

        $params = array_merge($params, [$search_param, $search_param, $search_param]);

     
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


function get_product_by_id($conn, $id) {
    $stmt = $conn->prepare("SELECT p.* FROM products p WHERE p.st_p_id = ?");
    $stmt->bind_param("i", $id); 
    $stmt->execute();            
    $result = $stmt->get_result(); 
    return $result->fetch_assoc(); 
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