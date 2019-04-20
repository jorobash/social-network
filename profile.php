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
    $verified = false;
    if(DB::query('SELECT username FROM users WHERE username = :username', array(':username' => $_GET['username']))){

        $username = DB::query('SELECT username FROM users WHERE username = :username', array(':username' => $_GET['username']))[0]['username'];
        $userid = DB::query('SELECT id FROM users WHERE username = :username', array(':username' => $_GET['username']))[0]['id'];
        $verified = DB::query('SELECT verified FROM users WHERE username = :username', array(':username' => $_GET['username']))[0]['verified'];

        $followerid = Login::isLoggedIn();

            if(isset($_POST['follow'])){
                if($userid != $followerid) {
                    if (!DB::query('SELECT follower_id FROM followers WHERE user_id = :user_id and follower_id = :followerid', array(':user_id' => $userid, ':followerid' => $followerid))) {
                        if ($followerid == 4) {
                            DB::query('UPDATE users SET verified = 1 WHERE id = :userid', array(':userid' => $userid));
                        }
                        DB::query('INSERT INTO followers VALUES (\'\', :user_id, :followerid)', array(':user_id' => $userid, ':followerid' => $followerid));
                    } else {
                        echo 'already following';
                    }

                }
                $isFollowing = true;
            }
            if(isset($_POST['unfollow'])){
                //check if the user is different from the logged user
                if($userid != $followerid){
                if(DB::query('SELECT follower_id FROM followers WHERE user_id = :user_id and follower_id = :followerid', array(':user_id' => $userid, ':followerid' => $followerid))){
                    if($followerid == 4){
                        DB::query('UPDATE users SET verified = 0 WHERE id = :userid', array(':userid' => $userid));
                    }
                        DB::query('DELETE FROM followers where user_id = :user_id and follower_id = :followerid', array(':user_id' => $userid, ':followerid' => $followerid));
                }
            }

                $isFollowing = false;
            }
            if(DB::query('SELECT follower_id FROM followers WHERE user_id = :user_id and follower_id = :followerid', array(':user_id' => $userid, ':followerid' => $followerid))){
                $isFollowing = true;
            }

            if(isset($_POST['post'])){
                $postbody = htmlspecialchars($_POST['postbody']);
                $loggedInUser = Login::isLoggedIn();

                if(strlen($postbody > 160 || strlen($postbody) < 1)){
                    die('Incorect length');
                }

                if($loggedInUser == $userid){
                    DB::query('INSERT INTO posts VALUES (\'\', :postbody, NOW(), :userid, 0)', array(':postbody' => $postbody, ':userid' => $userid));
                }else{
                    die('Incorecrt user');
                }

            }

        if(isset($_GET['postid'])){
            if(!DB::query('SELECT user_id FROM posts_likes WHERE post_id = :postid AND user_id = :userid', array(':postid' => $_GET['postid'], ':userid' => $followerid))){
                DB::query('UPDATE posts SET likes=likes+1 WHERE id = :postid', array(':postid' => $_GET['postid']));
                DB::query('INSERT INTO posts_likes VALUES(\'\', :postid, :userid)', array(':postid' => $_GET['postid'], ':userid' => $followerid));
            }else{
                echo 'YOu alredy like this post';
            }

        }

            $db = DB::query('SELECT * FROM posts WHERE user_id = :userid ORDER BY id DESC', array(':userid' => $userid));
            $posts = '';
            foreach($db as $post){
              $posts .= $post['body']."
              <form action='profile.php?username=$username&postid=". $post['id']."' method='post'>
                <input type='submit' name='like' value='like'>
              </form>
              <hr></br>";
            }
    }else{
        die('User not found');
    }
}

?>

<h1><?php echo $username; ?> Profile's <?php if($verified)  {echo ' - verified'; } ?></h1>
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
<form action="profile.php?username=<?php echo $username; ?>" method="post">
    <textarea name="postbody" id="postbody" cols="30" rows="10"></textarea>
    <input type="submit" name="post" value="Post">
</form>

<div class="posts">
    <?= $posts; ?>
</div>
