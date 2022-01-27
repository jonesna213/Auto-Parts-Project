<?php
    require_once('pagetitles.php');
    $pageTitle = HP_LOGIN_PAGE;
?>
<html>
    <head>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css"
            integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS"
            crossorigin="anonymous">
        <title><?= $pageTitle ?></title>
    </head>
    <body>
        <div class="card">
            <div class="card-body">
                <h1>Login to your Honda Parts Account</h1>
                <hr>
                <?php
                    require_once("navmenu.php");
                    
                    //If the session user_id is empty and the login button was pressed
                    if (empty($_SESSION['user_id']) && isset($_POST['login_submission'])) {
                        
                        //Get username and password
                        $user_name = isset($_POST['user_name']) ? $_POST['user_name'] : '';
                        $password = isset($_POST['password']) ? $_POST['password'] : '';
                        
                        //If there is contents inside the username and password
                        if (!empty($user_name) && !empty($password)) {
                            
                            require_once('dbconnection.php');
                            require_once('queryutils.php');
                            
                            //Sanitizing inputs
                            $user_name = filter_var($user_name, FILTER_SANITIZE_STRING);
                            $password = filter_var($password, FILTER_SANITIZE_STRING);
                            
                            $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
                                    or trigger_error('Error connecting to MYSQL server for'
                                    . DB_NAME, E_USER_ERROR);
                            
                            //Check if user exists
                            $query = "SELECT Id, userName, passwordHash, "
                                    . "accessPrivileges FROM Users WHERE "
                                    . "userName = ?";
                                    
                            $results = parameterizedQuery($dbc, $query, 's', $user_name)
                                    or trigger_error(mysqli_error($dbc), E_USER_ERROR);
                            
                            //If user was found, validate password
                            if (mysqli_num_rows($results) == 1) {
                                
                                $row = mysqli_fetch_array($results);
                                
                                if (password_verify($password, $row['passwordHash'])) {
                                    
                                    $_SESSION['user_id'] = $row['Id'];
                                    $_SESSION['user_name'] = $row['userName'];
                                    $_SESSION['access_privileges'] = $row['accessPrivileges'];
                                    
                                    //Redirect to the home page
                                    header('Location: index.php');
                                    exit;
                                    
                                } else { //Password was incorrect
                                    
                                    echo "<h4><p class='text-danger'>An "
                                            . "incorrect user name or password "
                                            . "was entered.</p></h4><hr>";
                                            
                                }
                            } elseif (mysqli_num_rows($results) == 0) {
                                //user dose not exist
                                echo "<h4><p class='text-danger'>An account "
                                        . "does not exist for this username:"
                                        . "<span class='font-weight-bold'> "
                                        . "($user_name)</span>. Please use a "
                                        . "different user name.</p></h4><hr>";
                            
                            } else { //Big problem
                            
                                echo "<h4><p class='text-danger'>Something "
                                        . "went terribly wrong!</p></h4><hr>";
                            }
                        } else {
                            //Output error message
                            echo "<h4><p class='text-danger'>You must enter "
                                    . "both a user name and password.</p></h4><h4>";
                        }
                    }
                    
                    //If a user is not logged in, show the form to login
                    if (empty($_SESSION['user_id'])) {
                ?>
                <form class="needs-validation" novalidate method="POST"
                        action="<?= $_SERVER['PHP_SELF'] ?>">
                    
                    <div class="form-group row">
                        <label for="user_name"
                                class="col-sm-2 col-form-label-lg">User Name</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" 
                                    id="user_name" name="user_name" 
                                    placeholder="Enter a user name" required>
                            <div class="invalid-feedback">
                                Please provide a vaild user name.
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="password"
                                class="col-sm-2 col-form-label-lg">Password</label>
                        <div class="col-sm-4">
                            <input type="password" class="form-control" 
                                    id="password" name="password" 
                                    placeholder="Enter a password" required>
                            <div class="form-group form-check">
                                <input type="checkbox"
                                        class="form-check-input"
                                        id="show_password_check"
                                        onclick="togglePassword()">
                                <label class="form-check-label"
                                        for="show_password_check">Show Password</label>
                            </div>
                            <div class="invalid-feedback">
                                Please provide a vaild password.
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-primary" type="submit"
                            name="login_submission">Log In</button>
                </form>
                <?php
                    //If a user is logged in
                    } elseif (isset($_SESSION['user_name'])) {
                        echo "<h4><p class='text-success'>You are logged in as:"
                                . " <strong>{$_SESSION['user_name']}</strong>.</p></h4>";
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
                    // Toggles between showing and hiding the entered password
                    function togglePassword() {
                        var password_entry = document.getElementById("password");
                        if (password_entry.type === "password") {
                            password_entry.type = "text";
                        } else {
                            password_entry.type = "password";
                        }
                    }
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
