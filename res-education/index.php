<?php // Do not put any HTML above this line
	session_start();
	require_once "pdo.php";
	require_once "util.php";
?>
<!DOCTYPE html>
<html>
<head>
<title>dff0ec9d - Banuka Vidusanka - Resume Registry</title>
<?php require_once "bootstrap.php"; ?>
</head>
<body>
<div class="container"><h1>Banuka Vidusanka 's Resume Registry</h1>


<?php 
if ( isset($_SESSION["success"]) ) {
			echo('<p style="color:green">'.$_SESSION["success"]."</p>\n");
			unset($_SESSION["success"]);
			  }
		if ( isset($_SESSION["error"]) ) {
			echo('<p style="color:red">'.$_SESSION["error"]."</p>\n");
			unset($_SESSION["error"]);
}	
		
if ( ! isset($_SESSION["user_id"]) ) {
	//NOT-LOGGED IN
    echo '<p><a href="login.php">Please log in</a></p>';
	$stmt = $pdo->query("SELECT profile_id, user_id, first_name, last_name, headline FROM Profile");
	
	if ($stmt->rowCount() == 0){
		// do nothing if table is empty
	}else{
		
		echo ('<table border="2" width = "50%">'."\n");
		echo '<thead><tr><th>Name</th><th>Headline</th></tr></thead>';
		while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
			echo "<tr><td>";
			echo('<a href="view.php?profile_id='.$row['profile_id'].'">');
			echo(htmlentities($row['first_name']))." ";
			echo(htmlentities($row['last_name']));
			echo('</a> ');
			echo("</td><td>");
			echo(htmlentities($row['headline']));
			echo("</td></tr>\n");
		}
		echo "</table>";
	}
	
}else{
	//LOGGED IN
	echo '<p><a href="logout.php">Logout</a></p>';
	$stmt = $pdo->query("SELECT profile_id, user_id, first_name, last_name, headline FROM Profile");

	echo '<p><strong>Welcome user: '.htmlentities($_SESSION['name']). '</strong></p>';

	if ($stmt->rowCount() == 0){
		// do nothing if table is empty
	}else{
		
		echo ('<table border="2" width = "50%">'."\n");
		echo '<thead><tr><th>Name</th><th>Headline</th><th>Action</th></tr></thead>';
		while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
			echo "<tr><td>";
			echo('<a href="view.php?profile_id='.$row['profile_id'].'">');
			echo(htmlentities($row['first_name']))." ";
			echo(htmlentities($row['last_name']));
			echo('</a> ');
			echo("</td><td>");
			echo(htmlentities($row['headline']));
			echo("</td><td>");
			echo('<a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a> / ');
			echo('<a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a>');
			echo("</td></tr>\n");
		}
		echo "</table>";
	}

	echo '<p><a href="add.php">Add New Entry</a></p>';
	

}
?>



</div>
</body>

