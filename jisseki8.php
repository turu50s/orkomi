<?php
ini_set( "display_errors", "OFF");

require_once('db_auth.php');

$level = $myauth->getAuthData('level');

$today = getdate();

if ($_SESSION['jup'] == '1') {
    $_SESSION['jup'] = '0';
} else {
    $_SESSION['err'] = '';
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-jp">
<meta http-equiv="Content-Style-Type" content="text/css">
<title>�������ӻ���</title>
<link rel="stylesheet" type="text/css" href="./honbu1.css">
</head>
<body onLoad="document.jisseki8.month.focus()">

<h2>�������ӡ������ˡ���<?php print($today['mon']); ?>�����������������<font color="white">(ñ�̡���)</font></h2>
<div class="contents">
<div class="menu">
   <a href="summary8.php">���ӻ���</a>
<a href="input.php">��������</a>
<br>
<a href="monthly8.php">���Ӻ���</a>
<br>
<a href="logout.php">��������</a>
</div>

<div class="up">

<table  border="1" cellspacing="0" cellpadding="5" class="pos">
    <tr><th colspan="3">���ӥǡ���</th></tr>
    <tr><th>��</th><th>���</th><th>���</th></tr>
<?php
// �޹��ǡ������١�����³
$dsn = 'mysql:dbname=sddb0040128103;host=sddb0040128103.cgidb';
$user = 'sddbMTgzOTIz';
$password = 'i8h4vf5U';

// �������ӥǡ������
try {
    //$db = new PDO('sqlite2:../SQLiteManager-1.2.0/orikomi.sqlite2');
    $db = new PDO($dsn, $user, $password);
    
	$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    $sql = "SELECT * FROM jisseki8  ORDER BY id ASC";

    $rs=$db->query($sql);
    $row = $rs->fetchAll();

    foreach ($row as $rec) {
       $date_w = explode("/",$rec['month']);
       if ($date_w[1] < 4) {
            $date = $date_w[1] + 12;
       } else {
            $date = $date_w[1];
       }

       $data[$date-4][0] = $rec['results'];
       $data[$date-4][1] = $rec['number'];
       $data[$date-4][2] = $date_w[1];
    }
} catch (PDOException $e) {
    die('error: '.$e->getMessage());
}
?>

<?php
// ����ɽ����
$i = 0;
$sum_hachi  = array();

foreach ($data as $line) {
    print('<tr><td class="mon">'.$line[2].'��</td>
    		   <td class="money">'.number_format($line[1]).'</td>
    		   <td class="money">'.number_format($line[0]).'</td></tr>');
    
     $sum_hachi[1]  += $line[1];
     $sum_hachi[0]  += $line[0];
    
}
//$cnt = $i;
//for ($j = 0; $j < 3; $j++) {
//    for ($i = 0; $i < $cnt; $i++) {
//      $sum_col[$j] += $data[$i][$j];
//    }
//}
print('<tr><th class="sum1">���</th><td class="sum">'
    .number_format($sum_hachi[1]).'</td><td class="sum">'.number_format($sum_hachi[0]).'</td></tr>');
?>
</table>

<div class="in">
<p>������ӽ�����(ñ�̡���)</p>

<?php 
//if ($today['mon'] == 1) {
//        $mm = 13;
//        $yy = $today['year'] - 1;
//    } else {
//        $yy = $today['year'];
//        $mm = $today['mon'];
//    }
    $date_in = date("Y-m-d", strtotime(date('Y-m-1').'-1 month'));
	list($yy_in,$mm_in,$dd_in) = explode('-', $date_in);
?>
<form method="post" action="jisseki8_up.php" name="jisseki8">
<table border="1" cellspacing="0" cellpadding="5">
    <tr><th>ǯ</th><th>��</th><th>���</th><th>���</th></tr>
    <tr>
    	<td class="mon"><input type="text" name="year" value="<? print($yy_in); ?>" size="4"></td>
        <td class="mon"><input type="text" name="month" value="<? print($mm_in); ?>" size="2"></td>
        <td class="money"><input type="text" name="number" size="5"></td>
        <td class="money"><input type="text" name="results" size="12"></td>
    </tr>
    <tr><td colspan="4"><input type="submit" name="ins" value="����"></td></tr>
</table>
<input type="hidden" name="tenpo" value="<?php print($tenpo); ?>">
</form>
</div>
<?php session_start(); ?>
<font color="Red"><?php print($_SESSION['err']); ?></font>
</div>
</div>
</body>
</html>