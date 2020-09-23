<?php
require_once "pdo.php";
session_start();

if ( ! isset($_SESSION["user_id"]) ) {
    die('ACCESS DENIED');
}

// Guardian: Make sure that profile_id is present
if ( ! isset($_GET['profile_id']) ) {
  $_SESSION['error'] = "Missing profile_id";
  header('Location: index.php');
  return;
}

if ( isset($_POST['delete']) && isset($_POST['profile_id']) ) {
	
	//delete from positions
	$stmt = $pdo->prepare("DELETE FROM position WHERE profile_id = :zip");
	$stmt->execute(array(':zip' => $_POST['profile_id']));
	window.console && console.log("delete positions");
	
	
    $sql = "DELETE FROM Profile WHERE profile_id = :zip";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':zip' => $_POST['profile_id']));
    $_SESSION['success'] = 'Profile deleted';
	
    header( 'Location: index.php' ) ;
    return;
}

$stmt = $pdo->prepare("SELECT first_name, last_name, profile_id FROM Profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for profile_id';
    header( 'Location: index.php' ) ;
    return;
}



?>
<h1>Deleteing Profile</h1>
<p>First Name:  <?= htmlentities($row['first_name']) ?></p>
<p>Last  Name:  <?= htmlentities($row['last_name']) ?></p>

<form method="post">
<input type="hidden" name="profile_id" value="<?= $row['profile_id'] ?>">
<input type="submit" value="Delete" name="delete">
<a href="index.php">Cancel</a>
</form>
