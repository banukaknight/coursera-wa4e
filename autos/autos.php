<?php
// Demand a GET parameter
if ( ! isset($_GET['name']) || strlen($_GET['name']) < 1  ) {
    die('Name parameter missing');
}
require_once "pdo.php";


// logout go back to index.php
if ( isset($_POST['logout']) ) {
    header('Location: index.php');
    return;
}

$failure = false;  // If we have no POST data
//input validation
//The mileage and year need to be integers - is_numeric() 
//if the make is empty less than 1 character in the string
if ( isset($_POST['make']) && isset($_POST['year']) && isset($_POST['mileage']) ) {
	if ( strlen($_POST['make']) < 1 ) {
		$failure = "Make is required";
	}else if ((!is_numeric($_POST['year'])) || !is_numeric($_POST['mileage'])){
		$failure = "Mileage and year must be numeric";
	}else{
		$failure = "GOOD";
		$stmt = $pdo->prepare('INSERT INTO autos
        (make, year, mileage) VALUES ( :mk, :yr, :mi)');
		$stmt->execute(array(
        ':mk' => $_POST['make'],
        ':yr' => $_POST['year'],
        ':mi' => $_POST['mileage'])
    );

	}
}	
?>

<!DOCTYPE html>
<html>
<head>
<title>0de95629 Banuka Vidusanka - Automobile Tracker</title>
<?php require_once "bootstrap.php"; ?>
</head>
<body>
<div class="container">
<?php
if ( isset($_REQUEST['name']) ) {
    echo "<h1>Tracking Autos for  ";
    echo htmlentities($_REQUEST['name']);
    echo "</h1>\n";
}

if ( $failure !== false ) {
	if( $failure === "GOOD")
	{
	echo('<p style="color: green;">'.htmlentities('Record inserted')."</p>\n");	
	}else{
	//failure messages
    echo('<p style="color: red;">'.htmlentities($failure)."</p>\n");
	}
}

?>
<form method="post">

<p>Make:
<input type="text" name="make" size="60"/></p>
<p>Year:
<input type="text" name="year"/></p>
<p>Mileage:
<input type="text" name="mileage"/></p>
<input type="submit" value="Add">
<input type="submit" name="logout" value="Logout">
</form>

<h2>Automobiles</h2>

<ul>
<?php
$stmt = $pdo->query("SELECT make, year, mileage FROM autos");
while( $row = $stmt->fetch(PDO::FETCH_ASSOC) ){
	echo "<li>";
	echo htmlentities($row['year']);
	echo '&nbsp;';
	echo htmlentities($row['make']);
	echo ' / ';
	echo htmlentities($row['mileage']);
	echo "</li>";
}
?>
</ul>

</div>
</body>
</html>