<!--Client ID:-->
<!--1c3f7ed5bcffec1-->
<!--Client secret:-->
<!--e3b94ac5a07eaffbb25debdb1cfa26ba02cea338-->

<!--https://imgur.com/#access_token=b07faa675773b93871f142f65ef04504faa857a0&expires_in=315360000&token_type=bearer&refresh_token=8aa274ba70c4cd1624d59c82b165a7fbfc7c8001&account_username=jorobash&account_id=106448157-->
<!--access_token = b07faa675773b93871f142f65ef04504faa857a0-->
<!--refresh_token = 8aa274ba70c4cd1624d59c82b165a7fbfc7c8001-->
<?php

include('./classes/DB.php');
include('./classes/Login.php');
include('./classes/Image.php');

if(Login::isLoggedIn()){
    $userid = Login::isLoggedIn();
}else {
    die('You are not logged in');
}
if(isset($_POST['uploadprofileimg'])){
    Image::upload('profileimg','UPDATE users
               SET profileimg = :profileimg
               WHERE id = :userid',
        array( 'userid' => $userid));
}
?>

<h1>My Account</h1>
<form action="my-account.php" method="post" enctype="multipart/form-data">
    Upload a profile image:
    <input type="file" name="profileimg">
    <input type="submit" name="uploadprofileimg" value="Upload Image">
</form>
