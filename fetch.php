<?php
$today     = getdate();
$yy        = $today['year'];
$mm        = $today['mon'];
$dd        = $today['mday'];

$date_k = $yy.'/'.$mm.'/%';
try {
    $db = new PDO('sqlite2:../SQLiteManager-1.2.0/orikomi.sqlite2');
    $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    $sql = "SELECT * FROM nippo WHERE date LIKE '$date_k'";
//    $sql = "SELECT * FROM nippo";
    $rs=$db->query($sql);
    $row = $rs->fetchAll();

    foreach ($row as $rec) {
       print($rec['date'].' - '.$rec['tenpo'].' - '.$rec['money'].'<br>');
    }
} catch (PDOException $e) {
    die('error: '.$e->getMessage());
}
?>