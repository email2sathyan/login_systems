<?PHP 
$reqlevel = 0;
include("membersonly.inc.php");?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>ADMIN</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body bgcolor="#FFFFFF" text="#FFFFFF" link="#FFFFFF" vlink="#CCCCCC" alink="#FFFFFF">

<?PHP 
$begin = @$HTTP_GET_VARS["begin"];
$sortby = @$HTTP_GET_VARS["sortby"];
$direction = @$HTTP_GET_VARS["direction"];

// make sure that $begin is a number
if ($begin == ""){$begin=0;}
// make sure there is something to sort by
if ($sortby == ""){$sortby="username";}
// make sure there is a direction to search to
if ($direction == ""){$direction="ASC";}

// get the number of records
$query = "Select * from ".$DBprefix."signup";  
$result = mysql_query($query); 
$rownumbers = mysql_num_rows($result);

// set the linkline variable to nothing
$linkline = "";
// check if 	there are more than 50 records
if (($rownumbers - $begin -50) > 50) {
	// add link to linkline
	$linkline = "$linkline <a href=\"admin.php?begin=". ($begin + 50) ."\"><font color=\"#000000>\">next 50</font></a> ";
}else{
	// check it there are enough rows for another page, more than 0 is needed
	if (($rownumbers - $begin -50) > 0){
		// add the link generated with the number of rows to the linklink
		$linkline = "$linkline <a href=\"admin.php?begin=". ($begin + 50) ."\"><font color=\"#000000>\">next ".($rownumbers - $begin - 50)."</font></a> ";
	}
}
// check if this is a page after the first, if it is, make a link to go back 1 page
if ($begin > 0){$linkline = "$linkline <a href=\"admin.php?begin=". ($begin - 50) ."\"><font color=\"#000000>\">last 50</font></a>";}
// add a link to the add userpage to the link line
$linkline = "$linkline <a href=\"user.php?action=adduser\"><font color=\"#000000>\">Add User</font></a> <a href=\"user.php?action=news\"><font color=\"#000000>\">Manage News</font></a>";

	// set the page jump line to nothing
	$pagejumpline = "";
	// check if there would be more than 6 pages
	if ($rownumbers > 300){
	
		// write links to the first 3 pages. And check if the current page isn't one of them. If it is, write bold number.
		if ($begin != (50 * 1)-50 ) {$pagejumpline = "$pagejumpline <a href=\"admin.php?begin=". ((1*50)-50) ."\"><font color=\"#000000>\">1</font></a> ";}else{$pagejumpline = "$pagejumpline <strong><font color=\"#000000>\">1</font></strong> ";}
		if ($begin != (50 * 2)-50 ) {$pagejumpline = "$pagejumpline <a href=\"admin.php?begin=". ((2*50)-50) ."\"><font color=\"#000000>\">2</font></a> ";}else{$pagejumpline = "$pagejumpline <strong><font color=\"#000000>\">2</font></strong> ";}
		if ($begin != (50 * 3)-50 ) {$pagejumpline = "$pagejumpline <a href=\"admin.php?begin=". ((3*50)-50) ."\"><font color=\"#000000>\">3</font></a> ";}else{$pagejumpline = "$pagejumpline <strong><font color=\"#000000>\">3</font></strong> ";}
		
		// check if it is needed to write numbers between (example: 1 2 3 ... 10 11 12 ... 98 99 100)
		if ($begin >= 150 && $begin < ($rownumbers-150)){
		
			// check it if is needed to write dots between the first 3 pages and the middel line
			// if the current page is 4 or 5 than it is not needed (example: 1 2 3 4 5 6 ... 98 99 100 after this page  1 2 3 ... 5 6 7 ... 98 99 100)
			// also check if there if there is needed for the 1ste number in the line. for example:
			// 1 2 3 4 5 ... 98 99 100 , in tis case the current page is 4, the page before 4 is 3 (doh) and 3 is already writen by default
			if ($begin != (50 * 4-50) ) {if ($begin != (50 * 5-50) ) {$pagejumpline = "$pagejumpline <font color=\"#000000>\">...</font> ";}
			$pagejumpline = "$pagejumpline <a href=\"admin.php?begin=". ($begin - 50) ."\"><font color=\"#000000>\">".((($begin+50)/50)-1)."</font></a> ";
			}
			// write the current page numer
			$pagejumpline = "$pagejumpline  <strong><font color=\"#000000>\">".(($begin+50)/50)."</font></strong> ";
			// check it there is needed for the 3de number in the middel line.
			if ($begin != (50 * (ceil($rownumbers/50)-3)-50) ) {$pagejumpline = "$pagejumpline <a href=\"admin.php?begin=". ($begin + 50) ."\"><font color=\"#000000>\">".((($begin+50)/50)+1)."</font></a> ";
			// check if there is neede for dots.
			if ($begin != (50 * (ceil($rownumbers/50)-4)-50) ) {$pagejumpline = "$pagejumpline <font color=\"#000000>\">...</font> ";}
		}
		// if there is no need for the middel line, place dots to seperate.
		}else{
			$pagejumpline = "$pagejumpline <font color=\"#000000>\">...</font> ";
		}
	
	// write the last 3 pages.
	if ($begin != (50 * (ceil($rownumbers/50)-2)-50) ) {$pagejumpline = "$pagejumpline <a href=\"admin.php?begin=". (((ceil($rownumbers/50)-2)*50)-50) ."\"><font color=\"#000000>\">".(ceil($rownumbers/50)-2)."</font></a> ";}else{$pagejumpline = "$pagejumpline <strong><font color=\"#000000>\">".(ceil($rownumbers/50)-2)."</font></strong> ";}
	if ($begin != (50 * (ceil($rownumbers/50)-1)-50) ) {$pagejumpline = "$pagejumpline <a href=\"admin.php?begin=". (((ceil($rownumbers/50)-1)*50)-50) ."\"><font color=\"#000000>\">".(ceil($rownumbers/50)-1)."</font></a> ";}else{$pagejumpline = "$pagejumpline <strong><font color=\"#000000>\">".(ceil($rownumbers/50)-1)."</font></strong> ";}
	if ($begin != (50 * (ceil($rownumbers/50)-0)-50) ) {$pagejumpline = "$pagejumpline <a href=\"admin.php?begin=". (((ceil($rownumbers/50)-0)*50)-50) ."\"><font color=\"#000000>\">".(ceil($rownumbers/50))."</font></a> ";}else{$pagejumpline = "$pagejumpline <strong><font color=\"#000000>\">".(ceil($rownumbers/50)-0)."</font></strong> ";}
	// actually put the result of al above on the page.
	echo "$pagejumpline<br>";
}

// write the linkline in the page (above the table)
echo "$linkline";
?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr> 
    <td width="22%" bgcolor="#000000"><div align="center"><a href=<?php 
	echo "\"admin.php?begin=$begin&amp;sortby=username&amp;direction=";
	if ($sortby == "username" && $direction=="ASC"){echo "DESC\"";}
	else {echo "ASC\"";}
	?>><font color="#FFFFFF">Username</font></a></div></td>
    <td width="26%" bgcolor="#333333"><div align="center"><a href=<?php 
	echo "\"admin.php?begin=$begin&amp;sortby=mailadres&amp;direction=";
	if ($sortby == "mailadres" && $direction=="ASC"){echo "DESC\"";}
	else {echo "ASC\"";}
	?>><font color="#FFFFFF">E-mailadres</font></div></td>
    <td width="6%" bgcolor="#000000"><div align="center"><a href=<?php 
	echo "\"admin.php?begin=$begin&amp;sortby=actnum&amp;direction=";
	if ($sortby == "actnum" && $direction=="ASC"){echo "DESC\"";}
	else {echo "ASC\"";}
	?>><font color="#FFFFFF">Activated </font></div></td>
    <td width="46%" bgcolor="#333333"><div align="center"><font color="#FFFFFF">Actions</font></div></td>
  </tr>
  <?PHP 
// set the color switcher to zero
$bgcolorswithcer = 0;
// make an query to retrieve al members
$query = "Select * from ".$DBprefix."signup ORDER BY '$sortby' $direction LIMIT $begin, 50 ";  
$result = mysql_query($query); 
// use a while loop to print al the results
while($row = mysql_fetch_array($result)){
?>
  <tr> 
    <td bgcolor="<?PHP  
// a simple script to change the value of the bgcolor atribuut of the TD tag.	
if ($bgcolorswithcer == 0) {
echo "#000099";
 }
else
{
echo "#000066";
} ?>"><div align="center"><font color="#FFFFFF"> 
        <?PHP  
		// print the usename retrieved from the database
		echo htmlspecialchars($row["username"],ENT_QUOTES); ?>
        </font></div></td>
    <td bgcolor="<?PHP  if ($bgcolorswithcer == 0) {
echo "#003399";
 }
else
{
echo "#003366";
} ?>"><div align="center"><font color="#FFFFFF"> 
        <?PHP  
		// print the mailadres retrieved from the database
		//htmlspecialchars() is used to make sure the users can't put any (working) html code in
		//there name that would screw up the admin page
		echo htmlspecialchars($row["mailadres"],ENT_QUOTES); ?>
        </font></div></td>
    <td bgcolor="<?PHP  if ($bgcolorswithcer == 0) {
echo "#000099";
 }
else
{
echo "#000066";
} ?>"> <div align="center"><font color="#FFFFFF"> 
        <?PHP  
		// check if the user has activated his account by looking at the activatingnumer. If 0 then it is activated else not.
		if ($row["actnum"] == "0"){echo "+";}
		else {echo "-";} ?>
        </font></div></td>
    <td bgcolor="<?PHP  if ($bgcolorswithcer == 0) {
echo "#003399";
$bgcolorswithcer = 1;
 }
else
{
echo "#003366";
$bgcolorswithcer = 0;
} ?>"><div align="center"> 
        <?PHP 
		//write the default actions.
		echo "<a href=\"user.php?action=more&username=". htmlspecialchars($row["username"],ENT_QUOTES)."\">More Info</a> "; 
	  	echo "<a href=\"user.php?action=delete&username=". htmlspecialchars($row["username"],ENT_QUOTES)."\">Delete User</a> ";
		//write the actions that depend on a value
		//if the user is an admin you can dismiss him else you can make him an admin
		//you can check if an user is an admin by looking at the value of 'userlevel' (a column int the database)
		//if it is more than 0 then it is a normal user if it is less than 0 the user is an admin
	  	if ($row["userlevel"]>=0){echo "<a href=\"user.php?action=makeadmin&username=". htmlspecialchars($row["username"],ENT_QUOTES)."\">Make Admin</A> ";
			//only if the user isn't a admin you can change the access level
			//and ofcourse the account has to be activated
			if ($row["actnum"]=="0"){
				echo " <a href=\"user.php?action=changelevel&username=". htmlspecialchars($row["username"],ENT_QUOTES )."\">Change access level</A>";}
		}
		else {echo "<a href=\"user.php?action=stopadmin&username=". htmlspecialchars($row["username"],ENT_QUOTES )."\">Dismiss Admin</A> ";}
		if ($row["actnum"] != "0"){echo "<a href=\"user.php?action=activate&username=". htmlspecialchars($row["username"],ENT_QUOTES)."\">Activate account</a> ";}
		?>
		</div>
		</td>
  </tr>
  
  <?php
  // and very important end the while loop
   }
   ?>
</table>
   <?PHP 
   // write the linkline & numberline in the page (below the table)
   echo "$linkline<br>$pagejumpline";
    ?>
</body>
</html>
