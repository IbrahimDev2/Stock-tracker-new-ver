<?php
session_start();
if (!defined('APP_INIT')) {
    define('APP_INIT', true);
}

// User authentication check
if (!isset($_SESSION['email'])) {
    header("Location: /Stock-tracker-new-ver/index.php");
    exit();
}

require_once '../connection.php';
require_once '../include/function.php';

// Retrieve and validate category ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    $category = get_category_by_id($conn, $id);

    if ($category) {
        // Check if category contains products before deletion
        $count_stmt = $conn->prepare("SELECT COUNT(*) as count FROM products WHERE st_p_category_id = ?");
        $count_stmt->bind_param("i", $id);
        $count_stmt->execute();
        $result = $count_stmt->get_result();
        $count_row = $result->fetch_assoc();

        if ($count_row['count'] > 0) {
            header('Location: index.php?error=Cannot delete category - it contains products');
        } else {
            if (delete_category($conn, $id)) {
                header('Location: index.php?message=Category deleted successfully');
            } else {
                header('Location: index.php?error=Failed to delete category');
            }
        }
    } else {
        header('Location: index.php?error=Category not found');
    }
} else {
    header('Location: index.php?error=Invalid category ID');
}

exit;
?>
