<?php
include('./classes/DB.php');
include('./classes/Login.php');
include('./classes/Post.php');
include('./classes/Image.php');
include('./classes/Notify.php');

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

            if(isset($_POST['deletepost'])){
                if(DB::query('SELECT id FROM posts WHERE id = :postid AND user_id = :userid',
                    array(':postid' => $_GET['postid'],':userid' => $followerid))){
                    // delete the current post
                    DB::query('DELETE FROM  posts WHERE id = :postid and user_id = :userid',
                        array(':postid' => $_GET['postid'], ':userid' => $followerid));
                    // delete likes related with this post
                    DB::query('DELETE FROM posts_likes WHERE post_id = :postid', array(':postid' => $_GET['postid']));
                    echo 'Post deleted';
                }
            }

            if(isset($_POST['post'])){
                if($_FILES['postimg']['size'] == 0){
                    Post::createPost($_POST['postbody'], $userid, $followerid);
                }else{
                  $postid = Post::createImagePost($_POST['postbody'], $userid, $followerid);
                    Image::upload('postimg',"UPDATE posts
                                   SET postimg = :postimg
                                   WHERE id = :postid" , array( ':postid' => $postid));
                }

            }

        if(isset($_GET['postid']) && !isset($_POST['deletepost'])){
         Post::likePost($_GET['postid'], $followerid);
        }

       $posts = Post::displayPost($userid, $username, $followerid);
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

<form action="profile.php?username=<?php echo $username; ?>" method="post" enctype="multipart/form-data">
    <textarea name="postbody" id="postbody" cols="30" rows="10"></textarea>
    <br> Upload an image:
    <input type="file" name="postimg">
    <input type="submit" name="post" value="Post">
</form>
<div class="posts">
    <?= $posts; ?>
</div>
