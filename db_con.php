<html>
<head><title>PHP TEST</title></head>
<body>

<?php
$path = '.' .PATH_SEPARATOR. '../PEAR';
// set_include_path(get_include_path() .PATH_SEPARATOR. $path);
ini_set('include_path',get_include_path() .PATH_SEPARATOR. $path);

require_once 'DB.php';

$dsn = array(
    'phptype'  => 'sqlite',
    'database' => '../SQLiteManager-1.2.0/orikomi.sqlite2',
);

$db = DB::connect($dsn);
if (PEAR::isError($db)) {
    die($db->getMessage());
}

$res =& $db->query('SELECT * from user');
if (PEAR::isError($res)) {
    die($res->getMessage());
}

while ($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)) {
    print($row['username'].' : ');
    print($row['password'].' / ');
    print($row['section'].' / ');
    print($row['level'].'<br>');
}


print('接続に成功しました');

$db->disconnect();

?>

</body>
</html>
