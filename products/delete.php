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
// Purpose: URL me diye gaye product ID ko delete karke listing par wapas bhejna
// Flow: Read ID -> Validate -> Fetch -> Delete -> Redirect with message
// ============================================================

// --- Include files ---
// require_once: file ko exactly ek dafa include karo (duplicate include se bacho)
require_once '../connection.php';  
require_once '../include/function.php';   // Business logic helpers: get_product_by_id(), delete_product()

// --- Read input ---
// $_GET['id'] : URL query string ka 'id' parameter (e.g., delete.php?id=7)
// isset(...)  : check karta hai key exist karti hai ya nahi
// intval(...) : jo bhi value aaye usko integer me convert kar do (invalid ho to 0 ban jata hai)
// ?: 0        : agar 'id' aya hi nahi to 0 (invalid id) le lo
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// --- Primary guard ---
// Agar id > 0 (valid positive integer) tabhi aage chalein, warna error ke sath redirect
if ($id > 0) {

    // --- Ensure record exists --- 
    // DB se product nikaal ke dekh lo waqai maujood hai ya nahi
    // Why: bina check kiye delete chalaya to user ko "kuch hua hi nahi" wali feeling aayegi
    $product = get_product_by_id($conn, $id);
    
    // Agar product mila...
    if ($product) {

        // --- Try delete operation ---
        // delete_product(...) : DB se record ko hard-delete karta hai
        // Best practice: yeh function internally prepared statements use kare + FK constraints handle kare
        if (delete_product($conn, $id)) {
            $_SESSION['deleted'] = 'Product deleted successfully!';
            // --- Success path ---
            // header('Location: ...') : HTTP redirect bhejo
            // Query string me success message pass kar rahe hain (simple approach)
            header('Location: index.php?message=Product deleted successfully');

        } else {

            // --- Failure path (DB error, constraint issue, etc.) ---
            header('Location: index.php?error=Failed to delete product');
        }

    } else {

        // --- Not found path ---
        // Galat ID ya already deleted record -> user ko clear error dikhao
        header('Location: index.php?error=Product not found');
    }

} else {

    // --- Invalid input path ---
    // Jab id missing/invalid ho (<= 0), to list pe wapas bhej do with error
    header('Location: index.php?error=Invalid product ID');
}

// --- Always terminate after redirect ---
// header() ke baad exit; zaroor karo: warna neeche ka output redirect ko break kar sakta hai
exit;
?>
