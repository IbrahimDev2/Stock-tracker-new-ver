<?php
session_start();
require_once '../connection.php';
require_once '../include/function.php';


// ----------------------------
// Step 2: Fetch Category ID
// ----------------------------

// Get 'id' from URL query string (e.g., delete.php?id=5)
// intval() converts it into integer to avoid SQL injection & invalid input
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;


// ----------------------------
// Step 3: Validate ID
// ----------------------------
if ($id > 0) { // Only proceed if ID is valid positive integer

    // Fetch category record by ID from database
    $category = get_category_by_id($conn, $id);

    // ----------------------------
    // Step 4: Check if Category Exists
    // ----------------------------
    if ($category) {
        
        // ----------------------------
        // Step 5: Check if Category Has Products
        // ----------------------------
        
        // Prepare SQL query to count products belonging to this category
        $count_stmt = $conn->prepare("SELECT COUNT(*) as count FROM products WHERE st_p_category_id = ?");
        $count_stmt->bind_param("i", $id); // Bind category ID as integer parameter
        $count_stmt->execute();
        $result = $count_stmt->get_result();
        $count_row = $result->fetch_assoc();


        // ----------------------------
        // Step 6: Block Deletion if Products Exist
        // ----------------------------
        if ($count_row['count'] > 0) {
            
            // If category has products, redirect back with error message
            header('Location: index.php?error=Cannot delete category - it contains products');
        
        } else {
            
            // ----------------------------
            // Step 7: Delete Empty Category
            // ----------------------------
            
            // If no products exist → safe to delete
            if (delete_category($conn, $id)) {
                
                // Redirect back with success message
                header('Location: index.php?message=Category deleted successfully');
            
            } else {
                // If deletion query failed
                header('Location: index.php?error=Failed to delete category');
            }
        }
    
    } else {
        // If category not found in DB
        header('Location: index.php?error=Category not found');
    }

} else {
    // If invalid ID passed in URL (0 or non-numeric)
    header('Location: index.php?error=Invalid category ID');
}

// ----------------------------
// Step 8: Exit Script
// ----------------------------
// Ensures no further code executes after redirection
exit;
?>
