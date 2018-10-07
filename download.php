<?php
$file = "nippo.xls";
$file_length = filesize($file);
header("Content-Disposition: attachment; filename=$file");
header("Content-Length:$file_length");
header("Content-Type: application/octet-stream");
readfile ($file);
?>