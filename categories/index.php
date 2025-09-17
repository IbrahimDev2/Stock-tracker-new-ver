<?php
session_start();
require_once '../connection.php';   
require_once '../include/function.php'; 
require_once '../include/header.php';  


$categories = get_all_categories($conn);
?>
<main>

</main>
<?php
require_once '../include/footer.php'; 
?>