<?php
require_once "pdo.php";
require_once "util.php";
session_start();

if (! isset($_SESSION['name'])) {
	die("ACCSESS DENIED");
	return;
}


// If the user requested logout go back to index.php
if (isset($_POST['cancel']) ) {
    header("Location: index.php");
    return;
}


if (isset($_POST['add'])) {
	unset($_SESSION['first_name']);
	unset($_SESSION['last_name']);
	unset($_SESSION['email']);
	unset($_SESSION['headline']);
	unset($_SESSION['summary']);

	if (strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1
     	|| strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1) {
		$_SESSION['error'] = "All fields are required";
		header("Location: add.php");
		return;
	}

	$msg = validatePos();
	if(is_string($msg)) {
		$_SESSION['error'] = $msg;
		header('Location: add.php');
		return;
	}

	if (! preg_match("/@/", $_POST['email'])) {
		$_SESSION['error'] = 'Email address must contain @';
		header("Location: add.php");
		return;
	}

	
		
		$stmt = $pdo->prepare('INSERT INTO profile(user_id, first_name, last_name, email, headline, summary)

							VALUES ( :uid, :fn, :ln, :em, :he, :su)');

		$stmt->execute(array(':uid' => $_SESSION['user_id'],

							':fn' => $_POST['first_name'],

							':ln' => $_POST['last_name'],

							':em' => $_POST['email'],

							':he' => $_POST['headline'],

							':su' => $_POST['summary'])

	);

		$profile_id = $pdo->lastInsertId();
		

		// Insert the position entries
		$rank = 1;
		for ($i=1 ; $i <= 9 ; $i++) { 
			
			if (isset($_POST['year'.$i]) && isset($_POST['desc'.$i])) {
				$year = $_POST['year'.$i];
				$desc = $_POST['desc'.$i];

				$stmt = $pdo->prepare("INSERT INTO position (profile_id, rank, year, description) VALUES (:pid, :rank, :year, :des)");
				$stmt->execute(array(':pid' => $profile_id,':rank' => $rank,':year' => $year,':des' => $desc ));
				$rank++;
			}
			else {
				continue;
			}		
		}
		

		$_SESSION['success'] = "Profile added";
    	header("Location:index.php");
    	return;
	
}


?>



<!DOCTYPE html>
<html>
<head>
<title>Uwem Effiong Uke's Profile Add</title>
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
<h1>Adding Profile for <?=htmlentities($_SESSION['name']); ?></h1>
<?php
//Flash message
flashMessage();

?>

<form method="post">
<p>First Name:
<input type="text" name="first_name" size="60"/></p>
<p>Last Name:
<input type="text" name="last_name" size="60"/></p>
<p>Email:
<input type="text" name="email" size="30"/></p>
<p>Headline:<br/>
<input type="text" name="headline" size="80"/></p>
<p>Summary:<br/>
<textarea name="summary" rows="8" cols="80"></textarea>
<p>
<p>Position:<br>
<input type="submit" id="addPos" value="+">
<div id="position_fields"></div>
</p>
<input type="submit" name="add" value="Add">
<input type="submit" name="cancel" value="Cancel">
</p>
</form>

<script src="js/jquery-3.3.1.min.js"></script>
<script type="text/javascript">
countPos = 0;

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
