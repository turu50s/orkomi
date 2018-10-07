<?php
// 半角数字だけかチェック
$pat = "/^[0-9]+$/";

$day = $_POST['day'];
$tenpo_l = array('seibu','chuo','masuo');

session_start();
$_SESSION['err'] = '';
$j = 0;
for ($i = 0; $i < 12; $i++) {
    $j++;
    $tenpo1 = 'seibu' . $j;
    $tenpo2 = 'chuo'  . $j;
    $tenpo3 = 'masuo' . $j;
    
    if (preg_match($pat,$_POST[$tenpo1]) && preg_match($pat,$_POST[$tenpo2]) && preg_match($pat,$_POST[$tenpo3])) {
       $seibu[$i] = $_POST[$tenpo1];
       $chuo[$i]  = $_POST[$tenpo2];
       $masuo[$i] = $_POST[$tenpo3];
    } else {
        
        $_SESSION['err'] = '半角数字のみ入力してください。';
        header('Location: http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/yosan.php');
        exit();
    }
}

// 折込データーベース接続
$dsn = 'mysql:dbname=sddb0040128103;host=sddb0040128103.cgidb';
$user = 'sddbMTgzOTIz';
$password = 'i8h4vf5U';

try {
    //$db = new PDO('sqlite2:../SQLiteManager-1.2.0/orikomi.sqlite2');
    $db = new PDO($dsn, $user, $password);
	
	$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    $sql = "DELETE FROM yosan";
    //$sql = "DROP TABLE yosan";
    $db->exec($sql);
    //$db->exec('VACUUM');
    //$sql = "CREATE TABLE yosan(id INTEGER NOT NULL PRIMARY KEY, tenpo TEXT(6), month TEXT(7), budget INTEGER(8))";
    //$db->exec($sql);
    
    foreach ($tenpo_l as $tenpo_i) {
        for ($i = 0; $i < 12; $i++) {
        	
        	if ($i > 8) {
        		$month = $i - 8;
        		$year  = $_POST['year'] + 1;
        	} else {
        		$month = $i + 4;
        		$year  = $_POST['year'];
        	}
        	$date_up = $year . '/' . $month;
            
        	switch ($tenpo_i) {
                case seibu:
                   $work = $seibu[$i];
                   break;
                case chuo:
                    $work = $chuo[$i];
                   break;
                case masuo:
                    $work = $masuo[$i];
                   break;
            }
            
        	$sql = "INSERT INTO yosan(tenpo,month,budget) VALUES('$tenpo_i','$date_up','$work')";
            $db->exec($sql);
      
        }
    }
    $_SESSION['err'] = '更新しました。';
} catch (PDOException $e) {
    die('error: '.$e->getMessage());
}

header('Location: http://www.asa-kashiwa.net/orikomi/yosan.php');

?>