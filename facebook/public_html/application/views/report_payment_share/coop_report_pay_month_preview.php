<style>
    .table-view>thead, .table-view>thead>tr>td, .table-view>thead>tr>th {
        font-size: 14px;
    }
    .table {
        color: #000;
    }
</style>
<?php
//echo '<pre>'; print_r($_GET); echo '</pre>';
if(@$_GET['month']!='' && @$_GET['year']!=''){
    $day = '';
    $month = @$_GET['month'];
    $year = (@$_GET['year']);
    $title_date = " เดือน ".@$month_arr[$month]." ปี ".(@$year);
}else{
    $day = '';
    $month = '';
    $year = (@$_GET['year']);
    $title_date = " ปี ".(@$year);
}

$num_rows = count($datas);
$page_limit = 20;
$page_i = 0;
$page_all = @ceil($num_rows/$page_limit);
$arr_total = array();
$page = 1;
//for($page = 1;$page<=$page_all;$page++){
    $page_start = (($page-1)*$page_limit);
    $per_page = $page*$page_limit ;
    ?>

    <div style="width: 1420px;"  class="page-break"> <?php // 1420px 1000px;?>
        <div class="panel panel-body" style="padding-top:10px !important;min-height: 1000px;">
            <table style="width: 100%;">
                <?php
                if(@$page == 1){
                    ?>
                    <tr>
                        <td style="width:100px;vertical-align: top;">

                        </td>
                        <td class="text-center">
                            <img src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.$_SESSION['COOP_IMG']); ?>" alt="Logo" style="height: 80px;" />
                            <h3 class="title_view"><?php echo @$_SESSION['COOP_NAME'];?></h3>
                            <h3 class="title_view">รายงานรับชำระค่าหุ้นและเงินกู้ประจำเดือน <?php echo @$title_date;?></h3>
<!--                            <h3 class="title_view">หน่วยงานเบิกเงินเดือน </h3>-->
                        </td>
                        <td style="width:100px;vertical-align: top;" class="text-right">
                            <a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
                            <?php
                            $get_param = '?';
                            foreach(@$_GET as $key => $value){
                                if($key != 'mem_type'){
                                    $get_param .= $key.'='.$value.'&';
                                }

                                if($key == 'mem_type'){
                                    foreach($value as $key2 => $value2){
                                        $get_param .= $key.'[]='.$value2.'&';
                                    }
                                }
                            }
                            $get_param = substr($get_param,0,-1);

                            ?>
                            <a class="no_print"  target="_blank" href="<?php echo base_url('/report_processor_data/coop_report_pay_month_excel'.$get_param); ?>">
                                <button class="btn btn-perview btn-after-input" type="button"><span class="icon icon icon-file-excel-o" aria-hidden="true"></span></button>
                            </a>
                        </td>
                    </tr>
                <?php } ?>
                <tr>
                    <td colspan="3" style="text-align: right;">
                        <span class="title_view">หน้าที่ <?php echo @$page.'/'.@$page_all;?></span><br>
                    </td>
                </tr>
            </table>

            <table class="table table-view table-center">
                <thead>
                <tr>
                    <th rowspan="2" style="width: 40px;vertical-align: middle;">เลขที่<br>สมาชิก</th>
                    <th rowspan="2" style="width: 200px;vertical-align: middle;">ชื่อ - สกุล</th>
                    <th rowspan="2" style="width: 120px;vertical-align: middle;">เลขประจำตัว</th>
                    <th rowspan="2" style="width: 80px;vertical-align: middle;">เงินฝาก</th>
                    <th colspan="2" style="width: 120px;vertical-align: middle;">หุ้น</th>
                    <th colspan="5" style="width: 400px;vertical-align: middle;">สามัญ/พิเศษ/สามัญโครงการ</th>
                    <th colspan="5" style="width: 400px;vertical-align: middle;">ฉุกเฉิน</th>
                    <th rowspan="2" style="width: 100px;vertical-align: middle;">เงินรวม</th>
                    <th rowspan="2" style="width: 100px;vertical-align: middle;">เลขที่เอกสาร</th>
                </tr>
                <tr>
                    <th style="width: 40px;vertical-align: middle;">งวดที่</th>
                    <th style="width: 80px;vertical-align: middle;">ค่าหุ้น</th>

                    <th style="width: 80px;vertical-align: middle;">เลขที่สัญญา</th>
                    <th style="width: 80px;vertical-align: middle;">งวดที่</th>
                    <th style="width: 80px;vertical-align: middle;">เงินต้น</th>
                    <th style="width: 80px;vertical-align: middle;">ดอกเบี้ย</th>
                    <th style="width: 80px;vertical-align: middle;">รวม</th>

                    <th style="width: 80px;vertical-align: middle;">เลขที่สัญญา</th>
                    <th style="width: 80px;vertical-align: middle;">งวดที่</th>
                    <th style="width: 80px;vertical-align: middle;">เงินต้น</th>
                    <th style="width: 80px;vertical-align: middle;">ดอกเบี้ย</th>
                    <th style="width: 80px;vertical-align: middle;">รวม</th>
                </tr>
                </thead>
                <tbody>
                <?php

                foreach ($datas as $key => $value){
                    $page_i++;?>
                    <tr>
                        <td style="text-align: center;"><?php echo $value['member_id'];?></td>
                        <td style="text-align: right;"><?php echo $value['full_name'];?></td>
                        <td style="text-align: right;"><?php echo $page_i.' | '.$page.' | '.$page_all;?></td>
                        <td style="text-align: right;"><?php echo number_format($balance,2);?></td>
                        <td style="text-align: right;"><?php echo $value['SHARE']['share']['']['contract_number'];?></td>
                        <td style="text-align: right;"><?php echo $value['SHARE']['share']['']['principal']['pay_amount'];?></td>
                        <?php
                        $count_loan_emergent = @count($value['LOAN']['emergent']);
                        $count_loan_normal = @count($value['LOAN']['normal']);
//                        echo @count($count_loan_normal);exit;
                        if($count_loan_normal >= $count_loan_emergent) {
                            $loan = $value['LOAN']['normal'];
                        }else if($count_loan_normal < $count_loan_emergent){
                            $loan = $value['LOAN']['emergent'];
                        }else{
                            $loan = array();
                        }
                        $i = 0;
                        if(!empty($loan)) {
                            foreach ($loan as $key2 => $item) {
                                if ($i == 0) {
                                    ?>
                                    <td style="text-align: right;"><?php echo $value['LOAN']['normal'][$key2]['contract_number']; ?></td>
                                    <td style="text-align: right;"><?php echo ''; ?></td>
                                    <td style="text-align: right;"><?php echo $value['LOAN']['normal'][$key2]['principal']['pay_amount']; ?></td>
                                    <td style="text-align: right;"><?php echo $value['LOAN']['normal'][$key2]['interest']['pay_amount']; ?></td>
                                    <td style="text-align: right;"><?php echo($value['LOAN']['normal'][$key2]['principal']['pay_amount'] + $value['LOAN']['normal'][$key2]['interest']['pay_amount']); ?></td>
                                    <td style="text-align: right;"><?php echo $value['LOAN']['emergent'][$key2]['contract_number']; ?></td>
                                    <td style="text-align: right;"><?php echo ''; ?></td>
                                    <td style="text-align: right;"><?php echo $value['LOAN']['emergent'][$key2]['principal']['pay_amount']; ?></td>
                                    <td style="text-align: right;"><?php echo $value['LOAN']['emergent'][$key2]['interest']['pay_amount']; ?></td>
                                    <td style="text-align: right;"><?php echo($value['LOAN']['emergent'][$key2]['principal']['pay_amount'] + $value['LOAN']['emergent'][$key2]['interest']['pay_amount']); ?></td>
                                    <td style="text-align: right;"></td>
                                    <td style="text-align: right;"></td>
                                    </tr>
                                    <?php
                                    $i++;
                                } else {
                                        $page_i++?>
                                    <tr>
                                        <td style="text-align: center;"></td>
                                        <td style="text-align: right;"></td>
                                        <td style="text-align: right;"></td>
                                        <td style="text-align: right;"></td>
                                        <td style="text-align: right;"></td>
                                        <td style="text-align: right;"></td>

                                        <td style="text-align: right;"><?php echo $item['contract_number']; ?></td>
                                        <td style="text-align: right;"><?php echo ''; ?></td>
                                        <td style="text-align: right;"><?php echo $item['principal']['pay_amount']; ?></td>
                                        <td style="text-align: right;"><?php echo $item['interest']['pay_amount']; ?></td>
                                        <td style="text-align: right;"><?php echo($item['principal']['pay_amount'] + $item['interest']['pay_amount']); ?></td>

                                        <td style="text-align: right;"></td>
                                        <td style="text-align: right;"></td>
                                        <td style="text-align: right;"></td>
                                        <td style="text-align: right;"></td>
                                        <td style="text-align: right;"></td>
                                        <td style="text-align: right;"></td>
                                        <td style="text-align: right;"></td>

                                    </tr>
                                <?php }
                            }
                        }else{ ?>

                            <td style="text-align: right;"></td>
                            <td style="text-align: right;"></td>
                            <td style="text-align: right;"></td>
                            <td style="text-align: right;"></td>
                            <td style="text-align: right;"></td>

                            <td style="text-align: right;"></td>
                            <td style="text-align: right;"></td>
                            <td style="text-align: right;"></td>
                            <td style="text-align: right;"></td>
                            <td style="text-align: right;"></td>
                            <td style="text-align: right;"></td>
                            <td style="text-align: right;"></td>

                            </tr>
                        <?php
                        }
                        ?>
                <?php

                if($page == $page_all){
                    ?>
                    <!--tr>
                        <td style="text-align: center;" colspan="3"><?php echo "รวมทั้งสิ้น ".number_format($count_member)." รายการ";?></td>
                        <td style="text-align: right;"><?php echo number_format($pay_amount,2);?></td>
                        <td style="text-align: right;"><?php echo number_format($real_pay_amount,2);?></td>
                        <td style="text-align: right;"><?php echo number_format($balance,2);?></td>
                        <td style="text-align: right;"></td>
                        <td style="text-align: right;"></td>
                        <td style="text-align: right;"></td>
                        <td style="text-align: right;"></td>
                        <td style="text-align: right;"></td>
                        <td style="text-align: right;"></td>
                        <td style="text-align: right;"></td>
                        <td style="text-align: right;"></td>
                        <td style="text-align: right;"></td>
                        <td style="text-align: right;"></td>
                        <td style="text-align: right;"></td>
                        <td style="text-align: right;"></td>
                    </tr-->
                <?php } ?>

                <?php
//                if(($page_i == 20)){
                                if(($page_i >= 20) && ($page != $page_all)){
                $page++;
                $page_i = 0?>
                <!-- ขึ้นหน้าใหม่ -->
                </tbody>
            </table>
        </div>
    </div>

<div style="width: 1420px;" class="page-break"> <?php // 1420px 1000px;
?>
<div class="panel panel-body" style="padding-top:10px !important;min-height: 1000px;">
<table style="width: 100%;">
    <?php
    if(@$page != 1){
        ?>
        <tr>
            <td style="width:100px;vertical-align: top;">

            </td>
            <td class="text-center">
                <img src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.$_SESSION['COOP_IMG']); ?>" alt="Logo" style="height: 80px;" />
                <h3 class="title_view"><?php echo @$_SESSION['COOP_NAME'];?></h3>
                <h3 class="title_view">รายงานรับชำระค่าหุ้นและเงินกู้ประจำเดือน <?php echo @$title_date;?></h3>
                <!--                            <h3 class="title_view">หน่วยงานเบิกเงินเดือน </h3>-->
            </td>
            <td style="width:100px;vertical-align: top;" class="text-right">
                <a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
                <?php
                $get_param = '?';
                foreach(@$_GET as $key => $value){
                    if($key != 'mem_type'){
                        $get_param .= $key.'='.$value.'&';
                    }

                    if($key == 'mem_type'){
                        foreach($value as $key2 => $value2){
                            $get_param .= $key.'[]='.$value2.'&';
                        }
                    }
                }
                $get_param = substr($get_param,0,-1);

                ?>
                <a class="no_print"  target="_blank" href="<?php echo base_url('/report_processor_data/coop_report_pay_month_excel'.$get_param); ?>">
                    <button class="btn btn-perview btn-after-input" type="button"><span class="icon icon icon-file-excel-o" aria-hidden="true"></span></button>
                </a>
            </td>
        </tr>
    <?php } ?>
    <tr>
        <td colspan="3" style="text-align: right;">
            <span class="title_view"> - หน้าที่ <?php echo @$page . '/' . @$page_all; ?></span><br>
        </td>
    </tr>
</table>

<table class="table table-view table-center">
<thead>
<tr>
    <th rowspan="2" style="width: 40px;vertical-align: middle;">เลขที่<br>สมาชิก</th>
    <th rowspan="2" style="width: 200px;vertical-align: middle;">ชื่อ - สกุล</th>
    <th rowspan="2" style="width: 80px;vertical-align: middle;">เลขประจำตัว</th>
    <th rowspan="2" style="width: 80px;vertical-align: middle;">เงินฝาก</th>
    <th colspan="2" style="width: 120px;vertical-align: middle;">หุ้น</th>
    <th colspan="5" style="width: 400px;vertical-align: middle;">สามัญ/พิเศษ/สามัญโครงการ</th>
    <th colspan="5" style="width: 400px;vertical-align: middle;">ฉุกเฉิน</th>
    <th rowspan="2" style="width: 100px;vertical-align: middle;">เงินรวม</th>
    <th rowspan="2" style="width: 100px;vertical-align: middle;">เลขที่เอกสาร</th>
</tr>
<tr>
    <th style="width: 40px;vertical-align: middle;">งวดที่</th>
    <th style="width: 80px;vertical-align: middle;">ค่าหุ้น</th>

    <th style="width: 80px;vertical-align: middle;">เลขที่สัญญา</th>
    <th style="width: 80px;vertical-align: middle;">งวดที่</th>
    <th style="width: 80px;vertical-align: middle;">เงินต้น</th>
    <th style="width: 80px;vertical-align: middle;">ดอกเบี้ย</th>
    <th style="width: 80px;vertical-align: middle;">รวม</th>

    <th style="width: 80px;vertical-align: middle;">เลขที่สัญญา</th>
    <th style="width: 80px;vertical-align: middle;">งวดที่</th>
    <th style="width: 80px;vertical-align: middle;">เงินต้น</th>
    <th style="width: 80px;vertical-align: middle;">ดอกเบี้ย</th>
    <th style="width: 80px;vertical-align: middle;">รวม</th>
</tr>
</thead>
<tbody>


<?php
if ($page == $page_all) {
    ?>
<!--    </tbody>-->
<!--    </table>-->
<!--    </div>-->
<!--    </div>-->
    <?php
}
?>
<?php
}
}
//exit;
//} ?>