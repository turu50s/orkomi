<html>
<head>
<title>������ڡ���</title>
<link rel="stylesheet" type="text/css" href="./login.css">
</head> 
<body  onLoad="document.login.username.focus()">
<h1>�޹����󡦥��������</h1>

<form method="post" action="<?php print($_SERVER['PHP_SELF']) ?>" name="login">
<table border="0">
	<tr>
		<th align="right">�桼����̾</th>
		<td><input type="text" name="username" size="15" maxlength="20"></td>
	</tr>
    <tr>
    	<th align="right">�ѥ����</th>
    	<td><input type="password" name="password" size="15" maxlength="20"></td>
    </tr>
    <tr>
    	<td colspan="2"><input type="submit" name="sub" value="������"></td>
    </tr>
</table>
<h2><?php print @$err ?></h2>
</form>

</body>
</html>
