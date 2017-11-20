<?PHP
$reqlevel = 0;
include("membersonly.inc.php");?>
<html>
<head>
<?PHP
// first we check what actions have to be preformed by obtaining the action variable from url.
// also we retrieve the username.
$username1 = @$HTTP_GET_VARS["username"];
$action1 = @$HTTP_GET_VARS["action"];
// and we get the new level for a user
$newlevel = @$HTTP_POST_VARS["newlevel"];
// we get the newsID
$newsID = @$HTTP_GET_VARS["newsID"];

$username2 = htmlspecialchars($username1,ENT_NOQUOTES);
$username2 = str_replace ('\"', "&quot;", $username2);
$username2 = str_replace ("\'", "&#039", $username2);

// because there are quite some actions we use a switch here. As argument we give the action retrieved from the url.
switch ($action1) {
// if the action is more than that indictes that the admin wants more information about the user.
case "more":
// therfore we make an query to the database to obtain that info.
$query = "Select * from ".$DBprefix."signup where username='$username1'";  
$result = mysql_query($query); 
// if there are results print them
if($row = mysql_fetch_array($result)){
?>
<title>More info about <?PHP 
//echo the username in the tile bar
echo htmlspecialchars($row["username"]);?>
</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body link="#FFFFFF" vlink="#FFFFFF" alink="#FFFFFF">
<a href="admin.php"><font color="#000000">Back to overview</font></a><br>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr bgcolor="#000000"> 
    <td><div align="center"><font color="#FFFFFF">Setting</font></div></td>
    <td bgcolor="#333333"><div align="center"><font color="#FFFFFF">Value</font></div></td>
  </tr>
  <tr bgcolor="#999999"> 
    <td>Username<br> </td>
    <td bgcolor="#CCCCCC"><div align="right"><?PHP 
	//echo the username in the table
	//htmlspecialchars() is used to make sure the users can't put any (working) html code in
	//there name that would screw up the admin page.
	echo $username2; ?></div></td>
  </tr>
  <tr bgcolor="#666666"> 
    <td>Password<br> </td>
    <td bgcolor="#999999"><div align="right"><?PHP 
	// echo a link to the show password function in the table
	echo "<a href=\"user.php?action=showpass&username=$username2\"><font color=\"#000000\">Show password</font></a>" ?></div></td>
  </tr>
  <tr bgcolor="#999999"> 
    <td>E-mailadres<br> </td>
    <td bgcolor="#CCCCCC"><div align="right"><?PHP 
	//echo the mail aderes in the table
	echo htmlspecialchars($row["mailadres"]); ?></div></td>
  </tr>
  <tr bgcolor="#666666"> 
    <td>Activated (if not activated then this field contains the activationcode)<br> 
    </td>
    <td bgcolor="#999999"><div align="right"> 
        <?PHP 
		//echo if the user activated his account. if not echo the activation number that should be enterd by the user.
		if ($row["actnum"] == "0") {echo "YES";}
	else{echo $row["actnum"];}?>
      </div></td>
  </tr>
  <tr bgcolor="#999999"> 
    <td>Acceslevel<br> </td>
    <td bgcolor="#CCCCCC"><div align="right"><?PHP 
	//echo if the user is an admin.
	if ($row["userlevel"] < 0) {echo "ADMIN";}
	else{echo $row["userlevel"];}?></div></td>
  </tr>
  <tr bgcolor="#666666"> 
    <td>Date user signed up<br> </td>
    <td bgcolor="#999999"><div align="right"><?PHP 
	//echo the date the user signed up
	echo $row["signupdate"]; ?></div></td>
  </tr>
  <tr bgcolor="#999999"> 
    <td>Date of the last log-in<br> </td>
    <td bgcolor="#CCCCCC"><div align="right"><?PHP
	//echo the date of the last login
	echo $row["lastlogin"]; ?></div></td>
  </tr>
  <tr bgcolor="#666666"> 
    <td>Date of the last log-in failure (for security only)<br> </td>
    <td bgcolor="#999999"><div align="right"><?PHP 
	//shows the date (and more important the time) of the last log-in failure
	//this is used to check if the users password isn't beening hacked by trying
	//a lot of passwords. the format of this value is something like 020120031132 (for example)
	//that means the user's last log-in attempt was on 2-1-2003 11:32.
	//to show it in an nice way we will format the this variable.
	//first we store the lastloginfail in the $tmp varialble.
	$tmp = $row["lastloginfail"]; 
	//now we check the length of the string. If the lengthe is 1 then there is a zero in the $tmp var.
	//This indicates that there were no loginfailures.
	if(strlen($tmp) == 1){
		//to indicate that there were no failure logins we don't echo a zero but we echo an sentance.
		//this because it is more clear.
		echo "No bad login's";
	}else{
		//if the length of $tmp is different from zero we may assum that there is a date strored in it.
		//to fix a protential bug we fist check the length of the string. Because 020120031232 is stored without
		//the first zero. But 100220032333 is stored as it is. if the length is 12 we know that it is stored as it is.
		//if it isn't 12 then it must be 11 indicating that the first zero hasn't been saved.
		if(strlen($tmp)==12){
			//Here we split up the string with substr. because we know the date/timeformat (ddmmyyyyhhmm) we can split
			//it into tiny part. Between those parts we put an sign that makes it easyer to read it.
			echo substr($tmp,0,2)."-".substr($tmp,2,2)."-".substr($tmp,4,4)." ".substr($tmp,8,2).":".substr($tmp,10,2);
		}else{
			//same as above only with an 1 char at the begin less.
			echo substr($tmp,0,1)."-".substr($tmp,1,2)."-".substr($tmp,3,4)." ".substr($tmp,7,2).":".substr($tmp,9,2);
		}
	}
	
	?></div></td>
  </tr>
  <tr bgcolor="#999999"> 
    <td>Number of failed log-ins (for security only)</td>
    <td bgcolor="#CCCCCC"><div align="right"><?PHP
	// the number of faillogin with a very short (5 minutes) periode of time between them
	// 6 is the number after which the account will be locked for 30 minutes
	echo $row["numloginfail"]; ?>	
	</div></td>
  </tr>
  <tr bgcolor="#333333"> 
    <td colspan="2" bgcolor="#000000">  
      <div align="center"><?PHP 
	  	// and pint the actions avaible again. just like in the admin page (admin.php)
	  	echo "<a href=\"user.php?action=delete&username=$username2\">Delete User</a> ";
	  	if ($row["userlevel"]>=0){echo "<a href=\"user.php?action=makeadmin&username=$username2\">Make Admin</A> ";
			if ($row["actnum"]=="0"){
				echo "<a href=\"user.php?action=levelincrease&username=$username2\">Increase level</a> 
					  <a href=\"user.php?action=abschangelevel&username=$username2\">Set the userslevel</a> ";
				if ($row["userlevel"] > 1) {echo "<a href=\"user.php?action=leveldecrease&username=$username2\">Decrease level</a> ";}
			}
		}
		else {echo "<a href=\"user.php?action=stopadmin&username=$username2\">Dismiss Admin</A> ";}
		if ($row["actnum"]!="0"){echo "<a href=\"user.php?action=activate&username=$username2\">Activate account</a> ";}
		?>
      </div></td>
  </tr>
</table>
<?PHP
 }else
// if the user was not found make an error page using htmlwrite.
// htmlwirte takes a message as first argument and a page title as second argument
// we use htmlwrite over echo because this (htmlwrite) generates a complete html page.
{htmlwrite("
error, can't find userinfo of user $username2<br><a href=\"admin.php\">back to overview</a>", "Error");}

// very importand!
// This break indicates that this is the end of the case.
break;

// if the admin wants to delete the user, echo an question if he is certain of that
// the 'page' contains a link to this page (user.php) with as action deleteconfirmed.
// which can only be called from this confirm.
case "delete":
htmlwrite("Are you sure you want to remove $username2 from the database?<br>
<a href=\"user.php?action=deleteconfirmed&username=$username2\">YES</a> 
<a href=\"admin.php\">NO</a>", "Delete user");
break;

//if the admin has confirm the delete of the user delete the user from the database and 
//echo a meesage that the user was deleted.
case "deleteconfirmed":
$query2 = "DELETE from ".$DBprefix."signup where username='$username1'";  
$result2 = mysql_query($query2); 
htmlwrite("$username2's account was deleted deleted from the database<br>
<a href=\"admin.php\">return to overview</a>", "Delete user");
break;

//in the admin wants to a activate an users account so that the user doesn't has to echo an confirm message
case "activate":
htmlwrite("Are you sure you want to activate $username2's account<br>
<a href=\"user.php?action=activateconfirmed&username=$username2\">YES</a> 
<a href=\"admin.php\">NO</a>", "Activate Account");
break;

//if the admin confirmed the activation, update the users data, change the actnum into 0
case "activateconfirmed":
$query = "UPDATE ".$DBprefix."signup Set Actnum = 0 where username='$username1'";  
$result = mysql_query($query); 
htmlwrite("$username2's account was activated.<BR>
<a href=\"admin.php\">return to overview</a><br>
<a href=\"user.php?action=more&username=$username2\">more information about $username2</a>", "Activate account");
break;

//if the admin want to make an admin out of an user, show an confirm.
case "makeadmin":
htmlwrite("Are you sure you want to transform $username2's account into an admin account<br>
<a href=\"user.php?action=makeadminconfirmed&username=$username2\">YES</a> 
<a href=\"admin.php\">NO</a>", "make admin");
break;

// if the admin confirms, set the admin value in the user's records to an negative number.
case "makeadminconfirmed":
// first we check if the current level is not zero
$query = "SELECT userlevel, username from ".$DBprefix."signup  where username='$username1'";  
$result = mysql_query($query); 
if($row = mysql_fetch_array($result)){
	// check what the query should be
	if($row["userlevel"] == 0){
		// set it to -1 if the level is 0(zero)
		$query = "UPDATE ".$DBprefix."signup Set userlevel=-1 where username='$username1'";  
	}else{
		// if not, just make it negative (current * -1 = negative)
		$query = "UPDATE ".$DBprefix."signup Set userlevel=userlevel*-1 where username='$username1'";  
	}
	$result = mysql_query($query); 
	// write a confirm
	htmlwrite("$username2's account was made into an admin account.<BR>
	<a href=\"admin.php\">return to overview</a><br>
	<a href=\"user.php?action=more&username=$username2\">more information about $username2</a>", "Make admin");
}
else
{
// no results found would mean that there is no matching user found, thus we print an error
htmlwrite("user $username2 was not found<BR>
<a href=\"admin.php\">return to overview</a>","ERROR");
}

break;

// if the admin wants to dismiss an admin ask for an confim
case "stopadmin":
htmlwrite("Are you sure you want to transform $username2's adminaccount into an normal account<br>
<a href=\"user.php?action=stopadminconfirmed&username=$username2\">YES</a> 
<a href=\"admin.php\">NO</a>", "Dismiss admin");
break;

// if the admin confirmed set the admin value of the users record back to an positive number
case "stopadminconfirmed":
$query = "UPDATE ".$DBprefix."signup Set userlevel=userlevel*-1 where username='$username1'";  
$result = mysql_query($query); 
htmlwrite("$username2's account was made into an normal account.<BR>
<a href=\"admin.php\">return to overview</a><br>
<a href=\"user.php?action=more&username=$username2\">more information about $username2</a>", "Dismiss admin");
break;

case "changelevel":
$query = "Select * from ".$DBprefix."signup where username='$username1'";  
$result = mysql_query($query); 
// if there are results print them
if($row = mysql_fetch_array($result)){
htmlwrite("what do you want to do with  $username2's accesslevel (current level = ".$row["userlevel"].")?<br>
<a href=\"user.php?action=levelincrease&username=$username2\">Increase</a><br>
<a href=\"user.php?action=abschangelevel&username=$username2\">Set the userslevel</a><br>
", "Change level");
if ($row["userlevel"] == 1) {echo "<font color=\"#999999\"><u>Decrease</u></font>";}
else {echo "<a href=\"user.php?action=leveldecrease&username=$username2\">Decrease</a>";}
}
break;

// if the admin wants to dismiss an admin ask for an confim
case "levelincrease":
htmlwrite("Are you sure you want to increase $username2's acceslevel?<br>
<a href=\"user.php?action=levelincreaseconfirmed&username=$username2\">YES</a> 
<a href=\"admin.php\">NO</a>", "Increase level");
break;

case "levelincreaseconfirmed":
$query = "UPDATE ".$DBprefix."signup Set userlevel=userlevel+1 where username='$username1'";  
$result = mysql_query($query); 
htmlwrite("$username2's accesslevel was raised.<BR>
<a href=\"admin.php\">return to overview</a><br>
<a href=\"user.php?action=more&username=$username2\">more information about $username2</a>", "Increase level");
break;

case "leveldecrease":
htmlwrite("Are you sure you want to decrease $username2's acceslevel?<br>
<a href=\"user.php?action=leveldecreaseconfirmed&username=$username2\">YES</a> 
<a href=\"admin.php\">NO</a>", "Decrease level");
break;

case "leveldecreaseconfirmed":
$query = "UPDATE ".$DBprefix."signup Set userlevel=userlevel-1 where username='$username1'";  
$result = mysql_query($query); 
htmlwrite("$username2's accesslevel was lowerd.<BR>
<a href=\"admin.php\">return to overview</a><br>
<a href=\"user.php?action=more&username=$username2\">more information about $username2</a>","Decrease level");
break;

case "abschangelevel":
htmlwrite("To what level would you like to set $username2's level (below zero will cause the account to become an admin account)<br>
<form action=\"user.php?action=abschangelevelconfirmed&username=$username2\" method=\"post\">
<input type=\"text\" value=\"\" name=\"newlevel\">
<input type=\"submit\" value=\"SET\"> 
<a href=\"admin.php\">Cancel</a>", "Change level");
break;

case "abschangelevelconfirmed":
$query = "UPDATE ".$DBprefix."signup Set userlevel=$newlevel where username='$username1'";  
$result = mysql_query($query); 
htmlwrite("$username2's accesslevel was set to $newlevel.<BR>
<a href=\"admin.php\">return to overview</a><br>
<a href=\"user.php?action=more&username=$username2\">more information about $username2</a>", "Change level");
break;

case "showpass":
// we make an query to the database to obtain userinfo.
$query2 = "Select * from ".$DBprefix."signup where username='$username1'";  
$result2 = mysql_query($query2); 
// if there are results print then echo the password, else echo an error message.
if($row2 = mysql_fetch_array($result2)){
htmlwrite("$username2's password is: <br><b>".$row2["password"]."</b><br>
<a href=\"admin.php\">return to overview</a><br>
<a href=\"user.php?action=more&username=$username2\">Back to more information about $username2</a>", "Change level");
}else {
htmlwirte("Error while getting password (userinfo could not be found)","ERROR");
}
break;

case "adduser":
htmlwrite("
	<a href=\"admin.php\">return to overview</a>

<form name=\"form1\" method=\"post\" action=\"user.php?action=adduserconfirmed\">
 <p> Username: <input name=\"ADD_username\" type=\"text\" id=\"ADD_Username\">
    <br>
    Password: <input name=\"ADD_password\" type=\"text\" id=\"ADD_password\">
    <br>
    E-mail adres: <input name=\"ADD_mailadres\" type=\"text\" id=\"ADD_mailadres\"><br>
    Userlevel: <input name=\"ADD_Userlevel\" type=\"text\" id=\"ADD_Userlevel\" value=\"1\"><br>
	<input name=\"ADD_activated\" type=\"checkbox\" value=\"1\" checked>Activate on create<br>
	<input name=\"ADD_MailActnum\" type=\"checkbox\" value=\"1\">Mail activationcode mail (only for unactivated accounts).
    <br>
    <input type=\"submit\" value=\"ADD USER\"></p></form>","ADD USER");
break;

case "adduserconfirmed":
// retrieve the values from the form
$ADD_username1 = @$HTTP_POST_VARS["ADD_username"];
$ADD_password1 = @$HTTP_POST_VARS["ADD_password"];
$ADD_mailadres1 = @$HTTP_POST_VARS["ADD_mailadres"];
$ADD_activated = @$HTTP_POST_VARS["ADD_activated"];
$ADD_MailActnum = @$HTTP_POST_VARS["ADD_MailActnum"];
$ADD_Userlevel = @$HTTP_POST_VARS["ADD_Userlevel"];


// make sure the checkboxes have the correct value (the box only give back a value if there checked. Else there value is empty)
// this 2 if's will make that value a 1 (one) if checked or a 0 (zero) if not checked
if ($ADD_activated == 1){
	$ADD_activated=1;
}else{
	$ADD_activated=0;
}

if ($ADD_MailActnum == 1){
	$ADD_MailActnum=1;
}else{
	$ADD_MailActnum=0;
}

// set the error variable to nothing
$error = "";

// checke if the values are filled in
if ($ADD_username1 == ""){$error = "$error<li>No username given<BR>\n";}
if ($ADD_password1 == ""){$error = "$error<li>No password given<BR>\n";}
if ($ADD_mailadres1== ""){$error = "$error<li>No mailadres given<BR>\n";}
if ($ADD_Userlevel== ""){$error = "$error<li>No userlevel given<BR>\n";}


// make an query which checks if the username OR the emailadres ar in the database. if they are append an error.
$query = "Select * from ".$DBprefix."signup where username='$ADD_username1' or mailadres='$ADD_mailadres1'";
$result = mysql_query($query); 
if ($row = mysql_fetch_array($result)){ 
if  ($row["username"] == $ADD_username1){$error = "$error<li>The username is already used<br>\n";}
if  ($row["mailadres"] == $ADD_mailadres1){$error = "$error<li>The e-mail adres is already used<br>\n";}
}

// if ther error variable is still an empty string. The summission was oke and you can start proccesing the submission
if ($error == ""){
	// first we check wat the date and time is for the signupdate field
	$datetime = date("d-m-Y G:i ");
	// also we create an activationcode if needed.
	if ($ADD_activated == 1){
		// the admin want the account to be activated. Therefor we can put the activationcode to 0
		$actnum = 0;
	}else{
		// if ADD_activated is 0 (zero) the admin want an unactivated account. Therefor a activationcode has to be generated.
		// generate an random code for the user neede to activate there account
		$actnum = "";
		// define the characters that may be in the code
		$chars_for_actnum = array ("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z","a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z","1","2","3","4","5","6","7","8","9","0");
			// take 20 times (1 to 20) an random char and add it to the $actnum variable
			for ($i = 1; $i <= 20; $i++) {
				$actnum = $actnum . $chars_for_actnum[mt_rand (0, count ($chars_for_actnum)-1)];
			}
	}

	// then we submit al this to the database
	$query = "INSERT INTO ".$DBprefix."signup (username, password, mailadres, actnum, userlevel, signupdate ,lastlogin, lastloginfail, numloginfail) VALUES ('$ADD_username1','$ADD_password1','$ADD_mailadres1','$actnum', '$ADD_Userlevel', '$datetime','0','0','0')";  
	$result = mysql_query($query); 

	// now we send a mail if needed
	if ($ADD_MailActnum == 1 && $ADD_activated==0){
			// if the admin wanted an unactivated account send a mail with the activationcode
			// note if you want to change this, you can modify it in config.php
			$mesage = $email_message_content;
			
			// mail the message to the user. you can adjust the title if you like,
			// in order to change the title you have to change the second field (=Sign up script user activationcode)
			mail($ADD_mailadres1, $email_message_title, $message, $email_message_header);
	}
	// and finally show a message that the action was succesfull
	htmlwrite("User created<br><a href=\"user.php?action=more&username=$ADD_username1\">more information about $ADD_username1</a><br><a href=\"admin.php\">back to overview</a>","ADD USER");
}else{
	// if $error is no longer a empty string there must have been error in the submision.
	// here we echo an nice line which says there are a couple of errors and we open an 
	// un-ordered list (just the <ul> tag) and we print the error. Also we include a link back to the
	// sign-upform
	htmlwrite ("The user could not be added to the database because of the following reason(s)<ul>
	$error
	</ul>Please return to <a href=\"user.php?action=adduser\">signup form</a> and try again.","ERROR");
}

break;

case "news":
	// get the current news items
	$query = "Select * from ".$DBprefix."news ORDER BY 'postDate' DESC";  
	$result = mysql_query($query); 
	$tmp = "[<a href=\"user.php?action=postNews\">Post news</a>]";
	while($row = mysql_fetch_array($result)){
		// we can't put it in the htmlwrite-function at once because of the while loop
		// therefore we put it into a variable first
		$tmp .= "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr> 
		<td colspan=\"3\" bgcolor=\"#CCCCCC\"><div align=\"center\">". $row["title"] ." [<a href=\"user.php?action=newsEdit&amp;newsID=".$row["newsID"]."\">edit</a>][<a href=\"user.php?action=newsDelete&amp;newsID=".$row["newsID"]."\">delete</a>]</div></td>
		  </tr><tr> <td bgcolor=\"#CCCCCC\">&nbsp;</td><td width=\"99%\">".$row["message"]."</td><td bgcolor=\"#CCCCCC\">&nbsp;</td></tr>
		  <tr><td colspan=\"3\" bgcolor=\"#CCCCCC\">posted by ". $row["poster"]." on ". $row["postDate"]."</td>
		  </tr></table><br>";
		  // and very important end the while loop
		 }
	htmlwrite($tmp,"manage news");
break;

case "validateNews":
	// retrieve the values from the form
	$EDITNEWS_title = @$HTTP_POST_VARS["title"];
	$EDITNEWS_message = @$HTTP_POST_VARS["message"];
	// reset the error variable
	$error = "";
	// check the fields for errors
	if($EDITNEWS_message == ""){$error = "$error <li>no message";}
	if($EDITNEWS_title == ""){$error = "$error <li>no title";}
	// check if the error field is empty
	if($error == ""){
		$datetime = date("Y-m-j H:i:s");
		$query = "INSERT INTO `".$DBprefix."news` (`title`, `message`, `poster`, `postDate`) VALUES ('".$EDITNEWS_title ."', '".$EDITNEWS_message."', '$user_currently_loged', '".$datetime."')";
		$result = mysql_query($query); 
		htmlwrite("the news has been posted, <a href=\"user.php?action=news\">Return to overview</a>","edit news succesfull");
	}else{
		htmlwrite("the news contains error(s): <ul>$error</ul><br> return to the <a href=\"user.php?action=postNews\">post news-page</a> and correct these errors.","ERROR");
	}
break;


case "revalidateNews":
	// retrieve the values from the form
	$EDITNEWS_title = @$HTTP_POST_VARS["title"];
	$EDITNEWS_message = @$HTTP_POST_VARS["message"];
	// reset the error variable
	$error = "";
	// check the fields for errors
	if($EDITNEWS_message == ""){$error = "$error <li>no message";}
	if($EDITNEWS_title == ""){$error = "$error <li>no title";}
	// check if the error field is empty
	if($error == ""){
		$query = "UPDATE ".$DBprefix."news SET title='".$EDITNEWS_title ."', message='".$EDITNEWS_message."' where newsID='".$newsID."' ";
		$result = mysql_query($query); 
		htmlwrite("the news has been edited, <a href=\"user.php?action=news\">Return to overview</a>","edit news succesfull");
	}else{
		htmlwrite("the news contains error(s): <ul>$error</ul><br> return to the <a href=\"user.php?action=newsEdit&amp;newsID=$newsID\">edit-page</a> and correct these errors.","ERROR");
	}
break;

case "newsDelete":
	htmlwrite("Are you sure you want to delete this post?<br>
	<a href=\"user.php?action=newsDeleteConfimed&newsID=$newsID\">YES</a> 
	<a href=\"user.php?action=news\">NO</a>", "Delete Message");
break;

case "newsDeleteConfimed":
	$query = "DELETE from ".$DBprefix."news where newsID='".$newsID."'";
	$result = mysql_query($query); 
	htmlwrite("The news was removed from the database<br><a href=\"user.php?action=news\">back to overview</a>","news deleted"); 
break;

case "newsEdit":
	$query = "Select * from ".$DBprefix."news where newsID='".$newsID."'";
	$result = mysql_query($query); 
	if ($row = mysql_fetch_array($result)){ 
		htmlwrite("<style type=\"text/css\">
		<!--
		.titlebox {
			color: #000000;
			background-color: #CCCCCC;
			text-align: center;
		}
		-->
		</style>
		<form name=\"form1\" method=\"post\" action=\"user.php?action=revalidateNews&amp;newsID=".$row["newsID"]."\">
		<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
		  <tr bgcolor=\"#CCCCCC\"> 
			<td colspan=\"2\"><div align=\"center\">
				  title: <input name=\"title\" type=\"text\" class=\"titlebox\" id=\"title\" size=\"40\" maxlength=\"40\" value=\"".$row["title"]."\">
			</div></td></tr>
		  <tr> 
			<td bgcolor=\"#CCCCCC\">&nbsp;</td>
			<td width=\"99%\"><div align=\"right\">
			message:<br>
				  <textarea name=\"message\" cols=\"75\" rows=\"10\">".$row["message"]."</textarea>
				</div></td></tr>
		  <tr bgcolor=\"#CCCCCC\"> 
			<td colspan=\"2\"><input type=\"submit\" value=\"Edit\">
				<input type=\"reset\" value=\"Reset Fields\"></td></tr>
		</table>
		</form>","Edit News");
	}else{
		htmlwrite("can't find that news item.","error");
	}
break;

case "postNews":
		htmlwrite("<style type=\"text/css\">
		<!--
		.titlebox {
			color: #000000;
			background-color: #CCCCCC;
			text-align: center;
		}
		-->
		</style>
		<form name=\"form1\" method=\"post\" action=\"user.php?action=validateNews\">
		<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
		  <tr bgcolor=\"#CCCCCC\"> 
			<td colspan=\"2\"><div align=\"center\">
				  title: <input name=\"title\" type=\"text\" class=\"titlebox\" id=\"title\" size=\"40\" maxlength=\"40\" value=\"".$row["title"]."\">
			</div></td></tr>
		  <tr> 
			<td bgcolor=\"#CCCCCC\">&nbsp;</td>
			<td width=\"99%\"><div align=\"right\">
			message:<br>
				  <textarea name=\"message\" cols=\"75\" rows=\"10\">".$row["message"]."</textarea>
				</div></td></tr>
		  <tr bgcolor=\"#CCCCCC\"> 
			<td colspan=\"2\"><input type=\"submit\" value=\"Post\">
				<input type=\"reset\" value=\"Reset Fields\"></td></tr>
		</table>
		</form>","Post News");
break;


// if the action variable does not match one of the actions above echo an message that the action doesn't exist.
default:
htmlwrite("The action u selected (\"$action1\") does not exist.<br>
<a href=\"admin.php\">return to overview</a>","ACTION DOES NOT EXIST");
}

// this function makes sure that everything the script returns becomes a complete htmlpage.
// the title argument is optional, the default value is "admin" (without qoutes).
function htmlwrite($message, $titel="ADMIN"){
echo"<title>$titel</title>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
</head>
<body link=\"#000000\" vlink=\"#000000\" alink=\"#000000\">
$message
";
}

?>
</body>
</html>
