<?php
include('./classes/DB.php');
include('./classes/Login.php');

if(Login::isLoggedIn()){
    $userid = Login::isLoggedIn();
}else{
    echo 'NOt logged in';
}
echo '<h1>Notifications</h1>';
if($notification = DB::query('SELECT * FROM notifications WHERE receiver = :userid', array(':userid' => $userid))){
    foreach($notification as $n){
        if($n['type'] == 1){
            $senderName = DB::query('SELECT username FROM users WHERE id = :senderid',array(':senderid' => $n['sender']))[0]['username'];
            $extra = "";
            if($n['extra'] != ""){
                $body = json_decode($n['extra']);
                $extra = $body->postbody;
            }
            echo "<h3> ".$senderName. " mentioned you in a post -". $extra. "</h3><hr>";
        }
    }
}

?>
