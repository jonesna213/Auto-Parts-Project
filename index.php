<?php
    require_once('pagetitles.php');
    $pageTitle = HP_HOME_PAGE;
?>
<html>
    <head>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css"
                integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS"
                crossorigin="anonymous">
        <link rel="stylesheet"
                href="https://use.fontawesome.com/releases/v5.8.1/css/all.css"
                integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf"
                crossorigin="anonymous">
        <title><?= $pageTitle ?></title>
    </head>
    <body>
        <div class="card">
            <div class="card-body">
                <h1>6th Gen Honda Auto Parts</h1>
                <hr>
                <?php
                    require_once("navmenu.php");
                ?>
                <h2>Products</h2>
                <form method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
                    <label for="search"><i class="fas fa-search"></i></label>
                    <input type="text" id="search" name="search" 
                            placeholder="Enter an item to search" required>
                    <button class="btn btn-primary" type='submit'>Search</button>
                </form>
                <?php
                    //Getting needed files
                    require_once('dbconnection.php');
                    require_once('hondaPartsFileConstants.php');
                    require_once('queryutils.php');

                    //Connecting to database
                    $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
                            or trigger_error('Error connecting to MYSQL server for'
                            . DB_NAME, E_USER_ERROR);
                    
                    
                    //If the search button was clicked
                    if (isset($_POST['search']))  {
                        $searchTerm = filter_var($_POST['search'], FILTER_SANITIZE_STRING);
                        $searchTerm = "%" . $searchTerm . "%";
                        
                        $query = "SELECT Id, name, price, imageFile, quantity FROM "
                            . "products as p LEFT OUTER JOIN userCart as c ON "
                            . "p.Id = c.product_id WHERE name LIKE ? ORDER BY name";
                    
                        $result = parameterizedQuery($dbc, $query, 's', $searchTerm)
                                or trigger_error(mysqli_error($dbc), E_USER_ERROR);
                        
                    } else { //To see all items
                        
                        $query = "SELECT Id, name, price, imageFile, quantity FROM "
                                . "products as p LEFT OUTER JOIN userCart as c ON "
                                . "p.Id = c.product_id ORDER BY name";
                                
                        $result = mysqli_query($dbc, $query)
                                or trigger_error('Error querying database project4'
                                . E_USER_ERROR);
                    }
                    
                    //If a user is logged in
                    if (isset($_SESSION['user_id'], $_SESSION['user_name'])) {
                    
                        $cartItems = [];
                        $user_id = $_SESSION['user_id'];
                        
                        $queryTwo = "SELECT product_id FROM userCart WHERE user_id = $user_id";
                        
                        $resultTwo = mysqli_query($dbc, $queryTwo)
                                or trigger_error('Error querying database project4'
                                . E_USER_ERROR);
                        
                        /*if there is items in the users cart add them to the
                          cartItems array*/  
                        if (mysqli_num_rows($resultTwo) > 0) {
                        
                            while ($row = mysqli_fetch_array($resultTwo)) {
                            
                                $id = $row['product_id'];
                                array_push($cartItems, $id);
                            }
                        }
                        
                        //If the remove from cart button was clicked
                        if (isset($_POST['id_to_remove_from_cart'])) {
                            
                            $id_to_remove = filter_var($_POST['id_to_remove_from_cart']
                                    , FILTER_SANITIZE_NUMBER_INT);
                            
                            $user_id = $_SESSION['user_id'];
                                
                            $query = "DELETE FROM userCart WHERE user_id = ? AND product_id = ?";
                                        
                            parameterizedQuery($dbc, $query, 'ii', $user_id, $id_to_remove)
                                    or trigger_error(mysqli_error($dbc), E_USER_ERROR);
                            
                            header("Location: index.php");
                        }
                        
                        //If the add to cart button was clicked
                        if (isset($_POST['id_to_add_to_cart'], $_POST['quantity'])) {
                        
                            $id_to_add = filter_var($_POST['id_to_add_to_cart']
                                    , FILTER_SANITIZE_NUMBER_INT);
                            $quantity = filter_var($_POST['quantity'], FILTER_SANITIZE_NUMBER_INT);
                            $user_id = $_SESSION['user_id'];
                                
                            $query = "INSERT INTO userCart VALUES (?, ?, ?)";
                                        
                            parameterizedQuery($dbc, $query, 'iii', $user_id, $id_to_add, $quantity)
                                    or trigger_error(mysqli_error($dbc), E_USER_ERROR);
                            
                            header("Location: index.php");
                        }
                        
                    }
                    
                    /*Checks that there was results given back from query.
                      Then creates table and inserts each product into 
                      the table.*/        
                    if (mysqli_num_rows($result) > 0) {
                ?>
                        <table class="table">
                            <tbody>
                    <?php
                        while($row = mysqli_fetch_assoc($result)) {
                            
                            $product_image_file = $row['imageFile'];
                            $productName = filter_var($row['name'], FILTER_SANITIZE_STRING);
                            $productId = filter_var($row['Id'], FILTER_SANITIZE_NUMBER_INT);
                            $productPrice = filter_var($row['price'], FILTER_SANITIZE_STRING);
                            $quantity = filter_var($row['quantity'], FILTER_SANITIZE_NUMBER_INT);
                            
                            //If the item is not in the cart set quantity to blank
                            if ($quantity < 1) {
                                $quantity = '';
                            }
                            
                            //If there is no image path it uses the default
                            if (empty($product_image_file)) {
                                
                                $product_image_file = HP_UPLOAD_PATH . HP_DEFAULT_PRODUCT_FILE_NAME;
                            }
                    ?>
                            <form method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
                                
                                <tr><td id="<?=$productId?>">
                                    <img src="<?=$product_image_file?>"
                                            class='img-thumbnail' 
                                            style='max-height:100px;' 
                                            alt="Product Image">
                                </td>
                                <td><?=$productName?></td>
                                <td><?=$productPrice?></td>
                                
                                <?php  
                                    //If a user is logged in show these items as well
                                    if (isset($_SESSION['user_id'], $_SESSION['user_name'])) {
                                ?>
                                        <td>
                                            <label for="quantity">Qty:</label>
                                            <input type="number" id="quantity" 
                                                    name="quantity" 
                                                    value="<?= $quantity?>" 
                                                    placeholder="0" required>
                                        </td>
                                <?php    
                                        /*Checks if the current item is in the
                                        cartItems array and if so adds a 
                                        remove from cart button, otherwise adds
                                        a add to cart button*/
                                        if (in_array($productId, $cartItems)) {
                                            $product_row = "<td><button "
                                                    . "class='btn btn-secondary'"
                                                    . "type='submit'>Remove From "
                                                    . "Cart</button></td>"
                                                    . "<input type='hidden' "
                                                    . "name='id_to_remove_from_cart' "
                                                    . "value='$productId'>";
                                        } else {
                                            $product_row = "<td><button "
                                                    . "class='btn btn-primary'"
                                                    . "type='submit'>Add to Cart"
                                                    . "</button></td>"
                                                    . "<input type='hidden' "
                                                    . "name='id_to_add_to_cart' "
                                                    . "value='$productId'>";
                                        }
                                        //If the user is an admin add the delete product icon
                                        if ($_SESSION['access_privileges'] == 'admin') {
                                            
                                            $product_row .= "<td><a class='nav-link' " .
                                                    "href='removeProduct.php?id_to_remove=" .
                                            $productId . "'><i class='fas fa-trash-alt'>"
                                                    . "</i></a></td>";
                                        }
                                        
                                        echo $product_row;
                                    } 
                                ?>
                                </tr>
                            </form>
                    
                    <?php                
                        }                          
                    ?>
                            </tbody>
                        </table>
                <?php
                    }
                    else { //No products are in the database
                ?>
                        <h3>No Products Found</h3>
                <?php
                    }
                ?>
            </div>
        </div>
        <script>
                /* JavaScript for disabiling form submission if there 
                are invalid fields*/
                    (function() {
                        'use strict';
                        window.addEventListener('load', function() {
                        /*Fetch all the form fields we want to apply custom 
                        bootstrap validation styles to*/
                            var forms = document.getElementsByClassName('needs-validation');
                            //Loop over them and prevent submission
                            var validation = Array.prototype.filter.call(forms,
                                    function(form) {
                                form.addEventListener('submit', function(event) {
                                    if (form.checkValidity() === false) {
                                        event.preventDefault();
                                        event.stopPropagation();
                                    }
                                    form.classList.add('was-validated');
                                }, false);
                            });
                        }, false);
                    })();
        </script>
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
                integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
                crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"
                integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut"
                crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"
                integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k"
                crossorigin="anonymous"></script>
    </body>
</html>
