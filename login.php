<?PHP
// retrieve the submitted values
$username1 = @$HTTP_POST_VARS["username"];
$password1 = @$HTTP_POST_VARS["password"];
$rememberMe = @$HTTP_POST_VARS["rememberMe"];

// make sure that rememberMe has a value
if ($rememberMe == "rememberMe"){
	$rememberMe = "1";
}else{
	$rememberMe = "0";
}

// let the config.php file connect to the database
include("config.php");

// check it the username exist
$query = "Select * from ".$DBprefix."signup where username='$username1'";
$result = mysql_query($query); 
if ($row = mysql_fetch_array($result)){ 
	// check if his account is activated, if not skip to this if's else case
	if ($row["actnum"] == "0"){
		// and check if his account is not loccked, if not skip to this if's else case
		if ($row["numloginfail"] <= 5){
			// finally we check the database to see if the password is correct, if not skip to this if's else case
			if ($row["password"] == $password1){
				// we determin the date for the lastlogin - field.
				$datetime = date("d-m-Y G:i ");
				// and we update that field
				$query = "UPDATE ".$DBprefix."signup Set lastlogin = '$datetime' where username='$username1'";  
				$result = mysql_query($query); 
				// now that the correct password is used to log-in, reset the numloginfail-field to 0
				$query = "UPDATE ".$DBprefix."signup Set numloginfail = '0' where username='$username1'";  
				$result = mysql_query($query); 
				// tell we want to work with sessions
				session_start();
				// remove al the data from the session (auto logoff)
				session_unset();
				// remove the session itself
				session_destroy();
				// put the password in the session
				@ session_register("pass");
				$HTTP_SESSION_VARS["pass"] = $password1;
				// put the username in the session
				@ session_register("id");
				$HTTP_SESSION_VARS["id"] = $username1;
				// send the the cookie if needed
				if($rememberMe=="1"){
				setcookie("rememberCookieUname",$username1,(time()+604800));
				setcookie("rememberCookiePassword",md5($password1),(time()+604800));
				}
				// go to the secured page.
				header("Location: members/index.php");
			}
			else{
				// else the password is incorrect. Therofore we have to update the numloginfield and lastloginfail field
				// first we set $datetime to the current time in a format that we can use to calculate with.
				$datetime = date("d")*10000000000 + date("m")*100000000 + date("Y")*10000 + date("G")*100 + date("i");
				// then we check if the last log-in fail was less than 5 minutes ago.
				if ($row["lastloginfail"] >= ($datetime-5)){
					// if it is  we update both the numloginfail & the lastloginfail fields.
					$query = "UPDATE ".$DBprefix."signup Set numloginfail = numloginfail + 1 where username='$username1'";  
					$result = mysql_query($query); 
					$query = "UPDATE ".$DBprefix."signup Set lastloginfail = '$datetime' where username='$username1'";  
					$result = mysql_query($query); 
				}
				else{
					// if it is more than 5 minutes ago, just set the lastloginfail field.
					$query = "UPDATE ".$DBprefix."signup Set lastloginfail = '$datetime' where username='$username1'";  
					$result = mysql_query($query); 
				}
		// and ofcourse we tell the user that his log-in failed.
		makeform($incorrectLogin);}
		}
		// if the numloginfail value is larger than 5 that means there someone tryed to break the password by brute force
		// we will now check how long ago the lock was engaged. it is is more than half an hour ago is, then we will unlock the account
		// and ask the user to login 1 more time to validate it is really him.
		else {
			$datetime = date("d")*10000000000 + date("m")*100000000 + date("Y")*10000 + date("G")*100 + date("i");
			if ($row["lastloginfail"] <= ($datetime-30)){
				// set the numloginfail value to 5 so the user has 1 change to enter his password.
				$query = "UPDATE ".$DBprefix."signup Set numloginfail = '5' where username='$username1'";  
				$result = mysql_query($query); 
				// ask the user to enter his username/password once again. Also we set the username field
				// to the name the username entered in the first login of this user. By doing this the makeform function
				// disables the username-field.
				makeform($underAttackReLogin, "$username1");
			}
			else{
			// if it is less than 30 minutes ago ask the user to wait untill the lock is released again.
				echo $underAttackPleaseWait;
			}
		}
	}
	// if the actnum is other than 0 that means the account has not been activated yet.
	else{
	makeform($accountNotActivated);
	}
}
// if the username does not exist we check it is filled in.
else{
	// if it isn't filled we assum that this is the page load and we show the form without an error.
	if ($username1 == ""){	
		makeform("");
	}
	else {
	// if the form is filled it that means that the username does not exist. Therefore we show the form
	// with an error. We can not change the numloginfail or lastloginfail fields for the brute forece attack
	// because the attack isn't pointed at one user.
		makeform($incorrectLogin);
	}
}

// this function shows the form.
// ....m($errormessage="", ... indicates an optionale argument for this function, same for $username.
function makeform($errormessage="", $username2 = ""){

// If you are planning to use A.L.S. for your website, enter the html for your login page below.
// note that that the php codes shouls stay in the place they are now.
// this means (example):
// --your html--
// the place for your errorcode: <?PHP ...(etc) ... ? > (without the space between ? and >)
// -- more html --
// form start, the form actions should be login.php
// the username field (login.php only, other pages: same rules as for other fields): 
// <input name="username" type="text" id="username" value=<?PHP ... (etc) ... ? > (without the space between ? and >)
// -- rest of form -- 
// -- rest of page --
// end of example
// ... (etc) ... indicates the php code between <?PHP and  ? > (without the space between ? and >)
// note: your are allowed to change arguments of the formfields, exept for: 
// 'name', 'id', 'type'. al other arguments maybe changed.
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>LOG-IN</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<h1>Log-In</h1>
    <?PHP 
// First we check if the errormessage variable is empty, if it is. we print the error message
if ($errormessage != ""){echo "<font color=\"#FF0000\"><strong>$errormessage</strong></font><br>";} ?>
<form name="form1" method="post" action="login.php">
  <p>username: 
    <input name="username" type="text" id="username" value=<?PHP
	// this code allow's us to put a value in the username-field.
	// this is so users can't login under a other name when there 
	// password is asked.
	echo "\"$username2\"";
	// this disables the field if the $username2 variable is NOT empty.
	if ($username2 != ""){echo "DISABLED";}
	?>>
    <br>
    password: 
    <input name="password" type="password" id="password"><br>
	<input type="checkbox" name="rememberMe" value="rememberMe">remember my login (7 days)<br>
    <br>
    <input type="submit" value="LOG IN">
  </p>
</form>
<a href="forgot.php">forgot password</a> <br>
<a href="signup.php">get an account</a> 
</body>
</html>
<?php } ?>