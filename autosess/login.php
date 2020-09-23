<?php // Do not put any HTML above this line
	session_start();
	
	$salt = 'XyZzy12*_';
	$stored_hash = '1a52e17fa899cf40fb04cfc42e6352f1';  // Pw is php123 

	if ( isset($_POST["cancel"] ) ) {
		// Redirect the browser to index.php
		header("Location: index.php");
		return;
	}
	if ( isset($_POST["email"]) && isset($_POST["pass"]) ) {
			unset($_SESSION["email"]);  // Logout current user	
		
	if ( strlen($_POST["email"]) < 1 || strlen($_POST['pass']) < 1 ) {
			$_SESSION["error"] = "Email  and password are required";
				header( 'Location: login.php' ) ;
				return;
		} else if (strpos($_POST["email"],'@') === false){
	// ensure user email contain AT symbol		
			$_SESSION["error"] = "Email must have an at-sign (@)";	
				header( 'Location: login.php' ) ;
				return;
		} else {	
			$check = hash('md5', $salt.$_POST['pass']);
			if ( $check == $stored_hash ) {
				//When the login succeeds 
				error_log("Login success ".$_POST['email']);
				
				// Redirect the browser to view.php
				$_SESSION["email"] = $_POST["email"];
				$_SESSION["success"] = "Logged in.";
				
				header("Location: view.php?name=".urlencode($_POST['email']));
				return;
			} else {
				$_SESSION["error"] = "Incorrect password.";
				//When bad password occur issue log message
				error_log("Login fail ".$_POST['email']." $check");
				header( 'Location: login.php' ) ;
				return;
			}
		}
	}
	
?>

<!DOCTYPE html>
<html>
<head>
<?php require_once "bootstrap.php"; ?>
<title>0d970b78 Banuka Vidusanka Login Here</title>
</head>
<body>

<div class="container">
<h1>Please Log In</h1>
<?php
    if ( isset($_SESSION["error"]) ) {
        echo('<p style="color:red">'.$_SESSION["error"]."</p>\n");
        unset($_SESSION["error"]);
    }
?>
<form method="POST">
<label for="nam">Email</label>
<input type="text" name="email" id="nam"><br/>
<label for="id_1723">Password</label>
<input type="text" name="pass" id="id_1723"><br/>
<input type="submit" value="Log In">
<input type="submit" name="cancel" value="Cancel">
</form>
<p>
For a password hint, view source and find a password hint
in the HTML comments.
<!-- Hint: The password is php123. -->
</p>
</div>
</body>
