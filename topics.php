<?php
include('./classes/DB.php');
include('./classes/Login.php');
include('./classes/Post.php');
include('./classes/Image.php');

if(isset($_GET{'topics'})){
    if($posts = DB::query("SELECT * FROM posts WHERE FIND_IN_SET(:topic, topics)", array(":topic" => $_GET['topics']))){
        foreach($posts as $topic){
//            echo '<pre>';
//            print_r($topic);
//            echo '</pre>';

//            echo $topic['body'];
        }
    }
}

//$text = "";
//$book = explode(" ", $text);
//echo count(array_filter($book, 'strlen'));
//
//$_SESSION['book'] = 1;
//$_POST['post'] = 'post';
//$_GET['GET'] = 'GET';
//var_dump($_SERVER['REQUEST_METHOD']);