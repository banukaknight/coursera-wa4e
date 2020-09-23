<?php
    session_start();
     require_once "pdo.php";   
// Demand a GET parameter
if ( ! isset($_SESSION["email"]) ) {
    die('ACCESS DENIED');
}


// logout go back to index.php
if ( isset($_POST['cancel']) ) {
    header('Location: index.php');
    return;
}

if ( isset($_POST['make']) && isset($_POST['model']) && isset($_POST['year']) && isset($_POST['mileage']) ) {
	if (( strlen($_POST['make']) < 1 ) ||( strlen($_POST['model']) < 1 ) ||( strlen($_POST['year']) < 1 ) ||( strlen($_POST['mileage']) < 1 )) {
		$_SESSION["error"] = "All fields are required";
		header( 'Location: add.php' ) ;
            return;
	}else if ((!is_numeric($_POST['year'])) || !is_numeric($_POST['mileage'])){
		$_SESSION["error"] = "Mileage and year must be numeric";
		header( 'Location: add.php' ) ;
            return;
	}else{
		$_SESSION["success"] = "Record added";
		$stmt = $pdo->prepare('INSERT INTO autos
        (make, model, year, mileage) VALUES ( :mk, :md, :yr, :mi)');
		$stmt->execute(array(
        ':mk' => $_POST['make'],
		':md' => $_POST['model'],
		':yr' => $_POST['year'],
        ':mi' => $_POST['mileage'])
    );
	
	header( 'Location: index.php' ) ;
            return;

	}
}	

?>
	
<!DOCTYPE html>
<html>
  <head>
    <title>ec872b98 Banuka Vidusanka - Automobile Tracker</title>
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
		<input type="text" name="make" size="60" /></p>
		<p>Model:
		<input type="text" name="model" size="60" /></p>
		<p>Year:
		<input type="text" name="year" /></p>
		<p>Mileage:
		<input type="text" name="mileage" /></p>
		<input type="submit" value="Add">
		<input type="submit" name="cancel" value="Cancel">
		</form>
	  
	  
	  
	  
    </div>
  </body>
</html>	  