<?php
ini_set( "display_errors", "Off");

// StrictStandarsのワーニングを消す。
ini_set('error_reporting', E_ALL & ~E_STRICT);

require_once('db_auth.php');

$level = $myauth->getAuthData('level');
if ($level > 1) {
    header('Location: http://www.asa-kashiwa.net/orikomi/input.php');
}

$today     = getdate();
$yy        = $today['year'];
$mm        = $today['mon'];
$dd        = $today['mday'];
$date_k = $yy.'/'.$mm.'/%';

// 折込データーベース接続
$dsn = 'mysql:dbname=sddb0040128103;host=sddb0040128103.cgidb';
$user = 'sddbMTgzOTIz';
$password = 'i8h4vf5U';

try {
    //$db = new PDO('sqlite2:../SQLiteManager-1.2.0/orikomi.sqlite2');
    $db = new PDO($dsn, $user, $password);
	
	$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
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
              $seibu[$date_r-4] = $rec['budget'];
              break;
           case 'chuo':
              $chuo[$date_r-4]  = $rec['budget'];
              break;
           case 'masuo':
              $masuo[$date_r-4] = $rec['budget'];
              break;
        }
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
<title>予算入力</title></head>
<link rel="stylesheet" type="text/css" href="./honbu.css">
<body>
<h2>予算入力（本部）　　<?php if ($mm < 4) {$yy = $yy - 1;} print($yy); ?>年度　　　　　　　　　<font color="white">(単位：千円)</font></h2>
<div class="menu">
<a href="summary.php">各店比較</a>
<a href="zenten.php">全店日報</a>
<a href="jisseki.php">今期実績</a>
<br>
<a href="logout.php">ログアウト</a>
</div>
<div class="up">
<form method="post" action="./yosan_in.php">
<table border="1" cellspacing="0" cellpadding="5">
    <tr><th></th><th>南部店</th><th>中央店</th><th>増尾店</th></tr>
    <tr><td>4月</td><td><input type="text" name="seibu1" value="<?php print($seibu[0]); ?>" size="10"></td>
                  <td><input type="text" name="chuo1" value="<?php print($chuo[0]); ?>" size="10"></td>
                  <td><input type="text" name="masuo1" value="<?php print($masuo[0]); ?>" size="10"></td></tr>
    <tr><td>5月</td><td><input type="text" name="seibu2" value="<?php print($seibu[1]); ?>" size="10"></td>
                  <td><input type="text" name="chuo2" value="<?php print($chuo[1]); ?>" size="10"></td>
                  <td><input type="text" name="masuo2" value="<?php print($masuo[1]); ?>" size="10"></td></tr>
    <tr><td>6月</td><td><input type="text" name="seibu3" value="<?php print($seibu[2]); ?>" size="10"></td>
                  <td><input type="text" name="chuo3" value="<?php print($chuo[2]); ?>" size="10"></td>
                  <td><input type="text" name="masuo3" value="<?php print($masuo[2]); ?>" size="10"></td></tr>
    <tr><td>7月</td><td><input type="text" name="seibu4" value="<?php print($seibu[3]); ?>" size="10"></td>
                  <td><input type="text" name="chuo4" value="<?php print($chuo[3]); ?>" size="10"></td>
                  <td><input type="text" name="masuo4" value="<?php print($masuo[3]); ?>" size="10"></td></tr>
    <tr><td>8月</td><td><input type="text" name="seibu5" value="<?php print($seibu[4]); ?>" size="10"></td>
                  <td><input type="text" name="chuo5" value="<?php print($chuo[4]); ?>" size="10"></td>
                  <td><input type="text" name="masuo5" value="<?php print($masuo[4]); ?>" size="10"></td></tr>
    <tr><td>9月</td><td><input type="text" name="seibu6" value="<?php print($seibu[5]); ?>" size="10"></td>
                  <td><input type="text" name="chuo6" value="<?php print($chuo[5]); ?>" size="10"></td>
                  <td><input type="text" name="masuo6" value="<?php print($masuo[5]); ?>" size="10"></td></tr>
    <tr><td>10月</td><td><input type="text" name="seibu7" value="<?php print($seibu[6]); ?>" size="10"></td>
                   <td><input type="text" name="chuo7" value="<?php print($chuo[6]); ?>" size="10"></td>
                   <td><input type="text" name="masuo7" value="<?php print($masuo[6]); ?>" size="10"></td></tr>
    <tr><td>11月</td><td><input type="text" name="seibu8" value="<?php print($seibu[7]); ?>" size="10"></td>
                   <td><input type="text" name="chuo8" value="<?php print($chuo[7]); ?>" size="10"></td>
                   <td><input type="text" name="masuo8" value="<?php print($masuo[7]); ?>" size="10"></td></tr>
    <tr><td>12月</td><td><input type="text" name="seibu9" value="<?php print($seibu[8]); ?>" size="10"></td>
                   <td><input type="text" name="chuo9" value="<?php print($chuo[8]); ?>" size="10"></td>
                   <td><input type="text" name="masuo9" value="<?php print($masuo[8]); ?>" size="10"></td></tr>
    <tr><td>1月</td><td><input type="text" name="seibu10" value="<?php print($seibu[9]); ?>" size="10"></td>
                  <td><input type="text" name="chuo10" value="<?php print($chuo[9]); ?>" size="10"></td>
                  <td><input type="text" name="masuo10" value="<?php print($masuo[9]); ?>" size="10"></td></tr>
    <tr><td>2月</td><td><input type="text" name="seibu11" value="<?php print($seibu[10]); ?>" size="10"></td>
                  <td><input type="text" name="chuo11" value="<?php print($chuo[10]); ?>" size="10"></td>
                  <td><input type="text" name="masuo11" value="<?php print($masuo[10]); ?>" size="10"></td></tr>
    <tr><td>3月</td><td><input type="text" name="seibu12" value="<?php print($seibu[11]); ?>" size="10"></td>
                  <td><input type="text" name="chuo12" value="<?php print($chuo[11]); ?>" size="10"></td>
                  <td><input type="text" name="masuo12" value="<?php print($masuo[11]); ?>" size="10"></td></tr>
</table>
<input type="hidden" name="year" value="<?php print($yy); ?>">
<input type="submit" value="更新">
<input type="reset"  value="クリア">

</form>

<?php session_start(); ?>
<font color="Red"><?php print($_SESSION['err']); ?></font>
</div>

</body>
</html>