<?php
    
    /*
        Purpose: To calculate the total given a string representation of 
            the price and the interger value of quantity.
        
        Description: Takes the string representation of the price and removes
            the "$" then converts it into a float data type. Then returns 
            the value of the converted price times the quantity rounded to
            2 decimal places.
        
        @param $price the string representation of the price
        @param $quantity the quantity of the item being purchased 
        
        @return $total returns the total amount for the item
    */
    function calculateTotal($price, $quantity) {
        $price = substr($price, 1); //Stored in database like "$1.99" as a string
        $price = floatval($price);
        
        $total = round($price * $quantity, 2);
        return $total;
    }
?>
