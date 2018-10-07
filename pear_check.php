<?php
require_once 'PEAR/Config.php';
require_once 'PEAR/Registry.php';
 
$config = new PEAR_Config();
$reg = new PEAR_Registry($config-&gt;get('php_dir'));
$packages = $reg-&gt;listPackages();
var_dump($packages);
?>