<?php
include('./classes/DB.php');
include('./classes/Login.php');
$showTimeLine = false;
if(Login::isLoggedIn()){
    $showTimeLine = true;
}else{
    echo 'NOt logged in';
}

$followingposts = DB::query('SELECT posts.body, posts.likes, users.username FROM users, posts, followers
where posts.user_id = followers.user_id
AND users.id = posts.user_id
AND follower_id = 1
ORDER BY posts.likes DESC');

foreach($followingposts as $posts){
    echo $posts['body']. " - " .$posts['username']. "<hr />";
}
//print_r($followingposts);die;
?>
