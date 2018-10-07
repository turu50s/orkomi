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
        Image_Graph::factory('title',array('全店',10)),
        Image_Graph::vertical(
            $Plotarea = Image_Graph::factory('plotarea'),
            $Legend   = Image_Graph::factory('legend'),
            80
        ),
        5
    )
);
$Legend->setPlotarea($Plotarea);
//
$Dataset =& Image_Graph::factory("dataset");
$Dataset->addPoint("前年", $_SESSION['kako_z']);
$Dataset->addPoint("予算", $_SESSION['yosan_z']);
$Dataset->addPoint("実績", $_SESSION['jisseki_z']);

//
$Plotarea =& $Graph->addNew("plotarea");

//
//$Plotarea->addNew("title", "Zenten");

//
$Plot =& $Plotarea->addNew("bar", $Dataset);

//
$Plot->setFillColor("yellow");

//
$AxisY =& $Plotarea->getAxis(IMAGE_GRAPH_AXIS_Y);
$AxisY->forceMinimum(5000); // Y軸の最少値を設定
$AxisY->setLabelInterval(2500);

//
$Font =& $Graph->addNew('font', '../font/ipag.ttf');
$Font->setSize(10);
$Graph->setFont($Font);

//
$Graph->done();

?> 