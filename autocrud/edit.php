<?php
require_once "pdo.php";
session_start();

if ( ! isset($_SESSION["email"]) ) {
    die('ACCESS DENIED');
}

// logout go back to index.php
if ( isset($_POST['cancel']) ) {
    header('Location: index.php');
    return;
}



if ( isset($_POST['make']) && isset($_POST['model']) && isset($_POST['year']) && isset($_POST['mileage']) ) {
	if ( strlen($_POST['make']) < 1 ) {
		$_SESSION["error"] = "Make is required";
		header( "Location: edit.php?autos_id=".$_POST['autos_id'] );
            return;
	}else if ((!is_numeric($_POST['year'])) || !is_numeric($_POST['mileage'])){
		$_SESSION["error"] = "Mileage and year must be numeric";
		header( "Location: edit.php?autos_id=".$_POST['autos_id'] );
            return;
	}else{
		
		$sql = "UPDATE autos SET make = :make,
            model = :model, year = :year, mileage = :mileage
            WHERE autos_id = :autos_id";
		$stmt = $pdo->prepare($sql);
		$stmt->execute(array(
        ':make' => $_POST['make'],
		':model' => $_POST['model'],
		':year' => $_POST['year'],
		':mileage' => $_POST['mileage'],
        ':autos_id' => $_POST['autos_id']));
		
		$_SESSION['success'] = 'Record updated';
		header( 'Location: index.php' ) ;
            return;

	}
}	

// Guardian: Make sure that autos_id is present
if ( ! isset($_GET['autos_id']) ) {
  $_SESSION['error'] = "Missing autos_id";
  header('Location: index.php');
  return;
}

$stmt = $pdo->prepare("SELECT * FROM autos where autos_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['autos_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for autos_id';
    header( 'Location: index.php' ) ;
    return;
}

// Flash pattern
if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}

$ma = htmlentities($row['make']);
$mo = htmlentities($row['model']);
$yr = htmlentities($row['year']);
$mi = htmlentities($row['mileage']);
$autos_id = $row['autos_id'];

?>


<!DOCTYPE html>
<html>
  <head>
    <title>ec872b98 Banuka Vidusanka - Automobile Tracker</title>
	<?php require_once "bootstrap.php"; ?>
  </head>
  <body>
    <div class="container">
	
<h1>Editing Automobile</h1>

<form method="post">
<p>Make<input type="text" name="make" size="40" value="<?= $ma ?>"/></p>
<p>Model<input type="text" name="model" size="40" value="<?= $mo ?>"/></p>
<p>Year<input type="text" name="year" size="10" value="<?= $yr ?>"/></p>
<p>Mileage<input type="text" name="mileage" size="10" value="<?= $mi ?>"/></p>
<input type="hidden" name="autos_id" value="<?= $autos_id ?>">
<input type="submit" value="Save">
<input type="submit" name="cancel" value="Cancel">
</form>




    </div>
  </body>
</html>	 