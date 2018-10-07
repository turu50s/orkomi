<?php
// 半角数字だけかチェック
$pat = "/^[0-9]+$/";

$day = $_POST['day'];

session_start();
$_SESSION['err'] = '';
$j = 0;
for ($i = 0; $i < 12; $i++) {
    $j++;
    $tenpo1a = 'hachi' . $j . 'a';
    $tenpo1b = 'hachi' . $j . 'b';
    
    if (preg_match($pat,$_POST[$tenpo1a]) && preg_match($pat,$_POST[$tenpo1b])) {
       $hachi[$i][0] = $_POST[$tenpo1a];
       $hachi[$i][1] = $_POST[$tenpo1b];
    } else {
       
        $_SESSION['err'] = '半角数字のみ入力してください。';
        header('Location: http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/yosan8.php');
        exit();
      
    }
}

// 折込データーベース接続
$dsn = 'mysql:dbname=sddb0040128103;host=sddb0040128103.cgidb';
$user = 'sddbMTgzOTIz';
$password = 'i8h4vf5U';

try {
    $db = new PDO($dsn, $user, $password);
	
	$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    $sql = "DELETE FROM jisseki8";
    //$sql = "DROP TABLE jisseki8";
    $db->exec($sql);
    //$db->exec('VACUUM');
    //$sql = "CREATE TABLE jisseki8(id INTEGER NOT NULL PRIMARY KEY, tenpo TEXT(6), month TEXT(7), number INTEGER(5), results INTEGER(10))";
    //$db->exec($sql);
    
    
        for ($i = 0; $i < 12; $i++) {
        	
        	if ($i > 8) {
        		$month = $i - 8;
        		$year  = $_POST['year'] + 1;
        	} else {
        		$month = $i + 4;
        		$year  = $_POST['year'];
        	}
        	$date_up = $year . '/' . $month;
            
        	$work_a = $hachi[$i][0];
        	$work_b = $hachi[$i][1];
			$tenpo = 'hachi';
        	$sql = "INSERT INTO jisseki8(tenpo,month,results,number) VALUES('$tenpo','$date_up','$work_b','$work_a')";
            $db->exec($sql);
      
    }
    $_SESSION['err'] = '更新しました。';
} catch (PDOException $e) {
    die('error: '.$e->getMessage());
}

header('Location: http://www.asa-kashiwa.net/orikomi/yosan8.php');

?>