<?php
require_once 'config.php';

session_unset();
session_destroy();

jsonResponse(['success' => true, 'message' => 'Logged out successfully.']);
?>
