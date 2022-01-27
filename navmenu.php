<?php
    $pageTitle = isset($pageTitle) ? $pageTitle : '';
    
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
?>
    <nav class="navbar sticky-top navbar-expand-md navbar-light"
            style="background-color: #FFFFFF;">
        <button class="navbar-toggler" type="button" data-toggle="collapse"
                data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup"
                aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav">
                    <a class="nav-item nav-link<?= $pageTitle == HP_HOME_PAGE ? ' active' : '' ?>" 
                            href=<?= dirname($_SERVER['PHP_SELF']) ?>><u>Home</u></a>
                <?php if (isset($_SESSION['user_id']) && isset($_SESSION['user_name'])) {?>
                    <a class="nav-item nav-link<?= $pageTitle == HP_VIEW_PROFILE_PAGE ? ' active' : '' ?>" 
                            href="viewprofile.php"><u>View/Edit Profile</u></a>
                            
                    <?php if ($_SESSION['access_privileges'] == 'admin') {?> 
                        <a class="nav-item nav-link<?= $pageTitle == HP_ADD_ITEM_PAGE ? ' active' : '' ?>" 
                            href="additem.php"><u>Add Item</u></a>
                    <?php }?>      
                    <a class="nav-item nav-link" 
                            href="logout.php"><u>Logout (<?=$_SESSION['user_name']?>)</u></a>
                    <?php 
                          require_once('dbconnection.php');
                                        
                          //Connecting to and querying database
                          $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
                                  or trigger_error('Error connecting to MYSQL server for'
                                  . DB_NAME, E_USER_ERROR);
                          $user_id = $_SESSION['user_id'];   
                          
                          //Query to get the number of items in the users cart      
                          $query = "SELECT COUNT(*) as items FROM userCart WHERE user_id = $user_id";
                                        
                          $result = mysqli_query($dbc, $query)
                                  or trigger_error('Error querying database movieListing'
                                  . E_USER_ERROR); 
                          
                          $row = mysqli_fetch_assoc($result);       
                          $items_in_cart = $row['items']; 
                          
                    ?>
                    
                    <a class="btn" href="shoppingcart.php">
                            <i class="fas fa-shopping-cart"></i>Cart(<?= $items_in_cart ?>)</a>
                 
                <?php  
                      } else {?>
                    <a class="nav-item nav-link<?= $pageTitle == HP_SIGN_UP_PAGE ? ' active' : '' ?>" 
                            href="signup.php"><u>Sign Up</u></a>
                    <a class="nav-item nav-link<?= $pageTitle == HP_LOGIN_PAGE ? ' active' : '' ?>" 
                            href="login.php"><u>Login</u></a>
                <?php
                      }
                ?>
            </div>
        </div>
    </nav> 
            
