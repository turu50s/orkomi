<?php
ini_set( "display_errors", "Off");

$path = '.' .PATH_SEPARATOR. '../PEAR';

ini_set('include_path',get_include_path() .PATH_SEPARATOR. $path);

require_once ("Image/Graph.php");

session_start();

//Image_Graph
$Graph =& Image_Graph::factory("graph", array(400,300));

$Graph->add(
    Image_Graph::vertical(
        Image_Graph::factory('title',array('各店',12)),
        Image_Graph::vertical(
            $Plotarea = Image_Graph::factory('plotarea'),
            $Legend   = Image_Graph::factory('legend'),
            90
        ),
        5
    )
);
$Legend->setPlotarea($Plotarea);

//
$Dataset1 =& Image_Graph::factory("dataset");
$Dataset1->addPoint('南部', $_SESSION['seibu_o']);
$Dataset1->addPoint('中央', $_SESSION['chuo_o']);
$Dataset1->addPoint('増尾', $_SESSION['masuo_o']);

$Dataset2 =& Image_Graph::factory("dataset");
$Dataset2->addPoint('南部', $_SESSION['seibu_yo']);
$Dataset2->addPoint('中央', $_SESSION['chuo_yo']);
$Dataset2->addPoint('増尾', $_SESSION['masuo_yo']);

$Dataset3 =& Image_Graph::factory("dataset");
$Dataset3->addPoint('南部', $_SESSION['seibu_j']);
$Dataset3->addPoint('中央', $_SESSION['chuo_j']);
$Dataset3->addPoint('増尾', $_SESSION['masuo_j']);

$Dataset1->setName('前年');
$Dataset2->setName('予算');
$Dataset3->setName('実績');

$Datasets = array($Dataset1, $Dataset2, $Dataset3);

//
// $plotarea =& $graph->addNew("plotarea");

//
// $plotarea->addNew("title", "kakuten");

//
//$Plot1 =& $Plotarea->addNew('bar', $Datasets[0]);
//$Plot2 =& $Plotarea->addNew('bar', $Datasets[1]);
//$Plot3 =& $Plotarea->addNew('bar', $Datasets[2]);
$Plot =& $Plotarea->addNew("bar", array(&$Datasets));

//
//$Fill =& Image_Graph::factory('Image_Graph_Fill_Array');
//$Fill->addColor('red', 'S');
//$Fill->addColor('blue', 'C');
//$Fill->addColor('yellow', 'M');
//$Plot->setFillStyle($fill);

//$Plot1->setFillColor("#00FF00");
//$Plot2->setFillColor("#00FFFF");
//$Plot3->setFillColor("#FFFF00");
//$Plot->setFillColor("#00FF00");

$FillArray =& Image_Graph::factory('Image_Graph_Fill_Array');
$FillArray->addColor('green');
$FillArray->addColor('blue');
$FillArray->addColor('yellow');

$Plot->setFillStyle($FillArray);

$Font =& $Graph->addNew('font', '../font/ipag.ttf');
$Font->setSize(12);
$Graph->setFont($Font);

$Graph->done();

?>
