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
	
	$msg = validateEdu();
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
		
		// Insert edu entrys
		$rankEdu = 1;
		for($i=1; $i<=9;$i++) {
			if ( ! isset($_POST['edu_year'.$i]) ) continue;
			if ( ! isset($_POST['edu_school'.$i]) ) continue;
			$year = $_POST['edu_year'.$i];
			$school = $_POST['edu_school'.$i];
			
			//extract institute if exist
			$institution_id = false;
			$stmt = $pdo->prepare('SELECT institution_id FROM institution WHERE name = :name');
			$stmt->execute(array(':name' => $school));
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			
			//if instutute exiist get id
			if ( $row !==false ) 
				{ $institution_id = $row['institution_id'];} 
			else{
			$stmt = $pdo->prepare('INSERT INTO Institution (  name ) VALUES ( :name )');
			$stmt->execute(array( ':name' => $school ));
			 $institution_id = $pdo->lastInsertID();
			}
			
			$stmt = $pdo->prepare('INSERT INTO Education ( profile_id, institution_id, year, rank) VALUES ( :profile_id, :institution_id, :year, :rankEdu )');
			$stmt->execute(array(
			 ':profile_id' => $profile_id,
			 ':institution_id' => $institution_id,
			 ':year' => $year,
			 ':rankEdu' => $rankEdu)
			 );
			 $rankEdu++;
		}
		
		
		
		$_SESSION['success'] = 'Profile added';
		header( 'Location: index.php' ) ;
        return;

}		
?>
	
<!DOCTYPE html>
<html>
  <head>
    <title>dff0ec9d Banuka Vidusanka - Automobile Tracker</title>
	<!-- head stuff here -->
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
		<textarea name="summary" rows="4" cols="80"></textarea>
		</p>

			<hr />
			Education: <input type="submit" id="addEdu" value="+">
			&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;
			&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;
			Position: <input type="submit" id="addPos" value="+">
			<br />
			
			<div id="edu_fields" style="padding: 5px; ; width: 45%; display: inline-block;">
			</div>
			
		<div id="position_fields" style="padding: 5px; width: 45%; display: inline-block; vertical-align: top">
		</div>
				
		<br />
		<p>
		<input type="submit" name = "add" value="Add" >
		<input type="submit" name="cancel" value="Cancel">
		</p>
		</form>
		

<script type="text/javascript" src="jquery.min.js">

</script>		
<script>
countPos = 0;
countEdu = 0;
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
            '<div style="padding: 10px; border: 1px solid blue;" id="position'+countPos+'"> \
            <p>Year: <input type="text" name="year'+countPos+'" value="" /> \
            <input type="button" value="-" \
                onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
            <textarea name="desc'+countPos+'" rows="4" cols="30"></textarea>\
            </div>');
    });
	
	$('#addEdu').click(function(event){
        event.preventDefault();
        if ( countEdu >= 9 ) {
            alert("Maximum of nine education entries exceeded");
            return;
        }
        countEdu++;
        window.console && console.log("Adding education "+countEdu);

        $('#edu_fields').append(
            '<div style="padding: 10px; border: 1px solid brown;" id="edu'+countEdu+'"> \
            <p>Year: <input type="text" name="edu_year'+countEdu+'" value="" /> \
            <input type="button" value="-" onclick="$(\'#edu'+countEdu+'\').remove();return false;"><br>\
            <p>School: <input type="text" size="30" name="edu_school'+countEdu+'" class="school" value="" />\
            </p></div>'
        );
		
        $('.school').autocomplete({ source: "school.php" });

    });
	
});
</script>	  
	  
	  
	  
    </div>
  </body>
</html>	  