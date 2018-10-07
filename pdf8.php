<?php
ini_set( "display_errors", "off");

// StrictStandarsのワーニングを消す。
ini_set('error_reporting', E_ALL & ~E_STRICT);

$path = '.' .PATH_SEPARATOR. '../PEAR';

ini_set('include_path',get_include_path() .PATH_SEPARATOR. $path);

require_once('japanese.php');

class MyPdf_Japanese extends PDF_Japanese {
	function Footer() {
		$this->setY(-20);
		$this->setFont('SJIS','B',10);
		$this->cell(0,10,'Page'.$this->pageNo().'/{nb}','T',0,'C');
	}
    function Header() {
        $this->SetFont('Arial','',10);
        $this->Cell(120);
        
        $this->Cell(30,10,date("Y/m/d"),0,0,'R');
        $this->Ln(20);
    }
}

// 折込日報データ抽出
$today = getdate();
$yy    = $today['year'];
$mm    = $today['mon'];
$dd    = $today['mday'];
if (empty($_POST['month'])) {
    $_POST['month'] = $mm;
}

// 折込データベース接続
$dsn = 'mysql:dbname=sddb0040128103;host=sddb0040128103.cgidb';
$user = 'sddbMTgzOTIz';
$password = 'i8h4vf5U';

// nippo data check!
try {
	$db = new PDO($dsn, $user, $password);
	
	$mm_prev = date('m', strtotime(date('Y-m-1').' -1 month'));
	$yy_prev = date('Y', strtotime(date('Y-m-1').' -1 month'));
	
	$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	$sql = "SELECT count(*) FROM nippo WHERE year = '$yy_prev' AND month = '$mm_prev' GROUP BY tenpo,year,month";
	$rs=$db->query($sql);
	$count = $rs->fetchColumn();
	
	if ($count > 0) {
		$mm = $mm_prev;
		$_POST['month'] = $mm;
		$yy = $yy_prev;
		$_POST['year'] = $yy;
	}

} catch (PDOException $e) {
    die('error: '.$e->getMessage());
}

//$yymm = $yy.'/'.$_POST['month'];
$mon = $_POST['month'];

try {
    //$db = new PDO('sqlite2:../SQLiteManager-1.2.0/orikomi.sqlite2');
    $db = new PDO($dsn, $user, $password);
	
	$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    $sql = "SELECT * FROM nippo8  WHERE year = '$yy' AND month = '$mon' ORDER BY year,month,day ASC";

    $rs=$db->query($sql);
    $row = $rs->fetchAll();

    foreach ($row as $rec) {
        $date = $rec['day'];
        
        $data[$date-1][0] = $rec['money'];
        $data[$date-1][1] = $rec['number'];
        $data[$date-1][2] = $rec['day'];
       
    }
} catch (PDOException $e) {
    die('error: '.$e->getMessage());
}

// 折込日報表作成
$i = 0;
$sum_hachi  = 0;
$sum_line = array();

foreach ($data as $line) {
    
        // 5日毎の集計
        $sum_hachi  += $line[0];
        if (($line[2] % 5  == 0) && ($line[2] != 30)) {
            
            $sum_line[2]  = $line[2];
            $sum_line[0]  = $sum_hachi;
            
        }
        if ($line[2] == date("d",mktime(0,0,0,$mm+1,0,$yy))) {
            $sum_line[2]  = $line[2];
            $sum_line[0]  = $sum_hachi;
            
        }
}
$today = getdate();
// PDF file output
ob_start();
$pdf8 = new MyPdf_Japanese('P','mm','A4');
$pdf8->AddSJISFont();
$pdf8->addPage();
$pdf8->setFont('SJIS','B','14');

$mon = $_POST['year'] . '/' . $_POST['month'] .'/'. $sum_line[2];
$pdf8->setXY(40,30);
$pdf8->cell(60,12,mb_convert_encoding('小溝会計事務所　御中','SJIS','auto'),'B',1,'C');
$pdf8->ln();
$pdf8->setXY(50,60);
$pdf8->write(12,mb_convert_encoding('何時もお世話になって居ります。','SJIS','auto'));
$pdf8->ln();
$pdf8->setXY(50,70);
$pdf8->write(12,mb_convert_encoding('折込金額を送ります。','SJIS','auto'));
$pdf8->ln();
$pdf8->setXY(55,100);
$pdf8->write(12,$mon.mb_convert_encoding(' 折込合計','SJIS','auto'));
$pdf8->ln();
$pdf8->setXY(55,110);
$pdf8->cell(45,12,mb_convert_encoding('店舗名','SJIS','auto'),1,0,'C');
//$pdf8->cell(25,12,mb_convert_encoding(枚数','SJIS','auto'),1,0,'C');
$pdf8->cell(60,12,mb_convert_encoding('金額','SJIS','auto'),1,0,'C');
$pdf8->ln();
$pdf8->setFont('SJIS','',14);

//$sum['number'] = $seibu['number'] + $chuo['number'] + $masuo['number'];
//$sum['results'] = $seibu['results'] + $chuo['results'] + $masuo['results'];

$pdf8->setX(55);
$pdf8->cell(45,12,mb_convert_encoding('松戸八ケ崎','SJIS','auto'),1,0,'C');
$pdf8->cell(60,12,number_format($sum_line[0]).mb_convert_encoding('円','SJIS','auto'),1,0,'R');
$pdf8->ln();

$pdf8->setXY(-150,-50);
$pdf8->cell(100,10,mb_convert_encoding('常葉総合サービス(株)','SJIS','auto'),0,0,'R');
$pdf8->ln();

$pdf8->AliasNbPages();
ob_end_clean();
$pdf8->output();
exit;
?>
