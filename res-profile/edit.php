<?php
require_once "pdo.php";
session_start();

if ( ! isset($_SESSION["user_id"]) ) {
    die('ACCESS DENIED');
}

// logout go back to index.php
if ( isset($_POST['cancel']) ) {
    header('Location: index.php');
    return;
}

if ( isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])  ) {
	if ( ( strlen($_POST['first_name']) < 1 )|| ( strlen($_POST['last_name']) < 1 )|| ( strlen($_POST['email']) < 1 )|| ( strlen($_POST['headline']) < 1 )|| ( strlen($_POST['summary']) < 1 ) ) {
		$_SESSION["error"] = "All fields are required";
		header( "Location: edit.php?profile_id=".$_POST['profile_id'] );
            return;
	}else if (strpos($_POST["email"],'@') === false) {
		$_SESSION["error"] = "Email address must contain @";
		header( "Location: edit.php?profile_id=".$_POST['profile_id'] );
            return;
	}else{
		
		$sql = "UPDATE Profile SET first_name = :first_name, last_name = :last_name, email = :email, headline = :headline, summary = :summary WHERE profile_id = :profile_id";
		$stmt = $pdo->prepare($sql);
		$stmt->execute(array(
		':profile_id' => $_POST['profile_id'],
		
        ':first_name' => $_POST['first_name'],
		':last_name' => $_POST['last_name'],
		':email' => $_POST['email'],
		':headline' => $_POST['headline'],
		':summary' => $_POST['summary']
		));
		
		$_SESSION['success'] = 'Record updated';
		header( 'Location: index.php' ) ;
            return;

	}
}	

// Guardian: Make sure that profile_id is present
if ( ! isset($_GET['profile_id']) ) {
  $_SESSION['error'] = "Missing profile_id";
  header('Location: index.php');
  return;
}

$stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for profile_id';
    header( 'Location: index.php' ) ;
    return;
}

// Flash pattern
if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}

$fn = htmlentities($row['first_name']);
$ln = htmlentities($row['last_name']);
$em = htmlentities($row['email']);
$hd = htmlentities($row['headline']);
$sm = htmlentities($row['summary']);

$user_id = $row['user_id']; //NEEDED or not
$profile_id = $row['profile_id'];

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
			alert("All fields are required");
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
          echo "<h1>Editing Profile for ";
          echo htmlentities($_SESSION["name"]);
          echo "</h1>\n";
      }
?>
<form name="myForm" method="post">
<p>First Name:<input type="text" name="first_name" size="40" value="<?= $fn ?>"/></p>
<p>Last Name:<input type="text" name="last_name" size="40" value="<?= $ln ?>"/></p>
<p>Email:<input type="text" name="email" size="40" value="<?= $em ?>"/></p>
<p>Headline:<br/><input type="text" name="headline" size="80" value="<?= $hd ?>"/></p>
<p>Summary:<br/><textarea name="summary" rows="8" cols="80"><?= $sm ?></textarea></p>

<input type="hidden" name="profile_id" value="<?= $profile_id ?>">
<input type="submit" value="Save" onclick="return validateForm()">
<input type="submit" name="cancel" value="Cancel">
</form>




    </div>
  </body>
</html>	 