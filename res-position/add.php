<?php
    session_start();
    require_once "pdo.php"; 
	require_once "util.php";	 
// Demand a session u_id
if ( ! isset($_SESSION["user_id"]) ) {
    die('ACCESS DENIED');
}

// logout go back to index.php
if ( isset($_POST['cancel']) ) {
    header('Location: index.php');
    return;
}

$stmt = $pdo->prepare("SELECT * FROM users where user_id = :xyz");
$stmt->execute(array(":xyz" => $_SESSION["user_id"]));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for user_id';
    header( 'Location: index.php' ) ;
    return;
}

//handle incoming data
if ( isset($_POST['add']) &&  isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])  ) {
	
	$msg = validateProfile();
	if (is_string($msg)){
		$_SESSION["error"] = $msg;
		header( "Location: add.php");
		return;
	}
	
	$msg = validatePos();
	if (is_string($msg)){
		$_SESSION["error"] = $msg;
		header( "Location: add.php");
		return;
	}
		
		$sql = "INSERT INTO Profile ( user_id, first_name, last_name, email, headline, summary) VALUES (:user_id, :first_name, :last_name, :email, :headline, :summary)";
		$stmt = $pdo->prepare($sql);
		$stmt->execute(array(
		':user_id' => $_SESSION['user_id'],
		':first_name' => $_POST['first_name'],
		':last_name' => $_POST['last_name'],
		':email' => $_POST['email'],
		':headline' => $_POST['headline'],
		':summary' => $_POST['summary']
		));
		$profile_id = $pdo->lastInsertID();
		
		// Insert position entrys
		$rank = 1;
		for($i=1; $i<=9;$i++) {
			if ( ! isset($_POST['year'.$i]) ) continue;
			if ( ! isset($_POST['desc'.$i]) ) continue;
			$year = $_POST['year'.$i];
			$desc = $_POST['desc'.$i];
			
			$stmt = $pdo->prepare('INSERT INTO Position ( profile_id, rank, year, description) VALUES ( :profile_id, :rank, :year, :description )');
			$stmt->execute(array(
			 ':profile_id' => $profile_id,
			 ':rank' => $rank,
			 ':year' => $year,
			 ':description' => $desc)
			 );
			 $rank++;
		}
		
		
		
		$_SESSION['success'] = 'Profile added';
		header( 'Location: index.php' ) ;
        return;

}		
?>
	
<!DOCTYPE html>
<html>
  <head>
    <title>06d2cbe8 Banuka Vidusanka - Automobile Tracker</title>
	<?php require_once "bootstrap.php"; ?>
	<!-- not using script validation atm -->
		<script> 
		function validateForm() {
		  var fn = document.forms["myForm"]["first_name"].value;
		  var ln = document.forms["myForm"]["last_name"].value;
		  var em = document.forms["myForm"]["email"].value;
		  var hd = document.forms["myForm"]["headline"].value;
		  var sm = document.forms["myForm"]["summary"].value;
		  
		  if ((fn == "")|| (ln == "")|| (em == "")|| (hd == "")|| (sm == "") || (fn == null)|| (ln == null)|| (em == null)|| (hd == null)|| (sm == null)) {
			alert("All values are required");
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
          echo "<h1>Adding Profile for ";
          echo htmlentities($_SESSION["name"]);
          echo "</h1>\n";
      
      flashMessages();
	  
		?>
			
		<form name="myForm" method="post" >
		<p>First Name:
		<input type="text" name="first_name" size="60"/></p>
		<p>Last Name:
		<input type="text" name="last_name" size="60"/></p>
		<p>Email:
		<input type="text" name="email" size="30"/></p>
		<p>Headline:<br/>
		<input type="text" name="headline" size="80"/></p>
		<p>Summary:<br/>
		<textarea name="summary" rows="8" cols="80"></textarea>
		</p>
		<p>
		Position: <input type="submit" id="addPos" value="+">
		<div id="position_fields">
		</div>
		</p>		
		
		<p>
		<input type="submit" name = "add" value="Add" >
		<input type="submit" name="cancel" value="Cancel">
		</p>
		</form>
		

<script type="text/javascript" src="jquery.min.js">
</script>		
<script>
countPos = 0;
// http://stackoverflow.com/questions/17650776/add-remove-html-inside-div-using-javascript
$(document).ready(function(){
    window.console && console.log('Document ready called');
    $('#addPos').click(function(event){
        // http://api.jquery.com/event.preventdefault/
        event.preventDefault();
        if ( countPos >= 9 ) {
            alert("Maximum of nine position entries exceeded");
            return;
        }
        countPos++;
        window.console && console.log("Adding position "+countPos);
        $('#position_fields').append(
            '<div id="position'+countPos+'"> \
            <p>Year: <input type="text" name="year'+countPos+'" value="" /> \
            <input type="button" value="-" \
                onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
            <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
            </div>');
    });
});
</script>	  
	  
	  
	  
    </div>
  </body>
</html>	  