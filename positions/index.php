<?php 
require_once "pdo.php";
session_start();


// If the user requested logout go back to index.php
if ( isset($_POST['logout']) ) {
    header('Location: index.php');
    return;
}


$stmt = $pdo->query("SELECT profile_id, first_name, last_name, headline FROM profile ");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);


/*foreach ($rows as $row) {
	echo "$name"."\n";
	# code...
}*/

?>



<!DOCTYPE html>
<html>
<head>
<title>Uwem Effiong Uke's Resume Registry</title>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

</head>
<body>
<div class="container">
<h1>Uwem Uke's Resume Registry</h1>
<?php
//Flash message
if (isset($_SESSION['success']) ) {
    echo('<p style="color:green">'.$_SESSION['success']."</p>\n");
    unset($_SESSION['success']);
}

if (isset($_SESSION['error']) ) {
    echo('<p style="color:red">'.htmlentities($_SESSION['error'])."</p>\n");
    unset($_SESSION['error']);
}

?>

<?php 
	//Show login link only if user is not in session
	if (! isset($_SESSION['name']) ) { 
?>
	<p><a href="login.php">Please log in</a></p>
<?php 
	} 
	//Show logout link only if user is in session
	else {
?>
	<p><a href="logout.php">Log out</a></p>
<?php
	}
?>

<?

//Show "No fows found" if there are no records in the database
if ($rows == false) {
	echo "<p>No rows found</p>";
}
else {
	// Create table even if user is not in session
	echo ('<table border="1">'."\n");
	echo ("<tr><th>Name</th><th>Headline</th>");

	// Create "Action" table-head only if user is in session
	if(isset($_SESSION['name']) ) {
		echo ("<th>Action</th>");
	}
	echo ("</tr>");
	

	foreach ($rows as $row) {
		echo ("<tr><td>");
		echo ('<a href="view.php?profile_id='.$row['profile_id'].'">');
		echo(htmlentities(ucfirst(strtolower($row['first_name']))." ".ucfirst(strtolower($row['last_name'])) ));
		echo('</a>');
		echo ("</td><td>");
		echo(htmlentities($row['headline']));
		echo ("</td>");
		if(isset($_SESSION['name'])) {
			echo ("<td>");
			echo('<a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a> / ');
		    echo('<a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a>');
			echo ("</td>");
		}
		echo ("</tr>\n");
	}

	echo('</table>'. "\n");
	echo "<br>";
}
?>
<?
if (isset($_SESSION['name'])) {
?>
	<p><a href="add.php">Add New Entry</a></p>
<?	
}
?>



<!--<tr><th>Name</th><th>Headline</th><tr>
<tr><td>
<a href="view.php?profile_id=305">Besnik Abrashi</a></td><td>
Web Developer</td></tr>
<tr><td>
<a href="view.php?profile_id=306">a b</a></td><td>
h</td></tr>
</table>-->
</div>
</body>
