<?php
    require_once('authorizeadminaccess.php');
    require_once('pagetitles.php');
    $page_title = HP_ADD_ITEM_PAGE;
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
                    
                    //Initialization
                    $display_add_product_form = true;
                    
                    $name = "";
                    $price = "";
                    
                    //Validating that fields were set
                    if (isset($_POST['add_product_submission'], 
                              $_POST['name'], $_POST['price'])) {
                        
                        //Getting needed files
                        require_once('dbconnection.php');
                        require_once('hondaPartsFileUtil.php');
                        require_once('queryutils.php');
                        
                        //getting all form fields into variables
                        $name = isset($_POST['name']) ? $_POST['name'] : '';
                        $price = isset($_POST['price']) ? $_POST['price'] : '';

                        
                        $file_error_message = validateProductImageFile();
                        
                        if (empty($file_error_message)) {
                        
                            //Connecting to database
                            $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD,
                                    DB_NAME) or trigger_error(
                                    'Error connecting to MySQL server for ' . DB_NAME,
                                    E_USER_ERROR);
                            
                            $product_image_file_path = addProductImageFileReturnPathLocation();
                            
                            //if there is no file path returned set it to the default
                            if (empty($product_image_file_path)) {
                                
                                $product_image_file_path = HP_UPLOAD_PATH . HP_DEFAULT_PRODUCT_FILE_NAME;
                            }
                            
                            //Sanitizing Variables
                            $name = filter_var($name, FILTER_SANITIZE_STRING);
                            $price = filter_var($price, FILTER_SANITIZE_STRING);
                            
                            //Creating/running insert statement
                            $query = "INSERT INTO products (name, price, "
                                    . "imageFile) VALUES (?, ?, ?)";
                            
                            parameterizedQuery($dbc, $query, 'sss', $name, $price, $product_image_file_path)
                                    or trigger_error(mysqli_error($dbc), E_USER_ERROR);
                            
                            
                                     
                            $display_add_product_form = false;
                    ?>
                        <h3 class="text-info">
                            The Following Product was Added:</h3><br> 
                        
                        <h1><?= $name ?></h1>
                        <div class="row">
                            <div class="col-2">
                                <img src="<?= $product_image_file_path ?>"
                                        class="img-thumbnail"
                                        style="max-height: 200px;" 
                                        alt="Product Image">
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
                            </div>
                        </div>
                        <hr>
                        <a class="btn btn-primary" href='<?= $_SERVER['PHP_SELF'] ?>'>Add Another Product</a></p>
                        
                <?php 
                        } else {
                            
                            //Echo error message to page
                            echo "<h5><p class='text-danger'>" 
                                    . $file_error_message . "</p></h5>";
                        }       
                    }
                    if ($display_add_product_form) {  //Outputs add product form
                ?>  
                <hr>
                <h2>Add a product</h2>
                <form   enctype="multipart/form-data"
                        class="needs-validation" novalidate method="POST"
                        action="<?= $_SERVER['PHP_SELF'] ?>">
                    <div class="form-group row">
                        <label for="name"
                                class="col-sm-3 col-form-label-lg">Name</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" 
                                    id="name" name="name"
                                    value="<?=$name ?>" 
                                    placeholder="Name" required>
                            <div class="invalid-feedback">
                                Please provide a vaild product name.
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="price"
                                class="col-sm-3 col-form-label-lg">Price (place a "$" before the price)
                        </label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" 
                                    id="price" name="price"
                                    value="<?=$price ?>" 
                                    placeholder="Price" required>
                            <div class="invalid-feedback">
                                Please provide a vaild price.
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="product_image_file"
                                class="col-sm-3 col-form-label-lg">
                                Product Image File
                        </label>
                        <div class="col-sm-8">
                            <input type="file" class="form-control" 
                                    id="product_image_file" 
                                    name="product_image_file" required>
                        </div>
                    </div>
                    <button class="btn btn-primary" type="submit"
                            name="add_product_submission">Add Product</button>
                </form>
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
                <?php
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
