
<?php
include('./classes/DB.php');
include('./classes/Login.php');

if(Login::isLoggedIn()){
    $userid = Login::isLoggedIn();
}else{
    die("Not logged in");
}

    if(isset($_GET['mid'])){
       $message =  DB::query('SELECT * FROM messages WHERE id = :mid
                              AND receiver = :receiver
                              OR sender = :sender',
                              array(':mid' => $_GET['mid'], ':receiver' => $userid, ':sender' => $userid))[0];
        echo '<h1>View Message</h1>';
        echo htmlspecialchars($message['body']);
        echo '</hr>';

        if($message['sender'] == $userid){
            $id = $message['receiver'];
        }else{
            $id = $message['sender'];
        }

        DB::query('UPDATE messages SET  `read_message` = 1 WHERE id = :mid', array(':mid' => $_GET['mid']));
        ?>

        <h1>Send a Message</h1>
        <form action="send_messages.php?receiver=<?= $id; ?>" method="post">
            <textarea name="body" id="" cols="30" rows="10"></textarea>
            <input type="submit" name="send" value="Send Message">
        </form>
        <?php
    }else{
?>
<h1>My messages</h1>
<?php
$messages = DB::query('SELECT  messages.*, users.username
                       FROM messages,users
                       WHERE (receiver = :receiver
                       OR sender = :sender)
                       AND users.id = messages.sender', array(':receiver' => $userid, ':sender' => $userid));
        var_dump($userid);
foreach($messages as $m){
   if($m['read_message'] == 0){
       echo "<strong><a href='my-messages.php?mid=".$m['id']."'>".$m['body'] . "</strong></a> sent by ". $m['username'] ."<hr>" ;
   }else{
       echo "<a href='my-messages.php?mid=".$m['id']."'>".$m['body'] . "</a> sent by ". $m['username'] ."<hr>" ;
   }
}

    }