<?PHP $reqlevel = 1; include("membersonly.inc.php");?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Navigationpage</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
welcome <?PHP 
//echo the username that was used to login from the session
echo ($user_currently_loged); 
echo ("<br>");
echo ($user_current_Rank);
echo ("<br>");
echo ($user_current_ammount_new." new messages");
?><br>
<a href="secu1.php" target="mainFrame">secured page 1</a><br>
<a href="secu2.php" target="mainFrame">secured page 2</a><br>
<a href="news.php" target="mainFrame">News</a><br>
<a href="messages.php" target="mainFrame">Message Center</a><br>
<?PHP
// check if the logged in user is an admin by checking his accesslevel.
// if he is, show the link, if he isn't just show the word admin.
if ($user_current_level < 0){
	// this actions happen if the user is an admin
	echo "<a href=\"admin.php\" target=\"mainFrame\">admin</a><br>";
}
else{
	// this actions happens if the user is not an admin
	echo "";
}
?>
<a href="changepass.php" target="mainFrame">change password</a> <br>
<a href="deleteaccount.php" target="mainFrame">delete account</a><BR>
<a href="../logoff.php" target="_top">logoff</a> 
</body>
</html>