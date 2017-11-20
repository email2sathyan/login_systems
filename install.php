<?PHP
// check if we have to update or even preupdate:
$action = $HTTP_GET_VARS["action"];
$preUpdate = $HTTP_GET_VARS["preUpdate"];
$special = $HTTP_GET_VARS["special"];
// let the config.php connect to the database.
include("config.php");

// do if pre-update if needed
if ($preUpdate == true){
	$queryX = "ALTER TABLE signup MODIFY actnum VARCHAR(20)";  
	$resultX = mysql_query($queryX); 
	// check it it was succesfull
	if ($resultX == 1){echo "signup Table succesfully altered.<br>";}
	// if it wasn't succesfull print the errornumer (with mysql_errno()) and an description of the error (with mysql_error())
	else{echo "Error while altering signup table (Errornumber ". mysql_errno() .": \"". mysql_error() ."\")<br>";}
}

// if we update we rename the table... else we create one
if ($action == "update"){
	$query = "RENAME TABLE signup TO ".$DBprefix."signup";  
	$result = mysql_query($query); 
	// check it it was succesfull
	if ($result == 1){echo "signup Table succesfully renamed.<br>";}
	// if it wasn't succesfull print the errornumer (with mysql_errno()) and an description of the error (with mysql_error())
	else{echo "Error while renaming signup table (Errornumber ". mysql_errno() .": \"". mysql_error() ."\")<br>";}

}
else{
	// make the signup table
	$query = "CREATE TABLE ".$DBprefix."signup(username VARCHAR(20), password VARCHAR(20), mailadres VARCHAR(100), actnum VARCHAR(20), userlevel TINYINT, signupdate VARCHAR(16), lastlogin VARCHAR(16), lastloginfail BIGINT,numloginfail TINYINT)";  
	$result = mysql_query($query); 
	// check it it was succesfull
	if ($result == 1){echo "signup Table succesfully created.<br>";}
	// if it wasn't succesfull print the errornumer (with mysql_errno()) and an description of the error (with mysql_error())
	else{echo "Error while creating signup table (Errornumber ". mysql_errno() .": \"". mysql_error() ."\")<br>";}
}	

// create the inbox
$query4 = "CREATE TABLE `".$DBprefix."inbox`(`messageID` SMALLINT NOT NULL AUTO_INCREMENT,`adres` VARCHAR(20) NOT NULL ,`sender` VARCHAR(20) NOT NULL ,`DateRecieved` DATETIME NOT NULL ,`title` VARCHAR(30) NOT NULL ,`message` TEXT NOT NULL,`isRead` TINYINT NOT NULL,PRIMARY KEY ( `messageID` ));";
$result4 = mysql_query($query4); 
// check it it was succesfull
if ($result4 == 1){echo "inbox Table succesfully created.<br>";}
// if it wasn't succesfull print the errornumer (with mysql_errno()) and an description of the error (with mysql_error())
else{echo "Error while creating inbox table (Errornumber ". mysql_errno() .": \"". mysql_error() ."\")<br>";}

// create the old messages folder
$query5 = "CREATE TABLE `".$DBprefix."OldMessages`(`messageID` SMALLINT NOT NULL AUTO_INCREMENT,`adres` VARCHAR(20) NOT NULL ,`sender` VARCHAR(20) NOT NULL ,`DateRecieved` DATETIME NOT NULL ,`title` VARCHAR(30) NOT NULL ,`message` TEXT NOT NULL,`isRead` TINYINT NOT NULL,PRIMARY KEY ( `messageID` ));";
$result5 = mysql_query($query5); 
// check it it was succesfull
if ($result5 == 1){echo "oldMessages Table succesfully created.<br>";}
// if it wasn't succesfull print the errornumer (with mysql_errno()) and an description of the error (with mysql_error())
else{echo "Error while creating oldMessages table (Errornumber ". mysql_errno() .": \"". mysql_error() ."\")<br>";}

// create the outbo folder
$query6 = "CREATE TABLE `".$DBprefix."Outbox`(`messageID` SMALLINT NOT NULL AUTO_INCREMENT,`adres` VARCHAR(20) NOT NULL ,`sender` VARCHAR(20) NOT NULL ,`DateSend` DATETIME NOT NULL ,`title` VARCHAR(30) NOT NULL ,`message` TEXT NOT NULL,PRIMARY KEY ( `messageID` ));";
$result6 = mysql_query($query6); 
// check it it was succesfull
if ($result6 == 1){echo "outbox Table succesfully created.<br>";}
// if it wasn't succesfull print the errornumer (with mysql_errno()) and an description of the error (with mysql_error())
else{echo "Error while creating outbox table (Errornumber ". mysql_errno() .": \"". mysql_error() ."\")<br>";}

// create the news database
$query7 = "CREATE TABLE `".$DBprefix."news`(`newsID` SMALLINT NOT NULL AUTO_INCREMENT,`title` VARCHAR(20) NOT NULL ,`message` TEXT NOT NULL ,`poster` VARCHAR(30) NOT NULL ,`postDate` DATETIME NOT NULL,PRIMARY KEY ( `newsID` ));";
$result7 = mysql_query($query7); 
// check it it was succesfull
if ($result7 == 1){echo "news Table succesfully created.<br>";}
// if it wasn't succesfull print the errornumer (with mysql_errno()) and an description of the error (with mysql_error())
else{echo "Error while creating news table (Errornumber ". mysql_errno() .": \"". mysql_error() ."\")<br>";}


// we only have to create an admin account on fresh install... not after an update
if ($action != "update"){
	// check if there isn't an admin account already.
	$query3 = "Select * from ".$DBprefix."signup where username='admin'";
	$result3 = mysql_query($query3); 
	if ($row3 = mysql_fetch_array($result3)){ 
	echo "Error while creating admin account, it already exist.<br>";
	}
	else
	{
	// determin the time
	$datetime = date("d-m-Y G:i ");
	// create the admin account
	$query2 = "INSERT INTO ".$DBprefix."signup VALUES ('admin','admin','-','0','-1','$datetime','0','0','0')";
	$result2 = mysql_query($query2); 
	// check it it was succesfull
	if ($result2 == 1){echo "Admin account succesfully created.<br>";}
	// if it wasn't succesfull print the errornumer (with mysql_errno()) and an description of the error (with mysql_error())
	else{echo "Error while creating admin account (Errornumber ". mysql_errno() ." :\"". mysql_error() ."\")<br>";}
	}
}else{
// the query was succesfull, this because something that is already correct, can't fail :) 
$result2 == 1;
}

// Print htat the installation is done if so, else print a failure message
if ($result == 1 && $result2 == 1 && $result4 == 1 && $result5 == 1 && $result6 == 1 && $result7 == 1){echo "Installation succesfull!";}else
{echo "There where some errors while installing Advance Login System";}

echo "<br><br><strong> DO NOT FORGET TO REMOVE THIS FILE AFTER THE INSTALLATION!!! </strong>";
?>
