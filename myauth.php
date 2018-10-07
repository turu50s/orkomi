<?php
require_once('db_auth.php');

print($myauth->getAuthData('section'));
// $field = $myauth->getAuthData();
// print("ユーザー名：".$field['username'].' / ');
// print("パスワード：".$field['password']);
// print("section   ：".$field['section']);
session_destroy();

?>