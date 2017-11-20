<?PHP 
// the default membersonly header
$reqlevel=1;
include("membersonly.inc.php");

// obtain the get and post varialbles
$action = @$HTTP_GET_VARS["action"];
$messageID = @$HTTP_GET_VARS["messageID"];
$box = @$HTTP_GET_VARS["box"];
$sortby = @$HTTP_GET_VARS["sortby"];
$direction = @$HTTP_GET_VARS["direction"];
$toAdres = @$HTTP_POST_VARS["adres"];
$title = @$HTTP_POST_VARS["title"];
$message = @$HTTP_POST_VARS["message"];
$adres = @$HTTP_GET_VARS["adres"];

// make sure that everything has a value
if ($box == "Outbox"){
		if ($sortby == ""){$sortby="DateSend";}
	}else{
		if ($sortby == ""){$sortby="DateRecieved";}
	}

if ($direction == ""){$direction="DESC";}

if ($action == ""){$action="showinbox";}

// preform the correct action
switch($action){
case "show":
showmessage($messageID, $box);
break;

case "showinbox":
showbox("inbox");
break;

case "sendmessage":
sendmessage();
break;

case "showoutbox":
showbox("Outbox");
break;

case "showold":
showbox("OldMessages");
break;

case "showoldmessages":
showbox("OldMessages");
break;

case "delete":
showdelete($messageID, $box);
break;

case "deletesure":
deletemessage($messageID, $box);
break;

case "move2old":
move2old($messageID, $box);
break;

case "move2inbox":
move2inbox($messageID, $box);
break;

case "markunread":
markunread($messageID, $box);
break;

case "markread":
markread($messageID, $box);
break;

case "compose":
compose();
break;

default:
// echo an error if the action doesn't exist
echo "The selected action (\"$action\") does not exists.";
break;
}

function showbox($box="inbox"){
// make sure the variables can be accessed within the function
global $sortby, $direction, $user_currently_loged_plain, $DBprefix;
?>
<html>
<head>
<title>MessageCenter 
<?PHP
// echo where we are
if ($box == "inbox"){ $boxLocation = "Inbox";}
if ($box == "Outbox"){$boxLocation =  "Outbox";}
if ($box == "OldMessages"){$boxLocation =  "Old Messages";}
echo $boxLocation;
?>
</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<div align="right"><font size="7">MessageCenter </font> </div>
<div align="right"><font size="6"><?PHP 
// echo where we are (again)
echo $boxLocation;
?></font></div>
<p>Actions: <?PHP 
// show all boxes exept for the one we are in
if ($box != "inbox"){ echo "<a href=\"messages.php?action=showinbox\">Inbox</a>, ";}
if ($box != "Outbox"){ echo "<a href=\"messages.php?action=showoutbox\">Outbox</a>, ";}
if ($box != "OldMessages"){ echo "<a href=\"messages.php?action=showold\">Old messages</a>,";}
?> 
<a href="messages.php?action=compose">Compose message</a></p>
<table width="100%" border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td width="5%">&nbsp;</td>
    <td width="12%" nowrap bgcolor="#333333">&nbsp;&nbsp;<a href=<?php 
	// make the columns sorteble
	echo "\"messages.php?action=show".$box."&amp;sortby=";
	
	if ($box != "Outbox"){echo "sender&amp;direction=";
		if ($sortby == "sender" && $direction=="ASC"){echo "DESC\"";}else {echo "ASC\"";}
	}else{
		echo "adres&amp;direction=";
			if ($sortby == "adres" && $direction=="ASC"){echo "DESC\"";}else {echo "ASC\"";}
	}
	?>><font color="#FFFFFF"><?PHP
	// show the correct prefix
	if ($box != "Outbox"){
		echo "From";
	}else{
		echo "To";
	}
	?></font></a>&nbsp;&nbsp;</td>
    <td width="3%" nowrap bgcolor="#333333">&nbsp;&nbsp;<a href=<?php 
	echo "\"messages.php?action=show".$box."&amp;sortby=Title&amp;direction=";
	if ($sortby == "Title" && $direction=="ASC"){
		echo "DESC\"";}
	else {
		echo "ASC\"";
	}
	?>><font color="#FFFFFF">Title</font></a>&nbsp;&nbsp;</td>
	<td width="4%" nowrap bgcolor="#333333">&nbsp;&nbsp;<a href=<?php 
	echo "\"messages.php?action=show".$box."&amp;sortby=";
	if ($box != "Outbox"){
		echo "DateRecieved&amp;direction=";	
		if ($sortby == "DateRecieved" && $direction=="ASC"){
			echo "DESC\"";
		}else {
			echo "ASC\"";
		}
	}else{
		echo "DateSend&amp;direction=";
		if ($sortby == "DateSend" && $direction=="ASC"){
			echo "DESC\"";
		}else {
			echo "ASC\"";
		}
	}
	?>><font color="#FFFFFF">
	<?PHP 
	// and show the correct words again
	if ($box != "Outbox"){echo "Date Recieved";}else{echo "Date Send";}?>
	</font></a>&nbsp;&nbsp;</td>
    <td width="76%" nowrap bgcolor="#333333">&nbsp;&nbsp;<font color="#FFFFFF">Actions</font>&nbsp;&nbsp;</td>
  </tr>
    <?PHP 
if ($box != "Outbox"){
$query = "Select * from ".$DBprefix.$box." WHERE adres='$user_currently_loged_plain' ORDER BY '$sortby' $direction";  
}else{
$query = "Select * from ".$DBprefix.$box." WHERE sender='$user_currently_loged_plain' ORDER BY '$sortby' $direction";  
}
$result = mysql_query($query); 
while($row = mysql_fetch_array($result)){
?>
  <tr>
    <td><?PHP 
	// only show the read / unread icoon if we are NOT in the outbox
	if ($box != "Outbox"){
		echo "<IMG src=\"mailread".$row["isRead"].".gif\" alt=\"";	
			if ($row["isRead"]==0){echo "Unread";}else{echo "Read";}	
				echo "\" width=\"16\" height=\"16\">"; 
			}
	?></td>
    <td nowrap>&nbsp;&nbsp;<?PHP
	// only make the name clickable if we are not in the outbox
	if ($box != "Outbox"){
		echo "<a href=\"messages.php?action=compose&amp;adres=".$row["sender"]."\">" . $row["sender"]. "</a>";
	}else{
		// in outbox we just show the adres
		echo $row["adres"];
	}
	 ?>&nbsp;&nbsp;</td>
    <td nowrap>&nbsp;&nbsp;<?PHP 
	// echo the title
	echo $row["title"];
	 ?>&nbsp;&nbsp;</td>
    <td nowrap>&nbsp;&nbsp;<?PHP 
	// echo the correct date
	if ($box != "Outbox"){
	echo $row["DateRecieved"];
	}else{
	echo $row["DateSend"];
	} ?>&nbsp;&nbsp;</td>
    <td nowrap>&nbsp;&nbsp;<a href="messages.php?action=show&messageID=<?PHP 
	// echo the messageID into ht link, echo the correct box
	echo $row["messageID"]."&amp;box=$box"; ?>">Show</a> 
	<?PHP
	// make sure the correct actions functions are there
		 if ($box == "inbox"){
			echo "<a href=\"messages.php?action=move2old&messageID=".$row["messageID"]."&amp;box=$box\">Move to old messages</a>";
		 }elseif($box == "OldMessages"){ 
			echo "<a href=\"messages.php?action=move2inbox&messageID=".$row["messageID"]."&amp;box=$box\">Move to inbox</a>";
		} ?>
    	<a href="messages.php?action=delete&messageID=<?PHP echo $row["messageID"]."&amp;box=$box"; ?>">Delete</a> 
	<?PHP 
	// the read/unread functions are only availble when you are not in the outbox
	if ($box != "Outbox"){
		if ($row["isRead"]==0){
			echo "<a href=\"messages.php?action=markread&amp;box=$box&amp;messageID=".$row["messageID"]."\">mark read</a>";
		}else{
			echo "<a href=\"messages.php?action=markunread&amp;box=$box&messageID=".$row["messageID"]."\">mark unread</a>";
		}
	}?>	
	&nbsp;&nbsp;</td>
  </tr>
    <?php } ?>
</table></td>
<p>&nbsp;</p>
</body>
</html>
<?PHP } 

function showmessage($messageID, $box){
global $user_currently_loged_plain, $DBprefix;

// make a correct query
if ($box == "Outbox"){
$query = "Select * from ".$DBprefix.$box." WHERE sender='$user_currently_loged_plain' AND messageID=$messageID";  
}else{
$query = "Select * from ".$DBprefix.$box." WHERE adres='$user_currently_loged_plain' AND messageID=$messageID";  
}

$result = mysql_query($query); 
if($row = mysql_fetch_array($result)){
	if ($box != "Outbox"){
	$query = "UPDATE ".$DBprefix.$box." SET isRead = '1' WHERE messageID = '".$messageID."'";  
	$result = mysql_query($query); 
	}
?>
<html>
<head>
<title>MessageCenter Show Message</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<div align="right"><font size="7">MessageCenter </font> </div>
<div align="right"><font size="6">Show Message</font></div>
<p>Actions: <a href="messages.php?action=showinbox">Inbox</a>, <a href="messages.php?action=showoutbox">Outbox</a>, <a href="messages.php?action=showold">Old messages</a>,

<a href="messages.php?action=delete&amp;messageID=<?PHP echo "$messageID&amp;box=$box"; ?>">Delete message</a></p>
<table width="100%" border="0" cellspacing="1" cellpadding="1">
  <tr> 
     <td width="97%" bgcolor="#666666" align="center"><font color="#FFFFFF"><?PHP echo $row["title"];?></font></td>
  </tr>
  <tr> 
    <td><?PHP echo $row["message"];?></td>
  </tr>
  <tr bgcolor="#666666"> 
    <td><font color="#FFFFFF">
	<?PHP
	if ($box != "Outbox"){
 	echo "Recieved from ".$row["sender"] ." on ".$row["DateRecieved"] ;
	}else{
	echo "Send to".$row["adres"] ." on ".$row["DateSend"] ;
	}
	?>
	
	</font></td>
  </tr>
</table>
<p>&nbsp;</p>
</body>
</html><?
}
}

function showdelete($messageID, $box){
global $user_currently_loged_plain, $DBprefix;
?>
<html>
<head>
<title>MessageCenter Delete message</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<div align="right"><font size="7">MessageCenter </font> </div>
<div align="right"><font size="6">Delete Message</font></div>
<p>Actions: <a href="messages.php?action=showinbox">Inbox</a>, <a href="messages.php?action=showoutbox">Outbox</a>, <a href="messages.php?action=showold">Old messages</a>, <a href="messages.php?action=show&amp;messageID=<?PHP echo "$messageID&amp;box=$box"; ?>">Show message</a></p>
<div align="center">
Are you sure you want to delete this message?<br>
<a href="messages.php?action=deletesure&amp;messageID=<?PHP echo "$messageID&amp;box=$box"; ?>">YES</a><br>
<a href="messages.php?action=show<?PHP echo $box;?>">NO</A>
</div>
</body>
</html>
<?PHP
}

function deletemessage($messageID, $box){
global $DBprefix;
// make a correct query
if ($box == "Outbox"){
$query = "DELETE from ".$DBprefix.$box." WHERE messageID='$messageID' AND sender='$user_currently_loged_plain'";  
}else{
$query = "DELETE from ".$DBprefix.$box." WHERE messageID='$messageID' AND adres='$user_currently_loged_plain'";  
}
$query = "";
$result = mysql_query($query); 
?>
<html>
<head>
<title>MessageCenter Delete message</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<div align="right"><font size="7">MessageCenter </font> </div>
<div align="right"><font size="6">Delete Message</font></div>
<p>Actions: <a href="messages.php?action=showinbox">Inbox</a>, <a href="messages.php?action=showoutbox">Outbox</a>, <a href="messages.php?action=showold">Old messages</a></p>
<div align="center">
The message has been deleted<br>
<a href="messages.php?action=show<?PHP echo $box; ?>">
return to <?PHP
if ($box == "inbox"){ echo "Inbox";}
if ($box == "Outbox"){ echo "Outbox";}
if ($box == "OldMessages"){ echo "Old Messages";}
?></A>
</div>
</body>
</html>

<?
}

function move2old($messageID, $box){
global $user_currently_loged_plain, $DBprefix;
if ($box == "Outbox"){
$query = "Select * from ".$DBprefix.$box." WHERE sender='$user_currently_loged_plain' AND messageID=$messageID";  
}else{
$query = "Select * from ".$DBprefix.$box." WHERE adres='$user_currently_loged_plain' AND messageID=$messageID";  
}
$result = mysql_query($query); 
if($row = mysql_fetch_array($result)){
$query = "INSERT INTO ".$DBprefix."OldMessages VALUES ('','". $row["adres"]."','". $row["sender"]."', '". $row["DateRecieved"]."', '". $row["title"]."', '". $row["message"]."', '". $row["isRead"]."')";
$result = mysql_query($query); 
$query = "DELETE from ".$DBprefix.$box." WHERE messageID='$messageID'";
$result = mysql_query($query); 
?>
<html>
<head>
<title>MessageCenter Move to old messages</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="refresh" content="3;URL=messages.php?action=showold">
</head>

<body>
<div align="right"><font size="7">MessageCenter </font> </div>
<div align="right"><font size="6">Move to old messages</font></div>
<p>Actions: <a href="messages.php?action=showinbox">Inbox</a>, <a href="messages.php?action=showoutbox">Outbox</a>, <a href="messages.php?action=showold">Old messages</a></p>
<div align="center">
The message has been moved<br>
</div>
</body>
</html>
<?PHP
}
}

function move2inbox($messageID, $box){
global $user_currently_loged_plain, $DBprefix;
if ($box == "Outbox"){
$query = "Select * from ".$DBprefix.$box." WHERE sender='$user_currently_loged_plain' AND messageID=$messageID";  
}else{
$query = "Select * from ".$DBprefix.$box." WHERE adres='$user_currently_loged_plain' AND messageID=$messageID";  
}
$result = mysql_query($query); 
if($row = mysql_fetch_array($result)){
$query = "INSERT INTO ".$DBprefix."inbox VALUES ('','". $row["adres"]."','". $row["sender"]."', '". $row["DateRecieved"]."', '". $row["title"]."', '". $row["message"]."', '". $row["isRead"]."')";
$result = mysql_query($query); 
$query = "DELETE from ".$DBprefix.$box." WHERE messageID='$messageID'";
$result = mysql_query($query); 
?>
<html>
<head>
<title>MessageCenter Move to inbox</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="refresh" content="3;URL=messages.php?action=showinbox">
</head>

<body>
<div align="right"><font size="7">MessageCenter </font> </div>
<div align="right"><font size="6">Move to inbox</font></div>
<p>Actions: <a href="messages.php?action=showinbox">Inbox</a>, <a href="messages.php?action=showoutbox">Outbox</a>, <a href="messages.php?action=showold">Old messages</a></p>
<div align="center">
The message has been moved<br>

</div>
</body>
</html>
<?PHP
}
}

function markunread($messageID, $box){
global $user_currently_loged_plain, $DBprefix;
if ($box == "Outbox"){
$query = "UPDATE ".$DBprefix.$box." SET isRead = '0' WHERE sender='$user_currently_loged_plain' AND messageID=$messageID";  
}else{
$query = "UPDATE ".$DBprefix.$box." SET isRead = '0' WHERE adres='$user_currently_loged_plain' AND messageID=$messageID";  
}
$result = mysql_query($query); 
showbox($box);
}

function markread($messageID, $box){
global $user_currently_loged_plain, $DBprefix;
if ($box == "Outbox"){
$query = "UPDATE ".$DBprefix.$box." SET isRead = '1' WHERE sender='$user_currently_loged_plain' AND messageID=$messageID";  
}else{
$query = "UPDATE ".$DBprefix.$box." SET isRead = '1' WHERE adres='$user_currently_loged_plain' AND messageID=$messageID";  
}
$result = mysql_query($query); 
echo mysql_error();
showbox($box);
}

function compose(){
global $adres;
?>
<html>
<head>
<title>MessageCenter Show Message</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
.title {
	background-color: #666666;
}
-->
</style>
</head>

<body
<div align="right"><font size="7">MessageCenter</font></div>
<div align="right"><font size="6">Compose Message</font></div>
<p>Actions: <a href="messages.php?action=showinbox">Inbox</a>, <a href="messages.php?action=showoutbox">Outbox</a>, <a href="messages.php?action=showold">Old messages</a></p>
      <form name="form1" method="post" action="messages.php?action=sendmessage">
<table width="100%" border="0" cellspacing="1" cellpadding="1">
  <tr> 
    <td width="3%" bgcolor="#666666"> <p>Title:</p></td>
            <td width="97%" bgcolor="#666666"><div alin align="right"><input type="text" name="title" class="title"></div></td>
  </tr>
  <tr> 
    <td bgcolor="#666666" colspan="1">
	Message:<br>
	<blockquote>
	Allowed HTML:<br>
              &lt;b&gt;<b>bold</b>&lt;/b&gt;<br>
              &lt;i&gt;<i>italic</i>&lt;/i&gt;<br>
              &lt;u&gt;<u>underline</u>&lt;/u&gt;<br>
              &lt;strong&gt;<strong>strong</strong>&lt;/strong&gt;<br>
              &lt;em&gt;<em>emphasis</em>&lt;/em&gt;<br>
              &lt;br&gt;<br>
              &lt;p&gt; &lt;/p&gt;</blockquote></td>
            <td bgcolor="#FFFFFF"><div align="right"><textarea name="message" cols="50" rows="10"></textarea></div></td>
  </tr>
  <tr bgcolor="#666666"> 
    <td>&nbsp;</td>
            <td><font color="#FFFFFF"> To 
              <input name="adres" type="text" size="21" maxlength="20" class="title" value="<?PHP echo $adres; ?>">
              <br>
              <input type="submit" value="Send Message">
              </font></td>
  </tr>
</table>


      </form></td>
</body>
</html>
<?PHP
}

function sendmessage(){
global $user_currently_loged_plain, $DBprefix, $message, $title, $box, $toAdres;
$error="";
if ($message == "") {$error = "$error <li> You haven't filled in a message";}
if ($title == "") {$error = "$error <li>You haven't filled in a title";}
if (strlen($toAdres) < 1 or strlen($toAdres) > 20 ) {$error = "$error <li>You haven't filled in a correct reciever";}

$query = "SELECT username FROM ".$DBprefix."signup WHERE username='$toAdres'";
$result = mysql_query($query); 
if($row = mysql_fetch_array($result)){
// user must exits
}else{$error = "$error <li>The reciever does not exist. Note that the reciever field is caps sensitive";}

if ($error == ""){
$datetime = date("Y-m-j H:i:s");
$query = "INSERT INTO `".$DBprefix."inbox` (`adres`, `sender`, `DateRecieved` , `title` , `message` , `isRead` ) VALUES ('$toAdres', '$user_currently_loged_plain', '$datetime' , '". formatTitle($title). "', '". formatMessage($message). "', '0');";
$result = mysql_query($query); 
$query = "INSERT INTO `".$DBprefix."Outbox` ( `adres` , `sender` , `DateSend` , `title` , `message`) VALUES ('$toAdres' ,'$user_currently_loged_plain', '$datetime', '". formatTitle($title). "', '". formatMessage($message). "');";
$result = mysql_query($query); 
?>
<html>
<head>
<title>MessageCenter Compose Message</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<div align="right"><font size="7">MessageCenter </font> </div>
<div align="right"><font size="6">Compose Message</font></div>
<p>Actions: <a href="messages.php?action=showinbox">Inbox</a>, <a href="messages.php?action=showoutbox">Outbox</a>, <a href="messages.php?action=showold">Old messages</a></p>
<div align="center">
The message has been send<br>

</div>
</body>
</html>
<?PHP
}else{
?>
<html>
<head>
<title>MessageCenter Compose Message</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<div align="right"><font size="7">MessageCenter </font> </div>
<div align="right"><font size="6">Compose Message</font></div>
<p>Actions: <a href="messages.php?action=showinbox">Inbox</a>, <a href="messages.php?action=showoutbox">Outbox</a>, <a href="messages.php?action=showold">Old messages</a></p>
<div align="center">
The message has NOT been send<br>
There where some errors in your message:<ul>
<?PHP echo $error; ?>
</ul>
Return to your message and correct these errors.
</div>
</body>
</html>
<?PHP
}

}


function formatTitle($Message){
$Message =str_replace ("&","&amp;",$Message);
$Message =str_replace ("<","&lt;",$Message);
$Message =str_replace (">","&gt;",$Message);
return $Message;
}

function formatMessage($Message){
$Message =str_replace ("&","&amp;",$Message);
$Message =str_replace ("<","&lt;",$Message);
$Message =str_replace (">","&gt;",$Message);
$Message =str_replace ("&lt;b&gt;","<b>",$Message);
$Message =str_replace ("&lt;/b&gt;","</b>",$Message);
$Message =str_replace ("&lt;i&gt;","<i>",$Message);
$Message =str_replace ("&lt;/i&gt;","</i>",$Message);
$Message =str_replace ("&lt;u&gt;","<u>",$Message);
$Message =str_replace ("&lt;/u&gt;","</u>",$Message);
$Message =str_replace ("&lt;strong&gt;","<strong>",$Message);
$Message =str_replace ("&lt;/strong&gt;","</strong>",$Message);
$Message =str_replace ("&lt;em&gt;","<em>",$Message);
$Message =str_replace ("&lt;/em&gt;","</em>",$Message);
$Message =str_replace ("&lt;br&gt;","<br>",$Message);
$Message =str_replace ("&lt;p&gt;","<p>",$Message);
$Message =str_replace ("&lt;/p&gt;","</p>",$Message);
$Message =str_replace ("&lt;br /&gt;","<br />",$Message);
$Message = nl2br($Message);
return $Message;
}
?>