<?php
ini_set( "display_errors", "Off");

// StrictStandars�Υ�˥󥰤�ä���
ini_set('error_reporting', E_ALL & ~E_STRICT);

require_once('db_auth.php');

$level = $myauth->getAuthData('level');

$today     = getdate();
$yy        = $today['year'];
$mm        = $today['mon'];
$dd        = $today['mday'];
$date_k = $yy.'/'.$mm.'/%';

// �޹��ǡ������١�����³
$dsn = 'mysql:dbname=sddb0040128103;host=sddb0040128103.cgidb';
$user = 'sddbMTgzOTIz';
$password = 'i8h4vf5U';

// ͽ���ơ��֥븡��
try {
    //$db = new PDO('sqlite2:../SQLiteManager-1.2.0/orikomi.sqlite2');
    $db = new PDO($dsn, $user, $password);
	
	$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    $sql = "SELECT * FROM yosan8";
    $rs=$db->query($sql);
    $row = $rs->fetchAll();
    
    foreach ($row as $rec) {
        $date_w = explode('/',$rec['month']);
        if ($date_w[1] < 4) {
            $date_r = $date_w[1] + 12;
        } else {
            $date_r = $date_w[1];
        }
        $hachi[$date_r-4] = $rec['budget'];
    }
} catch (PDOException $e) {
    die('error: '.$e->getMessage());
}

?>

<html>
<head>
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta http-equiv="Expires" content="Thu, 01 Dec 1994 16:00:00 GMT"> 
<title>ͽ������</title></head>
<link rel="stylesheet" type="text/css" href="./honbu.css">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<body> 
<script>
	$(function() {
		$('#clr').on("click", function() {
			$('.data').val('0');
		});
	});
</script>
<h2>ͽ���ǡ��������������ˡ���<?php if ($mm < 4) {$yy = $yy - 1;} print($yy); ?>ǯ�١�����������������<font color="white">(ñ�̡����)</font></h2>
<div class="menu">
<a href="summary8.php">���ӻ���</a>
<a href="input.php">��������</a>
<br>
<a href="logout.php">��������</a>
</div>
<div class="ji">
<div class="seki">
<!- ͽ���ǡ������� -->
<form id="form" method="post" action="./yosan8_in.php">
<table border="1" cellspacing="0" cellpadding="5">
    <tr><th></th><th>ͽ��</th></tr>
    <tr><td>4��</td><td><input type="text" name="hachi1" class="data" value="<?php print($hachi[0]); ?>" size="10"></td></tr>
    <tr><td>5��</td><td><input type="text" name="hachi2" class="data" value="<?php print($hachi[1]); ?>" size="10"></td></tr>
    <tr><td>6��</td><td><input type="text" name="hachi3" class="data" value="<?php print($hachi[2]); ?>" size="10"></td></tr>
    <tr><td>7��</td><td><input type="text" name="hachi4" class="data" value="<?php print($hachi[3]); ?>" size="10"></td></tr>
    <tr><td>8��</td><td><input type="text" name="hachi5" class="data" value="<?php print($hachi[4]); ?>" size="10"></td></tr>
    <tr><td>9��</td><td><input type="text" name="hachi6" class="data" value="<?php print($hachi[5]); ?>" size="10"></td></tr>
    <tr><td>10��</td><td><input type="text" name="hachi7" class="data" value="<?php print($hachi[6]); ?>" size="10"></td></tr>
    <tr><td>11��</td><td><input type="text" name="hachi8" class="data" value="<?php print($hachi[7]); ?>" size="10"></td></tr>
    <tr><td>12��</td><td><input type="text" name="hachi9" class="data" value="<?php print($hachi[8]); ?>" size="10"></td></tr>
    <tr><td>1��</td><td><input type="text" name="hachi10" class="data" value="<?php print($hachi[9]); ?>" size="10"></td></tr>
    <tr><td>2��</td><td><input type="text" name="hachi11" class="data" value="<?php print($hachi[10]); ?>" size="10"></td></tr>
    <tr><td>3��</td><td><input type="text" name="hachi12" class="data" value="<?php print($hachi[11]); ?>" size="10"></td></tr>
</table>
<input type="hidden" name="year" value="<?php print($yy); ?>">
<input type="submit" value="����">
<input type="reset"  value="�᤹">
<input type="button"  value="���ꥢ" id="clr">

</form>
</div>
</div>
<div class="er">
<?php session_start(); ?>
<font color="Red"><?php print($_SESSION['err']); ?></font>
</div>
</body>
</html>