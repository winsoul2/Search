<?php

//echo $date;exit;
$strFileName = $_SERVER["DOCUMENT_ROOT"] . PROJECTPATH . "/assets/document/coopmb1.txt";
//$strFileName = $_SERVER["DOCUMENT_ROOT"] . '/srusctsys' . "/assets/document/coopmb1.txt";
$file = $strFileName;
$txt = fopen($file, "w") or die("Unable to open file!");
//$txt = '';
$expoet_txt = array();
foreach($datas as $key => $data) {
    $member_date = "";
    if(!empty($data["member_date"])) {
        $date_arr = explode( '-', $data["member_date"]);
        $member_date = $date_arr[2]."".$date_arr["1"]."".($date_arr["0"]+543);
        if(!empty($data['req_resign_date'])){
            $date_arr = explode( '-', $data["req_resign_date"]);
            $req_resign_date = $date_arr[2]."".$date_arr["1"]."".($date_arr["0"]+543);
        }else{
            $req_resign_date = '';
        }
    }

    $address = "";

    if(@$data['c_address_no']) {
        $address .= @$data['c_address_no'];
    }
    if(@$data['c_address_moo']) {
        $address .= " หมู่ ".@$data['c_address_moo'];
    }
    if(@$data['c_address_village']) {
        $address .= @$data['c_address_village'];
    }
    if(@$data['c_address_road']) {
        $address .= " ถ.".@$data['c_address_road'];
    }
    if(@$data['c_address_soi']) {
        $address .= " ซ. ".@$data['c_address_soi'];
    }

    $txt_detail['1'] = '1020000625356';
    $txt_detail['2'] = $key + 1;
    $txt_detail['3'] = $year;
    $txt_detail['4'] = $data["id_card"];
    $txt_detail['5'] = $data["prename_full"];
    $txt_detail['6'] = $data["firstname_th"];
    $txt_detail['7'] = $data["lastname_th"];
    $txt_detail['8'] = !empty($data["nationality"]) ? $data["nationality"] : "TH";
    $txt_detail['9'] = number_format($data["share"],2, '.', '');
    $txt_detail['10'] = number_format($data["share_value"],2, '.', '');
    $txt_detail['11'] = $member_date;
    $txt_detail['12'] = !empty($data["mem_type_code"]) ? 2 : 1;
    $txt_detail['13'] = $req_resign_date;
    $txt_detail['14'] = $address;
    $txt_detail['15'] = $data['district_name'];
    $txt_detail['16'] = $data['amphur_name'];
    $txt_detail['17'] = $data['province_name'];

    $expoet_txt[] = implode('|', $txt_detail);
    foreach ($txt_detail as $txt_key => $txt_value){
        if($txt_key != '1'){
            echo '|';
//            $txt .= '|';
        }
        echo $txt_value;
//        $txt .= $txt_value;
    }
    echo "\n";
}
fwrite($txt, implode("\n", $expoet_txt    ));
fclose($txt);

header('Content-Description: File Transfer');
header('Content-Disposition: attachment; filename='.basename($file));
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($file));
header("Content-Type: text/plain");
readfile($file);

?>