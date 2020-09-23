<?php
    session_start();
        
// Demand a GET parameter
if ( ! isset($_SESSION["email"]) ) {
    die('Not logged in');
}
require_once "pdo.php";

// logout go back to index.php
if ( isset($_POST['logout']) ) {
    header('Location: index.php');
    return;
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
              echo('<p style="color:green">'.$_SESSION["success"]."</p>\n");
              unset($_SESSION["success"]);
          }  

       // Check if we are logged in!
          if ( ! isset($_SESSION["email"]) ) { ?>
      <p>Please 
      <a href="login.php">Log In</a> to start.</p><?php } else { ?>
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
      <p>
      <a href="add.php">Add New</a> | 
      <a href="logout.php">Logout</a></p><?php } ?>
    </div>
  </body>
</html>
