<?php

class Notify
{
    //mentions notify
    public static function createNotify($body = "", $postid = 0, $followerid = ''){
        $body = explode(" ", $body);

        $notify = array();

        foreach($body as $note){
            if(substr($note,0,1) == '@'){
                $notify[substr($note,1)] = array(
                    'type' => 1,
                    'extra'=> '{"postbody": "'. htmlentities(implode($body, " ")).'"}'
                );
            }
        }

        if(count(array_filter($body)) == 0 && $postid != 0){
            $getReceverSender = DB::query('SELECT posts.user_id as receiver,posts_likes.user_id as sender
                                              FROM posts, posts_likes
                                              WHERE posts.id = posts_likes.post_id
                                              AND posts.id = :postid AND posts_likes.user_id = :userid  ',
                                              array(':postid' => $postid, ':userid' => $followerid));
            $sender = $getReceverSender[0]['sender'];
            $receiver = $getReceverSender[0]['receiver'];
            DB::query('INSERT INTO notifications  values(\'\', :type, :receiver, :sender, :extra)',
                array(':type' => 2,':receiver' => $receiver, ':sender' => $sender, ':extra' => ""));
        }

        return $notify;
    }
}