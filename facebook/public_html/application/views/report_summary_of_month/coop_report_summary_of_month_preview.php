<style>
    .table-view>thead, .table-view>thead>tr>td, .table-view>thead>tr>th {
        font-size: 14px;
    }
    .table {
        color: #000;
    }
    @page { size: landscape; }
</style>
<?php
error_reporting(0);
//echo '<pre>'; print_r($_GET); echo '</pre>';
if(@$_GET['month']!='' && @$_GET['year']!=''){
    $day = '';
    $month = @$_GET['month'];
    $year = @$_GET['year'];
    $title_date = " เดือน ".@$month_arr[$month]." ปี ".(@$year);
}else{
    $day = '';
    $month = '';
    $year = @$_GET['year'];
    $title_date = " ปี ".(@$year);
}

$where = "";
if(@$_GET['department']){
    $where .= " AND id = '{$_GET['department']}'";
}
foreach ($data as $key => $value) {
    ?>
    <div style="width: 1500px;"  class="page-break">
        <div class="panel panel-body" style="padding-top:10px !important;height: 950px;">
            <table style="width: 100%;" border = '0'>
                <tr>
                    <td class="text-center" colspan="7">
                        <!--                            <img src="--><?php //echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.$_SESSION['COOP_IMG']); ?><!--" alt="Logo" style="height: 80px;" />-->
                        <h3 class="title_view text-left"><?php echo @$_SESSION['COOP_NAME'];?></h3>
                        <h3 class="title_view text-left">สรุปรายการรับชำระค่าหุ้นและเงินกู้<?php echo " ประจำ ".@$title_date;?></h3>
                        <h3 class="title_view text-left">
                            <?php echo " หน่วย ".@$value['mem_group_name'];?>
                        </h3>
                    </td>
                    <td style="vertical-align: top;" class="text-right">
                        <a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
                    </td>
                </tr>
                <tr>
                    <td colspan="6" style="text-align: right;"></td>
                    <td colspan="1" style="text-align: left;">
                        <span class="title_view">ค่าหุ้น(1)</span>
                    </td>
                    <td colspan="1" style="text-align: right;">
                        <span class="title_view"><?php echo number_format($value['SHARE'], 2);?> บาท</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="6" style="text-align: right;"></td>
                    <td colspan="1" style="text-align: left;">
                        <span class="title_view">ค่าธรรมเนียม(2)</span>
                    </td>
                    <td colspan="1" style="text-align: right;">
                        <span class="title_view"><?php number_format($value['REGISTER_FEE'],2);?> บาท</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="6" style="text-align: right;"></td>
                    <td colspan="1" style="text-align: left;">
                        <span class="title_view">เงินฝากออมทรัพย์</span><br>
                    </td>
                    <td colspan="1" style="text-align: right;">
                        <span class="title_view"><?php echo number_format($value['DEPOSIT'], 2);?> บาท</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="1" style="text-align: right;width:125px;">
                        <span class="title_view"> ยอดสมาชิก </span><br>
                    </td>
                    <td colspan="1" style="text-align: right;width:125px;">
                        <span class="title_view"><?php echo number_format($value['member_count'], 0);?> ราย</span><br>
                    </td>

                    <td colspan="1" style="text-align: right;width:125px;"></td>

                    <td colspan="1" style="text-align: right;width:125px;">
                        <span class="title_view">ทุนเรือนหุ้น</span><br>
                    </td>
                    <td colspan="1" style="text-align: right;width:125px;">
                        <span class="title_view"><?php echo number_format($value['total_share_collect_value'], 2);?> บาท</span><br>
                    </td>

                    <td colspan="1" style="text-align: right;width:125px;"></td>
                    <td colspan="1" style="text-align: left;width:125px;">
                        <span class="title_view">เงินฝากประจำ</span><br>
                    </td>
                    <td colspan="1" style="text-align: right;width:125px;">
                        <span class="title_view"><?php echo number_format(0, 2);?> บาท</span>
                    </td>
                </tr>
            </table>

            <table class="table table-view table-center">
                <thead>
                <tr>
                    <th rowspan="2" style="width: 80px;vertical-align: middle;"></th>
                    <th colspan="2" style="width: 160px;vertical-align: middle;">จำนวนฉบับ</th>
                    <th colspan="2" style="width: 160px;vertical-align: middle;">จำนวนเงินกู้</th>
                    <th colspan="2" style="width: 160px;vertical-align: middle;">เงินกู้คงเหลือ</th>
                    <th colspan="2" style="width: 160px;vertical-align: middle;">เงินต้น</th>
                    <th colspan="2" style="width: 160px;vertical-align: middle;">ดอกเบี้ย</th>
                    <th rowspan="2" style="width: 80px;vertical-align: middle;">รวมเงินกู้ (3)</th>
                </tr>
                <tr>
                    <th style="width: 80px;vertical-align: middle;">ใหม่</th>
                    <th style="width: 80px;vertical-align: middle;">ทั้งหมด</th>
                    <th style="width: 80px;vertical-align: middle;">ใหม่</th>
                    <th style="width: 80px;vertical-align: middle;">ทั้งหมด</th>
                    <th style="width: 80px;vertical-align: middle;">ใหม่</th>
                    <th style="width: 80px;vertical-align: middle;">ทั้งหมด</th>
                    <th style="width: 80px;vertical-align: middle;">ใหม่</th>
                    <th style="width: 80px;vertical-align: middle;">ทั้งหมด</th>
                    <th style="width: 80px;vertical-align: middle;">ใหม่</th>
                    <th style="width: 80px;vertical-align: middle;">ทั้งหมด</th>
                </tr>
                </thead>
                <tbody>
                <?php
                //            $value['loan_type'][11]['count_loan'] += $value['loan_type'][19]['count_loan'];
                //            $value['loan_type'][11]['count_loan'] += $value['loan_type'][20]['count_loan'];
                //            $value['loan_type'][11]['loan_amount'] += $value['loan_type'][19]['loan_amount'];
                //            $value['loan_type'][11]['loan_amount'] += $value['loan_type'][20]['loan_amount'];
                //            $value['loan_type'][11]['loan_amount_balance'] += $value['loan_type'][19]['loan_amount_balance'];
                //            $value['loan_type'][11]['loan_amount_balance'] += $value['loan_type'][20]['loan_amount_balance'];
                //            $value['loan_type'][11]['principal'] += $value['loan_type'][19]['principal'];
                //            $value['loan_type'][11]['principal'] += $value['loan_type'][20]['principal'];
                //            $value['loan_type'][11]['interest'] += $value['loan_type'][19]['interest'];
                //            $value['loan_type'][11]['interest'] += $value['loan_type'][20]['interest'];
                //
                //            $value['loan_type'][11]['count_loan_new'] += $value['loan_type'][19]['count_loan_new'];
                //            $value['loan_type'][11]['count_loan_new'] += $value['loan_type'][20]['count_loan_new'];
                //            $value['loan_type'][11]['loan_amount_new'] += $value['loan_type'][19]['loan_amount_new'];
                //            $value['loan_type'][11]['loan_amount_new'] += $value['loan_type'][20]['loan_amount_new'];
                //            $value['loan_type'][11]['loan_amount_balance_new'] += $value['loan_type'][19]['loan_amount_balance_new'];
                //            $value['loan_type'][11]['loan_amount_balance_new'] += $value['loan_type'][20]['loan_amount_balance_new'];
                //            $value['loan_type'][11]['principal_new'] += $value['loan_type'][19]['principal_new'];
                //            $value['loan_type'][11]['principal_new'] += $value['loan_type'][20]['principal_new'];
                //            $value['loan_type'][11]['interest_new'] += $value['loan_type'][19]['interest_new'];
                //            $value['loan_type'][11]['interest_new'] += $value['loan_type'][20]['interest_new'];

                foreach ($value['loan_type'] as $loan_type_id => $loan_type_detail) {
                    $principal += $loan_type_detail['principal'];
                    $interest += $loan_type_detail['interest'];
                    $total += $loan_type_detail['principal'] + $loan_type_detail['interest'];
                    ?>
                    <tr>
                        <td style="text-align: center;"><?php echo $loan_type_detail['loan_name'];?></td>
                        <td style="text-align: right;"><?php echo number_format($loan_type_detail['count_loan_new']);?></td>
                        <td style="text-align: right;"><?php echo number_format($loan_type_detail['count_loan']);?></td>
                        <td style="text-align: right;"><?php echo number_format($loan_type_detail['loan_amount_new'], 2);?></td>
                        <td style="text-align: right;"><?php echo number_format($loan_type_detail['loan_amount'], 2);?></td>
                        <td style="text-align: right;"><?php echo number_format($loan_type_detail['loan_amount_balance_new'], 2);?></td>
                        <td style="text-align: right;"><?php echo number_format($loan_type_detail['loan_amount_balance'], 2);?></td>
                        <td style="text-align: right;"><?php echo number_format($loan_type_detail['principal_new'], 2);?></td>
                        <td style="text-align: right;"><?php echo number_format($loan_type_detail['principal'], 2);?></td>
                        <td style="text-align: right;"><?php echo number_format($loan_type_detail['interest_new'], 2);?></td>
                        <td style="text-align: right;"><?php echo number_format($loan_type_detail['interest'], 2);?></td>
                        <td style="text-align: right;"><?php echo number_format($loan_type_detail['principal']+$loan_type_detail['interest'], 2);?></td>
                    </tr>
                <?php } ?>
                <tr>
                    <td colspan="7" style="text-align: center;"></td>
                    <td colspan="2" style="text-align: right;"><?php echo number_format($principal, 2);?></td>
                    <td colspan="2" style="text-align: right;"><?php echo number_format($interest, 2);?></td>
                    <td colspan="1" style="text-align: right;"><?php echo number_format($total, 2);?></td>
                </tr>
                <tr>
                    <td colspan="9" style="text-align: center;"></td>
                    <td colspan="2" style="text-align: left;"><?php echo 'รวมทั้งสิ้น (1+2+3)';?></td>
                    <td colspan="1" style="text-align: right;"><?php echo number_format($total+$value['SHARE']+$value['REGISTER_FEE'], 2);?></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
<?php } ?>
