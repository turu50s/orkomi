<?php
ini_set( "display_errors", "Off");

$path = '.' .PATH_SEPARATOR. '../Classes/';
ini_set('include_path',get_include_path() .PATH_SEPARATOR. $path);

require_once('PHPExcel.php');
require_once('PHPExcel/IOFactory.php');

//$excel = new PHPExcel();
// read template xls file
$reader = PHPExcel_IOFactory::createReader('Excel5');  
$excel = $reader->load("./template/nippo.xls");
// set active sheet
$excel->setActiveSheetIndex(0);
$sheet = $excel->getActiveSheet();

//
$sheet->getDefaultStyle()->getFont()->setName('ＭＳ Ｐゴシック');  
// 
$sheet->getDefaultStyle()->getFont()->setSize(12);  

// SESSION・

session_start();
$tenpo = $_SESSION['tenpo'];
$today = $_SESSION['date'];
//$tenpo = 'masuo';
//$today = getdate();

switch ($tenpo) {
    case 'seibu':
        $tenpo_h = '柏南部店';
        break;
    case 'chuo':
        $tenpo_h = '柏中央店';
        break;
    case 'masuo':
        $tenpo_h = '柏増尾店';
        break;
        case 'hachi':
        $tenpo_h = '八ヶ崎店';
        break;
}
//print(mb_convert_encoding($tenpo_h,'UTF-8',auto).'/'.$today['year'].'/'.$today['mon']);

$yy    = $today['year'];
$mm    = $today['mon'];
//print($tenpo.'/'.$today['mon']);
$date_o = $yy.'/'.$mm;
$sheet->setCellValue('C1', mb_convert_encoding($tenpo_h,'UTF-8',auto));
$sheet->setCellValue('F1', $date_o);

// 
$date_o = $yy.'/'.$mm;
$sheet->setCellValue('C1', mb_convert_encoding($tenpo_h,'UTF-8',auto));
$sheet->setCellValue('F1', $date_o);

// 折込データーベース接続
$dsn = 'mysql:dbname=sddb0040128103;host=sddb0040128103.cgidb';
$user = 'sddbMTgzOTIz';
$password = 'i8h4vf5U';

try {
    //$db = new PDO('sqlite2:../SQLiteManager-1.2.0/orikomi.sqlite2');
    $db = new PDO($dsn, $user, $password);
	
    $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    
    if ($tenpo == 'hachi') {
    	$sql = "SELECT * FROM yosan8 WHERE month = '$date_o'";
    } else {
    	$sql = "SELECT * FROM yosan WHERE month = '$date_o' AND tenpo = '$tenpo'";
    }
    
    $rs=$db->query($sql);
    $row = $rs->fetch(PDO::FETCH_ASSOC);
    $budget = $row['budget'] * 1000;
} catch (PDOException $e) {
    die('error: '.$e->getMessage());
}
$sheet->setCellValue('D2', $budget);
// 
try {
    if ($tenpo == 'hachi') {
    	$sql = "SELECT * FROM nippo8 WHERE year = '$yy' AND month = '$mm' ORDER BY year,month,day ASC";
    } else {
    	$sql = "SELECT * FROM nippo WHERE tenpo = '$tenpo' AND year = '$yy' AND month = '$mm' ORDER BY year,month,day ASC";
    }
    $rs=$db->query($sql);
    $week_list = array('日','月','火','水','木','金','土');
    $i = 5;
    while ($row = $rs->fetch(PDO::FETCH_ASSOC)) {
        
        $sheet->setCellValueByColumnAndRow(0, $i, $row['day']);
        
        $sheet->setCellValueByColumnAndRow(2, $i, $row['number']);
        $sheet->setCellValueByColumnAndRow(3, $i,$row['money']);
        $date  = $row['year'].'/'.$row['month'].'/'.$row['day'];
        $week  = date('w',strtotime($date));
        $yobi  = $week_list[$week];
        $sheet->setCellValueByColumnAndRow(1, $i, mb_convert_encoding($yobi,'UTF-8',auto));
        $i++;
    }
} catch (PDOException $e) {
    die('error: '.$e->getMessage());
}
//$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
//$writer->save("test.xls");

// File Download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="outfile.xls"');
header('Cache-Control: max-age=0');
$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
$writer->save('php://output');
exit;
?>