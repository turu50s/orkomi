<?php
$path = '.' .PATH_SEPARATOR. '../Classes/';
ini_set('include_path',get_include_path() .PATH_SEPARATOR. $path);

require_once('PHPExcel.php');
require_once('PHPExcel/IOFactory.php');

//$excel = new PHPExcel();
// read template xls file
$reader = PHPExcel_IOFactory::createReader('Excel5');  
$excel = $reader->load("./template/jisseki.xls");
// set active sheet
$excel->setActiveSheetIndex(0);
$sheet = $excel->getActiveSheet();

//
$sheet->getDefaultStyle()->getFont()->setName('ＭＳ Ｐゴシック');  
//
$sheet->getDefaultStyle()->getFont()->setSize(12);  

//

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
}
//print(mb_convert_encoding($tenpo_h,'EUC-JP',auto).'/'.$today['year'].'/'.$today['mon']);

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

// 今期実績データ抽出
try {
    //$db = new PDO('sqlite2:../SQLiteManager-1.2.0/orikomi.sqlite2');
    $db = new PDO($dsn, $user, $password);
	
	$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    $sql = "SELECT * FROM jisseki  ORDER BY id ASC";

    $rs=$db->query($sql);
    $row = $rs->fetchAll();

    foreach ($row as $rec) {
       $date_w = explode("/",$rec['month']);
       if ($date_w[1] < 4) {
            $date = $date_w[1] + 12;
       } else {
            $date = $date_w[1];
       }
       switch ($rec['tenpo']){
          case 'seibu':
            $data[$date-4][0][0] = $rec['results'];
            $data[$date-4][0][1] = $rec['number'];
            break;
          case 'chuo':
            $data[$date-4][1][0] = $rec['results'];
            $data[$date-4][1][1] = $rec['number'];
            break;
          case 'masuo':
            $data[$date-4][2][0] = $rec['results'];
            $data[$date-4][2][1] = $rec['number'];
            break;
       }
    }
} catch (PDOException $e) {
    die('error: '.$e->getMessage());
}
$i = 5;
foreach ($data as $line) {
	$sheet->setCellValueByColumnAndRow(1, $i,$line[0][1]);
    $sheet->setCellValueByColumnAndRow(2, $i,$line[0][0]);
    $sheet->setCellValueByColumnAndRow(3, $i,$line[1][1]);
    $sheet->setCellValueByColumnAndRow(4, $i,$line[1][0]);
    $sheet->setCellValueByColumnAndRow(5, $i,$line[2][1]);
    $sheet->setCellValueByColumnAndRow(6, $i,$line[2][0]);
    $i++;
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