<?php
ini_set( "display_errors", "off");

// StrictStandars�Υ�˥󥰤�ä���
ini_set('error_reporting', E_ALL & ~E_STRICT);

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
<link rel="stylesheet" type="text/css" href="./summary.css">
<script src="https://www.google.com/jsapi"></script>
</head>
<body>
<?php
switch ($tenpo) {
    case 'seibu':
        $tenpo_h = '����';
        break;
    case 'chuo':
        $tenpo_h = '���';
        break;
    case 'masuo':
        $tenpo_h = '����';
        break;
    case 'honbu':
    	$tenpo_h = '����';
    	break;
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
		$mm = (int)$mm_prev;
		$yy = $yy_prev;
	}

} catch (PDOException $e) {
    die('error: '.$e->getMessage());
}
	

?>
<h2>�޹����󽸷ס�<?php print($tenpo_h); ?>�ˡ���<?php printf("%4d",$yy); ?>ǯ<?php printf("%2d",$mm); ?>�������<font color="white">(ñ�̡����)</font></h2>
<!-- <p class="date"><?php print(date('Yǯm��d�� (D)').'<br>'); ?></p> -->

<div class="gr0">
<table  border="1 cellspacing="0" cellpadding="4">
    <tr><th>����</th><th>����Ź</th><th>����Ź</th><th>���</th></tr>
<?php
//$date_k = $yy.'/'.$mm;

// �޹��������
try {
	//$db = new PDO('sqlite:../SQLiteManager-1.2.0/orikomi.sqlite3');
	
	$db = new PDO($dsn, $user, $password);
	
	$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	$sql = "SELECT * FROM sddb0040128103.nippo WHERE year = '$yy' AND month = '$mm' ORDER BY year,month,day ASC";
    //$sql = "SELECT * FROM nippo WHERE year = '$yy' AND month = '$mm' ORDER BY year,month,day ASC";
    
	$rs=$db->query($sql);
	$row = $rs->fetchAll();

	foreach ($row as $rec) {
	   $date             = $rec['day'];
	   $data[$date-1][3] = $rec['day'];
	   
	   switch ($rec['tenpo']){
		  case 'seibu':
			$data[$date-1][0] = $rec['money'] / 1000;
			break;
		  case 'chuo':
			$data[$date-1][1] = $rec['money'] / 1000;
			break;
		  case 'masuo':
			$data[$date-1][2] = $rec['money'] / 1000;
			break;
	   }
	}
} catch (PDOException $e) {
    die('error: '.$e->getMessage());
}

$sum_line = array();
$line = '';

$cnt1 = 0;
foreach ($data as $line) {
   if ($line[3] == '') {
       $line[3] = $cnt1 + 1;
   }
   
	//print('<tr><td class="mon">'.$line[3].'��</td><td class="money">'
	//           .number_format($line[0]).'</td><td class="money">'
	//           .number_format($line[1]).'</td><td class="money">'
	//           .number_format($line[2]).'</td><td class="money">'
	print('<tr><td class="mon">'.$line[3].'��</td><td class="money">'
	          .number_format($line[0]).'</td><td class="money">'
	          
	          .number_format($line[2]).'</td><td class="money">'
	          .number_format($line[0] + $line[1] + $line[2]).'</td></tr>');
	
	$sum_line[0] += $line[0];
	$sum_line[1] += $line[1];
	$sum_line[2] += $line[2];
	
	if (($line[3] % 5  == 0) && ($line[3] != 30)) {
	   //print('<tr><td class="sum1">��</td><td class="sum">'
       //    .number_format($sum_line[0]).'</td><td class="sum">'
       //    .number_format($sum_line[1]).'</td><td class="sum">'
       //    .number_format($sum_line[2]).'</td><td class="sum">'
       print('<tr><td class="sum1">��</td><td class="sum">'
           .number_format($sum_line[0]).'</td><td class="sum">'
           
           .number_format($sum_line[2]).'</td><td class="sum">'
           .number_format(array_sum($sum_line)).'</td></tr>');
	}
	$cnt1++;
}

if (($line[3] % 5 != 0)  || ($line[3] == date("d",mktime(0,0,0,$mm+1,0,$yy)))) {
    //print('<tr><th class="sum1">��</th><td class="sum">'.number_format($sum_line[0]).'</td>
    //    <td class="sum">'.number_format($sum_line[1]).'</td><td class="sum">'.number_format($sum_line[2]).'</td>
    //    <td class="sum">'.number_format(array_sum($sum_line)).'</td></tr>');
    print('<tr><th class="sum1">��</th><td class="sum">'.number_format($sum_line[0]).'</td>
        <td class="sum">'.number_format($sum_line[2]).'</td>
        <td class="sum">'.number_format(array_sum($sum_line)).'</td></tr>');
}
?>
</table>
</div>

<?php
// ����������ɽ����
$date_o   = $yy.'/'.$mm;
$date_old = ($yy - 1).'/'.$mm;
try {
    $sql = "SELECT * FROM yosan WHERE month = '$date_o'";
    $rs=$db->query($sql);
    while ($row = $rs->fetch(PDO::FETCH_ASSOC)) {
        switch ($row['tenpo']) {
    	   case 'seibu':
    		  $seibu_yo = $row['budget'];
    		  break;
    	   case 'chuo':
    		  $chuo_yo  = $row['budget'];
    		  break;
    	   case 'masuo':
    		  $masuo_yo = $row['budget'];
    	      break;
        }
    }
    $sql = "SELECT * FROM jisseki_old WHERE month = '$date_old'";
    $rs=$db->query($sql);
    while ($row = $rs->fetch(PDO::FETCH_ASSOC)) {
        switch ($row['tenpo']) {
           case 'seibu':
              $seibu_o  = $row['results'];
              break;
           case 'chuo':
              $chuo_o   = $row['results'];
              break;
           case 'masuo':
              $masuo_o  = $row['results'];
              break;
        }
    }
} catch (PDOException $e) {
    die('error: '.$e->getMessage());
}
$gokei_yo = $seibu_yo + $chuo_yo + $masuo_yo;
$gokei_o  = $seibu_o  + $chuo_o  + $masuo_o;
$seibu_ra = round(((float)$sum_line[0] / (float)$seibu_yo) * 100,1);
$chuo_ra  = round(((float)$sum_line[1] / (float)$chuo_yo) * 100,1);
$masuo_ra = round(((float)$sum_line[2] / (float)$masuo_yo) * 100,1);
$gokei_ra = round(((float)array_sum($sum_line) / (float)$gokei_yo) * 100,1);

$seibu_rb = round(((float)$sum_line[0] / (float)$seibu_o) * 100,1);
$chuo_rb  = round(((float)$sum_line[1] / (float)$chuo_o) * 100,1);
$masuo_rb = round(((float)$sum_line[2] / (float)$masuo_o) * 100,1);
$gokei_rb = round(((float)array_sum($sum_line) / (float)$gokei_o) * 100,1);
?>

<div class="gr1">
<div class="gr3">
<div class="gr31">
<table border="1" cellspacing="0" cellpadding="5">
    <!--<tr><th></th><th>����Ź</th><th>���Ź</th><th>����Ź</th><th>���</th></tr>-->
    <tr><th></th><th>����Ź</th><th>����Ź</th><th>���</th></tr>
    <tr><td class="ti"><?php print('��ǯ����'); ?></td><td class="money"><?php print(number_format($seibu_o)); ?></td>
                                            <!--<td class="money"><?php print(number_format($chuo_o)); ?></td>-->
                                            <td class="money"><?php print(number_format($masuo_o)); ?></td>
                                            <td class="money"><?php print(number_format($gokei_o)); ?></td></tr>
    <tr><td class="ti"><?php print($mm.'��ͽ��'); ?></td><td class="money"><?php print(number_format($seibu_yo)); ?></td>
                                            <!--<td class="money"><?php print(number_format($chuo_yo)); ?></td>-->
                                            <td class="money"><?php print(number_format($masuo_yo)); ?></td>
                                            <td class="money"><?php print(number_format($gokei_yo)); ?></td></tr>
    <tr><td class="ti"><?php print($mm.'�����'); ?></td><td class="money"><?php print(number_format($sum_line[0])); ?></td>
                                            <!--<td class="money"><?php print(number_format($sum_line[1])); ?></td>-->
                                            <td class="money"><?php print(number_format($sum_line[2])); ?></td>
                                            <td class="money"><?php print(number_format(array_sum($sum_line))); ?></td></tr>
    <tr><td class="sum1"><?php print('ͽ����'); ?></td><td class="sum"><?php print($seibu_ra.'%'); ?></td>
                                            <!--<td class="sum"><?php print($chuo_ra.'%'); ?></td>-->
                                            <td class="sum"><?php print($masuo_ra.'%'); ?></td>
                                            <td class="sum"><?php print($gokei_ra.'%'); ?></td></tr>
    <tr><td class="sum1"><?php print('��ǯ��'); ?></td><td class="sum"><?php print($seibu_rb.'%'); ?></td>
                                            <!--<td class="sum"><?php print($chuo_rb.'%'); ?></td>-->
                                            <td class="sum"><?php print($masuo_rb.'%'); ?></td>
                                            <td class="sum"><?php print($gokei_rb.'%'); ?></td></tr>
</table>
</div>

<?php 
// ������ӥ���ս���
$_SESSION['seibu_o']  = $seibu_o;
$_SESSION['chuo_o']   = $chuo_o;
$_SESSION['masuo_o']  = $masuo_o;

$_SESSION['seibu_yo'] = $seibu_yo;
$_SESSION['chuo_yo']  = $chuo_yo;
$_SESSION['masuo_yo'] = $masuo_yo;

$_SESSION['seibu_j']  = $sum_line[0];
$_SESSION['chuo_j']   = $sum_line[1];
$_SESSION['masuo_j']  = $sum_line[2];

$_SESSION['kako_z']    = $gokei_o;
$_SESSION['yosan_z']   = $gokei_yo;
$_SESSION['jisseki_z'] = array_sum($sum_line);
?>
<script>
	google.load('visualization', '1.0', {'packages':['corechart']});
	google.setOnLoadCallback(drawChart);

	function drawChart() {
		var data = new google.visualization.arrayToDataTable([
				['','',{role:'style'}],
				['��ǯ',<? echo $gokei_o ?>,'red'],
				['ͽ��',<? echo $gokei_yo ?>,'green'],
				['����',<? echo array_sum($sum_line) ?>,'blue']
			]);
		var options = {
			title: '��Ź',
			width:200,
			height:250,
			legend: 'none'
		};

		var chart = new google.visualization.ColumnChart(document.getElementById('image1'));
		chart.draw(data, options);
	}
</script>
<script>
	google.load('visualization', '1.0', {'packages':['corechart']});
	google.setOnLoadCallback(drawChart);

	function drawChart() {
		var data = new google.visualization.DataTable();
			data.addColumn('string','');
			data.addColumn('number','��ǯ');
			data.addColumn('number','ͽ��');
			data.addColumn('number','����');
			data.addRows([
				['����', <? echo $seibu_o ?>, <? echo $seibu_yo ?>, <? echo $sum_line[0] ?>],
				
				['����', <? echo $masuo_o ?>, <? echo $masuo_yo ?>, <? echo $sum_line[2] ?>]
			]);
		//var data = new google.visualization.arrayToDataTable([
		//		['','��ǯ','ͽ��','����'],
		//		['����', <? echo $seibu_o ?>, <? echo $seibu_yo ?>, <? echo $sum_line[0] ?>],
		//		['���', <? echo $chuo_o ?>,<? echo $chuo_yo ?>, <? echo $sum_line[1] ?>],
		//		['����', <? echo $masuo_o ?>, <? echo $masuo_yo ?>, <? echo $sum_line[2] ?>]
		//	]);
		var options = {
			title: '��Ź',
			width:200,
			height:250
		};

		var chart = new google.visualization.ColumnChart(document.getElementById('image2'));
		chart.draw(data, options);
	}
</script>
<div class="gr32">
<ul id="image">
	<li id="image1"></li>
	<li id="image2"></li>
</ul>
</div>
</div>
<!--
<a href="image1.php"><img src="image1.php" width="200px" height="250px;"></a>
<a href="image2.php"><img src="image2.php" width="200px" height="250px;"></a>
-->
<?php 
//print('<img src="http://chart.apis.google.com/chart?chs=400x300&chd=t:'
//    .($seibu_o/100).','.($chuo_o/100).','.($masuo_o/100).'|'
//    .($seibu_yo/100).','.($chuo_yo/100).','.($masuo_yo/100).'|'
//    .($sum_col[0]/100).','.($sum_col[1]/100).','.($sum_col[2]/100)
//    .'&chdl=seibu|chuo|masuo&chxt=y&cht=bvg&chco=00FF00,00FFFF,FFFF00" width="200x" height="250px;">');
?>


<?php
// ǯ�ּ������ɽ����
try {
    $sql = "SELECT * FROM jisseki_old";
    $rs=$db->query($sql);
    $row = $rs->fetchAll();
    
    foreach ($row as $rec) {
    	$date_w = explode('/',$rec['month']);
    	if ($date_w[1] < 4) {
    	    $date_r = $date_w[1] + 12;
    	} else {
    		$date_r = $date_w[1];
    	}
    	switch ($rec['tenpo']) {
           case 'seibu':
              $seibu[$date_r-4][0] = $rec['results'];
              $seibu[$date_r-4][3] = $date_w[1];
              break;
           case 'chuo':
              $chuo[$date_r-4][0]  = $rec['results'];
              break;
           case 'masuo':
              $masuo[$date_r-4][0] = $rec['results'];
              break;
        }
    }
    $sql = "SELECT * FROM yosan";
    $rs=$db->query($sql);
    $row = $rs->fetchAll();
    
    foreach ($row as $rec) {
        $date_w = explode('/',$rec['month']);
    	if ($date_w[1] < 4) {
            $date_r = $date_w[1] + 12;
        } else {
            $date_r = $date_w[1];
        }
    	switch ($rec['tenpo']) {
           case 'seibu':
              $seibu[$date_r-4][1] = $rec['budget'];
              break;
           case 'chuo':
              $chuo[$date_r-4][1]  = $rec['budget'];
              break;
           case 'masuo':
              $masuo[$date_r-4][1] = $rec['budget'];
              break;
        }
    }
    
    $sql = "SELECT * FROM jisseki";
    $rs=$db->query($sql);
    $row = $rs->fetchAll();
    
    foreach ($row as $rec) {
        $date_w = explode('/',$rec['month']);
    	if ($date_w[1] < 4) {
            $date_r = $date_w[1] + 12;
        } else {
            $date_r = $date_w[1];
        }
    	switch ($rec['tenpo']) {
           case 'seibu':
              $seibu[$date_r-4][2] = $rec['results'] / 1000;
              break;
           case 'chuo':
              $chuo[$date_r-4][2]  = $rec['results'] / 1000;
              break;
           case 'masuo':
              $masuo[$date_r-4][2] = $rec['results'] / 1000;
              break;
        }
    }
} catch (PDOException $e) {
    die('error: '.$e->getMessage());
}
for ($i = 0; $i < 12; $i++) {
	for ($j = 0; $j < 3; $j++) {
		$zenten[$i][$j] = $seibu[$i][$j] + $chuo[$i][$j] + $masuo[$i][$j];
	}
}

?>
<div class="gr2">
<table border="1" cellspacing="0" cellpadding="5">
    <!--<tr><th></th><th colspan="3">����Ź</th><th colspan="3">���Ź</th><th colspan="3">����Ź</th>-->
    <tr><th></th><th colspan="3">����Ź</th><th colspan="3">����Ź</th>
        <th colspan="3">���</th></tr>
    <tr><th></th>
        <th class="ti">��ǯ����</th><th>ͽ��</th><th>����</th><!--<th class="ti">��ǯ����</th><th>ͽ��</th><th>����</th>-->
        <th class="ti">��ǯ����</th><th>ͽ��</th><th>����</th><th class="ti">��ǯ����</th><th>ͽ��</th><th>����</th></tr>
<?php

for ($i = 0; $i < 12; $i++) {
	//print('<tr><td class="mon">'.$seibu[$i][3].'��'.'</td><td class="money">'
	//                .number_format($seibu[$i][0]).'</td><td class="money">'.number_format($seibu[$i][1]).'</td><td class="money">'.number_format($seibu[$i][2]).'</td><td class="money">'
	//                .number_format($chuo[$i][0]).'</td><td class="money">'.number_format($chuo[$i][1]).'</td><td class="money">'.number_format($chuo[$i][2]).'</td><td class="money">'
	//                .number_format($masuo[$i][0]).'</td><td class="money">'.number_format($masuo[$i][1]).'</td><td class="money">'.number_format($masuo[$i][2]).'</td><td class="money">'
	print('<tr><td class="mon">'.$seibu[$i][3].'��'.'</td><td class="money">'
	                .number_format($seibu[$i][0]).'</td><td class="money">'.number_format($seibu[$i][1]).'</td><td class="money">'.number_format($seibu[$i][2]).'</td>
	                <td class="money">'
	                .number_format($masuo[$i][0]).'</td><td class="money">'.number_format($masuo[$i][1]).'</td><td class="money">'.number_format($masuo[$i][2]).'</td><td class="money">'
	                .number_format($zenten[$i][0]).'</td><td class="money">'.number_format($zenten[$i][1]).'</td><td class="money">'.number_format($zenten[$i][2]).'</td></tr>');
    for ($j = 0; $j < 3; $j++) {
	    $sub_seibu[$j]  += $seibu[$i][$j];
        $sub_chuo[$j]   += $chuo[$i][$j];
        $sub_masuo[$j]  += $masuo[$i][$j];
        $sub_zenten[$j] += $zenten[$i][$j];
    }
}

//print('<tr><th class="sum1">'.'���'.'</th><td class="sum">'
//                    .number_format($sub_seibu[0]).'</td><td class="sum">'.number_format($sub_seibu[1]).'</td><td class="sum">'.number_format($sub_seibu[2]).'</td><td class="sum">'
//                    .number_format($sub_chuo[0]).'</td><td class="sum">'.number_format($sub_chuo[1]).'</td><td class="sum">'.number_format($sub_chuo[2]).'</td><td class="sum">'
//                    .number_format($sub_masuo[0]).'</td><td class="sum">'.number_format($sub_masuo[1]).'</td><td class="sum">'.number_format($sub_masuo[2]).'</td><td class="sum">'
print('<tr><th class="sum1">'.'���'.'</th><td class="sum">'
                    .number_format($sub_seibu[0]).'</td><td class="sum">'.number_format($sub_seibu[1]).'</td><td class="sum">'.number_format($sub_seibu[2]).'</td><td class="sum">'
                    
                    .number_format($sub_masuo[0]).'</td><td class="sum">'.number_format($sub_masuo[1]).'</td><td class="sum">'.number_format($sub_masuo[2]).'</td><td class="sum">'
                    .number_format($sub_zenten[0]).'</td><td class="sum">'.number_format($sub_zenten[1]).'</td><td class="sum">'.number_format($sub_zenten[2]).'</td></tr>');
?>

</table>
</div>

<div class="off">
<?php if ($level > 2) { ?>
    <!-- <a href="logout.php">��������</a>����<a href="input.php">���Ͻ���</a>����<a href="summary_z.php">�����ٽ���</a> -->
    <a href="logout.php">��������</a>����<a href="input.php">���Ͻ���</a>
<?php //    header('Location: http://www.asa-kashiwa.net/orikomi/input.php'); ?>
<?php } else { ?>
    <a href="logout.php">��������</a>����<a href="zenten.php">��Ź����</a>����<a href="jisseki.php">��������</a>����<a href="summary_z.php">�����ٽ���</a>����<a href="summary8.php">ASAȬ����Ź</a>
<?php } ?>
</div>
</div>
</body>
</html>
