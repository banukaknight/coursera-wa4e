<?php
    session_start();
    require_once "pdo.php";    
// Demand a GET parameter
if ( ! isset($_GET['profile_id'])) {
    $_SESSION['error'] = "Missing profile_id";
	header('Location: index.php');
	return;
}
//"SELECT profile"
$stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if($row == false){
	$_SESSION['error'] = 'Bad value for profile_id';
    header( 'Location: index.php' ) ;
    return;
}

?>
<!DOCTYPE html>
<html>
  <head>
    <title>06d2cbe8 Banuka Vidusanka </title>
	<?php require_once "bootstrap.php"; ?>
  </head>
  <body>
    <div class="container">
      
	  <div class="container">
	  <h1>Profile information</h1>
		<p>First Name: <?= (htmlentities($row['first_name'])); ?>
		</p>
		<p>Last Name: <?= (htmlentities($row['last_name'])); ?>
		</p>
		<p>Email: <?= (htmlentities($row['email'])); ?>
		</p>
		<p>Headline:<br/>
		 <?= (htmlentities($row['headline'])); ?>
		</p>
		<p>Summary:<br/>
		 <?= (htmlentities($row['summary'])); ?>
		
		</p>
	  <p>
	  <a href="index.php">Done</a>
	  </div>
	  
  </body>
</html>
