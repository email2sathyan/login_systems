<?PHP
// NOTE adding extra fields is for the little more advance php/mysql'er
// If your not planing to add more fields you can ignore the lines with an # infront of it (that means lines with // # as first 4 chars) 

// retrieve al the variables that had been submited by the from
$username1 = $HTTP_POST_VARS["username"];
$mailadres1 = $HTTP_POST_VARS["mailadres"];
$password1 = $HTTP_POST_VARS["password"];
$confirmpassword1 = $HTTP_POST_VARS["confirmpassword"];

// # if you want to save more fields that is possible by adding them below this line
// # the santax is as followed:
// # 
// # $internalname = $HTTP_POST_VARS["name_you_gave_the_field_in_the_form"];
// # 
// # and ofcourse you replace internalname and name_you_gave_the_field_in_the_form with 
// # the information requested
// # futher you will have to remove the 2 slashes in front of the line.
// # you can see the above lines ($username = ...) as an example for what the result should  be
// # futher below in this script you will find the second step.

// generate an random code for the user neede to activate there account
$actnum = "";

// include config.php for the settings used below
include("config.php");

// check if the confirm mail is send AND manual confirm is off
if ($UseMailConfirm == false AND $makeAdminOnlyActivate == false){
	// set the actnum to 0 indicating the account is activated
	$actnum = "0";
}else{
	// tripod doesnt't support the normal way of making a code. Therefore we genereate a random number for tripod
	if ($TripodSupport == true){
		$actnum = rand(10000000,99999999999999);
	}else{
		// define the characters that may be in the code
		$chars_for_actnum = array ("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z","a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z","1","2","3","4","5","6","7","8","9","0");
		// take 20 times (1 to 20) an random char and add it to the $actnum variable
		for ($i = 1; $i <= 20; $i++) {
			$actnum = $actnum . $chars_for_actnum[mt_rand (0, count ($chars_for_actnum)-1)];
		}
	}
}
// +---------------------------------------------------+
// + the code below is writen by Jean Sebastien Chasle |
// +---------------------------------------------------+

// Added to check if UserName is Valid
// Regex of valid characters
$UserNameChars = "^[A-Za-z0-9_-]";
// Check to make sure it is valid
$UserNameGood = true;
if(!ereg("$UserNameChars",$username1))
{
	    $UserNameGood = false;
}
//End of addition to UserName validation

// Added to check if PassWord is Valid
// Regex of valid characters
$PassWordChars = "^[A-Za-z0-9_-]";
// Check to make sure it is valid
$PassWordGood = true;
if(!ereg("$PassWordChars",$password1))
{
	    $PassWordGood = false;
}
// End of addition to PassWord validation

// +------------------------------------+
// | end off Jean Sebastien Chasle code |
// | note that the variable set in this |
// | code block are used later on.      |
// + -----------------------------------+

// set the error variable to an empty string.
$error = "";

// check it the fields are not empty. if they are, append the error to the error variable ($error)
if ($username1 == ""){$error = "$error<li>No username given<BR>\n";}
if ($password1 == ""){$error = "$error<li>No password given<BR>\n";}
if ($mailadres1== ""){$error = "$error<li>No mailadres given<BR>\n";}

// check if the fields are not invalidid
if ($UserNameGood == false && $UsernameValCharOnly == true){$error = "$error<li>The username contains invalid chars.<BR>\n";}
if ($PassWordChars == false && $passwordValCharOnly == true){$error = "$error<li>The password contains invalid chars.<BR>\n";}
if (strlen($password1) < $passwordLengthMIN ){$error = "$error<li>The password contains to little chars.<BR>\n";}
if (strlen($password1) > $passwordLengthMAX ){$error = "$error<li>The password contains to much chars.<BR>\n";}
if (strlen($username1) < $usernameLengthMIN ){$error = "$error<li>The username contains to little chars.<BR>\n";}
if (strlen($username1) > $usernameLengthMAX ){$error = "$error<li>The username contains to much chars.<BR>\n";}

// we call the validadres() function to check the adres (the function is explain on the bottom of this page)
if (validadres($mailadres1) == false ){$error = "$error<li>The given e-mail adres is not valid<BR>\n";}

// # if you have added more requerd fields you can add them below this line so they are validated:
// # example santax:
// 
// # if ($internalname == ""){$error = "$error<li>your error message for this field<BR>\n";}
// 
// # and once again you can take the above code (if ($username1...) as an example for what the result should be


// check if the passwords match. if they don't append the error to the error variable ($errir)
if ($password1 <> $confirmpassword1) {$error = "$error<li>Passwords do not match<BR>\n";}


// make an query which checks if the username OR the emailadres ar in the database. if they are append an error.
$query = "Select * from ".$DBprefix."signup where username='$username1' or mailadres='$mailadres1'";
$result = mysql_query($query); 
if ($row = mysql_fetch_array($result)){ 
if  ($row["username"] == $username1){$error = "$error<li>Your username is already used by another member<br>\n";}
if  ($row["mailadres"] == $mailadres1){$error = "$error<li>Your e-mail adres is already registrated in our database<br>\n";}

// # if you want to validate fields to the database (beeing uniek) you can add that line below
// # example santax:
// #
// # if  ($row["internal_name_in_database"] == $internalnam){$error = "$error<li>Your error message for this fieldbr>\n";}
// #
// # internal_name_in_database should be an column name from the db.
}

// if ther error variable is still an empty string. The summission was oke and you can start proccesing the submission
if ($error == ""){
// first we check wat the date and time is for the signupdate field
$datetime = date("d-m-Y G:i ");
// then we submit al this to the database

// # the query can be adjusted for your db, but if it is information which is only important to admins (adres etc.),
// # it might me smart to add it to another table. that would be the following syntax
// #
// # $query = "INSERT INTO yourtablename (username, field1, field2, staticdata) VALUES ('$username1','$internalname1','$internalname2','abc')";  
// # $result = mysql_query($query);
// #
// # it this exampe column field1 gets the value of the intername1 variable. The intername1 variable it set to an value from the form
// # of course you replace yourtablename, field1, field2, staticdate, intername1, internalname2 with the names you chose for those fields
// # note that if you chose to put it in a seperate table, you include the username as an column. Else you don't know from who the results are.
// # 
// # to make it 100% clear I have included an example below:
// # > username enters there country in an textbox called textCountry
// # > user submits
// # > php gets txtCountry out of the post variables and puts it in $internnameCountry (example on line 14)
// # > after it is checked for not empty (example on line 42)
// # > it is added to the db, it is stored in a column called clmnCountry in a table called userinfo.
// # > query for that: 
// # > INSERT INTO userinfo (username, clmnCountr) VALUES ('$username1','$internnameCountry')

// the databae connection has already been made by config.php

$query = "INSERT INTO ".$DBprefix."signup (username, password, mailadres, actnum, userlevel, signupdate ,lastlogin, lastloginfail, numloginfail) VALUES ('$username1','$password1','$mailadres1','$actnum', '1', '$datetime','0','0','0')";  
$result = mysql_query($query); 

// we check if this isn't disabled
if ($UseMailConfirm == true){
// prepare the message (replae the %p and %a)
$message = $email_message_content;
$message = str_replace("%p",$actnum, $message);
$message = str_replace("%a","%", $message);
// mail the message to the user. you can adjust the title if you like,
// you can change al settings from the mail in config.php
mail($mailadres1, $email_message_title, $message, $email_message_header);
}

// and redirect the user to the activation page if not activated, else redirect to the loginpage
if ($UseMailConfirm == false AND $makeAdminOnlyActivate == true){
header("Location: login.php"); 
}else{
header("Location: activate.php"); 
}
}
else
// if $error is no longer a empty string there must have been error in the submision.
// here we echo an nice line which says there are a couple of errors and we open an 
// un-ordered list (just the <ul> tag) and we print the error. Also we include a link back to the
// sign-upform
{echo "You could not be added to the database because of the following reason(s)<ul>
$error
</ul>Please return to <a href=\"signup.php\">signup form</a> and try again.";
}

function validadres($MailAdres){
// first we assum that the adres is corect
$prereturn = true;
// then we check the length. if is is less then 5 chars long. the adres has to be invalid
if (strlen($MailAdres) < 5){$prereturn = false;}

// we split the email ares in 2 parts, we 'break' the adres at the @ sign
$partsNumber = split("@",$MailAdres);
// if there aren't 2 parts after the adres was split the adres is incorrect
if (count($partsNumber) <> 2) {$prereturn = false;}
else{
// else we save the 2 parts in $user and $domain
list($user,$domain) = split("@",$MailAdres);
// and we check if the user entered a part before the @.
if (strlen($user) < 1) {$prereturn = false;}
}

// after the validation $prereturn has either true or false.
// - false if one validation turned out bad
// - true if the adres is validid
// now we return this value back to the place it was requered.
return $prereturn;
}
?>