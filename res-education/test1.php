<?php
require_once "pdo.php";
require_once "util.php";
session_start();

$profile_id = 27;
$positions = loadPos($pdo, $profile_id);
$countposi = count($positions);
$JASONpositions = json_encode($positions);


print("<pre>".print_r($positions,true)."</pre>");
print "php ". $countposi;                       

?>
<html>
  <head>
    <meta name="generator"
    content="HTML Tidy for HTML5 (experimental) for Windows https://github.com/w3c/tidy-html5/tree/c63cc39" />
    <title></title>
  </head>
  <body>
  <p id="demo"></p>
  <script type="text/javascript" src="jquery.min.js"></script> 
  <script>
        document.getElementById(&quot;demo&quot;).innerHTML = &lt;?php echo $countposi ?&gt;;
        document.getElementById(&quot;demo&quot;).innerHTML = &lt;?php echo $JASONpositions ?&gt;;
        console.log(&quot;hi&quot;);
var JASONpositions = &lt;?php echo $JASONpositions ?&gt;;
for(var i = 0; i &lt; JASONpositions.length; i++) {
    var obj = JASONpositions[i];
console.log(&quot;sup&quot;);
    console.log(obj.year);
        console.log(obj.description);
        
}

</script></body>
</html>
