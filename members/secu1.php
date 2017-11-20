<?PHP
// $reqlevel sets the accesslevel a user needs to hace
// use 0 (zero) if you want to make it an admin only page.
// and 1 (one)  is default for an new user. 
$reqlevel = 1;
// this makes the page an member only page.
include("membersonly.inc.php");
//after this we use ? > to end the php part and we put the page.
?>
<html>
<head>
<title>secu 1</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
security page 1<br>
welcome <?PHP 
// with this script you can include the username of the user that is currently loged in.
echo ($user_currently_loged); ?>
</body>
</html>