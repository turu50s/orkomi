<?php /* Smarty version 2.6.26, created on 2010-10-20 15:35:36
         compiled from test.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'config_load', 'test.tpl', 1, false),array('modifier', 'date_format', 'test.tpl', 8, false),)), $this); ?>
<?php echo smarty_function_config_load(array('file' => 'test.conf'), $this);?>

<html>
<head><title><?php echo $this->_config[0]['vars']['title']; ?>
</title></head>
<body>
<H1><?php echo $this->_config[0]['vars']['title']; ?>
</H1>
<h2><?php echo @MY_TITLE; ?>
</h2>
こんにちは、<?php echo $this->_tpl_vars['name']; ?>
<br>
今日は、<?php echo ((is_array($_tmp=time())) ? $this->_run_mod_handler('date_format', true, $_tmp, '%Y年%m月%d日') : smarty_modifier_date_format($_tmp, '%Y年%m月%d日')); ?>
です。

</body>
</html>