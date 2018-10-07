<?php
ini_set( "display_errors", "Off");

require_once('db_auth.php');

$level = $myauth->getAuthData('level');
if ($level > 2) {
    header('Location: http://www.asa-kashiwa.net/orikomi/input.php');
}

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
<title>今期実績参照</title>
<link rel="stylesheet" type="text/css" href="./honbu.css">
</head>
<body onLoad="document.jisseki.month.focus()">

<h2>今期実績（本部）　　<?php print($today['mon']); ?>月　　　　　　　　　<font color="white">(単位：円)</font></h2>

<div class="menu">
   <a href="summary.php">各店比較</a>
   <a href="zenten.php">全店日報</a>
   <?php if ($level == 1) { ?>
   <a href="yosan.php">予算入力</a>
   <a href="monthly.php">月次更新</a>
   <?php  } ?>
   <a href="jisseki_ex.php">実績出力</a>
   <br>
   <a href="logout.php">ログアウト</a>
</div>

<div class="up">

<table  border="1" cellspacing="0" cellpadding="5" class="pos">
    <tr><th></th><th colspan="2">南部店</th><th colspan="2">中央店</th><th colspan="2">増尾店</th>
                 <th colspan="2">合計</th></tr>
    <tr><th>月</th><th>枚数</th><th>金額</th><th>枚数</th><th>金額</th><th>枚数</th><th>金額</th>
                    <th>枚数</th><th>金額</th></tr>
<?php
// 折込データーベース接続
$dsn = 'mysql:dbname=sddb0040128103;host=sddb0040128103.cgidb';
$user = 'sddbMTgzOTIz';
$password = 'i8h4vf5U';

// 今期実績データ抽出
try {
    //$db = new PDO('sqlite2:../SQLiteManager-1.2.0/orikomi.sqlite2');
    $db = new PDO($dsn, $user, $password);
    
	$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    $sql = "SELECT * FROM jisseki  ORDER BY id ASC";

    $rs=$db->query($sql);
    $row = $rs->fetchAll();

    foreach ($row as $rec) {
       $date_w = explode("/",$rec['month']);
       if ($date_w[1] < 4) {
            $date = $date_w[1] + 12;
       } else {
            $date = $date_w[1];
       }
       switch ($rec['tenpo']){
          case 'seibu':
            $data[$date-4][0][0] = $rec['results'];
            $data[$date-4][0][1] = $rec['number'];
            $data[$date-4][0][2] = $date_w[1];
            break;
          case 'chuo':
            $data[$date-4][1][0] = $rec['results'];
            $data[$date-4][1][1] = $rec['number'];
            break;
          case 'masuo':
            $data[$date-4][2][0] = $rec['results'];
            $data[$date-4][2][1] = $rec['number'];
            break;
       }
    }
} catch (PDOException $e) {
    die('error: '.$e->getMessage());
}
?>

<?php
// 実績表作成
$i = 0;
$sum_seibu  = array();
$sum_chuo   = array();
$sum_masuo  = array();
$sum_zenten = array();

foreach ($data as $line) {
    $sum_line[0] = $line[0][0] + $line[1][0] + $line[2][0];
    $sum_line[1] = $line[0][1] + $line[1][1] + $line[2][1];
    print('<tr><td class="mon">'.$line[0][2].'月</td><td class="money">'
               .number_format($line[0][1]).'</td><td class="money">'
               .number_format($line[0][0]).'</td><td class="money">'
               .number_format($line[1][1]).'</td><td class="money">'
               .number_format($line[1][0]).'</td><td class="money">'
               .number_format($line[2][1]).'</td><td class="money">'
               .number_format($line[2][0]).'</td><td class="money">'
               .number_format($sum_line[1]).'</td><td class="money">'
               .number_format($sum_line[0]).'</td></tr>');
    
     $sum_seibu[1]  += $line[0][1];
     $sum_seibu[0]  += $line[0][0];
     $sum_chuo[1]   += $line[1][1];
     $sum_chuo[0]   += $line[1][0];
     $sum_masuo[1]  += $line[2][1];
     $sum_masuo[0]  += $line[2][0];
     $sum_zenten[1] += $sum_line[1];
     $sum_zenten[0] += $sum_line[0];
}
//$cnt = $i;
//for ($j = 0; $j < 3; $j++) {
//    for ($i = 0; $i < $cnt; $i++) {
//      $sum_col[$j] += $data[$i][$j];
//    }
//}
print('<tr><th class="sum1">合計</th><td class="sum">'
    .number_format($sum_seibu[1]).'</td><td class="sum">'.number_format($sum_seibu[0]).'</td><td class="sum">'
    .number_format($sum_chuo[1]).'</td><td class="sum">'.number_format($sum_chuo[0]).'</td><td class="sum">'
    .number_format($sum_masuo[1]).'</td><td class="sum">'.number_format($sum_masuo[0]).'</td><td class="sum">'
    .number_format($sum_zenten[1]).'</td><td class="sum">'.number_format($sum_zenten[0]).'</td></tr>');
?>
</table>
<?php if ($level == 1) { ?>
<div class="in">
<p>前月実績修正：</p>
(単位：円)
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
<form method="post" action="jisseki_up.php" name="jisseki">
<table border="1" cellspacing="0" cellpadding="5">
    <tr><td colspan="4"><select name="tenpo1"><option value="seibu" >柏南部店</option>
                                             <option value="chuo">柏中央店</option>
                                             <option value="masuo">柏増尾店</option></select></td></tr>
    <tr><th>年</th><th>月</th><th>枚数</th><th>金額</th></tr>
    <tr>
    	<td class="mon"><input type="text" name="year" value="<? print($yy_in); ?>" size="4"></td>
        <td class="mon"><input type="text" name="month" value="<? print($mm_in); ?>" size="2"></td>
        <td class="money"><input type="text" name="number" size="5"></td>
        <td class="money"><input type="text" name="results" size="12"></td>
    </tr>
    <tr><td colspan="4"><input type="submit" name="ins" value="修正"></td></tr>
</table>
<input type="hidden" name="tenpo" value="<?php print($tenpo); ?>">
</form>
</div>
<?php } ?>
<?php session_start(); ?>
<font color="Red"><?php print($_SESSION['err']); ?></font>
</div>
</body>
</html>