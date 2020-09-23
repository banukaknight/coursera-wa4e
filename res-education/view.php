<?php
    session_start();
    require_once "pdo.php"; 
	require_once "util.php";	
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

//loadup position rows
$positions = loadPos($pdo, $_REQUEST['profile_id']);
$educations = loadEdu($pdo, $_REQUEST['profile_id']);
?>
<!DOCTYPE html>
<html>
  <head>
    <title>7bec2531 Banuka Vidusanka </title>
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
		
		<?php 
		
		if (count($educations) == 0){
		// do nothing if table is empty
		}else{
			echo "<p>Education</p><ul>";
			
			for($j=0;$j<count($educations);++$j){
				echo "<li>";
				echo $educations[$j]["year"].": ";
				echo $educations[$j]["name"];
				echo "</li>";
			}
			echo "</ul>";
		}
		
		if (count($positions) == 0){
		// do nothing if table is empty
		}else{
			echo "<p>Position</p><ul>";
			//print("<pre>".print_r($positions,true)."</pre>");
			
			for($j=0;$j<count($positions);++$j){
				
				echo "<li>";
				echo $positions[$j]["year"].": ";
				echo $positions[$j]["description"];
				echo "</li>";
			}
			echo "</ul>";
		}
		
		?>
		
	  <p>
	  <a href="index.php">Done</a>
	  </div>
	  
  </body>
</html>
