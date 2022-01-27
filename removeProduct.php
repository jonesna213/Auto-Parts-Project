<?php
    require_once('authorizeadminaccess.php');
    require_once('pagetitles.php');
    $page_title = HP_REMOVE_ITEM_PAGE;
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
        <title><?= $page_title ?></title>
    </head>
    <body>
        <div class="card">
            <div class="card-body">
                <h1>6th Gen Honda Auto Parts</h1>
                <hr>
                <?php
                    require_once('navmenu.php');
                ?>
                <h2>Remove an Item</h2>
                <?php
                    //Getting needed files
                    require_once('dbconnection.php');
                    require_once('hondaPartsFileUtil.php');
                    require_once('queryutils.php');
                    
                    //Connecting to database
                    $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD,
                                DB_NAME)
                                or trigger_error(
                            'Error connecting to MySQL server for ' . DB_NAME,
                            E_USER_ERROR);
                            
                    //If the Delete Item button was clicked
                    if (isset($_POST['delete_item_submission'], 
                            $_POST['Id'])) {
                        
                        $id = filter_var($_POST['Id'], FILTER_SANITIZE_NUMBER_INT);
                        $id = isset($id) ? $id : '';
                        
                        //Query Image file from database
                        $query = "SELECT imageFile FROM products WHERE id = ?";
                        
                        $result = parameterizedQuery($dbc, $query, 'i', $id)
                                or trigger_error(mysqli_error($dbc), E_USER_ERROR);
                                    
                        if (mysqli_num_rows($result) == 1) {
                            
                            $row = mysqli_fetch_assoc($result);
                                
                            $product_image_file = $row['imageFile'];
                                
                            /*If theres an image path in the database it will
                            use it to use the function removeProductImageFile() to
                            delete it from the images folder*/
                            if (!empty($product_image_file)) {
                                
                                removeProductImageFile($product_image_file);
                            }
                            
                            $query = "DELETE FROM products WHERE Id = ?";
                        
                            parameterizedQuery($dbc, $query, 'i', $id)
                                    or trigger_error(mysqli_error($dbc), E_USER_ERROR);
                                    
                            header("Location: index.php");
                            exit;
                        } else { //Big problem
                            
                            echo "<p class='text-danger'>An error occured.<br>"
                                    . "Try refreshing and try again</p>";
                        }
                         
                    }
                    //If the Don't Delete Item button was clicked
                    elseif (isset($_POST['do_not_delete_item_submission'])) {
                        
                        header("Location: index.php");
                        exit;
                    }   
                    //If the trash can icon was clicked on a item
                    elseif (isset($_GET['id_to_remove'])) {
                ?>
                        <h3 class="text-danger">Confirm Deletion of the 
                                Following Item:</h3><br>
                <?php
                        $id = filter_var($_GET['id_to_remove'], FILTER_SANITIZE_NUMBER_INT);
                        $id = isset($id) ? $id : '';
                        
                        $query = "SELECT * FROM products WHERE Id = ?";

                        $result = parameterizedQuery($dbc, $query, 'i', $id)
                                or trigger_error(mysqli_error($dbc), E_USER_ERROR);
                        
                        if (mysqli_num_rows($result) == 1) {
                                
                            $row = mysqli_fetch_assoc($result);
                                
                            $product_image_file = $row['imageFile'];
                                
                            //Sanitizing variables
                            $name = filter_var($row['name'], FILTER_SANITIZE_STRING);
                            $price = filter_var($row['price'], FILTER_SANITIZE_STRING);
                                
                    ?>
                    <div class="row">
                        <div class="col-2">
                            <img src="<?=$product_image_file?>" class="img-thumbnail"
                                style="max-height: 200px;" alt="Product Image">
                        </div>
                        <div class="col">
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <th scope="row">Name</th>
                                        <td><?= $name ?></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Price</th>
                                        <td><?= $price ?></td>
                                    </tr>
                                </tbody>
                            </table>
                            <!--Form with just the delete and dont delete buttons,
                                Buttons are styled with bootstrap (red and green)-->
                            <form method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
                                <div class="form-group row">
                                    <div class="col-sm-2">
                                        <button class="btn btn-danger" type="submit"
                                                name="delete_item_submission">Delete Item
                                        </button>
                                    </div>
                                    <div class="col-sm-2">
                                        <button class="btn btn-success" type="submit"
                                                name="do_not_delete_item_submission">
                                                Don't Delete Item
                                        </button>
                                    </div>
                                    <input type="hidden" name="Id" value="<?= $id ?>">
                                </div>
                            </form> 
                        </div>
                    </div>       
                <?php
                        }
                    } else { //Unintended page link sends back to homepage
                        
                        header("Location: index.php");
                        exit;
                    }                        
                ?>
            </div>
        </div>
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
