<?PHP
// retrieve the username, mailadres and the activation number from the url
$username1 = @$HTTP_GET_VARS["username"];
$actnum1 = @$HTTP_GET_VARS["actnum"];
$mailadres1 = @$HTTP_GET_VARS["mailadres"];

// check if a resend should be done:
$resend = @$HTTP_GET_VARS["resend"];

// let the config.php page connected to the database
include("config.php");

// if resend = 1 resend the activation code and continue as normal
// also check if email confirming is on
if ($resend == "1" && $UseMailConfirm = true){
	// check if the admin hasn't disabled this feature.
	if ($allowResend == true){
		$query = "Select * from ".$DBprefix."signup where username='$username1' And mailadres='$mailadres1'";
		$result = mysql_query($query); 
		// if it is update the user's records. Set actnum to 0
		if ($row = mysql_fetch_array($result)){ 
			// get the activtion numer
			$actnum = $row["actnum"];
			// get the message form config.php
			// prepare the message (replae the %p and %a)
			$message = $email_message_content;
			$message = str_replace("%p",$actnum, $message);
			$message = str_replace("%a","%", $message);
			// mail the message to the user. you can adjust the title if you like,
			// you can change al settings from the mail in config.php
			mail($mailadres1, $email_message_title, $message, $email_message_header);
			// tell the user the code has been resend
			makeform($activationCodeHasBeenResend);
			// stop excuting the script
			die();
		}else{
		// if there is no data found, the username/ email combination must be wrong.
		// therefore we give an error message, and stop the script (die)
		makeform($incorrectUserMailaders);
		die();}
	}else{
	// tell the user this feature is disabled
	makeform($disabledFeatures);
	// stop excuting the script
	die();
	}
	
}

// check it the combination of username/password is correct.
$query = "Select * from ".$DBprefix."signup where username='$username1' And actnum='$actnum1'";
$result = mysql_query($query); 
// if it is update the user's records. Set actnum to 0
if ($row = mysql_fetch_array($result)){ 
	$query = "UPDATE ".$DBprefix."signup Set actnum = '0' where username='$username1'";  
	$result = mysql_query($query); 
// show the "thank you for activating" page.
// for more info how to customize this page check login.php
?>

<html>
<head>
<title>Activate account</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<h1>Sign-Up Complete</h1>
<p>Thank you for activating your account. You may now <a href="login.php">login</a> 
</p>
</body>
</html>

<?PHP }
//if the username/actnum combination doesn't match check why.
else
{
//if the username is empty just show the form.
if ($username1 == ""){makeform();}
//but if the username isn't empyt that means the user did submit the form.
//therefore we print an error page and (again) the form.
else
{ 
makeform($incorrectUserActcode);
}
}
// the makeform function. prints the default form for activating.
function makeform($errormessage=""){
// for more info how to customize this page check login.php
?>
<html>
<head>
<title>Activate account</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<h1>Sign-Up Confirm Account</h1>
<form name="form1" method="get" action="activate.php">
   <?PHP 
// First we check if the errormessage variable is empty, if it is. we print the error message
if ($errormessage != ""){echo "<font color=\"#FF0000\"><strong>$errormessage</strong></font><br><br>";} ?>
  Thank you for signing up. An activationcode has been send to your e-mail adres.<br>
  Username: 
  <input name="username" type="text" id="username">
  <br>
  Activation code: 
  <input name="actnum" type="text" id="actnum">
  <br>
  <input type="submit" value="Activate">
</form>
<br>
<br>

<form name="form2" method="get" action="activate.php">
If you would like to have your activation code resend because you haven't got it, fill out the form below:<br>
  Username: 
  <input name="username" type="text" id="username">
  <br>
  E-Mailadres: 
  <input name="mailadres" type="text" id="mailadres">
  <input name="resend" type="hidden" id="resend" value="1">
  <br>
  <input type="submit" value="Resend Code">
</form>
</body>
</html>
<?PHP } ?>