<?php
ini_set( "display_errors", "Off");

require_once('db_auth.php');

$level = $myauth->getAuthData('level');
if ($level > 2) {
    header('Location: http://www.asa-kashiwa.net/orikomi/input.php');
}

$today = getdate();
$yy    = $today['year'];
$mm    = $today['mon'];
$dd    = $today['mday'];
if (empty($_POST['month'])) {
    $_POST['month'] = $mm;
}

// �޹��ǡ����١�����³
$dsn = 'mysql:dbname=sddb0040128103;host=sddb0040128103.cgidb';
$user = 'sddbMTgzOTIz';
$password = 'i8h4vf5U';

// nippo data check!
try {
	$db = new PDO($dsn, $user, $password);
	
	$mm_prev = date('m', strtotime(date('Y-m-1').' -1 month'));
	$yy_prev = date('Y', strtotime(date('Y-m-1').' -1 month'));
	
	$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	$sql = "SELECT count(*) FROM nippo WHERE year = '$yy_prev' AND month = '$mm_prev' GROUP BY tenpo,year,month";
	$rs=$db->query($sql);
	$count = $rs->fetchColumn();
	
	if ($count > 0) {
		$mm = $mm_prev;
		$_POST['month'] = $mm;
		$yy = $yy_prev;
		$_POST['year'] = $yy;
	}

} catch (PDOException $e) {
    die('error: '.$e->getMessage());
}
	

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-jp">
<meta http-equiv="Content-Style-Type" content="text/css">
<title>�޹����󹹿�</title>
<link rel="stylesheet" type="text/css" href="./honbu.css">
</head>
<body onLoad="document.mon.month.focus()">

<h2>�޹����󹹿��������ˡ���������������������<font color="white">(ñ�̡���)</font></h2>

<div class="menu">
   <a href="summary.php">��Ź���</a>
   <a href="jisseki.php">��������</a>
   <?php if ($level == 1) { ?>
	   <a href="update.php">���󹹿�</a>
	   <a href="yosan.php">ͽ������</a>
	   <a href="pdf.php">�������</a>
   <?php } else { ?>
   	   <a href="input.php">��������</a>
   <?php } ?>
   <br>
   <a href="logout.php">��������</a>
</div>

<div class="up">
<!--
<form method="post" action="<?php print($_SERVER['PHP_SELF']); ?>" name="mon">
    <input type="text" name="month" value="<?php print($_POST['month']) ?>" size="2">��
</form>
-->
<p style="font-size: 25px;"><?php print($_POST['month']) ?>��</p>
<table  border="1" cellspacing="0" cellpadding="5" class="pos">
    <tr><th></th><th colspan="2">����Ź</th><th colspan="2">���Ź</th><th colspan="2">����Ź</th>
                 <th colspan="2">���</th></tr>
    <tr><th>����</th><th>���</th><th>���</th><th>���</th><th>���</th><th>���</th><th>���</th>
                    <th>���</th><th>���</th></tr>
<?php

// �޹�����ǡ������

//$yymm = $yy.'/'.$_POST['month'];
$mon = $_POST['month'];

try {
    //$db = new PDO('sqlite2:../SQLiteManager-1.2.0/orikomi.sqlite2');
    $db = new PDO($dsn, $user, $password);
	
	$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    $sql = "SELECT * FROM nippo  WHERE year = '$yy' AND month = '$mon' ORDER BY year,month,day ASC";

    $rs=$db->query($sql);
    $row = $rs->fetchAll();

    foreach ($row as $rec) {
        //$date = explode("/",$rec['date']);
        $date = $rec['day'];
        
        switch ($rec['tenpo']){
            case 'seibu':
                $data[$date-1][0][0] = $rec['money'];
                $data[$date-1][0][1] = $rec['number'];
                $data[$date-1][0][2] = $rec['day'];
                break;
            case 'chuo':
                $data[$date-1][1][0] = $rec['money'];
                $data[$date-1][1][1] = $rec['number'];
                $data[$date-1][0][2] = $rec['day'];
                
                break;
            case 'masuo':
                $data[$date-1][2][0] = $rec['money'];
                $data[$date-1][2][1] = $rec['number'];
                $data[$date-1][0][2] = $rec['day'];
                break;
         }
    }
} catch (PDOException $e) {
    die('error: '.$e->getMessage());
}
?>

<?php
// �޹�����ɽ����
$i = 0;
$sum_seibu  = array();
$sum_chuo   = array();
$sum_masuo  = array();
$sum_zenten = array();

session_start();

foreach ($data as $line) {
    
    
        $sum_line[0] = $line[0][0] + $line[1][0] + $line[2][0];
        $sum_line[1] = $line[0][1] + $line[1][1] + $line[2][1];
        print('<tr><td class="mon">'.$line[0][2].'��</td><td class="money">'
               .number_format($line[0][1]).'</td><td class="money">'
               .number_format($line[0][0]).'</td><td class="money">'
               .number_format($line[1][1]).'</td><td class="money">'
               .number_format($line[1][0]).'</td><td class="money">'
               .number_format($line[2][1]).'</td><td class="money">'
               .number_format($line[2][0]).'</td><td class="money">'
               .number_format($sum_line[1]).'</td><td class="money">'
               .number_format($sum_line[0]).'</td></tr>');
               
        // 5����ν���
        $sum_seibu[1]  += $line[0][1];
        $sum_seibu[0]  += $line[0][0];
        $sum_chuo[1]   += $line[1][1];
        $sum_chuo[0]   += $line[1][0];
        $sum_masuo[1]  += $line[2][1];
        $sum_masuo[0]  += $line[2][0];
        $sum_zenten[1] += $sum_line[1];
        $sum_zenten[0] += $sum_line[0];
        if (($line[0][2] % 5  == 0) && ($line[0][2] != 30)) {
            print('<tr><th class="sum1">��</th><td class="sum">'
                .number_format($sum_seibu[1]).'</td><td class="sum">'
                .number_format($sum_seibu[0]).'</td><td class="sum">'
                .number_format($sum_chuo[1]).'</td><td class="sum">'
                .number_format($sum_chuo[0]).'</td><td class="sum">'
                .number_format($sum_masuo[1]).'</td><td class="sum">'
                .number_format($sum_masuo[0]).'</td><td class="sum">'
                .number_format($sum_zenten[1]).'</td><td class="sum">'
                .number_format($sum_zenten[0]).'</td></tr>');
               
            $_SESSION['days']   = $line[0][2];
            $_SESSION['seibu']  = $sum_seibu[0];
            $_SESSION['chuo']   = $sum_chuo[0];
            $_SESSION['masuo']  = $sum_masuo[0];
            $_SESSION['zenten'] = $sum_zenten[0];
            $_SESSION['month']  = $_POST['month'];
            $_SESSION['year']   = $_POST['year'];
            

        }
        if ($line[0][2] == date("d",mktime(0,0,0,$mm+1,0,$yy))) {
            $_SESSION['days']   = $line[0][2];
            $_SESSION['seibu']  = $sum_seibu[0];
            $_SESSION['chuo']   = $sum_chuo[0];
            $_SESSION['masuo']  = $sum_masuo[0];
            $_SESSION['zenten'] = $sum_zenten[0];
            $_SESSION['month']  = $_POST['month'];
            $_SESSION['year']   = $_POST['year'];
            
        }
}
if (($line[0][2] % 5 != 0 ) || ($line[0][2] == date("d",mktime(0,0,0,$today['mon']+1,0,$today['year'])))) {
    print('<tr><th class="sum1">��</th><td class="sum">'
        .number_format($sum_seibu[1]).'</td><td class="sum">'.number_format($sum_seibu[0]).'</td><td class="sum">'
        .number_format($sum_chuo[1]).'</td><td class="sum">'.number_format($sum_chuo[0]).'</td><td class="sum">'
        .number_format($sum_masuo[1]).'</td><td class="sum">'.number_format($sum_masuo[0]).'</td><td class="sum">'
        .number_format($sum_zenten[1]).'</td><td class="sum">'.number_format($sum_zenten[0]).'</td></tr>');
}
?>
</table>
</div>
</body>
</html>