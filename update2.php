<?php
session_start();
$_SESSION['mon'] = $_POST['month1'];
// 半角数字だけかチェック
$pat = "^[0-9]+$";

$day = $_POST['day'];
$tenpo_l = array('seibu','chuo','masuo');
session_start();
$_SESSION['err'] = '';
$_SESSION['upd'] = '1';
$j = 0;
for ($i = 0; $i < $_POST['day']; $i++) {
	$j++;
	$tenpo1   = 'seibu' . $j;
	$tenpo1_n = 'seibu_n' . $j;
	$tenpo2   = 'chuo' . $j;
	$tenpo2_n = 'chuo_n' . $j;
	$tenpo3   = 'masuo' . $j;
	$tenpo3_n = 'masuo_n' . $j;
	$days_w   = 'days'.$j;
	
	if (ereg($pat,$_POST[$tenpo1]) && ereg($pat,$_POST[$tenpo2]) && ereg($pat,$_POST[$tenpo3])
	 && ereg($pat,$_POST[$tenpo1_n]) && ereg($pat,$_POST[$tenpo2_n]) && ereg($pat,$_POST[$tenpo3_n])) {
	   $seibu[$i]   = $_POST[$tenpo1];
	   $chuo[$i]    = $_POST[$tenpo2];
	   $masuo[$i]   = $_POST[$tenpo3];
	   $seibu_n[$i] = $_POST[$tenpo1_n];
       $chuo_n[$i]  = $_POST[$tenpo2_n];
       $masuo_n[$i] = $_POST[$tenpo3_n];
       $days[$i]    = $_POST[$days_w];
	} else {
		
        $_SESSION['err'] = '半角数字のみ入力してください。';
        header('Location: http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/update.php');
	}
}

try {
    $db = new PDO('sqlite2:../SQLiteManager-1.2.0/orikomi.sqlite2');
    $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    
    foreach ($tenpo_l as $tenpo_i) {
        for ($i = 0; $i < $_POST['day']; $i++) {
            //$date_up = $_POST['year']."/".$_POST['month1'];
            $year_up = $_POST['year'];
        	$mon_up  = $_POST['month1'];
            $day_up  = $days[$i];
            
            $sql = "UPDATE nippo SET money = ?,number = ? WHERE year = '$year_up' AND month = '$mon_up' AND day = $day_up AND tenpo = '$tenpo_i'";
            $stt = $db->prepare($sql);
            switch ($tenpo_i) {
            	case seibu:
            	   $stt->execute(array($seibu[$i],$seibu_n[$i]));
            	   break;
            	case chuo:
            		$stt->execute(array($chuo[$i],$chuo_n[$i]));
                   break;
            	case masuo:
            		$stt->execute(array($masuo[$i],$masuo_n[$i]));
                   break;
            }
        }
    }
    $_SESSION['err'] = '更新しました。';
} catch (PDOException $e) {
    die('error: '.$e->getMessage());
}

header('Location: http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/update.php');

?>
