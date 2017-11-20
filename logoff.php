<?PHP
//tell we want to work with sessions
session_start();
// set the cookie vars to zero
setcookie("rememberCookieUname","session expired",(time()+604800));
setcookie("rememberCookiePassword","X@X",(time()+604800));
//remove al the data from the session
session_unset();
//remove the session itself
session_destroy();
//redirect to the login page
header("Location: login.php");
?>