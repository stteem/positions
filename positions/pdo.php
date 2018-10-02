<?php
try{
	$pdo = new PDO('mysql:host=localhost;port=33060;dbname=misc', 
   'fred', 'zap');
}
catch(PDOException $e) {
	print_r($e->getMessage());
}
	// See the "errors" folder for details...
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);





