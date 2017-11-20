<?PHP
$reqlevel = 1;
include("membersonly.inc.php");

// Check it the delete has been confirmed with the form
if (@$HTTP_GET_VARS["sure"]=="deleteconfirmed"){

// Make a query for the delete and exucute the query
$query = "DELETE from ".$DBprefix."signup where username='$HTTP_SESSION_VARS[id]'";  
$result = mysql_query($query); 

// also we clear there inbox, outbox and there old messages folder.
$query = "DELETE from ".$DBprefix."inbox WHERE adres='$HTTP_SESSION_VARS[id]'";
$result = mysql_query($query); 
$query = "DELETE from ".$DBprefix."outbox WHERE sender='$HTTP_SESSION_VARS[id]'";
$result = mysql_query($query); 
$query = "DELETE from ".$DBprefix."oldmessages WHERE adres='$HTTP_SESSION_VARS[id]'";
$result = mysql_query($query); 
// Log of the user more info about this in the logoff.php file
session_start();
session_unset();
session_destroy();
header("Location: ../login.php");
}
?>

<html>
<head>
<title>Delete account</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
Are you sure you want to remove your account from the database?<br>
<a href="deleteaccount.php?sure=deleteconfirmed">YES I AM SURE</a> 
</body>
</html>