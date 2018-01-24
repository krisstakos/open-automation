<?php //fail za izlizane na user-a
session_start();
$_SESSION = array();
session_destroy();
header('Location: index.php');
die();
?>