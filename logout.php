<?php
// 1. Resume the current session so PHP knows which session to destroy
session_start(); 

// 2. Remove all session variables (like $_SESSION['admin_logged_in'])
session_unset(); 

// 3. Completely destroy the session on the server
session_destroy(); 

// 4. Redirect the user back to the Home page
header("Location: index.php");

// 5. Always use exit() after a header redirect to stop the script
exit(); 
?>