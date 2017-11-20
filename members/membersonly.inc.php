<?PHP
// get the cookievars if they exist
$rememberCookieUname = @$HTTP_COOKIE_VARS["rememberCookieUname"];
$rememberCookiePassword = @$HTTP_COOKIE_VARS["rememberCookiePassword"];

// the security header which should be included in al memberpaga's
// first we include the configuration file which contains the database data
// also it will connect to the database.
include("../config.php");

// tell we want to work with sessions
session_start();

// the $HTTP_SESSION_VARS[id] in this query indicates that we want to retrieve the username from the session.
$query = "Select * from ".$DBprefix."signup where username='".@$HTTP_SESSION_VARS[id]."' And password = '".@$HTTP_SESSION_VARS[pass]."'";
$result = mysql_query($query); 

// if there are results check it the accesslevel is high enough. If there aren't results tell the user to log-in and stop (die) after that.
if ($row = mysql_fetch_array($result)){ 
	// set the current level into a variable for use on a page.
	$user_current_level = $row["userlevel"];
	// check if the user's access level is higher than zero. since if it is lower than zero he is an admin which have access to al pages.
	// and check if the user's level is high enough for this page.
	if ($reqlevel == 0 && $row["userlevel"] > 0){
		die("You need to be an admin for this page");
	}else{
		if ($row["userlevel"] < $reqlevel && $row["userlevel"] > 0){
			// it seems that the user's access level isn't high enough. Therefore 'die' (stop processing the page) with that message that the access level isn't high enough.
			die("Your acces level is not high enough for this page, <BR> Your access level: $row[userlevel] <BR>Level required: $reqlevel");
		}
	}

}else{
	// if there are no results, check for an cookie
	if ($rememberCookiePassword != "" && $rememberCookieUname != ""){
		// validate the cookie
		$query = "Select * from ".$DBprefix."signup where username='".$rememberCookieUname."'";
		$result = mysql_query($query); 
		// if there are results check it the accesslevel is high enough. If there aren't results tell the user to log-in and stop (die) after that.
		if ($row = mysql_fetch_array($result)){ 
			// check the password
			if (md5($row["password"]) == $rememberCookiePassword){
				// if the user has a valid cookie, set the session vars:
					// remove al the data from the session (auto logoff)
					session_unset();
					// remove the session itself
					session_destroy();
					// put the password in the session
					@ session_register("pass");
					$HTTP_SESSION_VARS["pass"] = $rememberCookiePassword;
					// put the username in the session
					@ session_register("id");
					$HTTP_SESSION_VARS["id"] =  $rememberCookieUname;
				//set the current level into a variable for use on a page.
				$user_current_level = $row["userlevel"];
				//check if the user's access level is higher than zero. since if it is lower than zero he is an admin which have access to al pages.
				//and check if the user's level is high enough for this page.
				if ($reqlevel == 0 && $row["userlevel"] > 0){
					die("You need to be an admin for this page");
				}else{
					if ($row["userlevel"] < $reqlevel && $row["userlevel"] > 0){
						//it seems that the user's access level isn't high enough. Therefore 'die' (stop processing the page) with that message that the access level isn't high enough.
						die("Your acces level is not high enough for this page, <BR> Your access level: $row[userlevel] <BR>Level required: $reqlevel");
					}
				}
			}else{die("Please log-in.");}
		}else{die("Please log-in.");}		
	}else{die("Please log-in.");}
}
// Below we set al variables which can be used in pages, things like the current logged user and his or hers level

// This will set the user_currently_loged variable
// first we remove the htmlcode from the username saved in a cookie
$user_currently_loged = htmlspecialchars($HTTP_SESSION_VARS["id"],ENT_NOQUOTES);
// Then we replace \" with "
$user_currently_loged = str_replace ('\"', "&quot;", $user_currently_loged);
$user_currently_loged = str_replace ("\'", "&#039", $user_currently_loged);
// and a variable where u get the plain username (only use for scripting!)
$user_currently_loged_plain = @$HTTP_SESSION_VARS["id"];

if ($user_current_level < 0){
	$user_current_Rank = "ADMIN";}
else{
	$user_current_Rank = @$ranks[$user_current_level];
}

// check if there are unread messages
$query = "Select * from ".$DBprefix."inbox where adres='".@$HTTP_SESSION_VARS[id]."' And isRead = '0'";
$result = mysql_query($query); 
$user_current_ammount_new = @mysql_num_rows($result); 
?>