<?php
require_once 'config.php';

// End the session and clear the session data
session_destroy();

// Redirect to homepage
header('Location: ' . $config['site_url']);
exit;
?>
