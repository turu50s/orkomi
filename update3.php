<?php
// 半角数字だけかチェック
$pat = "^[0-9]+$";

$day = $_POST['day'];
$tenpo_l = array('seibu','chuo','masuo');
session_start();
$_SESSION['err'] = '';
$j = 0;
for ($i = 0; $i < $_POST['day']; $i++) {
    $j++;
    $tenpo1   = 'seibu' . $j;
    $tenpo1_n = 'seibu_n' . $j;
    $tenpo2   = 'chuo' . $j;
    $tenpo2_n = 'chuo_n' . $j;
    $tenpo3   = 'masuo' . $j;
    $tenpo3_n = 'masuo_n' . $j;
    
    
       $seibu[$i]   = $_POST[$tenpo1];
       $chuo[$i]    = $_POST[$tenpo2];
       $masuo[$i]   = $_POST[$tenpo3];
       $seibu_n[$i] = $_POST[$tenpo1_n];
       $chuo_n[$i]  = $_POST[$tenpo2_n];
       $masuo_n[$i] = $_POST[$tenpo3_n];
       
       print($seibu[$i].'-'.$seibu_n[$i].'/'.$chuo[$i].'-'.$chuo_n[$i].'/'.$masuo[$i].'-'.$masuo_n[$i].'<br>');
  
   
}
print($_POST['day'].'-'.$_POST['month1'].'-'.$_POST['year']);
?>