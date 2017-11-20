<?PHP
$reqlevel = 1;
include("membersonly.inc.php");

// check if the admin alows this feature: the $allowChangePassword variable is set in config.php
if ($allowChangePassword == false){die($disabledFeatures);}

//retrieve the post vars.
$oldpass1 = @$HTTP_POST_VARS["oldpass"];
$password1 = @$HTTP_POST_VARS["password"];
$password2 = @$HTTP_POST_VARS["password2"];

//check if the password match and if there not empty
if ($password1 == $password2 && $password1 <> ""){
	//(re)check the database to see it the old password is correct
	$query = "Select * from ".$DBprefix."signup where username='$HTTP_SESSION_VARS[id]' And password='$oldpass1'";
	$result = mysql_query($query); 
	if ($row = mysql_fetch_array($result)){ 
		//update the password in the database
		$query = "UPDATE ".$DBprefix."signup Set password = '$password1' where username='$HTTP_SESSION_VARS[id]'";  
		$result = mysql_query($query); 
		//update the password in the session so you don't have to logoff
		$HTTP_SESSION_VARS["pass"] = $password1;
		//echo an confirm.
		echo "You password was changed ";
	}else{
		//it the check on the old password returns that it is incorrect we return that here.
		makeform("Your old password is not correct. pleast try again");
	}
}else{
//if there is no match between the 2 passwords, or if the are empty,
//check if it isn't the pageload (then the password is empty and empty = incorrect)
//if not empty it calls the function makeform() with the errormessage as argument
if ($password1 == ""){makeform("");}
else{makeform("You password do not match try again.");}
}

//this function will make the change password page and put the error argument in it
function makeform($errormessage){
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Change password</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<?PHP echo "<font color=\"#FF0000\"><strong>$errormessage</strong></font>";?>
<form name="form1" method="post" action="changepass.php">
  <p>old password: 
    <input name="oldpass" type="password" id="oldpass">
    <br>
    new password: 
    <input name="password" type="password" id="password">
	<br>
    new password: 
    <input name="password2" type="password" id="password2">
    <br>
    <input type="submit" value="Change">
  </p>
</form>
</body>
</html>
<?php } ?>