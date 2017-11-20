<?PHP $reqlevel = 0;include("membersonly.inc.php");?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>NEWS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body bgcolor="#FFFFFF" text="#000000" link="#FFFFFF" vlink="#CCCCCC" alink="#FFFFFF">
<?PHP 
// get the current news items
$query = "Select * from ".$DBprefix."news ORDER BY 'postDate' DESC";  
$result = mysql_query($query); 
while($row = mysql_fetch_array($result)){
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td colspan="3" bgcolor="#CCCCCC"><div align="center"><?PHP echo $row["title"]; ?></div></td>
  </tr>
  <tr> 
    <td bgcolor="#CCCCCC">&nbsp;</td>
    <td width="98%"><?PHP echo $row["message"]; ?></td>
	<td bgcolor="#CCCCCC">&nbsp;</td>

  </tr>
  <tr> 
    <td colspan="3" bgcolor="#CCCCCC">posted by <?PHP echo $row["poster"]; ?> 
      on <?PHP echo $row["postDate"]; ?></td>
  </tr>
</table>
<br> 
<?php
  // and very important end the while loop
	 }
   ?></table>
</body>
</html>
