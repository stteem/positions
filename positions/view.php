<?php
require_once "pdo.php";
session_start();


$profile_id = $_GET['profile_id'];

$stmt = $pdo->query("SELECT * FROM profile WHERE profile_id = '$profile_id'");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT * FROM position WHERE profile_id = '$profile_id'");
$pos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>



<!DOCTYPE html>
<html>
<head>
<title>Uwem Effiong Uke's Profile View</title>
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

</head>
<body>
<div class="container">
<h1>Profile information</h1>
<p>
<?php
foreach ($rows as $row) {
 	
	echo ("<p>First Name: ".$row['first_name']);
	echo "</p>";
	echo ("<p>Last Name: ".$row['last_name']);
	echo "</p>";
	echo ("<p>Email: ".$row['email']);
	echo "</p>";
	echo ("<p>Headline: <br>".$row['headline']);
	echo "</p>";
	echo "<p>";
	echo ("Summary: <br>".$row['summary']);
	echo "</p>";
}

echo ("<p>Positions:</p>");


foreach ($pos as $pid) {
	echo "<li>";
	echo ($pid['year']." ".$pid['description']);
	echo "</li>";
}

?>
</p>
<p>
<a href="index.php">Done</a>

</p>
<!--
<p>First Name:
Vuong</p>
<p>Last Name:
Nguyen</p>
<p>Email:
<a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="641001171024010a03080d170c140d07101116014a0a0110">[email&#160;protected]</a></p>
<p>Headline:<br/>
t&eacute;t</p>
<p>Summary:<br/>
test<p>
</p>
<a href="index.php">Done</a>-->
</div>
</body>
</html>
