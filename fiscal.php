<?php
//月次・年次処理 
$today = getdate();

$yy    = $today['year'];
$mm    = $today['mon'];
$dd    = $today['mday'];

          fiscal();


function fiscal() {
$dsn = 'mysql:dbname=sddb0040128103;host=sddb0040128103.cgidb';
	$user = 'sddbMTgzOTIz';
	$password = 'i8h4vf5U';

	try {
	    //$db = new PDO('sqlite2:../SQLiteManager-1.2.0/orikomi.sqlite2');
	    $db = new PDO($dsn, $user, $password);
		
	    $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	    
	    //$sql = "DROP TABLE jisseki_old";
	    //$db->exec($sql);
	    //$db->exec('VACUUM');
	    //$sql = "CREATE TABLE jisseki_old(id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT, tenpo TEXT(6), month TEXT(7), results INTEGER(10), number INTEGER(5))";
	    //$db->exec($sql);
	    
	    //$sql = "SELECT * FROM jisseki ORDER BY id ASC";
	    //$rs  = $db->query($sql);
	    //$row = $rs->fetchAll();
	    //unset($rs);
	    //foreach ($row as $rec) {
	    //    $tenpo   = $rec['tenpo'];
	    //    $month   = $rec['month'];
	    //    $results = $rec['results'] / 1000;
	    //    $number  = $rec['number'];
	        
	    //    $sql ="INSERT INTO jisseki_old (tenpo,month,results,number) VALUES('$tenpo','$month','$results','$number')";
	    //    $db->exec($sql);
	    //}
	    $sql = "DROP TABLE jisseki";
	    $db->exec($sql);
	    // $db->exec('VACUUM');
	    
	    $sql = "CREATE TABLE jisseki(id INTEGER NOT NULL PRIMARY KEY KEY AUTO_INCREMENT, tenpo TEXT(6), month TEXT(7), results INTEGER(10), number INTEGER(5))";
	    $db->exec($sql);
	} catch (PDOException $e) {
	    die('error: '.$e->getMessage());
	}
}
?>