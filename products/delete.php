<?php
session_start();
if (!defined('APP_INIT')) {
    define('APP_INIT', true);
}
// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    // If session does not exist, redirect to login page
    header("Location: /Stock-tracker-new-ver/index.php");
    exit();
}
// ============================================================
// DELETE PRODUCT ACTION (Controller endpoint)
// Purpose: Delete product by ID and redirect to listing page with message
// Flow: Read ID -> Validate -> Fetch -> Delete -> Redirect with message
// ============================================================

// --- Include files ---
require_once '../connection.php';
require_once '../include/function.php';   // Business logic helpers: get_product_by_id(), delete_product()

// --- Read input ---
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// --- Primary guard ---
if ($id > 0) {

    // --- Ensure record exists --- 
    $product = get_product_by_id($conn, $id);

    if ($product) {

        // --- Try delete operation ---
        if (delete_product($conn, $id)) {
            $_SESSION['deleted'] = 'Product deleted successfully!';
            // Redirect to product list with success message
            header('Location: index.php?message=Product deleted successfully');
        } else {
            // Redirect to product list with error message
            header('Location: index.php?error=Failed to delete product');
        }
    } else {
        // Redirect if product not found
        header('Location: index.php?error=Product not found');
    }
} else {
    // Redirect if invalid product ID
    header('Location: index.php?error=Invalid product ID');
}

// Always terminate after redirect
exit;
