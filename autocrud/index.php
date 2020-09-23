<?php // Do not put any HTML above this line
	session_start();
	require_once "pdo.php";
?>

<!DOCTYPE html>
<html>
<head>
<title>ec872b98 - Banuka Vidusanka - Welcome to Autos Database</title>
<?php require_once "bootstrap.php"; ?>
</head>
<body>
<div class="container">


<h1>Welcome to Autosmobiles Database - Banuka Vidusanka</h1>
<p><strong>Note:</strong> This  code is complete
</p>

<?php 
if ( ! isset($_SESSION["email"]) ) {
    echo '<p><a href="login.php">Please log in</a></p>';
	echo '<p>Attempt to <a href="add.php">add data</a> without logging in</p>';
}else{


$stmt = $pdo->query("SELECT make, model, year, mileage, autos_id FROM autos");

if ($stmt->rowCount() == 0){
	echo '<p>No rows found</p>';
}else{
	
	if ( isset($_SESSION["success"]) ) {
              echo('<p style="color:green">'.$_SESSION["success"]."</p>\n");
              unset($_SESSION["success"]);
          }
	if ( isset($_SESSION["error"]) ) {
        echo('<p style="color:red">'.$_SESSION["error"]."</p>\n");
        unset($_SESSION["error"]);
    }	  
	
	
	echo ('<table border="2" width = "50%">'."\n");
	echo '<thead><tr><th>Make</th><th>Model</th><th>Year</th><th>Mileage</th><th>Action</th></tr></thead>';
	while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
	echo "<tr><td>";
    echo(htmlentities($row['make']));
    echo("</td><td>");
    echo(htmlentities($row['model']));
    echo("</td><td>");
    echo(htmlentities($row['year']));
    echo("</td><td>");
	echo(htmlentities($row['mileage']));
    echo("</td><td>");
    echo('<a href="edit.php?autos_id='.$row['autos_id'].'">Edit</a> / ');
    echo('<a href="delete.php?autos_id='.$row['autos_id'].'">Delete</a>');
    echo("</td></tr>\n");
	}
	echo "</table>";
}

echo '<p><a href="add.php">Add New Entry</a></p>';
echo '<p><a href="logout.php">Logout</a></p>';

}
?>



</div>
</body>

