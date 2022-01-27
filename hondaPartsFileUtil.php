<?php
    require_once('hondaPartsFileConstants.php');
    
    /*
        Purpose: Validates an uploaded product image file
        
        Description:    Validates an uploaded product image file is not greater 
        than HP_MAX_FILE_SIZE (1/2 MB), and is either a jpg or png image type, 
        and has no errors. If the image file validates to these constraints, an 
        error message containing an empty string is returned. If there is an 
        error, a string containing constraints the file failed to validate to 
        are returned.
        
        @return string: Empty if validation is successful, otherwise error 
        string containing constraints the image file failed to validate to.
    */
    function validateProductImageFile() {
    
        $error_message = "";
        
        //Check for $_FILES being set and no errors
        if (isset($_FILES) && $_FILES['product_image_file']['error'] == UPLOAD_ERR_OK) {
            
            //Check for uploaded file < Max file size and acceptable image type
            if ($_FILES['product_image_file']['size'] > HP_MAX_FILE_SIZE) {
            
                $error_message = "The product file image must be less than " 
                        . HP_MAX_FILE_SIZE . " Bytes";
            }
            $image_type = $_FILES['product_image_file']['type'];
            
            $allowed_images = ['image/jpg', 'image/jpeg', 'image/pjpeg'
                    , 'image/png', 'image/gif'];    
                    
            if (!in_array($image_type, $allowed_images)) {
                
                if (empty($error_message)) {
                        
                    $error_message = "The product file image must be of type jpg, png, or gif.";
                } else {
                        
                    $error_message .= ", and be an image of type jpg, png, or gif.";
                }
            }
            //Checks if the file name is generic_product.png
            if ($_FILES['product_image_file']['name'] == 'generic_product.png') {
                
                if (empty($error_message)) {
                        
                    $error_message = "The name of your file cannot be generic_product.png";
                } else {
                        
                    $error_message .= ". The name of your file cannot be generic_product.png";
                }
            }
                
        } elseif (isset($_FILES) 
                && $_FILES['product_image_file']['error'] != UPLOAD_ERR_NO_FILE
                && $_FILES['product_image_file']['error'] != UPLOAD_ERR_OK) {
                
            $error_message = "Error uploading product image file.";
        }
        
        return $error_message;
    }
    
    /*
        Purpose: Move an uploaded product image file to the HP_UPLOAD_PATH 
        (images/) folder and return the path location.
        
        Description: Moves an uploaded product image file from the temporary 
        server locaton to the HP_UPLOAD_PATH (images/) folder IF a product image 
        file was uploaded and returns the path location of the uploaded file by 
        appending the file name to the HP_UPLOAD_PATH 
        (e.g. images/product_image.jpg). IF a product image file was NOT uploaded, 
        an empty string will be returned for the path.
        
        @return string: Path to product image file IF a file was uploaded AND 
        moved to the HP_UPLOAD_PATH (images/) folder, otherwise and empty string.
    */
    function addProductImageFileReturnPathLocation() {
        
        $product_file_path = "";
        
        //Check for $_FILES being sent and no errors
        if (isset($_FILES) && $_FILES['product_image_file']['error'] == UPLOAD_ERR_OK) {
        
            $product_file_path = 
                    HP_UPLOAD_PATH . $_FILES['product_image_file']['name'];
            
            if (!move_uploaded_file($_FILES['product_image_file']['tmp_name'], $product_file_path)) {
                
                $product_file_path = "";
            }
        }
        return $product_file_path;
    }
    
    /*
        Purpose: Removes a file given a path to that file.
        
        Description: Removes the file referenced by $product_file_path. Dosent 
        show error if file cannot be removed.
        
        @param $product_file_path
    */
    function removeProductImageFile($product_file_path) {
    
        @unlink($product_file_path);
    }
?>







