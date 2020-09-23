<?php

//util.php
function flashMessages() {
	if ( isset($_SESSION["success"]) ) {
				echo('<p style="color:green">'.$_SESSION["success"]."</p>\n");
				unset($_SESSION["success"]);
				  }
	if ( isset($_SESSION["error"]) ) {
				echo('<p style="color:red">'.$_SESSION["error"]."</p>\n");
				unset($_SESSION["error"]);
	}	
}

//utility
function validateProfile() {
	if ( ( strlen($_POST['first_name']) < 1 )|| ( strlen($_POST['last_name']) < 1 )|| ( strlen($_POST['email']) < 1 )|| ( strlen($_POST['headline']) < 1 )|| ( strlen($_POST['summary']) < 1 ) ) {
		return "All fields are required";
	}
	if (strpos($_POST["email"],'@') === false) {
		return "Email address must contain @";
	}
	return true;
}

// post date checkeck
function validatePos() {
	for($i=1; $i<=9;$i++) {
		if ( ! isset($_POST['year'.$i]) ) continue;
		if ( ! isset($_POST['desc'.$i]) ) continue;
		$year = $_POST['year'.$i];
		$desc = $_POST['desc'.$i];
		if ( strlen($year) ==0 || strlen($desc) == 0 ) {
			return "All fields are required in Posi";
		}
		if ( ! is_numeric($year) ) {
			return "Posi-year must be numeric";
		}
	}
	return true;
}

//post data check for edu
function validateEdu() {
	for($i=1; $i<=9;$i++) {
		if ( ! isset($_POST['edu_year'.$i]) ) continue;
		if ( ! isset($_POST['edu_school'.$i]) ) continue;
		$edu_year = $_POST['edu_year'.$i];
		$edu_school = $_POST['edu_school'.$i];
		if ( strlen($edu_year) ==0 || strlen($edu_school) == 0 ) {
			return "All fields are required in Edu";
		}
		if ( ! is_numeric($edu_year) ) {
			return "Edu-year must be numeric";
		}
	}
	return true;
}

function loadPos($pdo, $profile_id) {
	$stmt = $pdo->prepare('SELECT * FROM Position WHERE profile_id = :prof ORDER BY rank');
	$stmt->execute(array( ':prof' => $profile_id)) ;
	$positions = $stmt ->fetchAll(PDO::FETCH_ASSOC);
	// $positions = array();
	// while ( $row = $stmt ->fetch(PDO::FETCH_ASSOC) ) { $positions[] = $row;	}
	return $positions;
}

function loadEdu($pdo, $profile_id) {
	$stmt = $pdo->prepare('SELECT year, name FROM Education JOIN Institution ON Education.institution_id = Institution.institution_id WHERE profile_id = :prof ORDER BY rank');
	$stmt->execute(array( ':prof' => $profile_id)) ;
	$educations = $stmt ->fetchAll(PDO::FETCH_ASSOC);
	return $educations;
}

	
	
