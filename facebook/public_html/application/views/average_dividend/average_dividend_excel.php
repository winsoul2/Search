<?php

$yearfiscal =  isset($_GET['year']) ? date('Y')  + 543 : $_GET['year'] + 543;
$day = date('j');
$approve_date = isset($approve_date) && !empty($approve_date) ? explode(' ', $approve_date)[0] : date('Y-m-d') ;
$date = explode('-',$approve_date);
$year = $date[0] + 543;
$month = join('',explode('.',$month_short_arr[$date[1]]));
$day = $date[2];

$date_title = $yearfiscal."-".($day.$month.substr($year, 2));

if(isset($_GET['type']) && $_GET['type'] == 'transfer'){
    $file_name = "รายการโอนปันผลสำเร็จ".$date_title;
}else if(isset($_GET['type']) && $_GET['type'] == 'no_transfer'){
    $file_name = "รายการโอนปันผลไม่สำเร็จ".$date_title;
}else{
    $file_name = "การเงินปันผล เฉลี่ยคืน ประจำปี ".$date_title;
}

if ($_GET['debug'] != 'on') {

    header("Content-Disposition: attachment; filename=".$file_name.".xls");
    header("Content-type: application/vnd.ms-excel; charset=UTF-8");

}
date_default_timezone_set('Asia/Bangkok');
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        .num {
            mso-number-format: General;
        }

        .text {
            mso-number-format: "\@"; /*force text*/
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .table_title {
            font-family: AngsanaUPC, MS Sans Serif;
            font-size: 22px;
            font-weight: bold;
            text-align: center;
        }

        .table_title_right {
            font-family: AngsanaUPC, MS Sans Serif;
            font-size: 16px;
            font-weight: bold;
            text-align: right;
        }

        .table_header_top {
            font-family: AngsanaUPC, MS Sans Serif;
            font-size: 19px;
            font-weight: bold;
            text-align: center;
            border-top: thin solid black;
            border-left: thin solid black;
            border-right: thin solid black;
            border-bottom: thin solid black;
            border-collapse: collapse;
        }

        .table_header_mid {
            font-family: AngsanaUPC, MS Sans Serif;
            font-size: 19px;
            font-weight: bold;
            text-align: center;
            border-left: thin solid black;
            border-right: thin solid black;
        }

        .table_header_bot {
            font-family: AngsanaUPC, MS Sans Serif;
            font-size: 19px;
            font-weight: bold;
            text-align: center;
            border-bottom: thin solid black;
            border-left: thin solid black;
            border-right: thin solid black;
        }

        .table_header_bot2 {
            font-family: AngsanaUPC, MS Sans Serif;
            font-size: 19px;
            font-weight: bold;
            text-align: center;
            border: thin solid black;
        }

        .table_body {
            font-family: AngsanaUPC, MS Sans Serif;
            font-size: 21px;
            border: thin solid black;
            border-bottom: none;
            border-left: thin solid black;
            border-right: thin solid black;
            border-top: none;
            border-collapse: collapse;
        }

        .table_body_right {
            font-family: AngsanaUPC, MS Sans Serif;
            font-size: 21px;
            border: thin solid black;
            border-collapse: collapse;
            text-align: right;
        }

        .table_footer {
            font-family: AngsanaUPC, MS Sans Serif;
            font-size: 21px;
            border: thin solid black;
            border-collapse: collapse;
            text-align: right;
        }

    </style>
</head>
<body>
<?php
if (@$_GET['month'] != '' && @$_GET['year'] != '') {
    $day = '';
    $month = @$_GET['month'];
    $year = (@$_GET['year']);
    $title_date = " เดือน " . @$month_arr[$month] . " ปี " . (@$year);
} else {
    $day = '';
    $month = '';
    $year = (@$_GET['year']);
    $title_date = " ปี " . (@$year);
}

?>
<table class="table table-bordered">
    <tr>
        <th class="table_title" colspan="<?php echo (isset($_GET['year']) && isset($_GET['master_id'])) ? '19' : '8'; ?>">ประมาณการเงินปันผล เฉลี่ยคืน ประจำปี <?php echo $yearfiscal;?></th>
    </tr>
</table>
<table class="table table-bordered">
    <thead>
    <tr>
        <th rowspan="2" class="table_header_top" style="vertical-align: middle;">เลข<br>ทะเบียน<br></th>
        <th rowspan="2" class="table_header_top" style="vertical-align: middle;"><br>รหัสกลุ่ม<br></th>
        <th rowspan="2" class="table_header_top" style="vertical-align: middle;"><br>สังกัด<br></th>
        <th rowspan="2" class="table_header_top" style="vertical-align: middle;"><br>ชื่อ - สกุล<br></th>
        <th colspan="3" class="table_header_top" style="vertical-align: middle;">รายการปันผล เฉลี่ยคืน</th>

        <?php
        if(isset($_GET['year']) && isset($_GET['master_id'])) {
        ?>
            <th colspan="2" class="table_header_top" style="vertical-align: middle;">รายการหักปันผล เฉลี่ยคืน</th>
            <th rowspan="2"  class="table_header_top" style="vertical-align: middle;">
                ปันผลเฉลี่ยคืน<br>คงเหลือเข้า<br>บัญชีสีชมพู</th>
            <th rowspan="2" class="table_header_top" style="vertical-align: middle;">
                เงินของขวัญ<br>ปีใหม่<br>เข้าบัญชีชมพู<br>
            </th>
        <?php if(isset($_GET['type']) && $_GET['type'] == 'transfer'){ ?>
            <th rowspan="2" class="table_header_top" style="vertical-align: middle;">
                โอนปันผลเฉลี่ยคืน<br>สำเร็จเมื่อ<br><br>
            </th>
            <th rowspan="2" class="table_header_top" style="vertical-align: middle;">
                โอนเงินของขวัญ<br>สำเร็จเมื่อ<br><br>
            </th>
        <?php } ?>

        <?php
            }else{
        ?>
            <th rowspan="2" class="table_header_top" style="vertical-align: middle;">
                <br>เงินของขวัญ<br>
            </th>
            <th rowspan="2" class="table_header_top" style="vertical-align: middle;">
                <br>วิธีรับเงินปันผล เฉลี่ยคืน<br>
            </th>
        <?php } ?>
    </tr>
    <tr>
        <th class="table_header_top" style="vertical-align: middle;">ปันผล <br><?php echo $data[0]['divide_percent']; ?>
            %<br>&nbsp;
        </th>
        <th class="table_header_top" style="vertical-align: middle;">เฉลี่ยคืน
            <br><?php echo $data[0]['return_percent']; ?>%<br>&nbsp;
        </th>
        <th class="table_header_top" style="vertical-align: middle;">รวมปันผล<br>เฉลี่ยคืน</th>
        <?php if(isset($_GET['year']) && isset($_GET['master_id'])) {
            foreach ($type as $value) { ?>
                <th class="table_header_top" style="vertical-align: middle;"><?php echo $value; ?></th>
        <?php } ?>
            <th class="table_header_top" style="vertical-align: middle;">รวมหัก<br><br></th>

        <?php } ?>
    </tr>
    </thead>
    <tbody>
    <?php
    if (!empty($data)) {
        $deduct_all = [];
        $i = 0;
        foreach ($data as $key => $row) {
            $names = $row['prename_full'] . $row['firstname_th'] . " " . $row['lastname_th'];
            $i++;
            ?>
            <tr>
                <td class="table_body"
                    style="text-align: left;"><?php echo @$row['member_id']; ?></td>
                <td class="table_body"
                    style="text-align: center;"><?php echo @$row['mem_group_id']; ?></td>
                <td class="table_body"
                    style="text-align: left;min-width: 140px;"><?php echo @$row['mem_group_name']; ?></td>
                <td class="table_body"
                    style="text-align: left;min-width: 140px;"><?php echo @$names ?></td>
                <td class="table_body"
                    style="text-align: right;"><?php echo number_format(round($row['sum_dividend'], 2), 2); ?></td>
                <td class="table_body"
                    style="text-align: right;"><?php echo $row['sum_return'] <= 0 ? "-&nbsp;&nbsp;&nbsp;" : number_format(round($row['sum_return'], 2), 2); ?></td>
                <td class="table_body"
                    style="text-align: right;"><?php echo number_format($row['sum_dividend'] + $row['sum_return'], 2); ?></td>
                <?php
                if (isset($_GET['year']) && isset($_GET['master_id'])) {
                    $member_deduct = 0;
                    foreach ($type as $key => $value) {
                        echo '<td class="table_body" style="text-align: right">' . ($row[$key] <= 0 ? "-&nbsp;&nbsp;&nbsp;" : number_format($row[$key], 2)) . '</td>';
                        $member_deduct += $row[$key];
                        $deduct_all[$key] += $row[$key];
                    }
                    echo '<td class="table_body" style="text-align: right">' . ($member_deduct <= 0 ? "-&nbsp;&nbsp;&nbsp;" : number_format($member_deduct, 2)) . '</td>';
                    echo '<td class="table_body" style="text-align: right">' . number_format(($row['sum_dividend'] + $row['sum_return']) - $member_deduct, 2) . '</td>';
                }
                ?>
                <td class="table_body"
                    style="text-align: right;"><?php echo $row['gift_varchar'] <= 0 ? "-&nbsp;&nbsp;&nbsp;" : number_format($row['gift_varchar'], 2); ?></td>
                <?php if (isset($_GET['type']) && $_GET['type'] == 'transfer') { ?>
                    <td class="table_body"
                        style="text-align: center;min-width: 140px;"><?php echo $row['transfer_date'] ? date('d/m/Y H:i:s', strtotime($row['transfer_date'])) : " - "; ?></td>
                    <td class="table_body"
                        style="text-align: center;min-width: 140px;"><?php echo $row['transfer_gift_date'] ? date('d/m/Y H:i:s', strtotime($row['transfer_gift_date'])) : " - "; ?></td>
                <?php } ?>
<!--                <td class="table_body" style="text-align: right;min-width: 250px;">--><?php //echo $row['receive_name']; ?><!--</td>-->
            </tr>
            <?php

            $all_divi += round($row['sum_dividend'], 2);
            $all_return += round($row['sum_return'], 2);
            $all_gift_varchar += $row['gift_varchar'];
            $member_deduct_all += $member_deduct;
        }
        ?>
        <?php

        $all_return = array_sum(array_map(function($val){ return (double) number_format($val['sum_return'], 2,'.', ''); }, $data));
        $all_divi = array_sum(array_map(function($val){ return (double) number_format($val['sum_dividend'], 2,'.', ''); }, $data));
        ?>
        <tr class="foot-border">
            <td class="table_footer" style="text-align: center;font-weight:bold;" colspan="4">รวมทั้งหมด</td>
            <td class="table_footer" style="text-align: right;"><?php echo number_format(@$all_divi, 2); ?></td>
            <td class="table_footer" style="text-align: right;"><?php echo number_format(@$all_return, 2); ?></td>
            <td class="table_footer"
                style="text-align: right;"><?php echo number_format(@$all_divi + @$all_return, 2); ?></td>
            <?php
            if(isset($_GET['year']) && isset($_GET['master_id'])) {
                $member_deduct_all = 0;
                foreach ($type as $key => $value) {
                    echo '<td class="table_footer">' . ($deduct_all[$key] <= 0 ? "-&nbsp;&nbsp;&nbsp;" : number_format($deduct_all[$key], 2)) . '</td>';
                    $member_deduct_all += $deduct_all[$key];
                }
                echo '<td class="table_footer">' . number_format($member_deduct_all, 2) . '</td>';
                echo '<td class="table_footer" style="text-align: right;">' . number_format((@$all_divi + @$all_return) - @$member_deduct_all, 2) . '</td>';
            }
            ?>

            <td class="table_footer" style="text-align: right;"><?php echo number_format(@$all_gift_varchar, 2); ?></td>
            <?php if(isset($_GET['type']) && $_GET['type'] == 'transfer') {?>
                <td style="border-top: thin solid black; text-align: center;min-width: 140px;"></td>
            <?php } ?>
        </tr>
        <?php
    }
//    echo $i;
    ?>
    </tbody>
</table>
</body>
</html>