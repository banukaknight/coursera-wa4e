
<html>
<head>
	<title>Hangman by Banuka</title>
	<style>
body {
  background-color: linen;
}

h2 {
  color: maroon;
}
td {
	padding: 10px;
}
img {
	border-radius: 25px;
	border: 3px solid yellow;
}

input[type=button], input[type=submit], input[type=reset] {
  background-color: #4CAF50;
  color: white;
  padding: 6px 32px;
  margin: 4px 2px;
  cursor: pointer;
  border-radius: 5px;
}

input[type="submit"]:disabled {
  background: #dddddd;
}
	
</style>
	</head>
	<body>

<div style="padding:50px; background-image:url('./hang/bg.jpg');">
<h1><center><a href="https://banukaknight.github.io/">
Welcome to Hangman Game by Banuka</a></center></h1>

<?

$ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
#$VOWELS  = 'AEIOU';

$phrase_s = "";
$guessed_s = "";

if(isset($_COOKIE['phrase_c'])) {
	$phrase_s = $_COOKIE['phrase_c'];}
if(isset($_COOKIE['guessed_c'])) {
	$guessed_s = $_COOKIE['guessed_c'];
}


#load file and pick a word randomly.
#calling this function would rest the game and cookies
function loadWords() {
  $words =[];
  $numwords = 0;
  $fname = "./words.txt";

  if (file_exists($fname)) {
	  $fhandle = fopen($fname, "r");

	  while (true) {
		  $str = fgets($fhandle);
		  if (!$str) break;
		  $words[] = rtrim($str);
		  $numwords++;
	  }
	  fclose($fhandle);
	}else{
		#if words.txt file is not found, use array of phrases for the game.
		$words =["Smells Like Teen Spirit","Imagine","One","Billie Jean","Bohemian Rhapsody","Hey Jude","Like A Rolling Stone","I Can't Get No Satisfaction","God Save The Queen","Sweet Child O'Mine","London Calling","Waterloo Sunset","Hotel California","Your Song","Stairway To Heaven","The Twist","Live Forever","I Will Always Love You","Life On Mars?","Heartbreak Hotel","Over The Rainbow","What's Goin' On","Born To Run","Be My Baby","Creep","Bridge Over Troubled Water","Respect","Family Affair","Dancing Queen","Good Vibrations","Purple Haze","Yesterday","Jonny B Good","No Woman No Cry","Hallelujah","Every Breath You Take","A Day In The Life","Stand By Me","Papa's Got A Brand New Bag","Gimme Shelter","What'd I Say","Sultans Of Swing","God Only Knows","You've Lost That Lovin' Feeling","My Generation","Dancing In The Street","When Doves Cry","A Change Is Gonna Come","River Deep Mountain High","Best Of My Love"];

		$numwords = len($words);
	}

  global $phrase_s;
  $which = rand(0, $numwords - 1);
  $phrase_s =  strtoupper( $words[$which] );
  setcookie("phrase_c", $phrase_s, time() + 3600); #set new word
  setcookie("guessed_c", "", time() - 3600); #delete old guesses
}


function obscurePhrase(){
	global $phrase_s;
	global $guessed_s;
	global $ALPHABET;

	$phrase_a = str_split($phrase_s);
	$obscured_a = array(); #reset array

	foreach ($phrase_a as $char){
		if (!empty($char)){
			#if ( strpos($guessed_s, $char) !== false ) {
			if ( strstr($guessed_s, $char) ) {

				array_push($obscured_a,$char);
			}else if ( strstr($ALPHABET, $char) ){
				array_push($obscured_a,'_');
			}else{
				array_push($obscured_a,$char);
			}
		}
	}
	$obscured_s = join("&nbsp;",$obscured_a);
	echo "<h2>Phrase to Guess: $obscured_s</h2>";

	echo "<h3>Guessed sofar: $guessed_s</h3>";

	#check if WON!
	$obscured_temp = join("",$obscured_a);
	if ($obscured_temp == $phrase_s){
		echo "<h1>You WON! ...Loading new game...</h1>";
		loadWords();
		$page = $_SERVER['PHP_SELF'];
		header("Refresh: 5; url=$page"); #refresh after 4 sec
	}

}

function getHangman(){
	global $phrase_s;
	global $guessed_s;
	$hangcount = 0;

	$guessed_a = str_split($guessed_s);

	foreach ($guessed_a as $char){
		if (!empty($char)){
		if ( ! strstr($phrase_s, $char) ) {
			$hangcount += 1; #cout chars not in phrase.
		}}
	}

	if($hangcount<6){
		echo "<td><img src=\"./hang/$hangcount.png\" alt=\"Got $hangcount letters wrong\" ></td><td>";
	}else{
		echo "<td><img src=\"./hang/6gif.gif\" alt=\"Got $hangcount letters wrong\"><td><td>";
		echo "<h1>You LOST! ...Loading new game...</h1>";
		echo "<h2>Phrase was: $phrase_s</h2>";
		loadWords();
		$page = $_SERVER['PHP_SELF'];
		header("Refresh: 8; url=$page"); #refresh after few sec
	}

}

?>


<form method="POST">
<input type="submit" name="Startgame" value="START NEW GAME">
<i>Select Letter to guess:</i>
</form>

<?
if ( isset($_POST['Startgame']) ) {
	echo "<h2>... Loading New Game</h2>";
	loadWords();
	$page = $_SERVER['PHP_SELF'];
	header("Refresh: 1; url=$page"); #refresh after 1 sec
}
?>

<form method="POST" action="">
<?
$ALPHABET_a = str_split($ALPHABET);

foreach($ALPHABET_a as $i){
	$isdisabled = strstr($guessed_s, $i) ? 'disabled' : '';
	
	echo "<input class=\"alph\" type=\"submit\" name=\"action\" value=\"$i\" $isdisabled />";
	if($i == 'M'){echo "<br>";}
}
?>
</form>


<table>

<?
if (isset($_POST['action'])){
	
	global $guessed_s;
	$inputletter = $_POST['action'];
	if ( strstr($guessed_s, $inputletter) ) {
		#check if letter guessed before
		echo "<h5>Already tried this letter: $inputletter</h5>";
	}else if ( !(strstr($ALPHABET, $inputletter) ) ){
		echo "<h5>Not in ALPHABET: $inputletter</h5>";
	}else{
		echo "<h5>Letter entered: $inputletter</h5>";
		$guessed_s .= $inputletter;
	}

	global $phrase_s;
	setcookie("phrase_c", $phrase_s, time() + 3600);
	setcookie("guessed_c", $guessed_s, time() + 3600);
	
}else{
	echo "<h5>...Waiting for user input.</h5>";
}

getHangman(); #display hangman
obscurePhrase(); #display obscured phrase

?>

</td></tr></table>
<br>
<marquee><a href="https://www.linkedin.com/in/banuka/">Thank you for playing!</a></marquee>
</div>
</body>
</html>