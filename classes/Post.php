<?php

    class Post {

        //create posts
        public static function createPost($postbody,$userid, $loggedInUser ){
            $postbody = htmlspecialchars($_POST['postbody']);

            if(strlen($postbody > 160 || strlen($postbody) < 1)){
                die('Incorect length');
            }

            if($loggedInUser == $userid){
                DB::query('INSERT INTO posts VALUES (\'\', :postbody, NOW(), :userid, 0)', array(':postbody' => $postbody, ':userid' => $userid));
            }else{
                die('Incorecrt user');
            }
        }

        //like unlike posts
        public static function likePost($postid, $followerid){
            if(!DB::query('SELECT user_id FROM posts_likes WHERE post_id = :postid AND user_id = :userid', array(':postid' => $postid, ':userid' => $followerid))){
                DB::query('UPDATE posts SET likes=likes+1 WHERE id = :postid', array(':postid' => $postid));
                DB::query('INSERT INTO posts_likes VALUES(\'\', :postid, :userid)', array(':postid' => $postid, ':userid' => $followerid));
            }else{
                DB::query('UPDATE posts SET likes=likes-1 WHERE id = :postid', array(':postid' => $postid));
                DB::query('DELETE FROM  posts_likes WHERE post_id = :postid AND user_id = :userid', array(':postid' => $postid, ':userid' => $followerid));
            }
        }

        // show posts 
        public static function displayPost($userid, $username, $followerid){
            $db = DB::query('SELECT * FROM posts WHERE user_id = :userid ORDER BY id DESC', array(':userid' => $userid));
            $posts = '';
            foreach($db as $post){
                if(!DB::query('SELECT post_id FROM posts_likes WHERE post_id = :postid AND user_id = :userid', array(':postid' => $post['id'], ':userid'=>$followerid))){
                    $posts .= $post['body']."
                      <form action='profile.php?username=$username&postid=". $post['id']."' method='post'>
                        <input type='submit' name='like' value='like'>
                        <span>". $post['likes'] ." likes</span>
                      </form>
                      <hr></br>";
                }else{
                    $posts .= $post['body']."
                      <form action='profile.php?username=$username&postid=". $post['id']."' method='post'>
                        <input type='submit' name='unlike' value='unlike'>
                         <span>". $post['likes'] ." likes</span>
                      </form>
                      <hr></br>";
                }

            }
        }
    }