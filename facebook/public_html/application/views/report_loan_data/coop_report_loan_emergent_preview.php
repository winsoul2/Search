<style>
    .table-view>thead, .table-view>thead>tr>td, .table-view>thead>tr>th {
        font-size: 16px;
    }
    .table {
        color: #000;
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

if(@$_GET['report_date'] != ''){
    $date_arr = explode('/',@$_GET['report_date']);
    $day = (int)@$date_arr[0];
    $month = (int)@$date_arr[1];
    $year = (int)@$date_arr[2];
    $year -= 543;
    $file_name_text = $day."_".$month_arr[$month]."_".($year+543);
    $month_start = $month;
    $month_end = $month;
}else{
    if(@$_GET['month']!='' && $_GET['year']!=''){
        $day = '';
        $month = @$_GET['month'];
        $year = (@$_GET['year']-543);
        $file_name_text = $month_arr[$month]."_".($year+543);
        $month_start = $month;
        $month_end = $month;
    }else{
        $day = '';
        $month = date("m", strtotime($account_year['start_date']));
        $year = (@$_GET['year']-543);
        $file_name_text = ($year+543);
        $month_start = 1;
        $month_end = 12;
    }
}
if(@$day != ''){
    $text_day = ' วันที่ '.$day;
}else{
    $text_day = '';
}
//echo $month.'<br>';
$i = 0;
for($m_num = $month_start; $m_num <= $month_end; $m_num++){
    // echo $m." mod = ".($m % 12)."<br>";
    $increase_year = 0;
    $decrease_year = 0;
    $i++;
    if($m_num % 12 !=0){
        $m = $m_num % 12;
    }else{
        $m = 12;
    }
    if(@$_GET['report_date'] != '') {
        $month = sprintf("%02d",$month);
//            echo $month.'<br>';
        $day = sprintf("%02d",$day);
        $s_date = $year."-".$month."-".$day."  00:00:00.000";
        $e_date = $year."-".$month."-".$day."  23:59:59.000";
    }else{
//            echo $month.'<br>';
        $s_date = ($year) . '-' . sprintf("%02d", $m) . '-01' . ' 00:00:00.000';
        $increase_year = 0;
        $e_date = date('Y-m-t', strtotime($s_date)) . ' 23:59:59.000';
    }
    $where_check = " AND t1.approve_date BETWEEN '".$s_date."' AND '".$e_date."'";
    $this->db->select(array('t1.id as loan_id'));
    $this->db->from('coop_loan as t1');
    $this->db->join('coop_mem_apply as t2','t1.member_id = t2.member_id','inner');
    $this->db->join("coop_prename as t3 ", "t2.prename_id = t3.prename_id", "left");
    $this->db->join("coop_loan_reason as t4 ", "t1.loan_reason = t4.loan_reason_id", "left");
    $this->db->join("coop_loan_name as t5", "t1.loan_type = t5.loan_name_id", "left");
    $this->db->join("coop_loan_type as t6", "t5.loan_type_id = t6.id", "left");
    $this->db->where("t6.id = '".@$_GET['loan_type']."' AND t1.loan_status IN ('1','4') {$where_check}");
    if(@$_GET['loan_name'][@$_GET['report']]!=""){
        $this->db->where("t5.loan_name_id in (".implode(",", $_GET['loan_name'][@$_GET['report']]).")");
    }
    //$this->db->where("t1.loan_type = '".@$_GET['loan_type']."' AND t1.loan_status IN ('1','4') {$where_check}");
    $this->db->order_by('t1.createdatetime ASC');
    $rs_check = $this->db->get()->result_array();
    $row_check = @$rs_check[0];
//		echo $this->db->last_query();
//		print_r($row_check);
    if(@$row_check['loan_id']=='' && @$_GET['report_date']==''){
        continue;
    }

    // $this->db->where_in("loan_name_id", $_GET['loan_name'][$_GET['loan_type']);
    // var_dump($_GET['loan_name']);exit;
//        echo '<br>'.$this->db->last_query();exit;
    if(empty($_GET['loan_name'])){
        $loan_name = array($loan_type[$_GET['loan_type']]);
        $prefix_loan = array($loan_type[$_GET['loan_type']]);
    }else{
        $this->db->order_by("order_by asc");
        $this->db->where_in('loan_name_id', $_GET['loan_name'][$_GET['report']]);
        $coop_loan_name = $this->db->get("coop_loan_name")->result_array();
        $loan_name = array_column($coop_loan_name, 'loan_name');
        $prefix_loan = array();
        foreach ($coop_loan_name as $key => $value) {
            $this->db->where("start_date <=", $s_date);
            $this->db->where("type_id", $value['loan_name_id']);
            $tmp_prefix_loan = $this->db->get("coop_term_of_loan")->row_array()['prefix_code'];
            $loan_name[$key] .= " (".$tmp_prefix_loan.")";
            array_push($prefix_loan, $tmp_prefix_loan);
        }
    }


    ?>

    <div style="width: 1500px;" class="page-break">
        <div class="panel panel-body" style="padding-top:20px !important;height: 100%;min-height: 1000px;">
            <table style="width: 100%;">
                <tr>
                    <td style="width:100px;vertical-align: top;">
                        <img src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.$_SESSION['COOP_IMG']); ?>" alt="Logo" style="height: 80px;" />
                    </td>
                    <td class="text-center">
                        <h3 class="title_view"><?php echo @$_SESSION['COOP_NAME'];?></h3>
                        <h3 class="title_view"><?php echo "ทะเบียน ".implode(", ", $loan_name).$text_day."  เดือน  ".@$month_arr[$m]." ".(@$year+$increase_year+$decrease_year+543);?></h3>
                        <p>&nbsp;</p>
                    </td>
                    <td style="width:100px;vertical-align: top;" class="text-right">
                        <?php if($i == '1'){?>
                            <a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
                            <a href="<?php echo base_url(PROJECTPATH.'/report_loan_data/coop_report_loan_emergent_excel?'.$param); ?>" class="no_print"><button class="btn btn-perview btn-after-input" type="button"><span>XLS</span></button></a>
                        <?php } ?>
                    </td>
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
                    <th colspan="3" style="vertical-align: middle;">หนังสือกู้สำหรับ <?php echo implode(", ", $prefix_loan);?></th>
                    <th colspan="3" style="vertical-align: middle;">ผู้กู้</th>
                    <th rowspan="2" style="vertical-align: middle;width: 75px;">จำนวนเงินกู้</th>
                    <th colspan="4" style="vertical-align: middle;">การส่งเงินงวดชำระหนี้</th>
                    <th colspan="3" style="vertical-align: middle;">ผู้ค้ำประกัน</th>
                    <th rowspan="2" style="vertical-align: middle;width: 145px;">เหตุผลในการขอกู้</th>
                </tr>
                <tr>
                    <th style="width: 33px;vertical-align: middle;">ลำดับที่</th>
                    <th style="width: 40px;vertical-align: middle;">เลขที่สัญญา</th>
                    <th style="width: 50px;vertical-align: middle;">วันที่</th>
                    <th style="width: 60px;vertical-align: middle;">ทะเบียนสมาชิก</th>
                    <th style="width: 200px;vertical-align: middle;">ชื่อ -สกุล</th>
                    <th style="width: 90px;vertical-align: middle;">หน่วยงาน</th>
                    <th style="width: 40px;vertical-align: middle;">งวด</th>
                    <th style="width: 55px;vertical-align: middle;">เงินต้น</th>
                    <th style="width: 55px;vertical-align: middle;">ตั้งแต่</th>
                    <th style="width: 55px;vertical-align: middle;">ถึง</th>
                    <th style="width: 60px;vertical-align: middle;">ทะเบียนสมาชิก</th>
                    <th style="width: 200px;vertical-align: middle;">ชื่อ -สกุล</th>
                    <th style="width: 90px;vertical-align: middle;">หน่วยงาน</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $where = '';
                if(@$_GET['report_date'] != '') {
                    $month = sprintf("%02d",$month);
                    $day = sprintf("%02d",$day);
                    $s_date = $year."-".$month."-".$day."  00:00:00.000";
                    $e_date = $year."-".$month."-".$day."  23:59:59.000";
                    $where .= " AND t1.approve_date BETWEEN '" . $s_date . "' AND '" . $e_date . "'";
                }else {
                    $s_date = ($year) . '-' . sprintf("%02d", ($m)) . '-01' . ' 00:00:00.000';
                    $increase_year = 1;
                    if ($day != '') {
                        // $s_date = $year.'-'.sprintf("%02d",@$m).'-'.sprintf("%02d",@$day).' 00:00:00.000';
                        $e_date = $year . '-' . sprintf("%02d", @$m) . '-' . sprintf("%02d", @$day) . ' 23:59:59.000';
                        $where .= " AND t1.approve_date BETWEEN '" . $s_date . "' AND '" . $e_date . "'";
                    } else {
                        // $s_date = $year.'-'.sprintf("%02d",@$m).'-01'.' 00:00:00.000';
                        $e_date = date('Y-m-t', strtotime($s_date)) . ' 23:59:59.000';
                        $where .= " AND t1.approve_date BETWEEN '" . $s_date . "' AND '" . $e_date . "'";
                    }
                }
                //update money_per_period
                $sql_update = "update coop_loan as t1 set t1.money_per_period = (select principal_payment from coop_loan_period where loan_id = t1.id limit 1) where t1.loan_status = 1 AND t1.loan_type = ".@$_GET['loan_type'].$where;
                $this->db->query($sql_update);
                //

                $this->db->select(array('t1.id as loan_id',
                    't1.loan_type',
                    't1.contract_number',
                    't1.approve_date',
                    't2.member_id',
                    't2.employee_id',
                    't3.prename_short',
                    't2.firstname_th',
                    't2.lastname_th',
                    't2.level',
                    't1.period_amount',
                    't1.loan_amount',
                    'IF((t1.money_per_period is null or t1.money_per_period = ""), (select IF(t1.pay_type=1, coop_loan_period.principal_payment, coop_loan_period.total_paid_per_month) from coop_loan_period where loan_id = t1.id limit 1), t1.money_per_period) as money_per_period',
                    't4.loan_reason',
                    't10.guarantee_type'
                ));
                $this->db->from('coop_loan as t1');
                $this->db->join('coop_mem_apply as t2','t1.member_id = t2.member_id','inner');
                $this->db->join("coop_prename as t3 ", "t2.prename_id = t3.prename_id", "left");
                $this->db->join("coop_loan_reason as t4 ", "t1.loan_reason = t4.loan_reason_id", "left");
                $this->db->join("coop_loan_name as t5", "t1.loan_type = t5.loan_name_id", "left");
                $this->db->join("coop_loan_type as t6", "t5.loan_type_id = t6.id", "left");
                $this->db->join("coop_loan_guarantee as t10", "t1.id = t10.loan_id AND t10.guarantee_type != '3'", 'left');
                $this->db->where("t6.id = '".@$_GET['loan_type']."' AND t1.loan_status IN ('1','4') {$where}");
                if(@$_GET['loan_name'][@$_GET['report']]!=""){
                    $this->db->where("t5.loan_name_id in (".implode(",", $_GET['loan_name'][@$_GET['report']]).")");
                }
                $this->db->where("t1.contract_number NOT LIKE '%/%'");
                $this->db->order_by('t1.contract_number ASC, t10.guarantee_type, t1.approve_date ASC');
//                $this->db->limit(10);
                $rs = $this->db->get()->result_array();
//                						echo $this->db->last_query();exit;
                //						echo '<pre style="text-align: left">'; print_r($rs); echo '</pre>';
                if($_GET['dev'] == 'dev'){
                    echo $this->db->last_query();exit;
                    echo '<pre>';print_r($rs);exit;
                }
                $count_loan = -1;
                $loan_amount=0;
                $chk_contract_number = '';
                if(!empty($rs)){
                    $count_loan++;
                    foreach($rs as $key => $row){
                        $i+=1;
                        $this->db->select(array('period_count','date_period'));
                        $this->db->from('coop_loan_period');
                        $this->db->where("loan_id = '".@$row['loan_id']."'");
                        $this->db->order_by('period_count ASC');
                        $rs_period = $this->db->get()->result_array();
                        $new_count = false;
                        $first_period = '';
                        $last_period = '';
                        if(!empty($rs_period)){
                            foreach($rs_period as $key => $row_period){
                                if(@$row_period['period_count'] == '1'){
                                    $first_period = @$row_period['date_period'];
                                }
                                $last_period = @$row_period['date_period'];
                            }
                        }

                        if(@$row['contract_number'] != $chk_contract_number){
                            $loan_amount += @$row['loan_amount'];
                            $count_loan++;
                            $chk_contract_number = @$row['contract_number'];
                            $new_count = true;
                        }

                            $this->db->select(array(
                                "CONCAT(t3.prename_short, ' ', t2.firstname_th, ' ', t2.lastname_th) as fullname",
                                "t1.guarantee_person_id",
                                "t2.level AS guarantee_person_level"
                            ));
                            $this->db->from("coop_loan_guarantee_person as t1");
                            $this->db->join("coop_mem_apply as t2", "t1.guarantee_person_id = t2.member_id", "inner");
                            $this->db->join("coop_prename as t3", "t3.prename_id = t2.prename_id", "left");
                            $this->db->where("t1.loan_id", $row['loan_id']);
                            $this->db->where("t1.guarantee_person_id !=", "");
                            $guarantee = $this->db->get()->result_array();
                        ?>
                        <tr>
                        <td style="text-align: center;"><?php echo ($new_count) ? @$count_loan : ""?></td>
                        <td style="text-align: center;"><?php echo ($new_count) ? @$row['contract_number'] : ""?></td>
                        <td style="text-align: center;"><?php echo ($new_count) ? $this->center_function->mydate2date(@$row['approve_date']) : ""; ?></td>
                        <td style="text-align: center;"><?php echo ($new_count) ?  @$row['member_id'] : ""; ?></td>
                        <td style="text-align: left;"><?php echo ($new_count) ? @$row['prename_short'].@$row['firstname_th'].'  '.@$row['lastname_th'] : ""; ?></td>
                        <td style="text-align: left;"><?php echo ($new_count) ? @$mem_group_arr[@$row['level']] : ""; ?></td>
                        <td style="text-align: right;"><?php echo ($new_count) ? number_format(@$row['loan_amount'],2) : ""; ?></td>
                        <td style="text-align: center;"><?php echo ($new_count) ? number_format(@$row['period_amount'],0) : "";?></td>
                        <td style="text-align: right;"><?php echo ($new_count) ? number_format(@$row['money_per_period'],2) : "";?></td>
                        <td style="text-align: center;"><?php echo ($new_count) ? ($first_period)?@$this->center_function->ConvertToThaiDateMMYY($first_period,1,1):'' : "";?></td>
                        <td style="text-align: center;"><?php echo ($new_count) ? ($last_period)?@$this->center_function->ConvertToThaiDateMMYY($last_period,1,1):'' : "";?></td>
                        <?php
                        $text = "";
                        if(@$row['guarantee_type']==1 && @$row['loan_type']!=20){
                            foreach ($guarantee as $k => $v) {
                                if($k==0){
                                    ?>
                                    <td style="text-align: center;" id="s"><?php echo @$v['guarantee_person_id']; ?></td>
                                    <td style="text-align: left;"><?php echo @$v['fullname'];?></td>
                                    <td style="text-align: left;"><?php echo @$mem_group_arr[@$v['guarantee_person_level']]; ?></td>
                                    <td style="text-align: left;"><?php echo @$row['loan_reason'];?></td>
                                    </tr>
                                    <?php
                                }else{
                                    ?>
                                    <tr>
                                        <td style="text-align: center;"></td>
                                        <td style="text-align: center;"></td>
                                        <td style="text-align: center;"></td>
                                        <td style="text-align: center;"></td>
                                        <td style="text-align: center;"></td>
                                        <td style="text-align: center;"></td>
                                        <td style="text-align: center;"></td>
                                        <td style="text-align: center;"></td>
                                        <td style="text-align: center;"></td>
                                        <td style="text-align: center;"></td>
                                        <td style="text-align: center;"></td>
                                        <td style="text-align: center;"><?php echo @$v['guarantee_person_id']; ?></td>
                                        <td style="text-align: left;"><?php echo @$v['fullname']; ?></td>
                                        <td style="text-align: left;"><?php echo @$mem_group_arr[@$v['guarantee_person_level']]; ?></td>
                                        <td style="text-align: left;" id="sdsdsd"><?php echo @$row['loan_reason'];?></td>
                                    </tr>
                                    <?php
                                }
                            }
                            if(!empty($guarantee)){
                                continue;
                            }

                        }else if(@$row['guarantee_type']==2 || @$row['loan_type']==20){
                            $text .= "ใช้ทุนเรือนหุ้นค้ำประกัน ";
                        }

                        if($text!=""){
                            ?>
                            <td style="text-align: left;" colspan=3><?php echo $text; ?></td>
                            <?php
                        }else{
                            ?>
                            <td style="text-align: left;" colspan=3></td>
                            <?php
                        }

                        ?>

                        <td style="text-align: left;" id="sdsdsd"><?php echo @$row['loan_reason'];?></td>
                        </tr>
                        <?php

                    }

                }
                ?>
                </tbody>
            </table>

            <table style="width: 100%;" class="m-t-2">
                <tr>
                    <td style="width: 200px;"></td>
                    <td style="width: 150px;"><h3 class="title_view"><?php echo "เดือน ".$month_arr[$m];?></h3></td>
                    <td style="width: 40px;"><h3 class="title_view"><?php echo "รวม " ;?></h3></td>
                    <td style="width: 50px;    text-align: center;"><h3 class="title_view"><?php echo number_format($count_loan);?></h3></td>
                    <td style="width: 150px;"><h3 class="title_view"><?php echo "สัญญา ";?></h3></td>
                    <td style="width: 110px;"><h3 class="title_view"><?php echo "เป็นเงินจำนวน " ;?></h3></td>
                    <td style="width: 150px;    text-align: center;"><h3 class="title_view"><?php echo number_format($loan_amount) ;?></h3></td>
                    <td style="width: 50px;"><h3 class="title_view"><?php echo "บาท " ;?></h3></td>
                    <td></td>
                </tr>
            </table>
        </div>
    </div>
<?php } ?>