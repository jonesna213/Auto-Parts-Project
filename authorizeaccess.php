<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    //Not logged in, redirect to unauthorized.php script
    if (!isset($_SESSION['user_id']) && !isset($_SESSION['user_name'])) {
        
        header("Location: unauthorizedaccess.php");
        exit;
    }
?>
