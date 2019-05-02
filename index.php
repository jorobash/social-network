<?php
include('./classes/DB.php');
include('./classes/Login.php');
include('./classes/Post.php');
include('./classes/Comment.php');

$showTimeLine = false;
$userid = Login::isLoggedIn();
if(Login::isLoggedIn()){
    $showTimeLine = true;
}else{
    echo 'NOt logged in';
}

if(isset($_GET['postid'])){
    Post::likePost($_GET['postid'],$userid);
}

if(isset($_POST['comment'])){
    Comment::createComment($_POST['commentbody'],$_GET['postid'],$userid);
}

//search for username or post
if(isset($_POST['searchbox'])){
    $tosearch = explode(" ", $_POST['searchbox']);

    if(count($tosearch) == 1){
        $tosearch = str_split($tosearch[0], 2);
    }

    // search for username
    $whereclause = "";
    $paramsarray = array(":username" => '%'. $_POST['searchbox']. '%');
    for($i = 0; $i < count($tosearch); $i++){
        $whereclause .= " OR username LIKE :u$i ";
        $paramsarray[":u$i"] = $tosearch[$i];
    }
   $users = DB::query('SELECT users.username FROM users WHERE users.username LIKE :username ' . $whereclause .' ', $paramsarray);


//    search for postbody
    $whereclause = "";

    $paramsarray = array(":postbody" => '%'. $_POST['searchbox']. '%');
    for($i = 0; $i < count($tosearch); $i++){
        if($i % 2) {
            $whereclause .= " OR body LIKE :p$i ";
            $paramsarray[":p$i"] = $tosearch[$i];
        }
    }
   $posts = DB::query('SELECT posts.body FROM posts WHERE posts.body LIKE :postbody ' . $whereclause .' ', $paramsarray);
}

?>

<form action="index.php" method="post">
    <input type="text" name="searchbox" value="">
    <input type="submit" name="search" value="Search">
</form>
<?php

$followingposts = DB::query('SELECT posts.body,posts.id, posts.likes, users.username FROM users, posts, followers
where posts.user_id = followers.user_id
AND users.id = posts.user_id
AND follower_id = :userid
ORDER BY posts.likes DESC', array(':userid' => $userid));

foreach($followingposts as $posts){
    echo $posts['body']. " - " .$posts['username'];
            echo "<form action='index.php?postid=". $posts['id']."' method='post'>";
    if(!DB::query('SELECT post_id FROM posts_likes WHERE post_id = :postid AND user_id = :userid', array(':postid' => $posts['id'], ':userid'=>$userid))) {
       echo "<input type='submit' name='like' value='like'>";
    }else{
        echo "<input type='submit' name='unlike' value='unlike'>";
    }
                echo "<span> " . $posts['likes'] . " likes</span>
                  </form>
                      <form action='index.php?postid=".$posts['id']."' method='POST'>
                      <textarea name='commentbody'  cols='80' rows='2'></textarea></p>
                      <input type='submit' name='comment' value='Comment'>
                  </form>";
                    Comment::displayComments($posts['id']);
                  echo "<hr></br>";
}
//print_r($followingposts);die;
?>
