<?php
//Strict StandarsのWarningを消す。
ini_set('error_reporting', E_ALL & ~E_STRICT);
//ini_set('error_reporting', E_ALL);

//ini_set( "display_errors", "1");
//error_reporting(E_ALL);
//ini_set("register_globals","On");

// ユーザー認証
require_once('db_auth.php');

// 次画面へリダイレクト
$level = $myauth->getAuthData('level');

if ($level < 3) {
	header("Location: http://www.asa-kashiwa.net/orikomi/summary.php");
	exit();
} else {
	header("Location: http://www.asa-kashiwa.net/orikomi/input.php");
	exit();
}

?>
