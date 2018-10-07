<?php
$pat = "/^[0-9]+$/";

session_start();

$_SESSION['jup'] = '1';

$tenpo   = $_POST['tenpo1'];
$month   = $_POST['month'];
$results = $_POST['results'];
$number  = $_POST['number'];

//var_dump($_POST['results']);
//var_dump($_POST['number']);
//var_dump($_POST['year']);
//var_dump($_POST['month']);

$tenpo_h = '八ヶ崎店';

$today = getdate();

// 折込データーベース接続
$dsn = 'mysql:dbname=sddb0040128103;host=sddb0040128103.cgidb';
$user = 'sddbMTgzOTIz';
$password = 'i8h4vf5U';

if (preg_match($pat,$month) && preg_match($pat,$results) && preg_match($pat,$number)) {
	//if (($today['mon'] < 4) && ($month > 3)) {
	//	$yy = $_POST['year'] - 1;
	//} else {
	//	$yy = $_POST['year'];
	//}
	$month = ltrim($month,"0");
	$yymm = $_POST['year'].'/'.$month;
	
	//var_dump($yymm);

	
	try {
	    //$db = new PDO('sqlite2:../SQLiteManager-1.2.0/orikomi.sqlite2');
	    $db = new PDO($dsn, $user, $password);
		
		$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	   
	     
	    $sql = "UPDATE jisseki8 SET results = ?,number = ? WHERE month = '$yymm' AND tenpo = 'hachi'";
	    $stt = $db->prepare($sql);
	    $stt->execute(array($results,$number));
	    $_SESSION['err'] = $tenpo_h.'の'.$month.'月を更新しました。';
	} catch (PDOException $e) {
		die('error: '.$e->getMessage());
	}
} else {
	 $_SESSION['err'] = '半角数字のみ入力してください。';
}
header('Location: http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/jisseki8.php');

?>