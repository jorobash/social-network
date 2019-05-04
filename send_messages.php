
<?php
session_start();
$cstrong = True;
$token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));
if(!isset($_SESSION['token'])){
    $_SESSION['token'] = $token;
}
include('./classes/DB.php');
include('./classes/Login.php');

if(Login::isLoggedIn()){
    $userid = Login::isLoggedIn();
}else{
    die("Not logged in");
}

if(isset($_POST['send'])){

//    check for CSRF scripting attacs

    if(!isset($_POST['nocsrf'])){
        die("invalid token");
    }

    if($_POST['nocsrf'] != $_SESSION['token']){
        die('Invalid Token');
    }

if(DB::query('SELECT id FROM users WHERE id = :receiver', array(':receiver' => $_GET['receiver']))) {
    DB::query("INSERT INTO messages VALUES('', :body, :sender, :receiver, 0)",
        array(':body' => $_POST['body'], ':sender' => $userid, ':receiver' => $_GET['receiver']));
    echo "Message sent";
}else{
    die('invalid id');
}

    session_destroy();
}

?>
<h1>Send a Message</h1>
<form action="send_messages.php?receiver=<?= htmlspecialchars($_GET['receiver']) ?>" method="post">
    <textarea name="body" id="" cols="30" rows="10"></textarea>
    <input type="hidden" name="nocsrf" value="<?php echo $_SESSION['token'];?>">
    <input type="submit" name="send" value="Send Message">
</form>