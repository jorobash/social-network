<?php
include('./classes/DB.php');
include('./classes/Login.php');

if(Login::isLoggedIn()){
    echo 'Logged in!';
}else{
    echo 'NOt logged in';
}
?>
