<?php
if ($_GET['debug'] != 'on') {
    $file_name = "โอนเงินหลักประกัน2561-24ธค61";
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
            border-top: none;
            border-bottom: none;
            border-left: thin solid black;
            border-right: thin solid black;
            border-top: none;
            border-bottom: none;
        }

        .table_body_right {
            border-left: thin solid black;
            border-right: thin solid black;
            border-collapse: collapse;
            border-top: none;
            border-bottom: none;
        }

        .table_body_left {
            border-left: thin solid black;
            border-right: thin solid black;
            border-collapse: collapse;
            border-top: none;
            border-bottom: none;
        }

        .table_footer {
            font-family: AngsanaUPC, MS Sans Serif;
            font-size: 21px;
            border: thin solid black;
            border-collapse: collapse;
            text-align: right;
        }

        .right-text{
            text-align: right;
        }

        .center-text{
            text-align: center;
        }

    </style>
</head>
<body>
<table class="table table-bordered">
    <thead>
        <tr>
            <th  class="table_header_top" style="vertical-align: middle;">เลข<br>ทะเบียน<br></th>
            <th  class="table_header_top" style="vertical-align: middle;">ชื่อ - สกุล<br><br></th>
            <th  class="table_header_top" style="vertical-align: middle;">เงินฝาก<br>หลักประกัน<br>กู้รวมหนี้</th>
            <th  class="table_header_top" style="vertical-align: middle;"><br>สถานะ<br></th>
            <th  class="table_header_top" style="vertical-align: middle;">เงินฝาก<br>หลักประกัน<br>กู้สามัญ</th>
            <th  class="table_header_top" style="vertical-align: middle;"><br>สถานะ<br></th>
            <th  class="table_header_top" style="vertical-align: middle;">เงินฝาก<br>หลักประกัน<br>กู้ฉุกเฉิน</th>
            <th  class="table_header_top" style="vertical-align: middle;"><br>สถานะ<br></th>
        </tr>
    </thead>
    <tbody>
        <?php if(isset($data) && $data){
            foreach ($data as $index => $row) {
                $space = "-&nbsp;&nbsp;&nbsp;&nbsp;";

                $txt1 =  empty($row['insure_28']) ?  $space  : " ไม่สามารถโอนได้ ";
                $txt2 =  empty($row['insure_27']) ?  $space  : " ไม่สามารถโอนได้ ";
                $txt3 =  empty($row['insure_26']) ?  $space  : " ไม่สามารถโอนได้ ";

                ?>
                <tr>
                    <td style="text-align: center" class="table_body"><?php echo $row['member_id']; ?></td>
                    <td class="table_body"><?php echo $row['fullname']; ?></td>
                    <td style="text-align: right" class="table_body"><?php echo $row['insure_28'] ? number_format($row['insure_28'], 2) : $space; ?></td>
                    <td style="text-align: center" class="table_body"><?php echo $row['tfd_insure_28'] ? 'โอนแล้วเมื่อ '.$this->center_function->ConvertToThaiDate($row['tfd_insure_28']) : $txt1; ?></td>
                    <td style="text-align: right" class="table_body"><?php echo $row['insure_27'] ? number_format($row['insure_27'], 2) : $space; ?></td>
                    <td style="text-align: center" class="table_body"><?php echo $row['tfd_insure_27'] ? 'โอนแล้วเมื่อ '.$this->center_function->ConvertToThaiDate($row['tfd_insure_27']) : $txt2; ?></td>
                    <td style="text-align: right" class="table_body"><?php echo $row['insure_26'] ? number_format($row['insure_26'], 2) : $space;  ?></td>
                    <td style="text-align: center" class="table_body"><?php echo $row['tfd_insure_26'] ? 'โอนแล้วเมื่อ '.$this->center_function->ConvertToThaiDate($row['tfd_insure_26']) : $txt3; ?></td>
                </tr>
        <?php
            }

            $sum_28 = array_sum(array_map(function($val){ return $val['insure_28'];}, $data));
            $sum_27 = array_sum(array_map(function($val){ return $val['insure_27'];}, $data));
            $sum_26 = array_sum(array_map(function($val){ return $val['insure_26'];}, $data));
            ?>
            <tr>
                <td colspan="2" style="border: thin solid black; text-align: center">รวม</td>
                <td style="border: thin solid black; text-align: right"><?php echo number_format($sum_28, 2, '.', ',');?></td>
                <td style="border: thin solid black; text-align: center"></td>
                <td style="border: thin solid black; text-align: right"><?php echo number_format($sum_27, 2, '.', ',');?></td>
                <td style="border: thin solid black; text-align: center"></td>
                <td style="border: thin solid black; text-align: right"><?php echo number_format($sum_26, 2, '.', ',');?></td>
                <td style="border: thin solid black; text-align: center"></td>
            </tr>
        <?php
        }
        ?>
    </tbody>
</table>
</body>
</html>