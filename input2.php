<?php
ini_set( "display_errors", "Off");
// StrictStandarsのワーニングを消す。
ini_set('error_reporting', E_ALL & ~E_STRICT);

// 半角数字だけかチェック
session_start();
$pat = "/^[0-9]+$/";
$value = $_POST['money'];
$num   = $_POST['number'];
$mon   = $_POST['month'];
$day   = $_POST['day'];

$today = getdate();

if (preg_match($pat,$value) && preg_match($pat,$num) && preg_match($pat,$mon) && preg_match($pat,$day)) {
    $_SESSION['err'] = '';
	if (($today['mon'] == 12) && ($_POST['month'] == 1)) {
		$year = $_POST['year'] + 1;
	} else {
		$year = $_POST['year'];
	}
	$mon = $_POST['month'];
    //$yymm  = $year."/".$_POST['month'];
    $tenpo = $_POST['tenpo'];
    
    $dsn = 'mysql:dbname=sddb0040128103;host=sddb0040128103.cgidb';
	$user = 'sddbMTgzOTIz';
	$password = 'i8h4vf5U';
	
    try {
        //$db = new PDO('sqlite2:../SQLiteManager-1.2.0/orikomi.sqlite2');
        
        $db = new PDO($dsn, $user, $password);
	
        $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		if ($tenpo == 'hachi') {
	        $sql = "SELECT id FROM nippo8 WHERE year = '$year' AND month = '$mon' AND day = $day";
	    } else {
	        $sql = "SELECT id FROM nippo WHERE tenpo = '$tenpo' AND year = '$year' AND month = '$mon' AND day = $day";
        }
        $rs=$db->query($sql);
        $row = $rs->fetch(PDO::FETCH_ASSOC);
    
        unset($rs);     // 同じテーブルの更新前にPDO Statmentを開放する
    
        if (empty($row['id'])) {
            try {
                if ($tenpo == 'hachi') {
	                $sql = 'INSERT INTO nippo8(tenpo,year,month,day,money,number) VALUES(:tenpo,:year,:month,:day,:money,:number)';
	            } else {
	                $sql = 'INSERT INTO nippo(tenpo,year,month,day,money,number) VALUES(:tenpo,:year,:month,:day,:money,:number)';
                }
                $stt = $db->prepare($sql);
            
                $stt->bindParam(':tenpo',$_POST['tenpo'],PDO::PARAM_STR);
                $stt->bindParam(':year',$year,PDO::PARAM_INT);
                $stt->bindParam(':month',$mon,PDO::PARAM_INT);
                $stt->bindParam(':day',$_POST['day'],PDO::PARAM_INT);
                $stt->bindParam(':money',$_POST['money'],PDO::PARAM_INT);
                $stt->bindParam(':number',$_POST['number'],PDO::PARAM_INT);
                $stt->execute();
            } catch (PDOException $e) {
                die('error: '.$e->getMessage());
            }
        } else {
            try {
                $id = $row['id'];
                if ($tenpo == 'hachi') {
                	$sql = "UPDATE nippo8 SET money = ?,number=? WHERE id = $id";
                } else {
                	$sql = "UPDATE nippo SET money = ?,number=? WHERE id = $id";
                }
                $stt = $db->prepare($sql);
            
                $stt->execute(array($_POST['money'],$_POST['number']));
            } catch (PDOException $e) {
                die('error: '.$e->getMessage());
            }
        }
    } catch (PDOException $e) {
        die('error: '.$e->getMessage());
    }
} else {
	
    $_SESSION['err'] = '半角数字のみ入力してください。';
}
header('Location: http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/input.php');

?>
