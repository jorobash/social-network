<?php
include('./classes/DB.php');
include('./classes/Login.php');

if(Login::isLoggedIn()){
    $userid = Login::isLoggedIn();
}else{
    echo 'NOt logged in';
}

if($notification = DB::query('SELECT * FROM notifications WHERE receiver = :userid', array(':userid' => $userid))){
    foreach($notification as $n){
        print_r($n);
    }
}

?>
