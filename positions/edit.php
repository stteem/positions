<?php
require_once "pdo.php";
require_once "util.php";
session_start();


if (! isset($_SESSION['user_id'])) {
	die("ACCSESS DENIED");
    return;
}

if (isset($_POST['cancel'])) {
    header('Location: index.php');
    return;
}

// Make sure the REQUEST parameter is present
if (! isset($_REQUEST['profile_id'])) {
    $_SESSION['error'] = "Missing profile_id";
    header('Location: index.php');
    return;
}


if (isset($_POST['save'])) {
	
	if (strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1
     	|| strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1) {
		$_SESSION['error'] = "All fields are required";
		header("Location: edit.php?profile_id=".$_REQUEST["profile_id"]);
		return;
	}

    $msg = validatePos();
    if(is_string($msg)) {
        $_SESSION['error'] = $msg;
        header("Location: edit.php?profile_id=".$_REQUEST["profile_id"]);
        return;
    }

	if (! preg_match("/@/", $_POST['email'])) {
		$_SESSION['error'] = 'Email address must contain @';
		header("Location: edit.php?profile_id=".$_REQUEST["profile_id"]);
		return;
	}
	

    $sql = "UPDATE profile SET first_name = :first_name,
        last_name = :last_name, email = :email,
        headline = :headline, summary = :summary
        WHERE profile_id = :profile_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':first_name' => $_POST['first_name'],
        ':last_name' => $_POST['last_name'],
        ':email' => $_POST['email'],
        ':headline' => $_POST['headline'],
        ':summary' => $_POST['summary'],
        ':profile_id' => $_POST['profile_id']));



    //Clear out the old position entries
    $stmt = $pdo->prepare('DELETE FROM position WHERE profile_id = :pid');
    $stmt->execute(array(':pid' => $_REQUEST['profile_id']));

    //Insert the position entries
    $rank = 1;
    for ($i=1 ; $i <= 9 ; $i++) { 
        if (! isset($_POST['year'.$i])) continue;
        if (! isset($_POST['desc'.$i])) continue;
        $year = $_POST['year'.$i];
        $desc = $_POST['desc'.$i];

        $stmt = $pdo->prepare('INSERT INTO position (profile_id, rank, year, description) VALUES (:pid, :rank, :year, :des)');
        $stmt->execute(array(
            ':pid' => $_REQUEST['profile_id'],
            ':rank' => $rank,
            ':year' => $year,
            ':des' => $desc));
        $rank++;
    }

    //$_SESSION['green'] = 'Record edited';
    header( 'Location: index.php' );
    return;
}



$stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_REQUEST['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Could not load profile';
    header( 'Location: index.php' );
    return;
}

$f = htmlentities($row['first_name']);
$l = htmlentities($row['last_name']);
$e = htmlentities($row['email']);
$h = htmlentities($row['headline']);
$s = htmlentities($row['summary']);
$profile_id = $_GET['profile_id'];



?>


<!DOCTYPE html>
<html>
<head>
<title>Uwem Effiong Uke's Profile Edit</title>
<!-- bootstrap.php - this is HTML -->

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" 
    href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" 
    integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" 
    crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" 
    href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" 
    integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" 
    crossorigin="anonymous">

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

</head>
<body>
<div class="container">
<h1>Editing Profile for <?= htmlentities($_SESSION['name']); ?></h1>
<?php 
// Flash pattern
flashMessage();
?>
<form method="post" action="edit.php">
<p>First Name:
<input type="text" name="first_name" size="60"
value="<?= $f ?>"></p>
<p>Last Name:
<input type="text" name="last_name" size="60"
value="<?= $l ?>"></p>
<p>Email:
<input type="text" name="email" size="30"
value="<?= $e ?>"></p>
<p>Headline:<br/>
<input type="text" name="headline" size="80"
value="<?= $h ?>"></p>
<p>Summary:<br/>
<textarea name="summary" value="" rows="8" cols="80"><?= $s ?>
</textarea></p>
<p>
<input type="hidden" name="profile_id"
value="<?= $profile_id ?>"></p>
<?php 

// Load up the position rows
$positions = loadPos($pdo, $_REQUEST['profile_id']);

$pos = 0;
echo('<p>Position: <input type="submit" id="addPos" value="+">'."\n");
echo('<div id="position_fields">'."\n");
foreach ($positions as $position) {
    $pos++;
    echo('<div id="position'.$pos.'">'."\n");
    echo('<p>Year: <input type="text" name="year'.$pos.'"');
    echo('value="'.$position['year'].'"/>'."\n");
    echo('<input type="button" value="-" ');
    echo('onclick="$(\'#position'.$pos.'\').remove();return false;">'."\n");
    echo("</p>\n");
    echo('<textarea name="desc'.$pos.'" rows="8" cols="80">'."\n");
    echo(htmlentities($position['description'])."\n");
    echo("\n</textarea>\n</div>\n");
}
echo("</div></p>\n");

?>

<p>
<input type="submit" name="save" value="Save">
<input type="submit" name="cancel" value="Cancel">
</p>
</form>

<script src="js/jquery-3.3.1.min.js"></script>
<script type="text/javascript">

countPos = <?= $pos ?>;
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
