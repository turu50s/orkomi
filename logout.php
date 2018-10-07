<?php
session_start();
$_SESSION = array();
session_destroy();

header('Location: http://www.asa-kashiwa.net/orikomi/index.php');
?>