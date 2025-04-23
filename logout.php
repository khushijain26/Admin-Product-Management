<?php
// Start the session
session_start();

// Destroy all session variables (logout)
session_destroy();

// Redirect to login page
header("Location: login.php");
exit;
?>
