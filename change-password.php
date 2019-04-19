<?php
include('./classes/DB.php');
include('./classes/Login.php');

if(Login::isLoggedIn()){
   if(isset($_POST['chagepassword'])){


       $oldpassword = $_POST['aldpassword'];
       $newpassword = $_POST['newpassword'];
       $newpasswordrepeat = $_POST['newpasswordrepeat'];
       $userid = Login::isLoggedIn();

       // check if password matches
       if(password_verify($oldpassword, DB::query('SELECT password FROM users WHERE id = :userid',
           array(':userid' => $userid))[0]['password'])){
            if($newpassword == $newpasswordrepeat){
                    if(strlen($newpassword) >= 4 && strlen($newpassword) <= 60) {
                        DB::query('UPDATE users SET password = :newpassword WHERE id = :userid',
                            array(':newpassword' => password_hash($newpassword, PASSWORD_BCRYPT), ':userid' => $userid));
                        echo 'Password changed successfully!';
                    }
                }else{
                echo 'Password are not the same';
            }
            }else{
                 echo 'Passwords don\'t match!';
            }
       }

}else{
    die('NOt logged in');
}
?>

<h1>Change your password</h1>
<form action="change-password.php" method="post">
    <input type="password" name="aldpassword" value="" placeholder="Current password ..."></p>
    <input type="password" name="newpassword" value="" placeholder="New password ..."></p>
    <input type="password" name="newpasswordrepeat" value="" placeholder="Repeat password ..."></p>
    <input type="submit" name="chagepassword" value="Change Password">
</form>
