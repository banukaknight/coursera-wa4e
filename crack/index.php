<!DOCTYPE html>
<head><title>Banuka Vidusanka MD5 cracker</title></head>
<body>
<h1>Banuka Vidusanka - MD5 cracker</h1>
<p>This application takes an MD5 hash of a four digit pin and check all 10,000 possible four digit PINs to determine the PIN.</p>
<pre>
Debug Output:
<?php
$goodtext = "Not found";
// If there is no parameter, this code is all skipped
if ( isset($_GET['md5']) ) {
    $time_pre = microtime(true);
    $md5 = $_GET['md5'];

    // This is numbers
    $txt = "0123456789";
    $show = 15;
	$count1 = 0;
    // Outer loop go go through 0-9 for the
    // first position in our "possible" pre-hash number
    for($i=0; $i<strlen($txt); $i++ ) {
        $ch1 = $txt[$i];   // The first of 4 num

        // Our inner loop Not the use of new variables
        // $j and $ch2 
        for($j=0; $j<strlen($txt); $j++ ) {
            $ch2 = $txt[$j];  // Our second num
			for($m=0; $m<strlen($txt); $m++ ) {
				$ch3 = $txt[$m];  // Our 3rd num
				for($n=0; $n<strlen($txt); $n++ ) {
					$ch4 = $txt[$n];  // Our 4th num
//------------------
					// Concatenate the four characters together to 
					// form the "possible" pre-hash text
					$try = $ch1.$ch2.$ch3.$ch4;
					$count1 = $count1 + 1; //count checks
					// Run the hash and then check to see if we match
					$check = hash('md5', $try);
					if ( $check == $md5 ) {
						$goodtext = $try;
						break 4;   // Exit the inner loop
					}

					// Debug output until $show hits 0
					if ( $show > 0 ) {
						print "$check $try\n";
						$show = $show - 1;
					}
//--------------------			
				}
			}
		}
    }
	print "Total checks: ";
	print $count1;
	print "\n";
    // Compute elapsed time
    $time_post = microtime(true);
    print "Elapsed time: ";
    print $time_post-$time_pre;
    print "\n";
}
?>
</pre>
<!-- Use the very short syntax and call htmlentities() -->
<p>PIN: <?= htmlentities($goodtext); ?></p>
<form>
<input type="text" name="md5" size="40" />
<input type="submit" value="Crack MD5"/>
</form>
<ul>
<li><a href="index.php">Reset</a></li>
<li><a href="md5.php">MD5 Encoder</a></li>
<li><a
href="https://github.com/csev/wa4e/tree/master/code/crack"
target="_blank">Source code for this application</a></li>
</ul>
</body>
</html>

