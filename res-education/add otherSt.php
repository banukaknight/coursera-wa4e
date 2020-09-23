<?php
require_once "pdo.php";
require_once "util.php";

session_start();

if( ! isset($_SESSION['user_id'])) {
    die("ACCESS DENIED");
    return;
}

if ( isset($_POST['cancel'] ) ) {
    header("Location: index.php");
    return;
}

if ( isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary']) ) {

    $msg = validateProfile();
    if ( is_string($msg) ) {
        $_SESSION['error'] = $msg;
        header("Location: add.php");
        return;
    }

    $msg = validatePos();
    if ( is_string( $msg ) ) {
        $_SESSION['error'] = $msg;
        header("Location: add.php");
        return;
    }

    $msg = validateEdu();
        if (is_string($msg)) {
            $_SESSION['error'] = $msg;
            header("Location: add.php");
            return;
        }

    $stmt = $pdo->prepare('INSERT INTO profile (user_id, first_name, last_name, email, headline, summary) VALUES (:uid, :fn, :ln, :em, :he, :su)');
    $stmt->execute(array(
        ':uid' => $_SESSION['user_id'],
        ':fn' => $_POST['first_name'],
        ':ln' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':he' => $_POST['headline'],
        ':su' => $_POST['summary'])
    );
    $profile_id = $pdo->lastInsertId();

    $rank = 1;
    for($i=1; $i<=9; $i++) {
        if ( ! isset ($_POST['year'.$i]) ) continue;
        if ( ! isset ($_POST['desc'.$i]) ) continue;
        $year = $_POST['year'.$i];
        $desc = $_POST['desc'.$i];
        $stmt = $pdo->prepare('INSERT INTO Position (profile_id, rank, year, description) VALUES (:pid, :rank, :year, :desc)');
        $stmt->execute(array(
           ':pid' => $profile_id,
           ':rank' => $rank,
           ':year' => $year,
           ':desc' => $desc)
        );
        $rank++;
    }

    $rank = 1;
    for($i=1; $i<=9; $i++) {
        if (!isset($_POST['edu_year'.$i])) continue;
        if (!isset($_POST['edu_school'.$i])) continue;
        $year = $_POST['edu_year'.$i];
        $school = $_POST['edu_school'.$i];
        $institution_id = false;
        $stmt = $pdo->prepare('SELECT institution_id FROM institution WHERE name = :name');
        $stmt->execute(array(':name' => $school));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row !== false) $institution_id = $row['institution_id'];
        
        if ( $institution_id === false ) {
            $stmt = $pdo->prepare('INSERT INTO institution (name) VALUES (:name)');
            $stmt->execute(array(':name' => $school));
            $institution_id = $pdo->lastInsertId();
        }
        $stmt = $pdo->prepare('INSERT INTO education (profile_id, rank, year, institution_id) VALUES (:pid, :rank, :year, :iid)');
        $stmt->execute(array(
            ':pid' => $profile_id,
            ':rank' => $rank,
            ':year' => $year,
            ':iid' => $institution_id)
        );
        $rank++;
    }

    $_SESSION['success'] = "Profile added";
    header("Location: index.php");
    return;
}

?>
<!DOCTYPE html>
<html>
<head>
<title>George Nicolae Pascu Profile Add</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css"> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.0/jquery.min.js" integrity="sha256-xNzN2a4ltkB44Mc/Jz3pT4iU1cmeR0FkXs4pru/JxaQ=" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>
</head>

<body style="font-family: sans-serif;">
<div class="container">
<h1>Adding Profile for <?= htmlentities($_SESSION['name']); ?></h1>
<?php flashMessages(); ?>
<form method="post">
    <!-- Added required field here to make the field required -->
    <p>First Name:
        <input type="text" name="first_name" size="60"/>
    </p>
    <p>Last Name:
        <input type="text" name="last_name" size="60"/>
    </p>
    <p>Email:
        <input type="text" name="email" size="30"/>
    </p>
    <p>Headline: <br>
        <input type="text" name="headline" size="80"/>
    </p>
    <p>Summary:<br>
        <textarea name="summary" rows="8" cols="80"></textarea>
    </p>
    <p>Education:
        <input type="submit" id="addEdu" value="+">
        <div id="edu_fields"></div>
    </p>
    <p>Position:
        <input type="submit" id="addPos" value="+">
        <div id="position_fields"></div>
    </p>
    <p>
    <input type="submit" value="Add"/> 
    <input type="submit" name="cancel" value="Cancel">
    </p>
</form>
<script>
    countPos = 0;
    countEdu = 0;
    $(document).ready(function(){
    window.console && console.log('Document ready called');
    $('#addPos').click(function(event){
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

        $('#addEdu').click(function(event){
            event.preventDefault();
            if ( countEdu >=9 ) {
                alert("Maximum of nine education entries exceeded");
                return;
            }
            countEdu++;
            window.console && console.log("Adding education "+countEdu);
            var source = $("#edu-template").html();
            $('#edu_fields').append(source.replace(/@COUNT@/g,countEdu));

            $('.school').autocomplete({
                source: "school.php"
            });
        });

        $('.school').autocomplete({
            source: "school.php"
        });    
    });
</script>

<script id="edu-template" type="text">
    <div id="edu@COUNT@">
        <p>Year: <input type="text" name="edu_year@COUNT@" value="" />
        <input type="button" value="-" onclick="$('#edu@COUNT@').remove();return false;"><br>
        <p>School: <input type="text" size="80" name="edu_school@COUNT@" class="school" value=""></p>
        </p>
    </div>
</script>

</div>
</body>
</html>