{config_load file = 'test.conf'}
<html>
<head><title>{#title#}</title></head>
<body>
<H1>{#title#}</H1>
<h2>{$smarty.const.MY_TITLE}</h2>
こんにちは、{$name}<br>
今日は、{$smarty.now|date_format:'%Y年%m月%d日'}です。

</body>
</html>
