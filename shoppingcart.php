<?php
    require_once('authorizeaccess.php');
    require_once('pagetitles.php');
    $pageTitle = HP_CART_PAGE;
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
                    //Getting needed files
                    require_once("navmenu.php");
                    require_once('dbconnection.php');
                    require_once('queryutils.php');
                    require_once('calculateTotal.php');
                    
                    $userId = $_SESSION['user_id'];
                    
                    //Connecting to database
                    $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
                            or trigger_error('Error connecting to MYSQL server for'
                            . DB_NAME, E_USER_ERROR);
                    
                    //Query to get all the items in the users cart
                    $query = "SELECT product_id, name, price, imageFile, quantity FROM "
                            . "userCart as u INNER JOIN products as p ON "
                            . "u.product_id = p.Id WHERE user_id = ?";
                                    
                    $results = parameterizedQuery($dbc, $query, 'i', $userId)
                            or trigger_error(mysqli_error($dbc), E_USER_ERROR);
                ?>
                <h2>Your Shopping Cart</h2><br>
                <?php
                    //If the checkout button was clicked
                    if (isset($_POST['checkoutSubmission'], $_POST['grandTotal'])) {
                        $grandTotal = $_POST['grandTotal'];
                ?>
                        <h3>Confirm your Order</h3><br>
                        <table class="table">
                            <tbody>
                <?php
                        //For each of the items in the users cart create a table element for it
                        while ($row = mysqli_fetch_assoc($results)) {
                            $productName = filter_var($row['name'], FILTER_SANITIZE_STRING);
                            $productPrice = filter_var($row['price'], FILTER_SANITIZE_STRING);
                            $quantity = filter_var($row['quantity'], FILTER_SANITIZE_NUMBER_INT);
                            $total = calculateTotal($productPrice, $quantity);
                            
                ?>
                            <tr>
                                <td><?=$productName?></td>
                                <td><?=$productPrice?></td>
                                <td>Qty: <?=$quantity?></td>
                                <td>$<?=$total?></td>
                            </tr>
                <?php
                        }
                ?>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td><b>Grand Total:<b></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>$<?=$grandTotal?></td>
                            </tr>
                            </tbody>
                        </table>
                        <form method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
                            <button class="btn btn-primary" 
                                    name="placeOrderSubmission"
                                    type="submit">Place Order</button>
                            <input type="hidden" name="grandTotal" value="<?=$grandTotal?>"> 
                            <button class="btn btn-danger" 
                                    name="cancelOrderSubmission"
                                    type="submit">Cancel Order</button>
                        </form>
                <?php
                        //If the place order button was clicked
                    } elseif (isset($_POST['placeOrderSubmission'], $_POST['grandTotal'])) {
                        
                        $grandTotal = $_POST['grandTotal'];
                        
                        //Query to remove the items from the users cart
                        $query = "DELETE FROM userCart WHERE user_id = ?";
                        
                        parameterizedQuery($dbc, $query, 'i', $userId)
                                or trigger_error(mysqli_error($dbc), E_USER_ERROR);
                ?>
                        
                        <div class="row">
                            <div class="col-3">
                            </div>
                            <div class="col-5">
                                <h3 class="text-success">The Following Order Was Placed!</h3>
                                <table class="table">
                                    <tbody>
                <?php   
                        //creates a table of the items that got removed (ordered) from the users cart
                        while ($row = mysqli_fetch_assoc($results)) {
                            $productName = filter_var($row['name'], FILTER_SANITIZE_STRING);
                            $productPrice = filter_var($row['price'], FILTER_SANITIZE_STRING);
                            $quantity = filter_var($row['quantity'], FILTER_SANITIZE_NUMBER_INT);
                ?>
                                        <tr>
                                            <td><?=$productName?></td>
                                            <td><?=$productPrice?></td>
                                            <td>Qty: <?=$quantity?></td>
                                        </tr>
                <?php
                        }
                ?>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td><b>Grand Total:<b></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td>$<?=$grandTotal?></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <a class="btn btn-primary" 
                                        href="index.php">Return To Home</a>
                            </div>
                            <div class="col-3">
                            </div>
                        </div>
                <?php
                        //If the cancel order button was clicked
                    } elseif (isset($_POST['cancelOrderSubmission'])) {
                        
                        header('Location: shoppingcart.php');
                        exit;
                        
                        //If the remove from cart button was clicked
                    } elseif (isset($_POST['idToRemove'])) {
                        $idToRemove = $_POST['idToRemove'];
                        
                        $query = "DELETE FROM userCart WHERE user_id = ? AND "
                                . "product_id = ?";
                        
                        parameterizedQuery($dbc, $query, 'ii', $userId, $idToRemove)
                                or trigger_error(mysqli_error($dbc), E_USER_ERROR);
                        
                        header('Location: shoppingcart.php');
                        
                        //Show all the items in the users cart
                    } else {
                        if (mysqli_num_rows($results) > 0) {
                            
                            $grandTotal = 0;
                ?>
                            <div class="row">
                                <div class="col-6">
                                    <table class="table">
                                        <tbody>
                <?php
                            //Creates a table out of the items in the users cart
                            while($row = mysqli_fetch_assoc($results)) {
                                $productId = filter_var($row['product_id'], FILTER_SANITIZE_NUMBER_INT);
                                $productName = filter_var($row['name'], FILTER_SANITIZE_STRING);
                                $productPrice = filter_var($row['price'], FILTER_SANITIZE_STRING);
                                $productImage = $row['imageFile'];
                                $quantity = filter_var($row['quantity'], FILTER_SANITIZE_NUMBER_INT);
                                $total = calculateTotal($productPrice, $quantity);
                                
                                $grandTotal += $total;
                ?>
                                <tr>
                                    <td>
                                        <img src="<?=$productImage?>"
                                                class='img-thumbnail' 
                                                style='max-height:100px;' 
                                                alt="Product Image">
                                    </td>
                                    <td><?= $productName?></td>
                                    <td><?= $productPrice?></td>
                                    <td>Qty: <?= $quantity?></td>
                                    <td>$<?= $total?></td>
                                    <td>
                                        <form method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
                                            <button class="btn btn-danger" type='submit'>Remove From Cart</button>
                                            <input type="hidden" name="idToRemove" value="<?=$productId?>"> 
                                        </form>
                                    </td>
                                </tr>
                <?php
                                
                            }
                ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-2">
                                </div>
                                <div class="col-2">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th scope="row">Grand Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <form method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
                                            <tr><td>$<?=$grandTotal?></td></tr>
                                            <tr><td><br><br>
                                                <button class="btn btn-primary" 
                                                        name="checkoutSubmission" 
                                                        type='submit'>Check Out</button>
                                                <input type="hidden" name="grandTotal" value="<?=$grandTotal?>">
                                            </td></tr>
                                            </form>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                <?php
                        } else { //No items are in the users cart
                ?>
                            <h4>Your shopping cart is empty</h4>
                <?php
                        }
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
