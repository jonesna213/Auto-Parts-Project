<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    //if not an admin, redirect to unauthorizedaccess.php
    if ($_SESSION['access_privileges'] != 'admin') {
        
        header("Location: unauthorizedaccess.php");
        exit;
    }
?>
