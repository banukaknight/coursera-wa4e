<?php
    session_start();
     require_once "pdo.php";   
// Demand a session u_id
if ( ! isset($_SESSION["user_id"]) ) {
    die('ACCESS DENIED');
}

// logout go back to index.php
if ( isset($_POST['cancel']) ) {
    header('Location: index.php');
    return;
}

$stmt = $pdo->prepare("SELECT * FROM users where user_id = :xyz");
$stmt->execute(array(":xyz" => $_SESSION["user_id"]));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for user_id';
    header( 'Location: index.php' ) ;
    return;
}

if ( isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])  ) {
	if ( ( strlen($_POST['first_name']) < 1 )|| ( strlen($_POST['last_name']) < 1 )|| ( strlen($_POST['email']) < 1 )|| ( strlen($_POST['headline']) < 1 )|| ( strlen($_POST['summary']) < 1 ) ) {
		$_SESSION["error"] = "All values are required";
		header( "Location: add.php?profile_id=".$_POST['profile_id'] );
            return;
	}else if (strpos($_POST["email"],'@') === false) {
		$_SESSION["error"] = "Email address must contain @";
		header( "Location: add.php?profile_id=".$_POST['profile_id'] );
            return;
	}else{
		
		$sql = "INSERT INTO Profile ( user_id, first_name, last_name, email, headline, summary) VALUES (:user_id, :first_name, :last_name, :email, :headline, :summary)";
		$stmt = $pdo->prepare($sql);
		$stmt->execute(array(
		':user_id' => $_SESSION['user_id'],
		
        ':first_name' => $_POST['first_name'],
		':last_name' => $_POST['last_name'],
		':email' => $_POST['email'],
		':headline' => $_POST['headline'],
		':summary' => $_POST['summary']
		));
		
		$_SESSION['success'] = 'Record added';
		header( 'Location: index.php' ) ;
            return;

	}
}		





// Flash pattern
if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}




?>
	
<!DOCTYPE html>
<html>
  <head>
    <title>06d2cbe8 Banuka Vidusanka - Automobile Tracker</title>
	<?php require_once "bootstrap.php"; ?>
		<script>
		function validateForm() {
		  var fn = document.forms["myForm"]["first_name"].value;
		  var ln = document.forms["myForm"]["last_name"].value;
		  var em = document.forms["myForm"]["email"].value;
		  var hd = document.forms["myForm"]["headline"].value;
		  var sm = document.forms["myForm"]["summary"].value;
		  
		  if ((fn == "")|| (ln == "")|| (em == "")|| (hd == "")|| (sm == "") || (fn == null)|| (ln == null)|| (em == null)|| (hd == null)|| (sm == null)) {
			alert("All values are required");
			return false;
		  }else if(! em.includes("@")){
			 alert("Email address must contain @");
			return false; 
		  }else{
			  return true;
		  }
		  
		}
		</script>	
  </head>
  <body>
    <div class="container">
      <?php
      if ( isset($_SESSION["user_id"]) ) {
          echo "<h1>Adding Profile for ";
          echo htmlentities($_SESSION["name"]);
          echo "</h1>\n";
      }

      if ( isset($_SESSION["success"]) ) {
              echo('<p style="color:green">'.htmlentities($_SESSION["success"])."</p>\n");
              unset($_SESSION["success"]);
          }  
	  if ( isset($_SESSION["error"]) ) {
              echo('<p style="color:red">'.htmlentities($_SESSION["error"])."</p>\n");
              unset($_SESSION["error"]);
          }  
		?>
			
		<form name="myForm" method="post" >
		<p>First Name:
		<input type="text" name="first_name" size="60"/></p>
		<p>Last Name:
		<input type="text" name="last_name" size="60"/></p>
		<p>Email:
		<input type="text" name="email" size="30"/></p>
		<p>Headline:<br/>
		<input type="text" name="headline" size="80"/></p>
		<p>Summary:<br/>
		<textarea name="summary" rows="8" cols="80"></textarea>
		<p>
		<input type="submit" value="Add" onclick="return validateForm()">
		<input type="submit" name="cancel" value="Cancel">
		</p>
		</form>
	  
	  
	  
	  
    </div>
  </body>
</html>	  