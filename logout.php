<?php
session_start(); 
session_unset(); 
session_destroy(); 
header("Location: /Stock-tracker-new-ver/index.php"); 
exit();
?>
