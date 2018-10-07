<?php
try {
	$db = new PDO('sqlite2:../SQLiteManager-1.2.0/orikomi.sqlite2');
	$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	$rs=$db->query('SELECT * FROM USER');
	while ($row = $rs->fetch(PDO::FETCH_ASSOC)) {
		print($row['username'].' + ');
		print($row['password'].' + ');
		print($row['section'].' + ');
		print($row['level']."<br>");
	}
} catch (PDOException $e) {
	$file = fopen('err.log','a');
	flock($file,LOCK_EX);
	fwrite($file,$e->getMessage()."\n");
	flock($file,LOCK_UN);
	fclose($file);
	die('error: '.$e->getMessage());
}
?>
