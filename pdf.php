<?php
ini_set( "display_errors", "Off");

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
session_start();
$today = getdate();

$pdf = new MyPdf_Japanese('P','mm','A4');
$pdf->AddSJISFont();
$pdf->addPage();
$pdf->setFont('SJIS','B','14');

$mon = $_SESSION['year'] . '/' . $_SESSION['month'] .'/'. $_SESSION['days'];

$pdf->setXY(40,30);
$pdf->cell(60,12,mb_convert_encoding('小溝会計事務所　御中','SJIS',auto),'B',1,'C');
$pdf->ln();
$pdf->setXY(50,60);
$pdf->write(12,mb_convert_encoding('何時もお世話になって居ります。','SJIS',auto));
$pdf->ln();
$pdf->setXY(50,70);
$pdf->write(12,mb_convert_encoding('各店別折込金額を送ります。','SJIS',auto));
$pdf->ln();
$pdf->setXY(55,100);
$pdf->write(12,$mon.mb_convert_encoding(' 折込合計','SJIS',auto));
$pdf->ln();
$pdf->setXY(55,110);
$pdf->cell(45,12,mb_convert_encoding('店舗名','SJIS',auto),1,0,'C');
//$pdf->cell(25,12,mb_convert_encoding(枚数','SJIS',auto),1,0,'C');
$pdf->cell(60,12,mb_convert_encoding('金額','SJIS',auto),1,0,'C');
$pdf->ln();
$pdf->setFont('SJIS','',14);

//$sum['number'] = $seibu['number'] + $chuo['number'] + $masuo['number'];
//$sum['results'] = $seibu['results'] + $chuo['results'] + $masuo['results'];

$pdf->setX(55);
$pdf->cell(45,12,mb_convert_encoding('柏南部店','SJIS',auto),1,0,'C');
//$pdf->cell(25,12,number_format($seibu['number']).mb_convert_encoding('枚','SJIS',auto),1,0,'R');
$pdf->cell(60,12,number_format($_SESSION['seibu']).mb_convert_encoding('円','SJIS',auto),1,0,'R');
$pdf->ln();
$pdf->setX(55);
$pdf->cell(45,12,mb_convert_encoding('柏中央店','SJIS',auto),1,0,'C');
//$pdf->cell(25,12,number_format($chuo['number']).mb_convert_encoding('枚','SJIS',auto),1,0,'R');
$pdf->cell(60,12,number_format($_SESSION['chuo']).mb_convert_encoding('円','SJIS',auto),1,0,'R');
$pdf->ln();
$pdf->setX(55);
$pdf->cell(45,12,mb_convert_encoding('柏増尾店','SJIS',auto),1,0,'C');
//$pdf->cell(25,12,number_format($masuo['number']).mb_convert_encoding('枚','SJIS',auto),1,0,'R');
$pdf->cell(60,12,number_format($_SESSION['masuo']).mb_convert_encoding('円','SJIS',auto),1,0,'R');
$pdf->ln();
$pdf->setX(55);
$pdf->cell(45,12,mb_convert_encoding('合計','SJIS',auto),1,0,'C');
//$pdf->cell(25,12,number_format($sum['number']).mb_convert_encoding('枚','SJIS',auto),1,0,'R');
$pdf->cell(60,12,number_format($_SESSION['zenten']).mb_convert_encoding('円','SJIS',auto),1,0,'R');

$pdf->setXY(-150,-50);
$pdf->cell(100,10,mb_convert_encoding('伸光堂千葉販売(株)','SJIS',auto),0,0,'R');
$pdf->ln();

$pdf->AliasNbPages();
$pdf->output();
exit;
?>
