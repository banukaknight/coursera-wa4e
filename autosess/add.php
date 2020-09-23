<?php
    session_start();
        
// Demand a GET parameter
if ( ! isset($_SESSION["email"]) ) {
    die('Not logged in');
}
require_once "pdo.php";

// logout go back to index.php
if ( isset($_POST['cancel']) ) {
    header('Location: view.php');
    return;
}

if ( isset($_POST['make']) && isset($_POST['year']) && isset($_POST['mileage']) ) {
	if ( strlen($_POST['make']) < 1 ) {
		$_SESSION["error"] = "Make is required";
		header( 'Location: add.php' ) ;
            return;
	}else if ((!is_numeric($_POST['year'])) || !is_numeric($_POST['mileage'])){
		$_SESSION["error"] = "Mileage and year must be numeric";
		header( 'Location: add.php' ) ;
            return;
	}else{
		$_SESSION["success"] = "Record inserted";
		$stmt = $pdo->prepare('INSERT INTO autos
        (make, year, mileage) VALUES ( :mk, :yr, :mi)');
		$stmt->execute(array(
        ':mk' => $_POST['make'],
        ':yr' => $_POST['year'],
        ':mi' => $_POST['mileage'])
    );
	
	header( 'Location: view.php' ) ;
            return;

	}
}	

?>
	
<!DOCTYPE html>
<html>
  <head>
    <title>0d970b78 Banuka Vidusanka - Automobile Tracker</title>
	<?php require_once "bootstrap.php"; ?>
  </head>
  <body>
    <div class="container">
      <?php
      if ( isset($_SESSION["email"]) ) {
          echo "<h1>Tracking Autos for  ";
          echo htmlentities($_SESSION["email"]);
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
			
		<form method="post">
		<p>Make:
		<input type="text" name="make" size="60"/></p>
		<p>Year:
		<input type="text" name="year"/></p>
		<p>Mileage:
		<input type="text" name="mileage"/></p>
		<input type="submit" value="Add">
		<input type="submit" name="cancel" value="Cancel">
		</form>
	  
	  
	  
	  
    </div>
  </body>
</html>	  