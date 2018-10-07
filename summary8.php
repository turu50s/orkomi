<?php
ini_set( "display_errors", "off");

// StrictStandars�Υ�˥󥰤�ä���
//ini_set('error_reporting', E_ALL & ~E_STRICT);

require_once('db_auth.php');

session_start();
$_SESSION['err'] = '';

$level = $myauth->getAuthData('level');
$tenpo = $myauth->getAuthData('section');

//$level = 1;
//$tenpo = 'honbu';

// 

$sum       = 0;
$sum_all   = 0;
$date_h    = '';

$today     = getdate();
$yy        = $today['year'];
$mm        = $today['mon'];
$dd        = $today['mday'];
$data = array(array(),array(),array());

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-jp">
<meta http-equiv="Content-Style-Type" content="text/css">
<title>�޹����󽸷�</title>
<link rel="stylesheet" type="text/css" href="./summary8.css">
<script src="https://www.google.com/jsapi"></script>
</head>
<body>
<?php
$tenpo_h = 'Ȭ����';

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
	$sql = "SELECT count(*) FROM nippo8 WHERE year = '$yy_prev' AND month = '$mm_prev' GROUP BY tenpo,year,month";
	$rs=$db->query($sql);
	$count = $rs->fetchColumn();
	
	if ($count > 0) {
		$mm = date('m', strtotime(date('Y-m-1').' -1 month'));
		$yy = date('Y', strtotime(date('Y-m-1').' -1 month'));
	}
} catch (PDOException $e) {
    die('error: '.$e->getMessage());
}
	

?>
<h2>�޹����󽸷ס�<?php print($tenpo_h); ?>�ˡ���<?php print($yy); ?>ǯ<?php print($mm); ?>�������<font color="white">(ñ�̡����)</font></h2>
<!-- <p class="date"><?php print(date('Yǯm��d�� (D)').'<br>'); ?></p> -->
<div class="gr1">
<div class="gr0">
<table  border="1 cellspacing="0" cellpadding="5">
    <tr><th>����</th><th>����</th></tr>
<?php
//$date_k = $yy.'/'.$mm;

// �޹��������
try {
	//$db = new PDO('sqlite:../SQLiteManager-1.2.0/orikomi.sqlite3');
	
	$db = new PDO($dsn, $user, $password);
	
	$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	$sql = "SELECT * FROM sddb0040128103.nippo8 WHERE year = '$yy' AND month = '$mm'  AND tenpo='hachi' ORDER BY year,month,day ASC";
    
	$rs=$db->query($sql);
	$row = $rs->fetchAll();

	foreach ($row as $rec) {
	   $date             = $rec['day'];
	   $data[$date-1][1] = $rec['day'];
	   
	   $data[$date-1][0] = $rec['money'] / 1000;
	}
} catch (PDOException $e) {
    die('error: '.$e->getMessage());
}

$sum_line = 0;
$line = '';

$cnt1 = 0;
foreach ($data as $line) {
    if ($line[1] == '') {
       $line[1] = $cnt1 + 1;
    }
   
	print('<tr><td class="mon">'.$line[1].'��</td><td class="money">'
	           .number_format($line[0]).'</td></tr>');
	
	$sum_line += $line[0];
	
	if (($line[1] % 5  == 0) && ($line[1] != 30)) {
	   print('<tr><td class="sum1">��</td><td class="sum">'
           .number_format($sum_line).'</td></tr>');
	}
	$cnt1++;
}

if (($line[1] % 5 != 0)  || ($line[1] == date("d",mktime(0,0,0,$today['mon']+1,0,$today['year'])))) {
    print('<tr><th class="sum1">��</th><td class="sum">'.number_format($sum_line).'</td></tr>');
}
?>
</table>
</div>

<?php
// ǯ�ּ������ɽ����
try {
    $sql = "SELECT * FROM jisseki8_old where tenpo = 'hachi'";
    $rs=$db->query($sql);
    $row = $rs->fetchAll();
    
    foreach ($row as $rec) {
    	$date_w = explode('/',$rec['month']);
    	if ($date_w[1] < 4) {
    	    $date_r = $date_w[1] + 12;
    	} else {
    		$date_r = $date_w[1];
    	}
        $hachi[$date_r-4][0] = $rec['results'];
        $hachi[$date_r-4][3] = $date_w[1];
    }
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
        $hachi[$date_r-4][1] = $rec['budget'];
        $hachi[$date_r-4][3] = $date_w[1];
    }
    
    $sql = "SELECT * FROM jisseki8 where tenpo = 'hachi'";
    $rs=$db->query($sql);
    $row = $rs->fetchAll();
    
    foreach ($row as $rec) {
        $date_w = explode('/',$rec['month']);
    	if ($date_w[1] < 4) {
            $date_r = $date_w[1] + 12;
        } else {
            $date_r = $date_w[1];
        }
        $hachi[$date_r-4][2] = $rec['results'] / 1000;
    }
} catch (PDOException $e) {
    die('error: '.$e->getMessage());
}
?>

<div class="gr2">
<table border="1" cellspacing="0" cellpadding="5">
    <tr><th></th><th colspan="3">ǯ�ּ���</th></tr>
    <tr><th>������</th>
        <th class="ti">��ǯ����</th><th>ͽ��</th><th>����</th></tr>
<?php

for ($i = 0; $i < 12; $i++) {
	print('<tr><td class="mon">'.$hachi[$i][3].'��'.'</td><td class="money">'
	      .number_format($hachi[$i][0]).'</td><td class="money">'.number_format($hachi[$i][1]).'</td><td class="money">'.number_format($hachi[$i][2]).'</td></tr>');
    for ($j = 0; $j < 3; $j++) {
	    $sub_hachi[$j]  += $hachi[$i][$j];
    }
}

print('<tr><th class="sum1">'.'���'.'</th><td class="sum">'
      .number_format($sub_hachi[0]).'</td><td class="sum">'.number_format($sub_hachi[1]).'</td><td class="sum">'.number_format($sub_hachi[2]).'</td></tr>');
?>

</table>
</div>

<?php
// ����������ɽ����
$date_o   = $yy.'/'.$mm;
$date_old = ($yy - 1).'/'.$mm;
try {
    $sql = "SELECT * FROM yosan8 WHERE month = '$date_o'";
    $rs=$db->query($sql);
    while ($row = $rs->fetch(PDO::FETCH_ASSOC)) {
    	$hachi_yo = $row['budget'];
    }
    $sql = "SELECT * FROM jisseki8_old WHERE tenpo = 'hachi' AND  month = '$date_old'";
    $rs=$db->query($sql);
    while ($row = $rs->fetch(PDO::FETCH_ASSOC)) {
        $hachi_o  = $row['results'];
    }
} catch (PDOException $e) {
    die('error: '.$e->getMessage());
}
$hachi_ra = round(((float)$sum_line / (float)$hachi_yo) * 100,1);
$hachi_rb = round(((float)$sum_line / (float)$hachi_o) * 100,1);
?>
<div class="gr3">
<table border="1" cellspacing="0" cellpadding="5">
    <tr><th></th><th>�������</th></tr>
    <tr><td><?php print('��ǯ����'); ?></td><td class="money"><?php print(number_format($hachi_o)); ?></td></tr>
    <tr><td><?php print($mm.'��ͽ��'); ?></td><td class="money"><?php print(number_format($hachi_yo)); ?></td></tr>
    <tr><td><?php print($mm.'�����'); ?></td><td class="money"><?php print(number_format($sum_line)); ?></td></tr>
    <tr><th class="sum1"><?php print('ͽ����'); ?></th><td class="sum"><?php print($hachi_ra.'%'); ?></td></tr>
    <tr><th class="sum1"><?php print('��ǯ��'); ?></th><td class="sum"><?php print($hachi_rb.'%'); ?></td></tr>
</table>


<?php 
// ������ӥ���ս���
$_SESSION['hachi_o']  = $hachi_o;
$_SESSION['hachi_yo'] = $hachi_yo;
$_SESSION['hachi_j']  = $sum_line;
?>
<script>
	google.load('visualization', '1.0', {'packages':['corechart']});
	google.setOnLoadCallback(drawChart);

	function drawChart() {
		var data = new google.visualization.arrayToDataTable([
				['','',{role:'style'}],
				['��ǯ',<? echo $hachi_o ?>,'red'],
				['ͽ��',<? echo $hachi_yo ?>,'green'],
				['����',<? echo $sum_line ?>,'blue']
			]);
		var options = {
			title: 'Ȭ����',
			width:200,
			height:250,
			legend: 'none'
		};

		var chart = new google.visualization.ColumnChart(document.getElementById('image1'));
		chart.draw(data, options);
	}
</script>


	<p id="image1"></p>
</div>

<div class="off">
<?php if ($level > 2) { ?>
    <a href="logout.php">��������</a>����<a href="input.php">��������</a>����<a href="yosan8.php">ͽ������</a>����<a href="jisseki8.php">���ӽ���</a>����<a href="pdf8.php">�������</a>
<?php } else { ?>
    	<a href="logout.php">��������</a>����<a href="summary.php">����Ʋ��Ź</a>
<?php } ?>
</div>
</div>
</body>
</html>
