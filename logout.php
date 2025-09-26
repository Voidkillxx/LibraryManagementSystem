<?php

session_start();

session_unset();
session_destroy();

if(isset($_COOKIE[session_name()])){
    setcookie(session_name(),'',3600,'');
}
header("Location: login.php");
exit;