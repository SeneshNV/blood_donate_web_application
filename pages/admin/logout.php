<?php
// Start the session
session_start();

// Destroy the session
session_destroy();

// Redirect to the dashboard
header("Location: ../../index.html");
exit();
?>
