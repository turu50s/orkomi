<?php
//月次・年次処理 
$today = getdate();

$yy    = '2013';
$mm    = '11';

monthly($yy,$mm);
print('<p>月次更新OK。</p>');
    
// 月次処理
function monthly($yy,$mm) {
    // 日報ＤＢ集計
    
    try {
        $db = new PDO('sqlite2:../SQLiteManager-1.2.0/orikomi.sqlite2');
        $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        $sql = "SELECT tenpo,year,month,sum(money),sum(number) FROM nippo WHERE year = '$yy' AND month = '$mm' GROUP BY tenpo,year,month";
        
        $rs=$db->query($sql);
        $row = $rs->fetchAll();
        unset($rs);
        foreach ($row as $rec) {
            //$date_j  = $yy.'/'.$mm;
            $tenpo   = $rec['tenpo'];
            $month   = $rec['year'].'/'.$rec['month'];
            
            $results = $rec['sum(money)'];
            $number  = $rec['sum(number)'];
            $sql = "INSERT INTO jisseki(tenpo,month,results,number) VALUES('$tenpo','$month','$results','$number')";
            $db->exec($sql);
        }
        
        del_nippo($yy,$mm);
    } catch (PDOException $e) {
        die('error: '.$e->getMessage());
    }
}       

function del_nippo($yy,$mm) {
    try {
        $db = new PDO('sqlite2:../SQLiteManager-1.2.0/orikomi.sqlite2');
        $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        //$sql = "DROP TABLE nippo";
        $sql = "DELETE FROM nippo WHERE year = '$yy' AND month = '$mm'";
        $db->exec($sql);
        $db->exec('VACUUM');
        
        //$sql = "CREATE TABLE nippo(id INTEGER NOT NULL PRIMARY KEY, tenpo TEXT(6), date TEXT(10), money INTEGER(8), number INTEGER(4))";
        //$db->exec($sql);
    } catch (PDOException $e) {
        die('error: '.$e->getMessage());
    }
}

?>