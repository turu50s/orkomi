<?php
try {
	$db = new PDO('sqlite2:../SQLiteManager-1.2.0/orikomi.sqlite2');
	$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	$rs = $db->query('SELECT * FROM USER');
	for ($i=0;$i<$rs->columnCount();$i++) {
		$data = $rs->getColumnMeta($i);
		print($data['name']);
		print($data['native_type']);
		print($data['len']);
	}
} catch (PDOException $e) {
	print('err : ' . $e->getMessage());
}
?>
