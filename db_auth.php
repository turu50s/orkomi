<?php

$path = '.' .PATH_SEPARATOR. '../PEAR';

// set_include_path(get_include_path() .PATH_SEPARATOR. $path);
ini_set('include_path',get_include_path() .PATH_SEPARATOR. $path);

// error_reporting(E_ALL);

require_once('Auth/Auth.php');


function loginFunction($username, $status){
	switch ($status) {
		case AUTH_IDLED:
		case AUTH_EXPIRED:
			$err = 'ログイン期間が過ぎています。再ログインして下さい。';
			session_destroy();
			break;
		case AUTH_WRONG_LOGIN:
			$err = 'ユーザーＩＤ／パスワードが間違っています。';
			session_destroy();
			break;
	}
	require_once('loginform.php');
}

$params = array(
    'dsn' => 'mysqli://sddbMTgzOTIz:i8h4vf5U@sddb0040128103.cgidb/sddb0040128103',
    'table' => 'user',
	'usernamecol' => 'username',
    'passwordcol' => 'password',
    'db_fields'   => '*',
	'cryptType'	  => 'md5'
);

$myauth = new Auth('DB', $params,'loginFunction');

$myauth->start();


if (!$myauth->getAuth()){
	exit();
}

?>
