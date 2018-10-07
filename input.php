<?php
ini_set( "display_errors", "off");

require_once('db_auth.php');
// session_destroy();

$level = $myauth->getAuthData('level');
$tenpo = $myauth->getAuthData('section');

if ($level < 3) {
    header('Location: http://www.asa-kashiwa.net/orikomi/summary.php');
}

$today = getdate();
$yy    = $today['year'];
$mm    = $today['mon'];
$dd    = $today['mday'];

session_start();
$_SESSION['tenpo'] = $tenpo;
$_SESSION['date'] = $today;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-jp">
<meta http-equiv="Content-Style-Type" content="text/css">
<title>折込日報入力</title>
<link rel="stylesheet" type="text/css" href="./input.css">
</head>
<body onLoad="document.input.number.focus()">
<?php
switch ($tenpo) {
	case 'seibu':
		$tenpo_h = '南部';
		break;
	case 'chuo':
		$tenpo_h = '中央';
		break;
	case 'masuo':
		$tenpo_h = '増尾';
		break;
	case 'hachi':
		$tenpo_h = '八ヶ崎';
	
}
?>

<h2>折込日報入力（<?php print($tenpo_h); ?>）</h2>
<p class="date"><?php print(date('Y年m月d日 (D)').'<br>'); ?></p>
<div class="menu">
<? if ($tenpo == 'hachi'): ?>
	<a href="summary8.php">実績参照</a>
	<a href="yosan8.php">予算更新</a>
	<a href="jisseki8.php">実績修正</a>
	<a href="pdf8.php">報告書出力</a>
<? else: ?>
	<a href="summary.php">全店参照</a>
<? endif; ?>
<a href="nippo_ex.php">日報出力</a>
<br>
<a href="logout.php">ログアウト</a>
</div>
<?php 
// 予算データ抽出
$date_o = $yy.'/'.$mm;

// 折込データーベース接続
$dsn = 'mysql:dbname=sddb0040128103;host=sddb0040128103.cgidb';
$user = 'sddbMTgzOTIz';
$password = 'i8h4vf5U';

try {
	//$db = new PDO('sqlite2:../SQLiteManager-1.2.0/orikomi.sqlite2');
    
    $db = new PDO($dsn, $user, $password);
	
    $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    
    if ($tenpo == 'hachi') {
		$sql = "SELECT * FROM yosan8 WHERE month = '$date_o'";
	} else {
	    $sql = "SELECT * FROM yosan WHERE month = '$date_o' AND tenpo = '$tenpo'";
    }
    $rs=$db->query($sql);
    $row = $rs->fetch(PDO::FETCH_ASSOC);
    $budget = $row['budget'] * 1000;
} catch (PDOException $e) {
    die('error: '.$e->getMessage());
}

// 日報データ check!
try {
	//$db = new PDO($dsn, $user, $password);
	
	$mm_prev = date('m', strtotime(date('Y-m-1').' -1 month'));
	$yy_prev = date('Y', strtotime(date('Y-m-1').' -1 month'));
	
	$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	if ($tenpo == 'hachi') {
		$sql = "SELECT count(*) FROM nippo8 WHERE year = '$yy_prev' AND month = '$mm_prev' GROUP BY tenpo,year,month";
	} else {
		$sql = "SELECT count(*) FROM nippo WHERE year = '$yy_prev' AND month = '$mm_prev' GROUP BY tenpo,year,month";
	}
	
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
<div class="up">
<h3><?php printf("%2d",$mm); ?>月　　　　予算：　<?php print(number_format($budget)); ?>円</h3>

<table border="1" cellspacing="0" cellpadding="5">
	<tr><th></th><th></th><th colspan="2">当日</th><th colspan="2">累計</th><th></th></tr>
	<tr><th>日付</th><th>曜日</th><th>枚数</th><th>金額</th><th>枚数</th><th>金額</th><th>予算比</th></tr>

<?php
// 日報データ出力

try {
	if ($tenpo == 'hachi') {
		$sql = "SELECT * FROM nippo8 ORDER BY year,month,day ASC";
	} else {
		$sql = "SELECT * FROM nippo WHERE tenpo = '$tenpo' ORDER BY year,month,day ASC";
	}
	$rs=$db->query($sql);
	$sum_number = 0;
	$sum_money  = 0;
	$week_list = array('日','月','火','水','木','金','土');
	
	while ($row = $rs->fetch(PDO::FETCH_ASSOC)) {
		//$date = explode("/",$row['day']);
		$sum_number += $row['number'];
		$sum_money  += $row['money'];
		$rate  = round(($sum_money / $budget) * 100,1);
		$date  = $row['month'].'/'.$row['day'];
		$week  = date('w',strtotime($date));
		$yobi  = $week_list[$week];
		
		print('<tr><td class="mon">'.$row['day'].'日</td><td>'.$yobi.'</td><td class="money">'
		  .$row['number'].'</td><td class="money">'
		  .number_format($row['money']).'</td><td class="money">'.number_format($sum_number).
		  '</td><td class="money">'.number_format($sum_money).'</td><td class="money">'.$rate.'%</td></tr>');
	
	}
} catch (PDOException $e) {
	die('error: '.$e->getMessage());
}

?>
</table>
<?php if ($level == 3) { 
		$date_in = date("Y-m-d", strtotime("+1 day"));
		list($yy_in,$mm_in,$dd_in) = explode('-', $date_in);
?>

<!-- 日報データ入力 -->
<div class="in">
<p>入力欄：(単位：円)</p>

<form method="post" action="input2.php" name="input">
<table border="1" cellspacing="0" cellpadding="5">
    <tr><th>年</th><th>月</th><th>日付</th><th>枚数</th><th>金額</th></tr>
    <tr>
    	<td class="mon"><input type="text" name="year" value="<? print($yy_in); ?>" size="4"></td>
        <td class="mon"><input type="text" name="month" value="<? print($mm_in); ?>" size="2"></td>
        <td class="mon"><input type="text" name="day" value="<? print($dd_in); ?>" size="2"></td>
        <td class="money"><input type="text" name="number" size="5"></td>
    	<td class="money"><input type="text" name="money" size="12"></td></tr>
    <tr><td colspan="5"><input type="submit" name="ins" value="登録"></td></tr>
</table>
<input type="hidden" name="tenpo" value="<?php print($tenpo); ?>">
<!--<input type="hidden" name="year" value="<?php print($yy); ?>">-->

</form>
</div>
<font color="Red"><?php print($_SESSION['err']); ?></font>
</div>
<?php } ?>
</body>
</html>
