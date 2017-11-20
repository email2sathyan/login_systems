<?PHP
//tell we want to work with sessions
session_start();
//remove al the data from the session
session_unset();
//remove the session itself
session_destroy();
//redirect to the login page
header("Location: ../login.php");
?>