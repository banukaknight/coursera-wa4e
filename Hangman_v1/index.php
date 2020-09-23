<h1><center><a href="https://github.com/banukaknight/banukaknight.github.io">
Welcome to Hangman by Banuka</a></center></h1>

<?

$ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
$VOWELS  = 'AEIOU';

$phrase_s = "";
$guessed_s = "";

if(isset($_COOKIE['phrase_c'])) {
	$phrase_s = $_COOKIE['phrase_c'];}
if(isset($_COOKIE['guessed_c'])) {
	$guessed_s = $_COOKIE['guessed_c'];
}


#load file and pick a word randomly.
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
	echo "<h3>Phrase to Guess: $obscured_s";

	echo "<h3>Guessed sofar: $guessed_s";

	#check if WON!
	$obscured_temp = join("",$obscured_a);
	if ($obscured_temp == $phrase_s){
		echo "<h1>You WON! ...Loading new game...</h1>";
		loadWords();
		$page = $_SERVER['PHP_SELF'];
		header("Refresh: 3; url=$page"); #refresh after 1 sec
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
		echo "<img src=\"./hang/$hangcount.png\" alt=\"Got $hangcount letters wrong\" >";
	}else{
		echo "<img src=\"./hang/6.png\" alt=\"Got $hangcount letters wrong\">";
		echo "<h1>Game Over! ...Loading new game...</h1>";
		echo "<h3>Phrase was: $phrase_s</h3>";
		loadWords();
		$page = $_SERVER['PHP_SELF'];
		header("Refresh: 3; url=$page"); #refresh after 1 sec
	}


}


?>

<form method="POST">
<input type="submit" name="Startgame" value="START NEW GAME">
</form>

<?
if ( isset($_POST['Startgame']) ) {
	echo "<h2>... Loading New Game</h2>";
	loadWords();
	$page = $_SERVER['PHP_SELF'];
	header("Refresh: 1; url=$page"); #refresh after 1 sec
}
?>

<form method="POST">
<h4>Enter Letter: <input type="text" name="lettr"> 
<input type="submit"><br>
</form>


<?


if ( isset($_POST['lettr']) and ($_POST['lettr'] !== "")) {
	global $guessed_s;
	$inputletter = strtoupper( $_POST['lettr'][0] );
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
	
	

	#$_POST = array(); #reset POST data
	
}
getHangman();
obscurePhrase();

#
#unset($_POST['lettr']);
?>

