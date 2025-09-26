<?php 

    $conn = new mysqli("db","root","rootpassword","library_db");

    if($conn -> connect_error){
        die("Connection error: " . $conn-> connect_error);
    