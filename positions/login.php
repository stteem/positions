<?php
require_once "pdo.php";
session_start();

if (isset($_POST['cancel'])) {
  header("Location: index.php");
  return;
}


// p' OR '1' = '1
$salt = 'XyZzy12*_';

if ( isset($_POST['email']) && isset($_POST['pass'])  ) {
    unset($_SESSION['email']);
    unset($_SESSION['pass']);

    $email = htmlentities($_POST['email']); 
    $pass = htmlentities($_POST['pass']);

    if ( strlen($email) < 1 || strlen($pass) < 1 ) {
        $_SESSION['error'] = "User name and password are required";
        header('Location: login.php');
        return;
    }

    //Check if email exists
    elseif ( strpos($email,'@') ) {
      //(preg_match("/@/", $who))

      $check = hash('md5', $salt.$pass);

      $sql = "SELECT user_id, name FROM users 
        WHERE email = :em AND password = :pw";

      $stmt = $pdo->prepare($sql);
      $stmt->execute(array(
        ':em' => $email, 
        ':pw' => $check));
      $row = $stmt->fetch(PDO::FETCH_ASSOC);

      if ( $row !== FALSE ) {

        try {
          throw new Exception("Login success ".$email);
        }
        catch (Exception $ex) {
          error_log($ex->getMessage());
        }
       
        //$_SESSION["success"] = "Logged in.";
        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['name'] = $row['name'];
        header("Location: index.php");
        return;
      } 

      else { 
        try {
            throw new Exception("Login fail ".$email);
          }
        catch (Exception $e) {
          error_log($e->getMessage());
        }

        $_SESSION["error"] = "Incorrect email or password.";
        header("Location: login.php");
        return;
        
      }
    }
    else {
        $_SESSION['error'] = "Email must have an at-sign (@)";
        header('Location: login.php');
        return;
    }
}
?>


<!DOCTYPE html>
<html>
<head>
<title>Uwem Uke's Login Page</title>
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
<h1>Please Log In</h1>
<?php 
if (isset($_SESSION['error']) ) {
    echo('<p style="color:red">'.htmlentities($_SESSION['error'])."</p>\n");
    unset($_SESSION['error']);
}
?>

<form method="POST" action="login.php">
<label for="email">Email</label>
<input type="text" name="email" id="email"><br>
<label for="id_1723">Password</label>
<input type="password" name="pass" id="id_1723"><br>
<input type="submit" onclick="return doValidate();" value="Log In">
<input type="submit" name="cancel" value="Cancel">
</form>

<p>
For a password hint, view source and find an account and password hint
in the HTML comments.
<!-- Hint: 
The account is umsi@umich.edu
The password is the three character name of the 
programming language used in this class (all lower case) 
followed by 123. -->
</p>
</div>
<script type="text/javascript">
  function doValidate() {
    console.log('Validating...');
    try {
        addr = document.getElementById('email').value;
        pw = document.getElementById('id_1723').value;
        console.log("Validating addr="+addr+" pw="+pw);
        if (addr == null || addr == "" || pw == null || pw == "") {
            alert("Both fields must be filled out");
            return false;
        }
        if ( addr.indexOf('@') == -1 ) {
            alert("Invalid email address");
            return false;
        }
        return true;
    } catch(e) {
        return false;
    }
    return false;
}

</script>
</body>