<?php
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

// ・ｽﾕ･・ｽ・ｽ・ｽ・ｽ  
$sheet->getDefaultStyle()->getFont()->setName('ＭＳ Ｐゴシック');  
// ・ｽﾕ･・ｽ・ｽ・ｽﾈ･・ｽ・ｽ・ｽ・ｽ・ｽ  
$sheet->getDefaultStyle()->getFont()->setSize(12);  

// SESSION・ｽﾑｿ・ｽ・ｽ・ｽ・ｽ・ｽ

session_start();
//$tenpo = $_SESSION['tenpo'];
//$today = $_SESSION['date'];
$tenpo = 'masuo';
$today = getdate();

switch ($tenpo) {
    case 'seibu':
        $tenpo_h = '柏西部店';
        break;
    case 'chuo':
        $tenpo_h = '柏中央店';
        break;
    case 'masuo':
        $tenpo_h = '柏増尾店';
        break;
}
//print(mb_convert_encoding($tenpo_h,'EUC-JP',auto).'/'.$today['year'].'/'.$today['mon']);

$yy    = $today['year'];
$mm    = $today['mon'];
//print($tenpo.'/'.$today['mon']);
$date_o = $yy.'/'.$mm;
$sheet->setCellValue('C1', mb_convert_encoding($tenpo_h,'UTF-8',auto));
$sheet->setCellValue('F1', $date_o);

// ﾍｽ・ｽ・ｽDB・ｽ・ｽ・ｽ・ｽ・ｽ
$date_o = $yy.'/'.$mm;
$sheet->setCellValue('C1', mb_convert_encoding($tenpo_h,'UTF-8',auto));
$sheet->setCellValue('F1', $date_o);

try {
    $db = new PDO('sqlite2:../SQLiteManager-1.2.0/orikomi.sqlite2');
    $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    
    $sql = "SELECT * FROM yosan WHERE month = '$date_o' AND tenpo = '$tenpo'";
    $rs=$db->query($sql);
    $row = $rs->fetch(PDO::FETCH_ASSOC);
    $budget = $row['budget'] * 1000;
} catch (PDOException $e) {
    die('error: '.$e->getMessage());
}
$sheet->setCellValue('D2', $budget);
// ・ｽ・ｽ・ｽ・ｽDB・ｽ・ｽ・ｽ
try {
    
    $sql = "SELECT * FROM nippo WHERE tenpo = '$tenpo' ORDER BY id ASC";
    $rs=$db->query($sql);
    $week_list = array('日','月','火','水','木','金','土');
    $i = 5;
    while ($row = $rs->fetch(PDO::FETCH_ASSOC)) {
        $date = explode("/",$row['date']);
        $sheet->setCellValueByColumnAndRow(0, $i, $date[2]);
        
        $sheet->setCellValueByColumnAndRow(2, $i, $row['number']);
        $sheet->setCellValueByColumnAndRow(3, $i,$row['money']);
        
        $week  = date('w',strtotime($row['date']));
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