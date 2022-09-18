<?php
if(@$_GET['download']!=""){
    header("Content-type: application/vnd.ms-excel;charset=utf-8;");
    header("Content-Disposition: attachment; filename=export.xls");
    date_default_timezone_set('Asia/Bangkok');
}
?>
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
    @page { size: landscape;margin: 20px;}
</style>
<?php
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
$loan_type = $loan_type['loan_type'];
$loan_type = str_replace("เงินกู้","",$loan_type);

$date_start_txt = $start_date;
$date_end_txt = $end_date;
//echo $count_loan_reason;exit;
    ?>

    <div style="width: 1500px;" class="page-break">
        <div class="panel panel-body" style="padding-top:20px !important;height: 100%;min-height: 1000px;">
            <table style="width: 100%;">
                <tr>
                    <?php if(@$_GET['download']==""){ ?>
                    <td style="width:100px;vertical-align: top;">
                        <img src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.$_SESSION['COOP_IMG']); ?>" alt="Logo" style="height: 80px;" />
                    </td>
                    <?php } ?>
                    <td class="text-center" style="text-align: center;" <?php echo @$_GET['download']!=""? "colspan='".($count_loan_reason+2)."'":"colspan='2'"?>>
                        <h3 class="<?php echo @$_GET['download']==""?"title_view":"table_title" ?>"><?php echo @$_SESSION['COOP_NAME'];?></h3>
                        <h3 class="<?php echo @$_GET['download']==""?"title_view":"table_title" ?>"><?php echo "9.3 รายการคำขอกู้".$loan_type."ใหม่ สรุปวงเงินกู้และวัตถุประสงค์";?></h3>
                        <h3 class="<?php echo @$_GET['download']==""?"title_view":"table_title" ?>"><?php echo "ระหว่างวันที่ ".$date_start_txt." - ".$date_end_txt;?></h3>
                    </td>
                    <?php if(@$_GET['download']==""){ ?>
                    <td style="width:100px;vertical-align: top;" class="text-right">
                        <?php if($i == '1'){?>
                            <a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
<!--                            <a href="--><?php //echo base_url(PROJECTPATH.'/report_new_loan_request/coop_report_loan_emergent_excel?'.$param); ?><!--" class="no_print"><button class="btn btn-perview btn-after-input" type="button"><span>XLS</span></button></a>-->
                            <a class="no_print" target="_blank" onclick="goto()">
                                <button class="btn btn-perview btn-after-input" type="button"><span class="icon icon icon-file-excel-o" aria-hidden="true"></span></button>
                            </a>
                        <?php } ?>
                    </td>
                    <?php } ?>
                </tr>
                <tr>
                    <td colspan="3">
                        <h3 class="title_view">
                        </h3>
                    </td>
                </tr>
            </table>
            <table class="table table-view table-center">
                <thead>
                <tr>
                    <th class="<?php echo @$_GET['download']!=""? "table_header_top":""?>" colspan="1" style="vertical-align: middle;">วงเงินกู้</th>
                    <?php foreach ($loan_reason as $key => $item) { ?>
                        <th class="<?php echo @$_GET['download']!=""? "table_header_top":""?>" rowspan="1" style="vertical-align: middle;"><?php echo $item['loan_reason'];?></th>
                    <?php } ?>
                    <th class="<?php echo @$_GET['download']!=""? "table_header_top":""?>" rowspan="1" style="vertical-align: middle;">รวม</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $all_num_total = 0;
                $all_loan_amount_total = 0;
                foreach($data as $loan_amonth => $arr_loan_reason){
                    $num_total = 0;
                    $loan_amount_total = 0;
                ?>
                        <tr>
                            <td class="<?php echo @$_GET['download']!=""? "table_body":""?>" style="text-align: right;" colspan="1"><?php echo number_format($loan_amonth,2);?></td>
                            <?php foreach ($loan_reason as $reason_id => $item) { ?>
                                <td class="<?php echo @$_GET['download']!=""? "table_body":""?>" style="text-align: right;" colspan="1">
                                    <?php
                                    $num_loan = 0;
                                    foreach($arr_loan_reason as $key => $value) {
                                        if ($key == $reason_id) {
                                            $num_loan++;
                                            $num_total += $value['num_loan'];
                                            $loan_amount_total += $loan_amonth * $value['num_loan'];
                                            echo $value['num_loan'] . '<br>' . number_format($loan_amonth * $value['num_loan'],2);
                                        }
                                    }
                                    if($num_loan == 0){
//                                        echo "0<br>0.00";
                                    }
                                    ?>
                                </td>
                            <?php } ?>
                            <td class="<?php echo @$_GET['download']!=""? "table_body":""?>" style="text-align: right;" colspan="1"><?php echo $num_total . '<br>' . number_format($loan_amount_total,2);?></td>
                        </tr>
                <?php
                } ?>

                </tbody>
                <tbody style="border-top: 1px solid #000;">
                <tr>
                    <td class="<?php echo @$_GET['download']!=""? "table_body":""?>" style="text-align: left;" colspan="1"><?php echo 'รวม (ราย) <br> รวม (บาท)';?></td>
                    <?php
                    $sum_num_total = 0;
                    $sum_loan_amount_total = 0;
                    foreach ($loan_reason as $reason_id => $item) { ?>
                        <td class="<?php echo @$_GET['download']!=""? "table_body":""?>" style="text-align: right;" colspan="1">
                            <?php
                                echo $item['num_loan_total'].'<br>'.number_format($item['loan_amount_total'],2);
                                $sum_num_total += $item['num_loan_total'];
                                $sum_loan_amount_total += $item['loan_amount_total'];
                            ?>
                        </td>
                    <?php } ?>
                    <td class="<?php echo @$_GET['download']!=""? "table_body":""?>" style="text-align:right;" colspan="1"><?php echo $sum_num_total . '<br>' . number_format($sum_loan_amount_total,2);?></td>
                </tr>
                </tbody>
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