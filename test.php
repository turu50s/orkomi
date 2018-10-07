<?php
//print($_POST['seibu1'].' '.$_POST['chuo1'].' '.$_POST['masuo1']."<br>");
//for ($i=1; $i < 2; $i++) {
//    $$seibu = 'seibu' . $i;
//}
//$$seibu = $_POST['seibu1'];
//print($$seibu."<br>");

for ($i = 0; $i < $_POST['day']; $i++) {
    $j++;
    $tenpo1 = 'seibu' . $j;
    $tenpo2 = 'chuo'  . $j;
    $tenpo3 = 'masuo' . $j;
    
    $seibu[$i] = $_POST[$tenpo1];
    $chuo[$i]  = $_POST[$tenpo2];
    $masuo[$i] = $_POST[$tenpo3];
       
    print(($i+1).': '.$seibu[$i].' '.$chuo[$i].' '.$masuo[$i]."<br>");
}

$tenpo_l = array('seibu','chuo','masuo');
$seibu =1; $chuo = 2; $masuo = 3;
foreach ($tenpo_l as $tenpo_i) {
	
	print $$tenpo_i."<br>";
}
$date_d = $_POST['year'].'/'.$_POST['month'].'/'.$_POST['day'].'////<br>';
print($date_d);
$tenpo_l = array('seibu','chuo','masuo');
foreach ($tenpo_l as $tenpo_i) {
        for ($i = 0; $i < $_POST['day']; $i++) {
            $date_up = $_POST['year'].'/'.$_POST['month'].'/'.($i+1);
            switch ($tenpo_i) {
                case seibu:
                    $tenpo_t = 'À¾Éô';
                    break;
                case chuo:
                    $tenpo_t = 'Ãæ±û';
                    break;
                case masuo:
                    $tenpo_t = 'ÁýÈø';
                    break;
            }
            print($date_up.' - '.$tenpo_t.'/'."${$tenpo_i}[$i]".' - '.${$tenpo_i}[1].'<br>');
        }
}
?>