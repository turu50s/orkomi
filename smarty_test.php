<?php
ini_set('display_errors',1);
define('SMARTY_DIR','../Smarty/libs/');
require_once(SMARTY_DIR.'Smarty.class.php');

$smarty = new Smarty();

$smarty->template_dir = './templates/';
$smarty->compile_dir  = './templates_c/';
$smarty->config_dir   = './configs/';
$smarty->cache_dir    = './cache/';

define('MY_TITLE','Smarty sample');

$smarty->assign('name','World');
$smarty->display('test.tpl');
?>