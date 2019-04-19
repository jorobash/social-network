<?php
include('./classes/DB.php');
include('./classes/Login.php');
$tokenIsValid = false;

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
    if(isset($_GET['token'])) {
        $token = $_GET['token'];
        if (DB::query('SELECT user_id FROM password_tokens WHERE token = :token', array(':token' => sha1($token)))) {
            $user_id = DB::query('SELECT user_id FROM password_tokens WHERE token = :token', array(':token' => sha1($token)))[0]['user_id'];
            $tokenIsValid = true;

            if (isset($_POST['chagepassword'])) {

                $newpassword = $_POST['newpassword'];
                $newpasswordrepeat = $_POST['newpasswordrepeat'];
                $userid = Login::isLoggedIn();

                // check if password matches
                if ($newpassword == $newpasswordrepeat) {
                    if (strlen($newpassword) >= 4 && strlen($newpassword) <= 60) {
                        DB::query('UPDATE users SET password = :newpassword WHERE id = :userid',
                            array(':newpassword' => password_hash($newpassword, PASSWORD_BCRYPT), ':userid' => $userid));
                        echo 'Password changed successfully!';
                        DB::query('DELETE FROM password_tokens WHERE user_id = :userid', array(':userid' => $user_id));
                    }
                } else {
                    echo 'Password are not the same';
                }
            }

        } else {
            die('token is invalid');
        }
    }else{
        die('Not logged in');
    }
}
?>

<h1>Change your password</h1>
<form action="<?php if(!$tokenIsValid){echo 'change-password.php' ;} else{echo  'change-password.php?token='. $token. ''; } ?>" method="post">
   <?php if(!$tokenIsValid) { echo  '<input type="password" name="aldpassword" value="" placeholder="Current password ..."></p>' ;} ?>
    <input type="password" name="newpassword" value="" placeholder="New password ..."></p>
    <input type="password" name="newpasswordrepeat" value="" placeholder="Repeat password ..."></p>
    <input type="submit" name="chagepassword" value="Change Password">
</form>
