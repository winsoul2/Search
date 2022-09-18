<?php
if(@$_GET['download']!="") {
    header("Content-type: application/vnd.ms-excel;charset=utf-8;");
    header("Content-Disposition: attachment; filename=export.xls");
    date_default_timezone_set('Asia/Bangkok');
}

$param = '';
if(!empty($_GET)){
    foreach(@$_GET as $key => $value){

        if(is_array($value[$_GET['report']])){
            $param .= $key.'=';
            foreach ($value[$_GET['report']] as $k => $v) {
                $param .= $v;
                if($k < (sizeof($value[$_GET['report']])-1)){
                    $param .= ',';
                }
            }
            $param .= '&';
        }else{
            $param .= $key.'='.$value.'&';
        }
    }
}
$i = 1;
?>
<!--			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />-->
<style>
    .table-view>thead, .table-view>thead>tr>td, .table-view>thead>tr>th {
        font-size: 16px;
    }
    .table {
        color: #000;
    }
    .num {
        mso-number-format:General;
    }
    .text{
        mso-number-format:"\@";/*force text*/
    }
    .text-center{
        text-align: center;
    }
    .text-left{
        text-align: left;
    }
    .table_title{
        font-family: AngsanaUPC, MS Sans Serif;
        font-size: 22px;
        font-weight: bold;
        text-align:center;
    }
    .table_header_top{
        font-family: AngsanaUPC, MS Sans Serif;
        font-size: 19px;
        font-weight: bold;
        text-align:center;
        border-top: thin solid black;
        border-left: thin solid black;
        border-right: thin solid black;
    }
    .table_header_mid{
        font-family: AngsanaUPC, MS Sans Serif;
        font-size: 19px;
        font-weight: bold;
        text-align:center;
        border-left: thin solid black;
        border-right: thin solid black;
    }
    .table_header_bot{
        font-family: AngsanaUPC, MS Sans Serif;
        font-size: 19px;
        font-weight: bold;
        text-align:center;
        border-bottom: thin solid black;
        border-left: thin solid black;
        border-right: thin solid black;
    }
    .table_header_bot2{
        font-family: AngsanaUPC, MS Sans Serif;
        font-size: 19px;
        font-weight: bold;
        text-align:center;
        border: thin solid black;
    }
    .table_body{
        font-family: AngsanaUPC, MS Sans Serif;
        font-size: 21px;
        border: thin solid black;
    }
    .table_body_center{
        font-family: AngsanaUPC, MS Sans Serif;
        font-size: 21px;
        border: thin solid black;
        text-align:center;
    }
    .table_body_right{
        font-family: AngsanaUPC, MS Sans Serif;
        font-size: 21px;
        border: thin solid black;
        text-align:right;
    }
</style>
<div style="width: 1500px;" class="page-break">
<div class="panel panel-body" style="padding-top:20px !important;height: 100%;min-height: 1000px;">
<table style="width: 100%;">
    <tr>
        <?php if(@$_GET['download']==""){ ?>
            <td style="width:100px;vertical-align: top;">
                <img src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.$_SESSION['COOP_IMG']); ?>" alt="Logo" style="height: 80px;" />
            </td>
        <?php } ?>
            <td class="text-center" style="text-align: center;" <?php echo @$_GET['download']!=""? "colspan='17'":"colspan='2'"?>>
                <h3 class="<?php echo @$_GET['download']==""?"title_view":"table_title" ?>"><?php echo @$_SESSION['COOP_NAME'];?></h3>
                <h3 class="<?php echo @$_GET['download']==""?"title_view":"table_title" ?>">รายงานคำขอกู้ฉุกเฉินระหว่างวันที่ <?php echo $start_date.' - '.$end_date; ?></h3>
            </td>
        <?php if(@$_GET['download']==""){ ?>
            <td style="width:100px;vertical-align: top;" class="text-right">
                <?php if($i == '1'){?>
                    <a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
                    <a class="no_print" target="_blank" onclick="goto()">
                        <button class="btn btn-perview btn-after-input" type="button"><span class="icon icon icon-file-excel-o" aria-hidden="true"></span></button>
                    </a>
                <?php } ?>
            </td>
        <?php } ?>
    </tr>
</table>
<table class="table table-view table-center">
    <thead>
        <tr>
            <th class="<?php echo @$_GET['download']!=""? "table_header_top":""?>" rowspan="2">ลำดับที่</th>
            <th class="<?php echo @$_GET['download']!=""? "table_header_top":""?>" rowspan="2">วันที่กู้</th>
            <th class="<?php echo @$_GET['download']!=""? "table_header_top":""?>" rowspan="2">เลขที่สัญญา</th>
            <th class="<?php echo @$_GET['download']!=""? "table_header_top":""?>" rowspan="2">ชื่อ - นามสกุลผู้กู้</th>
            <th class="<?php echo @$_GET['download']!=""? "table_header_top":""?>" rowspan="2">เลขที่สมาชิก</th>
            <th class="<?php echo @$_GET['download']!=""? "table_header_top":""?>" rowspan="2">หน่วยงาน</th>
            <th class="<?php echo @$_GET['download']!=""? "table_header_top":""?>" rowspan="2" width="120px">เงินเดือน</th>
            <th class="<?php echo @$_GET['download']!=""? "table_header_top":""?>" rowspan="2">จำนวนเงินกู้</th>
            <th class="<?php echo @$_GET['download']!=""? "table_header_top":""?>" rowspan="2">จำนวนงวด</th>
            <th class="<?php echo @$_GET['download']!=""? "table_header_top":""?>" rowspan="2">ชำระงวดละ</th>
            <th class="<?php echo @$_GET['download']!=""? "table_header_top":""?>" rowspan="2">ตั้งแต่</th>
            <th class="<?php echo @$_GET['download']!=""? "table_header_top":""?>" rowspan="2">จนถึง</th>
            <th class="<?php echo @$_GET['download']!=""? "table_header_top":""?>" colspan="3">ผู้ค้ำประกัน</th>
            <th class="<?php echo @$_GET['download']!=""? "table_header_top":""?>" rowspan="2">ทางใช้ประโยชน์</th>
            <th class="<?php echo @$_GET['download']!=""? "table_header_top":""?>" rowspan="2">ลำดับคำขอ</th>
        </tr>
        <tr>
            <th class="<?php echo @$_GET['download']!=""? "table_header_top":""?>">เลขที่สมาชิก</th>
            <th class="<?php echo @$_GET['download']!=""? "table_header_top":""?>">ชื่อ - นามสกุล</th>
            <th class="<?php echo @$_GET['download']!=""? "table_header_top":""?>">สังกัด</th>

        </tr>

    </thead>
    <tbody>
    <?php
    $loan_amount_all_total = 0;
    $paid_per_month_all_total = 0;
    $num_loan_all_total = 0;
    foreach ($datas as $loan_date => $data) {
        $loan_amount_total = 0;
        $paid_per_month_total = 0;
        foreach ($data as $key => $value) {
            $i=0;
            $loan_amount_total += $value['loan_amount'];
            $paid_per_month_total += $value['total_paid_per_month'];
            $loan_amount_all_total += $value['loan_amount'];
            $paid_per_month_all_total += $value['total_paid_per_month'];
            $num_loan_all_total++;
            foreach ($value['guarantee']['person_id'] as $order => $guarantee) {
                $i++;
                $date_start_period = $this->center_function->ConvertToThaiDateMMYY($value['date_start_period']);
                $max_date_period = $this->center_function->ConvertToThaiDateMMYY($value['max_date_period']);
                $show_date = $this->center_function->mydate2date($loan_date);
                if($i == '1'){
                ?>
                <tr>
                    <td class="<?php echo @$_GET['download']!=""? "table_body_center":""?>"><?php echo $key+1; ?></td>
                    <td class="<?php echo @$_GET['download']!=""? "table_body_center":""?>"><?php echo $value['show_date']; ?></td>
                    <td class="<?php echo @$_GET['download']!=""? "table_body_center":""?>"><?php echo $value['contract_number']; ?></td>
                    <td class="<?php echo @$_GET['download']!=""? "table_body":""?>"><?php echo $value['full_name']; ?></td>
                    <td class="<?php echo @$_GET['download']!=""? "table_body_center":""?>" style="mso-number-format:'@';"><?php echo $value['member_id']; ?></td>
                    <td class="<?php echo @$_GET['download']!=""? "table_body_center":""?>"><?php echo $value['mem_group_id']; ?></td>
                    <td class="<?php echo @$_GET['download']!=""? "table_body_right":""?>"><?php echo number_format($value['salary'], 2); ?></td>
                    <td class="<?php echo @$_GET['download']!=""? "table_body_right":""?>"><?php echo number_format($value['loan_amount'], 2); ?></td>
                    <td class="<?php echo @$_GET['download']!=""? "table_body_center":""?>"><?php echo number_format($value['period_amount'], 0); ?></td>
                    <td class="<?php echo @$_GET['download']!=""? "table_body_right":""?>"><?php echo number_format($value['total_paid_per_month'], 2); ?></td>
                    <td class="<?php echo @$_GET['download']!=""? "table_body_center":""?>"><?php echo $date_start_period?></td>
                    <td class="<?php echo @$_GET['download']!=""? "table_body_center":""?>"><?php echo $max_date_period; ?></td>
                    <?php if($value['guarantee_type'] == '2'){?>
                    <td class="<?php echo @$_GET['download']!=""? "table_body":""?>"><?php echo ''; ?></td>
                    <td class="<?php echo @$_GET['download']!=""? "table_body":""?>"><?php echo 'ใช้ทุนเรือนหุ้นค้ำประกัน'; ?></td>
                    <td class="<?php echo @$_GET['download']!=""? "table_body":""?>"><?php echo ''; ?></td>
                    <?php }else{ ?>
                    <td class="<?php echo @$_GET['download']!=""? "table_body_center":""?>" style="mso-number-format:'@';"><?php echo $guarantee; ?></td>
                    <td class="<?php echo @$_GET['download']!=""? "table_body":""?>"><?php echo $value['guarantee']['full_name'][$order]; ?></td>
                    <td class="<?php echo @$_GET['download']!=""? "table_body":""?>"><?php echo $value['guarantee']['mem_group_name'][$order]; ?></td>
                    <?php } ?>
                    <td class="<?php echo @$_GET['download']!=""? "table_body":""?>"><?php echo $value['loan_reason']; ?></td>
                    <td class="<?php echo @$_GET['download']!=""? "table_body":""?>"><?php echo $value['loan_id']; ?></td>
                </tr>
                <?php }else{ ?>
                <tr>
                    <td class="<?php echo @$_GET['download']!=""? "table_body":""?>" colspan="12"></td>
                    <td class="<?php echo @$_GET['download']!=""? "table_body_center":""?>" style="mso-number-format:'@';"><?php echo $guarantee; ?></td>
                    <td class="<?php echo @$_GET['download']!=""? "table_body":""?>"><?php echo $value['guarantee']['full_name'][$order]; ?></td>
                    <td class="<?php echo @$_GET['download']!=""? "table_body":""?>"><?php echo $value['guarantee']['mem_group_name'][$order]; ?></td>
                    <td class="<?php echo @$_GET['download']!=""? "table_body":""?>" colspan="2"></td>
                </tr>
                <?php }

            }
        } ?>
        <tr style="background: #eee">
            <td class="<?php echo @$_GET['download']!=""? "table_body":""?>" colspan = "2"></td>
            <td class="<?php echo @$_GET['download']!=""? "table_body_right":""?>">รวม</td>
            <td class="<?php echo @$_GET['download']!=""? "table_body_center":""?>"><?php echo $show_date;?></td>
            <td class="<?php echo @$_GET['download']!=""? "table_body_center":""?>"><?php echo $key+1;?></td>
            <td class="<?php echo @$_GET['download']!=""? "table_body":""?>"></td>
            <td class="<?php echo @$_GET['download']!=""? "table_body":""?>"></td>
            <td class="<?php echo @$_GET['download']!=""? "table_body_right":""?>"><?php echo number_format($loan_amount_total, 2);?></td>
            <td class="<?php echo @$_GET['download']!=""? "table_body":""?>"></td>
            <td class="<?php echo @$_GET['download']!=""? "table_body_right":""?>"><?php echo number_format($paid_per_month_total, 2);?></td>
            <td class="<?php echo @$_GET['download']!=""? "table_body":""?>" colspan="7"></td>
        </tr>
        <?php } ?>

    </tbody>
    <tfoot>
        <tr>
            <td class="<?php echo @$_GET['download']!=""? "table_body_right":""?>" colspan = "4">รวมระหว่างวันที่ <?php echo $start_date.' - '.$end_date; ?></td>
            <td class="<?php echo @$_GET['download']!=""? "table_body_center":""?>"><?php echo $num_loan_all_total;?></td>
            <td class="<?php echo @$_GET['download']!=""? "table_body":""?>"></td>
            <td class="<?php echo @$_GET['download']!=""? "table_body":""?>"></td>
            <td class="<?php echo @$_GET['download']!=""? "table_body_right":""?>"><?php echo number_format($loan_amount_all_total, 2);?></td>
            <td class="<?php echo @$_GET['download']!=""? "table_body":""?>"></td>
            <td class="<?php echo @$_GET['download']!=""? "table_body_right":""?>"><?php echo number_format($paid_per_month_all_total, 2);?></td>
            <td class="<?php echo @$_GET['download']!=""? "table_body":""?>" colspan="7"></td>
        </tr>
    </tfoot>
</table>


</div>
</div>

<?php if(@$_GET['download']==""){ ?>
    <script>
        function goto(){
            // console.log(window.location.href );
            window.open(window.location.href+'&download=1');
        }
    </script>
<?php } ?>