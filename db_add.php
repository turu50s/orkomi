<?php
$path = '.' .PATH_SEPARATOR. '../PEAR';
// set_include_path(get_include_path() .PATH_SEPARATOR. $path);
ini_set('include_path',get_include_path() .PATH_SEPARATOR. $path);

// error_reporting(E_ALL);

require_once('Auth/Auth.php');

$params = array(
    'dsn' => 'sqlite://../SQLiteManager-1.2.0/orikomi.sqlite2',
    'table' => 'user',
	'usernamecol' => 'username',
    'passwordcol' => 'password',
    'db_fields'   => '',
	'cryptType'	  => 'md5'
);

$myauth = new Auth('DB', $params);


if ($myauth->addUser('user','pass')){
	print('ゆーざーを追加しました。');
}

?>
