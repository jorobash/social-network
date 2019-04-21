<?php 
	include('classes/DB.php');

	if(isset($_POST['createacount'])) {
		$username = $_POST['username'];
		$password = $_POST['password'];
		$email = $_POST['email'];

		if (!DB::query('SELECT username FROM users WHERE username = :username', array(':username' => $username))) {
			if(strlen($username) >=3 && strlen($username) <=32){
				if(preg_match('/[a-zA-Z0-9_]+/', $username)){
					if(strlen($password) >= 4 && strlen($password) <= 60){
					if(filter_var($email, FILTER_VALIDATE_EMAIL)){
							DB::query('INSERT INTO users
						VALUES(\'\',:username,:password,:email, \'0\', \'\')', array(':username' => $username, ':password' => password_hash($password, PASSWORD_BCRYPT), ':email' => $email));
							echo 'success';
					}else{
						echo 'Invalid email';
					}
					}else{
						echo 'Invalid password';
					}
				}else {
					echo 'Invalid username';
				}
			}else {
				echo 'The username have to be between 3 and 32 characters';
			}
		}else {
			echo 'The username already exists';
		}
	}
 ?>

 <h1>Register</h1>
 <form action="create-account.php" method="post">
 	<input type="text" name="username" value="" placeholder="username ..."></p>
 	<input type="password" name="password" value="" placeholder="password ..." ></p>
 	<input type="email" name="email" value="" placeholder="someone@soesite.com"></p>
 	<input type="submit" name="createacount" value="Create Account">
 </form>