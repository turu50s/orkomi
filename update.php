<?php
require_once('db_auth.php');

$level = $myauth->getAuthData('level');
if ($level > 1) {
    if ($level > 2) {
	   header('Location: http://www.asa-kashiwa.net/orikomi/input.php');
    } else {
	   header('Location: http://www.asa-kashiwa.net/orikomi/sumamry.php');
    }
}

$today     = getdate();
$yy        = $today['year'];
$mm        = $today['mon'];
$dd        = $today['mday'];

session_start();
if (empty($_POST['month'])) {
	$_POST['month'] = $mm;

}
if ($_SESSION['upd'] == '1') {
	$_SESSION['upd'] = '0';
	$_POST['month'] = $_SESSION['mon'];
} else {
	$_SESSION['err'] = '';
}
?>
<html>
<head>
<title>折込日報更新</title>
<link rel="stylesheet" type="text/css" href="./honbu1.css">
</head>
<body onLoad="document.mon.month.focus()">
<h2>折込日報更新（本部）　　　　　　　　　　　　　　　<font color="white">(単位：円)</font></h2>

<div class="menu">
<a href="summary.php">各店比較</a>
<a href="zenten.php">全店日報</a>
<a href="jisseki.php">今期実績</a>
<a href="yosan.php">予算入力</a>
<br>
<a href="logout.php">ログアウト</a>
</div>

<div class="up">
<form method="post" action="<?php print($_SERVER['PHP_SELF']); ?>" name="mon">
    <input type="text" name="month" value="<?php print($_POST['month'])?>" size="2">月
</form>

<form method="post" action="update2.php" name="update">

<table  border="1 cellspacing="0" cellpadding="5">
    <tr><th></th><th colspan="2">西部店</th><th colspan="2">中央店</th><th colspan="2">増尾店</th>
                 <th colspan="2">合計</th></tr>
    <tr><th>日付</th><th>枚数</th><th>金額</th><th>枚数</th><th>金額</th><th>枚数</th><th>金額</th>
                    <th>枚数</th><th>金額</th>
<?php
if (($today['mon'] == 12) && ($_POST['month'] == 1)) {
	$yy++;
}
if (($today['mon'] == 1) && ($_POST['month'] == 12)) {
    $yy--;
}
//$yymm = $yy.'/'.$_POST['month'];
$mon = $_POST['month'];

// 折込日報データ抽出
try {
    $db = new PDO('sqlite2:../SQLiteManager-1.2.0/orikomi.sqlite2');
    $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    $sql = "SELECT * FROM nippo WHERE year = '$yy' AND month = '$mon' ORDER BY year,month,day ASC";

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
// 折込日報表作成
$sum_col = array();

    
$i = 0;
foreach ($data as $line) {
    $i++;
    $sum_line[0] = $line[0][0] + $line[1][0] + $line[2][0];
    $sum_line[1] = $line[0][1] + $line[1][1] + $line[2][1];
    
    $sum_col[0][0] += $line[0][0];
    $sum_col[0][1] += $line[0][1];
    $sum_col[1][0] += $line[1][0];
    $sum_col[1][1] += $line[1][1];
    $sum_col[2][0] += $line[2][0];
    $sum_col[2][1] += $line[2][1];
    
    print('<tr><td class="mon">'.$line[0][2].'日</td><td>'
               
               .'<input type="text" name="'.'seibu_n'.$i.'" value="'.$line[0][1].'" size="4"></td><td>'
               .'<input type="text" name="'.'seibu'.$i.'" value="'.$line[0][0].'" size="10"></td><td>'
               .'<input type="text" name="'.'chuo_n'.$i.'" value="'.$line[1][1].'" size="4"></td><td>'
               .'<input type="text" name="'.'chuo'.$i.'" value="'.$line[1][0].'" size="10"></td><td>'
               .'<input type="text" name="'.'masuo_n'.$i.'" value="'.$line[2][1].'" size="4"></td><td>'
               .'<input type="text" name="'.'masuo'.$i.'" value="'.$line[2][0].'" size="10"></td><td>'
               .$sum_line[1].'</td><td>'.number_format($sum_line[0]).'</td><tr>');
    print('<input type="hidden" name="'.days.$i.'" value="'.$line[0][2].'">');
}
$cnt = $i;

$sum_sum[0] = $sum_col[0][0]+ $sum_col[1][0] + $sum_col[2][0];
$sum_sum[1] = $sum_col[0][1]+ $sum_col[1][1] + $sum_col[2][1];

print('<tr><th class="sum1">合計</th><td class="sum">'
    .number_format($sum_col[0][1]).'</td><td class="sum">'
    .number_format($sum_col[0][0]).'</td><td class="sum">'
    .number_format($sum_col[1][1]).'</td><td class="sum">'
    .number_format($sum_col[1][0]).'</td><td class="sum">'
    .number_format($sum_col[2][1]).'</td><td class="sum">'
    .number_format($sum_col[2][0]).'</td><td class="sum">'
    .number_format(($sum_sum[1])).'</td><td class="sum">'
    .number_format(($sum_sum[0])).'</td></tr>');
?>
</table>

<input type="hidden" name="day" value="<?php print($cnt); ?>">
<input type="hidden" name="year" value="<?php print($yy); ?>">
<input type="hidden" name="month1" value="<?php print($_POST['month']); ?>">
<input type="submit" name="upd" value="更新">
</form>
<font color="Red"><?php print($_SESSION['err']); ?></font>
</div>
</body>
</html>