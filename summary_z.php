<?php
ini_set( "display_errors", "Off");

require_once('db_auth.php');

session_start();
$_SESSION['err'] = '';

$level = $myauth->getAuthData('level');
$tenpo = $myauth->getAuthData('section');

// 

$sum       = 0;
$sum_all   = 0;
$date_h    = '';
$today     = getdate();
if ($today['mon'] == 1) {
    $yy   = $today['year'] - 1;
    $mm   = 12;
} else  {
    $mm   = $today['mon'] - 1;
}
	

$dd       = $today['mday'];
// $data = array(array(),array(),array());

?>
<html>
<META http-equiv="Content-Type" content="text/html; charset=EUC-JP">
<head>
<title>折込日報集計</title>
<link rel="stylesheet" type="text/css" href="./summary.css">
</head>
<body>
<?php
switch ($tenpo) {
    case 'seibu':
        $tenpo_h = '西部';
        break;
    case 'chuo':
        $tenpo_h = '中央';
        break;
    case 'masuo':
        $tenpo_h = '増尾';
        break;
    case 'honbu':
    	$tenpo_h = '本部';
    	break;
}

?>
<h2>折込日報集計（<?php print($tenpo_h); ?>）　　<?php print($mm); ?>月　　　　　　　　　<font color="white">(単位：千円)</font></h2>
<!-- <p class="date"><?php print(date('Y年m月d日 (D)').'<br>'); ?></p> -->

<div class="gr0">
<table  border="1 cellspacing="0" cellpadding="5">
    <tr><th>日付</th><th>西部店</th><th>中央店</th><th>増尾店</th><th>合計</th></tr>
<?php
//$date_k = $yy.'/'.$mm;

// 折込日報作成
try {
	$db = new PDO('sqlite2:../SQLiteManager-1.2.0/orikomi.sqlite2');
	$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	$sql = "SELECT * FROM nippo WHERE year = '$yy' AND month = '$mm' ORDER BY year,month,day ASC";

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


foreach ($data as $line) {
   
	print('<tr><td class="mon">'.$line[3].'日</td><td class="money">'
	           .number_format($line[0]).'</td><td class="money">'
	           .number_format($line[1]).'</td><td class="money">'
	           .number_format($line[2]).'</td><td class="money">'
	           .number_format($line[0] + $line[1] + $line[2]).'</td></tr>');
	
	$sum_line[0] += $line[0];
	$sum_line[1] += $line[1];
	$sum_line[2] += $line[2];
	
	if (($line[3] % 5  == 0) && ($line[3] != 30)) {
	   print('<tr><td class="sum1">計</td><td class="sum">'
           .number_format($sum_line[0]).'</td><td class="sum">'
           .number_format($sum_line[1]).'</td><td class="sum">'
           .number_format($sum_line[2]).'</td><td class="sum">'
           .number_format(array_sum($sum_line)).'</td></tr>');
	}
}

if (($line[3] % 5 != 0)  || ($line[3] == date("d",mktime(0,0,0,$today['mon']+1,0,$today['year'])))) {
    print('<tr><th class="sum1">計</th><td class="sum">'.number_format($sum_line[0]).'</td>
        <td class="sum">'.number_format($sum_line[1]).'</td><td class="sum">'.number_format($sum_line[2]).'</td>
        <td class="sum">'.number_format(array_sum($sum_line)).'</td></tr>');
}
?>
</table>
</div>

<?php
// 当月実績比較表作成
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
<table border="1" cellspacing="0" cellpadding="5">
    <tr><th></th><th>西部店</th><th>中央店</th><th>増尾店</th><th>合計</th></tr>
    <tr><td><?php print('前年実績'); ?></td><td class="money"><?php print(number_format($seibu_o)); ?></td>
                                            <td class="money"><?php print(number_format($chuo_o)); ?></td>
                                            <td class="money"><?php print(number_format($masuo_o)); ?></td>
                                            <td class="money"><?php print(number_format($gokei_o)); ?></td></tr>
    <tr><td><?php print($mm.'月予算'); ?></td><td class="money"><?php print(number_format($seibu_yo)); ?></td>
                                              <td class="money"><?php print(number_format($chuo_yo)); ?></td>
                                              <td class="money"><?php print(number_format($masuo_yo)); ?></td>
                                              <td class="money"><?php print(number_format($gokei_yo)); ?></td></tr>
    <tr><td><?php print($mm.'月実績'); ?></td><td class="money"><?php print(number_format($sum_line[0])); ?></td>
                                              <td class="money"><?php print(number_format($sum_line[1])); ?></td>
                                              <td class="money"><?php print(number_format($sum_line[2])); ?></td>
                                              <td class="money"><?php print(number_format(array_sum($sum_line))); ?></td></tr>
    <tr><th class="sum1"><?php print('予算比'); ?></th><td class="sum"><?php print($seibu_ra.'%'); ?></td>
                                          <td class="sum"><?php print($chuo_ra.'%'); ?></td>
                                          <td class="sum"><?php print($masuo_ra.'%'); ?></td>
                                          <td class="sum"><?php print($gokei_ra.'%'); ?></td></tr>
    <tr><th class="sum1"><?php print('前年比'); ?></th><td class="sum"><?php print($seibu_rb.'%'); ?></td>
                                          <td class="sum"><?php print($chuo_rb.'%'); ?></td>
                                          <td class="sum"><?php print($masuo_rb.'%'); ?></td>
                                          <td class="sum"><?php print($gokei_rb.'%'); ?></td></tr>
</table>
</div>

<?php 
// 当月実績グラフ出力
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

<div class="image">
<a href="image1.php"><img src="image1.php" width="200px" height="250px;"></a>
<a href="image2.php"><img src="image2.php" width="200px" height="250px;"></a>
<?php 
//print('<img src="http://chart.apis.google.com/chart?chs=400x300&chd=t:'
//    .($seibu_o/100).','.($chuo_o/100).','.($masuo_o/100).'|'
//    .($seibu_yo/100).','.($chuo_yo/100).','.($masuo_yo/100).'|'
//    .($sum_col[0]/100).','.($sum_col[1]/100).','.($sum_col[2]/100)
//    .'&chdl=seibu|chuo|masuo&chxt=y&cht=bvg&chco=00FF00,00FFFF,FFFF00" width="200x" height="250px;">');
?>
</div>

<?php
// 年間実績比較表作成
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
    <tr><th></th><th colspan="3">西部店</th><th colspan="3">中央店</th><th colspan="3">増尾店</th>
        <th colspan="3">合計</th></tr>
    <tr><th></th>
        <th class="ti">前年実績</th><th>予算</th><th>実績</th><th class="ti">前年実績</th><th>予算</th><th>実績</th>
        <th class="ti">前年実績</th><th>予算</th><th>実績</th><th class="ti">前年実績</th><th>予算</th><th>実績</th></tr>
<?php

for ($i = 0; $i < 12; $i++) {
	print('<tr><td class="mon">'.$seibu[$i][3].'月'.'</td><td class="money">'
	                .number_format($seibu[$i][0]).'</td><td class="money">'.number_format($seibu[$i][1]).'</td><td class="money">'.number_format($seibu[$i][2]).'</td><td class="money">'
	                .number_format($chuo[$i][0]).'</td><td class="money">'.number_format($chuo[$i][1]).'</td><td class="money">'.number_format($chuo[$i][2]).'</td><td class="money">'
	                .number_format($masuo[$i][0]).'</td><td class="money">'.number_format($masuo[$i][1]).'</td><td class="money">'.number_format($masuo[$i][2]).'</td><td class="money">'
	                .number_format($zenten[$i][0]).'</td><td class="money">'.number_format($zenten[$i][1]).'</td><td class="money">'.number_format($zenten[$i][2]).'</td></tr>');
    for ($j = 0; $j < 3; $j++) {
	    $sub_seibu[$j]  += $seibu[$i][$j];
        $sub_chuo[$j]   += $chuo[$i][$j];
        $sub_masuo[$j]  += $masuo[$i][$j];
        $sub_zenten[$j] += $zenten[$i][$j];
    }
}

print('<tr><th class="sum1">'.'合計'.'</th><td class="sum">'
                    .number_format($sub_seibu[0]).'</td><td class="sum">'.number_format($sub_seibu[1]).'</td><td class="sum">'.number_format($sub_seibu[2]).'</td><td class="sum">'
                    .number_format($sub_chuo[0]).'</td><td class="sum">'.number_format($sub_chuo[1]).'</td><td class="sum">'.number_format($sub_chuo[2]).'</td><td class="sum">'
                    .number_format($sub_masuo[0]).'</td><td class="sum">'.number_format($sub_masuo[1]).'</td><td class="sum">'.number_format($sub_masuo[2]).'</td><td class="sum">'
                    .number_format($sub_zenten[0]).'</td><td class="sum">'.number_format($sub_zenten[1]).'</td><td class="sum">'.number_format($sub_zenten[2]).'</td></tr>');
?>

</table>
</div>

<div class="off">
<?php
if ($level > 2) {
?>
    <a href="logout.php">ログアウト</a>　　<a href="input.php">入力処理</a>　　<a href="summary.php">今月度集計</a>
<?php //    header('Location: http://www.asa-kashiwa.net/orikomi/input.php'); ?>
<?php } else {?>
    <a href="logout.php">ログアウト</a>　　<a href="zenten.php">全店日報</a>　　<a href="jisseki.php">今期実績</a>　　<a href="summary.php">今月度集計</a>
<?php } ?>
</div>
</div>
</body>
</html>
