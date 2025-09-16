<?php
session_start();


require_once '../connection.php';
require_once '../include/function.php';


$id = isset($_GET['id']) ? intval($_GET['id']) : 0;


if ($id > 0) {


    $product = get_product_by_id($conn, $id);

    if ($product) {

        if (delete_product($conn, $id)) {
            $_SESSION['deleted'] = 'Product deleted successfully!';

            header('Location: index.php?message=Product deleted successfully');
        } else {


            header('Location: index.php?error=Failed to delete product');
        }
    } else {


        header('Location: index.php?error=Product not found');
    }
} else {


    header('Location: index.php?error=Invalid product ID');
}


exit;
