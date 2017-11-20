<?PHP
$reqlevel = 2;
include("membersonly.inc.php");?>
<html>
<head>
<title>secu 2</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
security page 2<br>
welcome <?PHP 
// with this script you can include the username of the user that is currently loged in.
echo ($user_currently_loged); ?>
</body>
</html>