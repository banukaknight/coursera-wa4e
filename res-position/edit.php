<?php
require_once "pdo.php";
require_once "util.php";
session_start();

if ( ! isset($_SESSION["user_id"]) ) {
    die('ACCESS DENIED');
}
if ( ! isset($_GET['profile_id'])) {
    $_SESSION['error'] = "Missing profile_id";
	header('Location: index.php');
	return;
}
// logout go back to index.php
if ( isset($_POST['cancel']) ) {
    header('Location: index.php');
    return;
}


if ( isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])  ) {
	
	$msg = validateProfile();
	if (is_string($msg)){
		$_SESSION["error"] = $msg;
		header( "Location: edit.php?profile_id=" . $_REQUEST["profile_id"]);
		return;
	}
	$msg = validatePos();
	if (is_string($msg)){
		$_SESSION["error"] = $msg;
		header( "Location: add.php");
		return;
	}

	
		$sql = "UPDATE Profile SET first_name = :first_name, last_name = :last_name, email = :email, headline = :headline, summary = :summary WHERE profile_id = :profile_id ";
		$stmt = $pdo->prepare($sql);
		$stmt->execute(array(
		':profile_id' => $_REQUEST['profile_id'],
        ':first_name' => $_POST['first_name'],
		':last_name' => $_POST['last_name'],
		':email' => $_POST['email'],
		':headline' => $_POST['headline'],
		':summary' => $_POST['summary']
		));
		
		//del old
		$stmt = $pdo->prepare('DELETE FROM Position WHERE profile_id = :pid');
		$stmt->execute(array( ':pid' => $_GET['profile_id']));
		
		//inserts
		$rank = 1;
		for($i=1; $i<=9;$i++) {
			if ( ! isset($_POST['year'.$i]) ) continue;
			if ( ! isset($_POST['desc'.$i]) ) continue;
			$year = $_POST['year'.$i];
			$desc = $_POST['desc'.$i];
			
			$stmt = $pdo->prepare('INSERT INTO Position ( profile_id, rank, year, description) VALUES ( :profile_id, :rank, :year, :description )');
			$stmt->execute(array(
			 ':profile_id' => $_REQUEST['profile_id'],
			 ':rank' => $rank,
			 ':year' => $year,
			 ':description' => $desc)
			 );
			 $rank++;
		}
		
		$_SESSION['success'] = 'Record updated';
		header( 'Location: index.php' ) ;
            return;

	
}	

$stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :xyz ");
$stmt->execute(array(":xyz" => $_REQUEST['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);

//check if profile exist
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for profile_id';
    header( 'Location: index.php' ) ;
    return;
}
//check if profile is owned by the logged in user
if( htmlentities($row['user_id']) != $_SESSION['user_id'] ) {
	$_SESSION['error'] = $_SESSION['name'].' does not own the profile';
    header( 'Location: index.php' ) ;
    return;
}

$fn = htmlentities($row['first_name']);
$ln = htmlentities($row['last_name']);
$em = htmlentities($row['email']);
$hd = htmlentities($row['headline']);
$sm = htmlentities($row['summary']);

$user_id = $row['user_id']; //NEEDED or not
$profile_id = $row['profile_id'];

//loadup position rows
$positions = loadPos($pdo, $_REQUEST['profile_id']);
$countposi = count($positions);
$JASONpositions = json_encode($positions);

?>


<!DOCTYPE html>
<html>
  <head>
    <title>06d2cbe8 Banuka Vidusanka - Automobile Tracker</title>
	<?php require_once "bootstrap.php"; ?>
		
  </head>
  <body>
    <div class="container">
	
<h1>Editing Profile for: <?= htmlentities($_SESSION["name"]); ?></h1>
         
<?php flashMessages(); ?> <!-- function from util.php -->

<form name="myForm" method="post">
<p>First Name:<input type="text" name="first_name" size="40" value="<?= $fn ?>"/></p>
<p>Last Name:<input type="text" name="last_name" size="40" value="<?= $ln ?>"/></p>
<p>Email:<input type="text" name="email" size="40" value="<?= $em ?>"/></p>
<p>Headline:<br/><input type="text" name="headline" size="80" value="<?= $hd ?>"/></p>
<p>Summary:<br/><textarea name="summary" rows="8" cols="80"><?= $sm ?></textarea></p>

<input type="hidden" name="profile_id" value="<?= $profile_id ?>">

		<p>
		Position: <input type="submit" id="addPos" value="+">
		<div id="position_fields">
	
	
	
	
		</div>
		</p>		

<input type="submit" value="Save" >
<input type="submit" name="cancel" value="Cancel">
</form>

<script type="text/javascript" src="jquery.min.js">
</script>		
<script>
countPos = 0;
// http://stackoverflow.com/questions/17650776/add-remove-html-inside-div-using-javascript
$(document).ready(function(){
    window.console && console.log('Document ready called');
	
	//pre-exist stuff zzzzzzzzzzzzzzzzzzzzzzz
	var JASONpositions = <?php echo $JASONpositions ?>;
	
	// SOMETHING IS WRONG HERE. it doesn't replace all the values
	
	for(j=0; j<JASONpositions.length; ++j){
	var obj = JASONpositions[j];
	countPos++;
	window.console && console.log("PRE position "+countPos);		
	$('#position_fields').append(
            '<div id="position'+countPos+'"> \
            <p>Year: <input type="text" name="year'+countPos+'" value="'+ obj.year+'" /> \
            <input type="button" value="-" \
                onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
            <textarea name="desc'+countPos+'" rows="8" cols="80">'+ obj.description+'</textarea>\
            </div>');
	
	
	}
	
	// new add button stuff
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