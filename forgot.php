<?PHP
// retrieve the variables entered into the (submitted) form
$username1 = @$HTTP_POST_VARS["username"];
$mailadres1 = @$HTTP_POST_VARS["mailadres"];

// let the config.php file connect to the database
include("config.php");

// check if the admin alows this feature: the $AllowForgotPassword variable is set in config.php
if ($AllowForgotPassword == false){die($disabledFeatures);}

// check if the combination of username / mailadres exist
$query = "Select * from ".$DBprefix."signup where username='$username1' And mailadres='$mailadres1'";
$result = mysql_query($query); 
// if it does retrieve the password and mail it to the user
if ($row = mysql_fetch_array($result)){
	// prepare the message (replace %p and %a)
	$message = $message_forgot_password;
	$message = str_replace("%p",$row["password"], $message);
	$message = str_replace("%a","%", $message);
	// the text that should be within the message is stored now in the $message variable. 
	// "\n" (without qoutes) indicates a new line.
	mail($mailadres1, $title_forgot_password, $message, $email_message_header);
	//a nd print a gage telling that the password was mailed. And that it should arrive soon.
	echo "The password has been send to your e-mailades. You should recieve it within a couple of minutes.<br><a href=\"login.php\">back to log-in page</a>";
	}
else{
	// if the combination doesn't exist check it the form was filled in (on pageload the form is not filled in).
	// if the form was not filled in, show the "forgot password" page (makeform()). Without an argument since there is no error
	if ($username1 == ""){makeform();}
	else
	// if the form was filled in. show the "forgot password" page (makeform()) with an error on top.
	{makeform($incorrectUserMailaders);}
}

// This function will show the "forgot password" page (makeform()). The argument is an 'optionale' argument.
function makeform($errormessage=""){
// for more info how to customize this page check login.php

?>
<html>
<head>
<title>Forgot password</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<h1>Forget Password</h1>
<form name="form1" method="post" action="forgot.php">
  <a href="login.php">Back to loginform</a><br>
  <?PHP 
// print the error message
echo "<font color=\"#FF0000\"><strong>$errormessage</strong></font><br>"; ?>

  Username: 
  <input type="text" name="username">
  <br>
  E-Mailadres: 
  <input type="text" name="mailadres">
  <br>
  <input type="submit" value="Send password">
</form>
</body>
</html>
<?PHP } ?>