<?php
require_once('db_auth.php');

print($myauth->getAuthData('section'));
// $field = $myauth->getAuthData();
// print("�桼����̾��".$field['username'].' / ');
// print("�ѥ���ɡ�".$field['password']);
// print("section   ��".$field['section']);
session_destroy();

?>