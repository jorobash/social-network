<?php
include('./classes/DB.php');
include('./classes/Login.php');

if(Login::isLoggedIn()){
    echo 'Logged In';
} else{
    echo 'Not logged in';
}

if(isset($_GET['username'])){
    $username = "";
    $isFollowing = false;
    if(DB::query('SELECT username FROM users WHERE username = :username', array(':username' => $_GET['username']))){

        $username = DB::query('SELECT username FROM users WHERE username = :username', array(':username' => $_GET['username']))[0]['username'];
        $userid = DB::query('SELECT id FROM users WHERE username = :username', array(':username' => $_GET['username']))[0]['id'];

        $followerid = Login::isLoggedIn();
        if($userid != $followerid){
            if(isset($_POST['follow'])){

                if(!DB::query('SELECT follower_id FROM followers WHERE 	user_id = :user_id', array(':user_id' => $userid))){
                    DB::query('INSERT INTO followers VALUES (\'\', :user_id, :followerid)', array(':user_id' => $userid, ':followerid' => $followerid));
                }else{
                    echo 'already following';
                }

                $isFollowing = true;
            }
            if(isset($_POST['unfollow'])){

                if(DB::query('SELECT follower_id FROM followers WHERE 	user_id = :user_id', array(':user_id' => $userid))){
                        DB::query('DELETE FROM followers where user_id = :user_id and follower_id = :followerid', array(':user_id' => $userid, ':followerid' => $followerid));
                }

                $isFollowing = false;
            }
            if(DB::query('SELECT follower_id FROM followers WHERE 	user_id = :user_id', array(':user_id' => $userid))){
                $isFollowing = true;
            }
        }
    }else{
        die('User not found');
    }
}

?>

<h1><?php echo $username; ?> Profile's</h1>
<form action="profile.php?username=<?php echo $username; ?>" method="post">
    <?php
    if($userid != $followerid){
        if($isFollowing){
            echo ' <input type="submit" name="unfollow" value="Unfollow">';
        }else{
            echo ' <input type="submit" name="follow" value="Follow">';
        }
    }
    ?>
</form>
