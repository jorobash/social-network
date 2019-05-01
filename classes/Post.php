<?php

    class Post {

        //create posts
        public static function createPost($postbody,$userid, $loggedInUser ){
            $postbody = htmlspecialchars($_POST['postbody']);

            if(strlen($postbody > 160 || strlen($postbody) < 1)){
                die('Incorect length');
            }

            $topics = self::getTopics($postbody);

            if($loggedInUser == $userid){

                //check if there is any mantions if it is send notifications to the mantioned user
                if(count(self::notify($postbody)) != 0){
                    foreach(self::notify($postbody) as $key => $notify){
                        $sender = $loggedInUser;
                        $receiver   = DB::query('SELECT id FROM users WHERE username = :username'
                            ,array(':username' => $key))[0]['id'];
                       // check if the mentioned user exists
                       if($receiver != 0){
                           DB::query('INSERT INTO notifications  values(\'\', :type, :receiver, :sender)',
                               array(':type' => $notify,':receiver' => $receiver, ':sender' => $sender));
                       }
                    }
                }

                DB::query('INSERT INTO posts VALUES (\'\', :postbody, NOW(), :userid, 0, \'\', :topics)',
                    array(':postbody' => $postbody, ':userid' => $userid, ':topics' => $topics));
            }else{
                die('Incorecrt user');
            }
        }

        //post image
        public static function createImagePost($postbody,$userid, $loggedInUser ){
            $postbody = htmlspecialchars($_POST['postbody']);

            if(strlen($postbody > 160)){
                die('Incorect length');
            }

           $topics = self::getTopics($postbody);

            if($loggedInUser == $userid){
                DB::query('INSERT INTO posts VALUES (\'\', :postbody, NOW(), :userid, 0, \'\', :topics)',
                    array(':postbody' => $postbody, ':userid' => $userid, ':topics' => $topics));
                $postid = DB::query('SELECT id
                           FROM posts
                           WHERE user_id = :userid
                           ORDER BY id DESC LIMIT 1',
                    array(':userid' => $loggedInUser))[0]['id'];
                return $postid;
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

        public static function notify($body){
            $body = explode(" ", $body);
            $notify = array();

            foreach($body as $note){
                if(substr($note,0,1) == '@'){
                    $notify[substr($note,1)] = 1;
                }

            }

            return $notify;
        }

        // find if some is mention
        public static function link_add($text){

            $text = explode(" ",$text);
            $newString = "";

            foreach($text as $word){
                if(substr($word,0,1) == "@"){
                    $newString = "<a href='profile.php?username=".substr($word, 1) ."'>". htmlspecialchars($word)  . "</a>";
                }else if(substr($word,0,1) == "#"){
                    $newString = "<a href='topics.php?topics=".substr($word, 1) ."'>". htmlspecialchars($word)  . "</a>";
                }else {
                    $newString .= htmlspecialchars($word). " ";
                }
            }

            return $newString;
        }

        public static function getTopics($text){
            $text = explode(" ",$text);
            $topics = "";

            foreach($text as $word){
                if(substr($word,0,1) == "#") {
                    $topics .= substr($word, 1).",";
                }
            }

            return $topics;
        }

        // show posts
        public static function displayPost($userid, $username, $followerid){
            $db = DB::query('SELECT * FROM posts WHERE user_id = :userid ORDER BY id DESC', array(':userid' => $userid));
            $posts = '';
            foreach($db as $post){
                if(!DB::query('SELECT post_id FROM posts_likes WHERE post_id = :postid AND user_id = :userid', array(':postid' => $post['id'], ':userid'=>$followerid))){
                    $posts .= "<img src='". $post['postimg']."' style='width: 140px; height: 140px;'>". self::link_add($post['body'])."
                      <form action='profile.php?username=$username&postid=". $post['id']."' method='post'>
                        <input type='submit' name='like' value='like'>
                        <span>". $post['likes'] ." likes</span>";
                        if($userid == $followerid){
                            $posts .= "<input type='submit' name='deletepost' value='x'>";
                        }
                    $posts .= "</form>
                      <hr></br>";
                }else{
                    $posts .= "<img src='". $post['postimg']."' style='width: 140px; height: 140px;'>".self::link_add($post['body'])."
                      <form action='profile.php?username=$username&postid=". $post['id']."' method='post'>
                        <input type='submit' name='unlike' value='unlike'>
                         <span>". $post['likes'] ." likes</span>";
                    if($userid == $followerid){
                        $posts .= "<input type='submit' name='deletepost' value='x'>";
                    }
                    $posts .= "</form>
                      <hr></br>";
                }

            }

            return $posts;
        }

    }