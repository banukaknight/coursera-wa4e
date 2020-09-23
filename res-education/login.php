<?php // Do not put any HTML above this line
	require_once "pdo.php";
	require_once "util.php";
	session_start();
	
	$salt = 'XyZzy12*_';
	 // Pw is php123 

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
			$stmt = $pdo->prepare('SELECT user_id, name FROM users
			WHERE email = :em AND password = :pw');
			$stmt->execute(array( ':em' => $_POST['email'], ':pw' => $check));
			$row = $stmt->fetch(PDO::FETCH_ASSOC);

			
			if ( $row !== false ) {
				$_SESSION['name'] = $row['name'];
				$_SESSION['user_id'] = $row['user_id'];

				//When the login succeeds - found record
				error_log("Login success ".$_POST['email']);
				
				// Redirect the browser to index.php
				$_SESSION["success"] = "Logged in.";
				
				header("Location: index.php");
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
<title>7bec2531 Banuka Vidusanka Login Here</title>

<script>
function doValidate() {
console.log('Validating...');
try {
pw = document.getElementById('id_1723').value;
em = document.getElementById('nam').value;
console.log("Validating pw="+pw);
if (pw == null || pw == "" || em == null || em == "") {
alert("Both fields must be filled out");
return false;
}else if(!em.includes("@")){
alert("Invalid email address");
return false;	
}

return true;
} catch(e) {
return false;
}
return false;
}
</script>

</head>
<body>

<div class="container">
<h1>Please Log In</h1>
<?php flashMessages(); ?> <!-- function from util.php -->
<form name="myForm" method="POST">
<label for="nam">Email</label>
<input type="text" name="email" id="nam"><br/>
<label for="id_1723">Password</label>
<input type="password" name="pass" id="id_1723"><br/>
<input type="submit" value="Log In" onclick="return doValidate();">
<input type="submit" name="cancel" value="Cancel">
</form>
<p>
For a password hint, view source and find a password hint
in the HTML comments.
<!-- Hint: The password is php123. -->
</p>
</div>
</body>
