<?php
//���ǯ������ 
$today = getdate();

$yy    = $today['year'];
$mm    = $today['mon'];
$dd    = $today['mday'];
if ($mm == 1) {
        $mm  = 13;
        $yy--;
}
$mm--; 

 
// �޹��ǡ������١�����³
$dsn = 'mysql:dbname=sddb0040128103;host=sddb0040128103.cgidb';
$user = 'sddbMTgzOTIz';
$password = 'i8h4vf5U';

try {
    //$db = new PDO('sqlite2:../SQLiteManager-1.2.0/orikomi.sqlite2');
    $db = new PDO($dsn, $user, $password);
	
    $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    $sql = "SELECT count(*) FROM nippo8 WHERE year = '$yy' AND month = '$mm' GROUP BY year,month";
    $rs=$db->query($sql);
    $count = $rs->fetchColumn();
    
} catch (PDOException $e) {
    die('error: '.$e->getMessage());
}
        
if ($count) {
	monthly($yy,$mm);
	print('<p>�����ټ����ɲä��ޤ�����</p>');
	
    if ($mm == 3) {
    	fiscal();
    	print('<br><p>��ǯ�ټ��Ӻ������ޤ���</p>');
    }
    
    print('<p><a href="yosan8.php">���</a></p>');
} else {
    print('<p>���ӥǡ��������ѤߤǤ���</p>');
    print('<p><a href="jisseki8.php">���</a></p>');
}

// �����

function monthly($yy,$mm) {
    // ����ģ½���
    $dsn = 'mysql:dbname=sddb0040128103;host=sddb0040128103.cgidb';
	$user = 'sddbMTgzOTIz';
	$password = 'i8h4vf5U';

    try {
        //$db = new PDO('sqlite2:../SQLiteManager-1.2.0/orikomi.sqlite2');
        $db = new PDO($dsn, $user, $password);
	
        $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        $sql = "SELECT tenpo,year,month,sum(money),sum(number) FROM nippo8 WHERE year = '$yy' AND month = '$mm' GROUP BY tenpo,year,month";
        
        $rs=$db->query($sql);
        $row = $rs->fetchAll();
        unset($rs);
        foreach ($row as $rec) {
            //$date_j  = $yy.'/'.$mm;
            $tenpo   = $rec['tenpo'];
            $month   = $rec['year'].'/'.$rec['month'];
            
            $results = $rec['sum(money)'];
            $number  = $rec['sum(number)'];
            $sql = "INSERT INTO jisseki8(tenpo,month,results,number) VALUES('$tenpo','$month','$results','$number')";
            $db->exec($sql);
        }
        
        del_nippo($yy,$mm);
    } catch (PDOException $e) {
        die('error: '.$e->getMessage());
    }
}       

function del_nippo($yy,$mm) {
	$dsn = 'mysql:dbname=sddb0040128103;host=sddb0040128103.cgidb';
	$user = 'sddbMTgzOTIz';
	$password = 'i8h4vf5U';

    try {
        //$db = new PDO('sqlite2:../SQLiteManager-1.2.0/orikomi.sqlite2');
        $db = new PDO($dsn, $user, $password);
		
		$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        //$sql = "DROP TABLE nippo8";
        $sql = "DELETE FROM nippo8 WHERE year = '$yy' AND month = '$mm'";
        $db->exec($sql);
        //$db->exec('VACUUM');
        
        //$sql = "CREATE TABLE nippo8(id INTEGER NOT NULL PRIMARY KEY, tenpo TEXT(6), date TEXT(10), money INTEGER(8), number INTEGER(4))";
        //$db->exec($sql);
    } catch (PDOException $e) {
        die('error: '.$e->getMessage());
    }
}
function fiscal() {
	$dsn = 'mysql:dbname=sddb0040128103;host=sddb0040128103.cgidb';
	$user = 'sddbMTgzOTIz';
	$password = 'i8h4vf5U';

	try {
	    //$db = new PDO('sqlite2:../SQLiteManager-1.2.0/orikomi.sqlite2');
	    $db = new PDO($dsn, $user, $password);
		
	    $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	    
	    $sql = "DROP TABLE jisseki8_old";
	    $db->exec($sql);
	    //$db->exec('VACUUM');
	    $sql = "CREATE TABLE jisseki8_old(id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT, tenpo TEXT(6), month TEXT(7), results INTEGER(10), number INTEGER(5))";
	    $db->exec($sql);
	    
	    $sql = "SELECT * FROM jisseki8 ORDER BY id ASC";
	    $rs  = $db->query($sql);
	    $row = $rs->fetchAll();
	    unset($rs);
	    foreach ($row as $rec) {
	        $tenpo   = $rec['tenpo'];
	        $month   = $rec['month'];
	        $results = $rec['results'] / 1000;
	        $number  = $rec['number'];
	        
	        $sql ="INSERT INTO jisseki8_old (tenpo,month,results,number) VALUES('$tenpo','$month','$results','$number')";
	        $db->exec($sql);
	    }
	    $sql = "DROP TABLE jisseki8";
	    $db->exec($sql);
	    // $db->exec('VACUUM');
	    
	    $sql = "CREATE TABLE jisseki8(id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT, tenpo TEXT(6), month TEXT(7), results INTEGER(10), number INTEGER(5))";
	    $db->exec($sql);
	} catch (PDOException $e) {
	    die('error: '.$e->getMessage());
	}
}
?>