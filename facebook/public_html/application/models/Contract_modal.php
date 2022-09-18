<?php
class Contract_modal extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model("Loan_calculator_model", "LoanCalc");
    }

    private $_contract = null;
    private $_atmContract = null;

    public function current($member = "", $status = ''){
        if($member == ""){
           return;
        }
        //Loan Contract
        if($status != "") {
            $this->db->where(array('loan_status' => $status));
        }else{
            $this->db->where(array('loan_status' => '1'));
        }

        $this->_contract = $this->db->select(
            array('t1.admin_id','t1.loan_type', 't1.petition_number','t1.loan_amount', 't1.loan_amount_balance', 't1.money_per_period', 't1.period_amount',
                't1.date_start_period', 't1.id','t1.contract_number', 't1.period_now', 't1.approve_date', 't1.date_last_interest', 't2.loan_name', 't2.loan_type_id')
        )->from('coop_loan t1')->join('coop_loan_name t2', 't1.loan_type=t2.loan_name_id', 'inner')
		->where(array('member_id' => $member))->order_by('createdatetime', 'desc')->get()->result_array();

        //ATM Contract
        $_atm_field = [
            'loan_atm_id',
            'member_id',
            'approve_date',
            'total_amount_approve as loan_amount',
            'convert((total_amount_approve - total_amount_balance), decimal(16,2)) as loan_amount_balance',
            'contract_number',
            'petition_number',
            '99 as loan_type'
        ];

        $_atm_where = [
            'loan_atm_status' => 1
        ];

        $this->db->where($_atm_where);
        $this->_atmContract = $this->db->select($_atm_field)->from('coop_loan_atm')->where(array('member_id' => $member))->order_by('approve_date', 'desc')
            ->get()->result_array();

        return $this;
    }

    public function findContract($loanId){
        $where = array('id' => $loanId);
        return $this->db->get_where("coop_loan" , $where, 1)->row();
    }

    public function get(){
        return $this->_contract;
    }

    public function getATM(){
        return $this->_atmContract;
    }

    public function getTermOfLoan(){
        $this->db->order_by("start_date ASC");
        return $this->db->get_where("coop_term_of_loan", "start_date <= '".date('Y-m-d')."'")->result_array();
    }

    public function getTermOfLoanUseCurrent(){
        $rs_rule = self::getTermOfLoan();
        foreach($rs_rule as $key => $value){
            $arr_data['rs_rule'][$value['type_id']] = $value;
        }
    }

    public function getLoanReason(){
        return $this->db->get('coop_loan_reason')->result_array();
    }

    public function getLoanType(){
        return $this->db->get_where('coop_loan_type', array('loan_type_status'=> '1'))->result_array();
    }

    public function getLoanNameByTypeId($type_id){
        $this->db->where("loan_type_id = '".$type_id."' AND loan_name_status = 1");
        return  $this->db->get('coop_loan_name')->result_array();
    }

    public function getLoanTypeByLoanNameId($type_id){
        return  $this->db->get_where('coop_loan_name', "loan_name_id = '".$type_id."' AND loan_name_status = 1", 1)
            ->row()->loan_type_id;
    }

    function getPrevLoan($member_id, $createdatetime){

        $date_interesting = date("Y-m-d", strtotime(join('-', array_reverse(explode('/', $createdatetime)))));

        $prev_loan_active_arr = array();
        $i=0;
        $this->db->select(array(
            '*'
        ));
        $this->db->from('coop_loan as t1');
        $this->db->where("t1.member_id = '".$member_id."' AND t1.loan_status = '1' AND t1.loan_amount_balance <> 0");
        $prev_loan_active = $this->db->get()->result_array();

        foreach($prev_loan_active as $key => $value){
            $prev_loan_active_arr[$i]['ref_id'] = $value['id'];
            $prev_loan_active_arr[$i]['contract_number'] = $value['contract_number'];
            $prev_loan_active_arr[$i]['loan_amount_balance'] = $value['loan_amount_balance'];
            $prev_loan_active_arr[$i]['checked'] = "principal";
            $loan_amount = $value['loan_amount_balance'];//เงินกู้
            $loan_id = $value['id'];//ใช้หาเรทดอกเบี้ยใหม่ 26/5/2562
            $loan_type = $value['loan_type'];
            $date1 = $value['date_last_interest'];//วันคิดดอกเบี้ยล่าสุด
            $date2 = $date_interesting;

            $this->db->select(array(
                '*'
            ));
            $this->db->from('coop_finance_month_detail as t1');
            $this->db->where("
				t1.loan_id = '".$value['id']."'
				AND t1.run_status = '0'
				AND t1.pay_type = 'principal'
			");
            $row = $this->db->get()->result_array();
            $principal_month = 0;
            $interest_loan = 0;
            if(sizeof($row) && $this->Setting_model->get("loan_prev_deduct_finance_month")==1) {
                foreach ($row as $key2 => $value2) {
                    $principal_month += $value2['pay_amount'];
                }
                $principal = $value['loan_amount_balance'] - $principal_month;
                if($this->Setting_model->get("calc_interest_on_getPrevLoan_have_finance_month_detail")==1){
                    $calc_interest = array();
                    $calc_interest['loan_id'] = $value['id'];
                    $calc_interest['entry_date'] = $date2;
                    $calc_interest['loan_type'] = $loan_type;
                    //$interest_loan = $this->loan_libraries->calc_interest_loan_multi_rate($loan_amount, $loan_type, $date1, $date2);
                    $interest_arrears = $this->LoanCalc->calc('PL', $calc_interest);
                    $interest_loan = ROUND($interest_arrears['interest_arrear_bal'], 2);
                }
            }else{
                //$interest_loan = $this->loan_libraries->calc_interest_loan_multi_rate($loan_amount, $loan_type, $date1, $date2);
                $calc_interest = array();
                $calc_interest['loan_id'] = $value['id'];
                $calc_interest['entry_date'] = $date2;
                $calc_interest['loan_type'] = $loan_type;
                $interest_arrears = $this->LoanCalc->calc('PL', $calc_interest);
                $interest_loan = ROUND($interest_arrears['interest_arrear_bal'], 2);
                $principal = $value['loan_amount_balance'];
            }

            $fee = 0;
            $prev_loan_active_arr[$i]['fee'] = $fee;
            $prev_loan_active_arr[$i]['interest'] = $interest_loan;
            $prev_loan_active_arr[$i]['type'] = 'loan';
            $prev_loan_active_arr[$i]['prev_loan_total'] = $principal;
            $prev_loan_active_arr[$i]['principal_without_finance_month'] = $principal;
            $i++;
        }


        return $prev_loan_active_arr;
    }

    function cal_days_in_year($year){
        return 365;

    }
    function force_summonth($summonth,$period){
        return $summonth;
    }

    function parseFloat($amt){
        if(is_string($amt)){
            $_amt = explode(',', $amt);
            return  (double)join("",$_amt);
        }
        return $amt;
    }

    public function calc_period($contract)
    {
        $contract['createdatetime'] = date('Y-m-d', strtotime(str_replace('/', '-', $contract['createdatetime'])." -543 Year"));
        $interest = (double)$contract['interest_per_year']; // อัตราดอกเบี้ย
        $loan = self::parseFloat($contract['loan_amount']);; // จำนวนเงินกู้
        $pay_type = $contract['pay_type']; // ปรเภท ชำระเท่ากันทุกงวด,ต้นเท่ากันทุกงวด
        $period = self::parseFloat($contract['pay_amount']) ;// จำนวน งวด  หรือ เงิน แล้วแต่ type
        $period_old = 0; // จำนวน งวด  หรือ เงิน แล้วแต่ type  เก่า
        $money_period_2 = self::parseFloat($contract['money_period_1']); // ยอดผ่อนชำระต่อเดือน  เก่า
        $_day = $day = (double)date('d', strtotime($contract['createdatetime']));
        $month = (double)date('n', strtotime($contract['createdatetime']));
        //$year  = (double)$data_post["year"];
        $year = (double)date('Y', strtotime($contract['createdatetime']));
        $_period_amt = self::parseFloat($contract['period_amount']);
        $rounding_calc_period_interest =  $this->center_function->rounding_calc_period_interest();
        $round_interest = $this->Setting_model->get("round_interest");
        //หาดอกเบี้ยหัก ณ ที่จ่าย
        $payment_interest_current = 0;

        $date_interest = ($year) . "-" . $month . "-" . $day;
//        $date_interest_start = date('Y-m-d', strtotime('-1 day', strtotime($date_interest)));
//        $date_interest_end = date('Y-m-t', strtotime($date_interest));
//        $data_interest_count = date_diff(date_create($date_interest_start), date_create($date_interest_end));
//        $date_amt = $data_interest_count->format('%a');
//        if ($day >= 6) {
//            if ($date_amt) {
//                $payment_interest_current = ROUND(($loan * ($interest / 100) / self::cal_days_in_year(($year))) * $date_amt, 0, PHP_ROUND_HALF_UP);
//            }
//        }
//
        $day_of_month = 0;
        if ($day >= 6) {
            $day_of_month = date('t', strtotime($date_interest . " +1 month"));
        } else {
            $day_of_month = date('t', strtotime($date_interest));
        }
        if ($day >= 6) {
            //$day = date('t', strtotime($day_of_month));
            $month += 1;
        }
        
        if ($month > 12) {
            $month = 1;
            $year += 1;
        }

        $period_type = 2; // ประเภท งวดหรือจำนวนเงิน
        $loan_type = $contract["loan_type"]; // ประเภทการกู้เงิน


        //echo " period_type: $period_type, pay_type: $pay_type";exit;

        if ($period_type == '1' && $pay_type == '2') {
            $total_per_period = $loan / $period;

            $date_start = ($year) . "-" . $month . "-" . $day;
            $date_period_1 = date('Y-m-t', strtotime('+1 month', strtotime($date_start)));
            $diff = date_diff(date_create($date_start), date_create($date_period_1));
            $date_count = $diff->format("%a");
            $date_count = 31;
            $interest_period_1 = ((($loan * $interest) / 100) / self::cal_days_in_year(($year))) * $date_count;

            $per_period = ($loan * (($interest / 100) / 12)) / (1 - pow(1 / (1 + (($interest / 100) / 12)), $period));
            if ($period_old == $period) {
                $period = $money_period_2;
            } else {
                $period = $per_period;
            }
            $period_type = 2;
        }
        $date_start = ($year) . "-" . $month . "-" . $day;
        $pay_period = $loan / $period;
        $a = ceil($pay_period / 10) * 10;
        $daydiff = date('t', strtotime(($year) . "-" . $month . "-" . $day)) - $day;

        $loan_remain = $loan;
        $is_last = FALSE;
        $total_loan_pri = 0;
        $total_loan_int = 0;
        $total_loan_pay = 0;
        $d = $period - 1;
        for ($i = 1; $i <= $period; $i++) {
            if ($loan_remain <= 0) {
                break;
            }

            if($i == 1 && $day <= 5){
                $day -= 1;
            }

            if ($pay_type == 1) {
                if ($period_type == 1) {
                    if ($month > 12) {
                        $month = 1;
                        $year += 1;
                    }

                    $loan_pri = ceil($a / $rounding_calc_period_interest) * $rounding_calc_period_interest;
                    $nummonth = cal_days_in_month(CAL_GREGORIAN, $month, ($year));
                    $summonth = $nummonth;
                    $daydiff = 31 - $day;
                    if ($i == 1) {
                        if ($daydiff >= 0) {
                            //$month += 1;
                            if ($month > 12) {
                                $month = 1;
                                $year += 1;
                            }
                            $nummonth = cal_days_in_month(CAL_GREGORIAN, $month, ($year));
                            $summonth = $nummonth;
                            $summonth = $daydiff + 31;
                        }
                    }

                    $summonth = self::force_summonth($summonth, $i);
                    //echo "1 :: ".$daydiff." :: ".$summonth." :: ".$date_start; exit;
                    $loan_int = round($loan_remain * ($interest / (self::cal_days_in_year(($year)) / $summonth)) / 100, $round_interest);
                    if ($loan_pri < 0) {
                        $loan_pri = 0;
                    }
                    $loan_pay = $loan_pri + $loan_int;
                    $loan_remain -= ceil($loan_pri / $rounding_calc_period_interest) * $rounding_calc_period_interest;
                } else if ($period_type == 2) {
                    if ($month > 12) {
                        $month = 1;
                        $year += 1;
                    }
                    
                    if ($i == 1) {
                        $nummonth = cal_days_in_month(CAL_GREGORIAN, $month, ($year));
                        $daydiff = $nummonth - $day;
                        $summonth = $daydiff;
                        if ($daydiff >= 0) {
                            if ($month > 12) {
                                $month = 1;
                                $year += 1;
                            }
                            $nummonth = cal_days_in_month(CAL_GREGORIAN, $month, ($year));
                            $summonth += $nummonth + 1;
                        }
                    }else{
                        $nummonth = cal_days_in_month(CAL_GREGORIAN, $month, ($year));
                        $summonth = $nummonth;
                    }

                    $summonth = self::force_summonth($summonth, $i);
                    //echo "2 :: ".$daydiff." :: ".$summonth; exit;
                    $loan_pri = ceil($period / $rounding_calc_period_interest) * $rounding_calc_period_interest;
                    $loan_int = round($loan_remain * ($interest / (self::cal_days_in_year(($year)) / $summonth)) / 100, $round_interest);
                    if ($loan_pri < 0) {
                        $loan_pri = 0;
                    }
                    $loan_pay = $loan_pri + $loan_int;
                    $loan_remain -= ceil($loan_pri / 10) * 10;
                }

            } else if ($pay_type == 2) {
                if ($period_type == 1) {
                    if ($month > 12) {
                        $month = 1;
                        $year += 1;
                    }

                    $loan_pri = ceil($a / $rounding_calc_period_interest) * $rounding_calc_period_interest;
                    $nummonth = cal_days_in_month(CAL_GREGORIAN, $month, ($year));
                    $summonth = $nummonth;
                    $daydiff = date('t', strtotime(($year) . '-' . sprintf('%02d', $month) . '-' . $day)) - $day;
                    if ($i == 1) {
                        if ($daydiff >= 0) {
                            $month += 1;
                            if ($month > 12) {
                                $month = 1;
                                $year += 1;
                            }
                            $nummonth = cal_days_in_month(CAL_GREGORIAN, $month, ($year));
                            $summonth = $nummonth;
                            $summonth = $daydiff;
                        }
                    }
                    $summonth = self::force_summonth($summonth, $i);
                    //echo "3 :: ".$daydiff." :: ".$summonth; exit;
                    $loan_int = round($loan_remain * ($interest / (self::cal_days_in_year(($year)) / $summonth)) / 100, $round_interest);
                    $loan_pri = $loan_pri - $loan_int;

                    if ($loan_pri < 0) {
                        $loan_pri = 0;
                    }
                    $loan_pay = $loan_pri + $loan_int;
                    $loan_remain -= ceil($loan_pri / $rounding_calc_period_interest) * $rounding_calc_period_interest;

                } else if ($period_type == 2) {
                    if ($month > 12) {
                        $month = 1;
                        $year += 1;
                    }
                    $nummonth = cal_days_in_month(CAL_GREGORIAN, $month, ($year));
                    $summonth = $nummonth;
                    $daydiff = date('t', strtotime(($year) . '-' . sprintf('%02d', $month) . '-' . $day)) - $day;

                    if ($i == 1) {
                        if ($daydiff >= 0) {
                            //$month += 1;

                            if ($month > 12) {
                                $month = 1;
                                $year += 1;
                            }
                            $nummonth = cal_days_in_month(CAL_GREGORIAN, $month, ($year));
                            $summonth = $nummonth;
                            if ($_day > 5) {
                                $summonth = $daydiff + $nummonth + 1;
                            } else {
                                $summonth = $daydiff;
                            }
                        }
                    }
                    $summonth = self::force_summonth($summonth, $i);
                    $loan_pri = ceil($period / 1) * 1;
                    $loan_int = round($loan_remain * ($interest / (self::cal_days_in_year(($year)) / $summonth)) / 100, $round_interest);
                    $loan_pri = $loan_pri - $loan_int;
                    $forcast_remain = round($loan_remain - $loan_pri, 0);

                    if (round($forcast_remain, 0) < round($period, 0) && $i > ($_period_amt - 1)) {
                        $loan_pri = round($loan_remain, 0);
                    }
                    if ($loan_pri < 0) {
                        $loan_pri = 0;
                    }
                    $loan_pay = $loan_pri + $loan_int;
                    $loan_remain -= $loan_pri ;
                }
                //echo ($loan_int);exit;
            }
            if ($loan_remain <= 0) {
                $loan_pri += $loan_remain;
                $loan_pay = $loan_pri + $loan_int;
                $loan_remain = 0;
                @$count = $count + 1;
            }

            $sumloan = $loan_remain + $loan_pri;
            $sumloanarr[] = $loan_remain + $loan_pri;
            $sumint[] = $loan_int;
            if ($i == $period) {
                $loan_pri = $sumloanarr[$d];
                $loan_pay = $loan_pri + $loan_int;
            }

            @$total_loan_int += $loan_int;
            @$total_loan_pri += $loan_pri;
            @$total_loan_pay += $loan_pay;

            @$total_loan_pri_m += $loan_pri;
            @$total_loan_int_m += $loan_int;
            @$total_loan_pay_m += $loan_pay;

            if($i === 1){
                $contract['date_period_1'] = $nummonth."/".$month."/".$year;
                $contract['date_start_period'] = ($year)."-".sprintf('%02d',$month)."-".$nummonth;
                $contract['money_period_1'] =  number_format($loan_pay,2);
                $contract['first_interest'] = $loan_int;
                $contract['summonth_period_1'] = $summonth;
            }

            if($i === 2){
                $contract['date_period_2'] = $nummonth."/".$month."/".$year;
                $contract['money_period_2'] =  $loan_pay;
                $contract['summonth_period_2'] = $summonth;
            }

            //$contract['coop_loan_period'][$i]['loan_id'] = $contract['id'];
            $contract['coop_loan_period'][$i]['period_count'] =  $i;
            $contract['coop_loan_period'][$i]['outstanding_balance']=  number_format($sumloan,2,".","");
            $contract['coop_loan_period'][$i]['date_period']= ($year)."-".sprintf('%02d',$month)."-".$nummonth;
            $contract['coop_loan_period'][$i]['date_count']= $summonth;
            $contract['coop_loan_period'][$i]['interest']= number_format($loan_int,2,".","");
            $contract['coop_loan_period'][$i]['principal_payment']=number_format($loan_pri,2,".","");
            $contract['coop_loan_period'][$i]['total_paid_per_month'] = number_format($loan_pay,2,".","");

            if($is_last) {
                break;
            }
            $month++;

            if($month > 12){

                if ($month > 12) {
                    $total_loan_int_m = 0;
                    $total_loan_pri_m = 0;
                    $total_loan_pay_m = 0;
                }

            }else if(($i-1) == $d){
                $is_last = TRUE;
            }

        }
        $contract['last_period'] = date('Y-m-t',strtotime('-1 month',strtotime(($year)."-".$month."-".$nummonth)));
        $contract['loan_interest_amount'] = number_format($total_loan_int,0,".","");
        $contract['total_loan_pri'] = number_format($total_loan_pri,0,".","");
        $contract['loan_amount_total'] = number_format($total_loan_pay,0,".","");
        $contract['loan_amount_total_balance'] = number_format($total_loan_pay,0,".","");
        $contract['max_period'] = $i-1;
        $contract['interest_current_value'] = number_format($payment_interest_current, 0);

        return $contract;

    }

    /**
     * สร้างเลขคำข้อของสัญญากู้เงิน
     * @author adisak.sununtha@gmail.com
     */
    public function findTermOfLoan($loan_type = ""){

       return $this->db->select('*')->from('coop_term_of_loan tb1')
            ->join('coop_loan_name tb2', 'tb1.type_id=tb2.loan_name_id', 'inner')
            ->where(array('tb2.loan_name_id' => $loan_type))
            ->get()->row_array();

    }

    /**
     * สร้างเลขคำข้อของสัญญากู้เงิน
     * @author adisak.sununtha@gmail.com
     */
    public function generatePetitionNumber($loan_type = ""){

        $this->db->select('accm_month_ini');
        $this->db->from('coop_account_period_setting');
        $this->db->limit(1);

        $row_period_setting = $this->db->get()->row_array();
        $accm_month_ini = @$row_period_setting['accm_month_ini'];
        if((int)date('m') < (int)$accm_month_ini){
            $year_now = date('Y');
        }else{
            $year_now = date('Y')+1;
        }

        $year = substr($year_now+543, 2, 2);
        $prefix = self::findTermOfLoan($loan_type)['prefix_code'];

        $this->db->select('MAX(CONVERT(RIGHT(petition_number, 4), DECIMAL)) AS petition_number');
        $this->db->from("coop_loan");
        $this->db->where("YEAR(createdatetime) = '".$year_now."' and loan_type = '".$loan_type."'");
        $rs_petition_number = $this->db->get()->result_array();
        $row_petition_number = @$rs_petition_number[0];
        $petition_number = (int)@$row_petition_number['petition_number']+1;
        return sprintf('%s%s%05d',$year, $prefix, @$petition_number);

    }

    /**
     * บันทึกข้อมูลหลักประกันประเภทหุ้น
     * @author adisak.sununtha@gmail.com
     */
    public function shareGuarantee($loan_id, $item){

        $data_insert = array();
        $data_insert['loan_id'] = $loan_id;
        $data_insert['guarantee_type'] = $item['type'];
        $data_insert['amount'] = $item['amount'];
        $data_insert['price'] = $item['estimate'];
        $data_insert['remark'] = $item['remark'];
        $this->db->insert('coop_loan_guarantee', $data_insert);
    }

    /**
     * บันทึกข้อมูลหลักประกันประเภทบุลคล
     * @author adisak.sununtha@gmail.com
     */
    public function personalGuarantee($loan_id, $item){
        $data_insert_person = array();
        $data_insert_person['loan_id'] = $loan_id;
        $data_insert_person['guarantee_person_id'] = $item['number'];
        $data_insert_person['guarantee_person_amount'] = $item['amount'];
        $data_insert_person['guarantee_person_amount_balance'] = $item['amount'];
        $this->db->insert('coop_loan_guarantee_person', $data_insert_person);

        $data_insert = array();
        $data_insert['loan_id'] = $loan_id;
        $data_insert['guarantee_type'] = $item['type'];
        $data_insert['amount'] = $item['amount'];
        $data_insert['price'] = $item['estimate'];
        $data_insert['remark'] = $item['remark'];
        $data_insert['member_id'] =  $item['number'];
        $this->db->insert('coop_loan_guarantee', $data_insert);

    }

    /**
     * บันทึกข้อมูลหลักประกันประเภทเงินฝาก
     * @author adisak.sununtha@gmail.com
     */
    public function depositGuarantee($loan_id, $item){

        $data_insert = array();
        $data_insert['loan_id'] = $loan_id;
        $data_insert['guarantee_type'] = $item['type'];
        $data_insert['amount'] = $item['amount'];
        $data_insert['price'] = $item['estimate'];
        $data_insert['remark'] = $item['remark'];
        $data_insert['account_id'] = $item['number'];
        $this->db->insert('coop_loan_guarantee', $data_insert);

    }

    /**
     * บันทึกข้อมูลหลักประกันประเภทหลักทรัพย์ค้ำประกัน
     * @author adisak.sununtha@gmail.com
     */
    public function realEstateGuarantee($loan_id, $item){
        $data_insert_real_estate = array();
        $data_insert_real_estate['loan_id'] = @$loan_id;
        $data_insert_real_estate['land_number'] = $item['number'];
        $data_insert_real_estate['estate_name'] = $item['name'];
        $data_insert_real_estate['estimate_value'] = $item['estimate'];
        $data_insert_real_estate['amount_value'] = $item['amount'];

        $this->db->insert('coop_loan_guarantee_real_estate', $data_insert_real_estate);

        $data_insert = array();
        $data_insert['loan_id'] = $loan_id;
        $data_insert['guarantee_type'] = $item['type'];
        $data_insert['amount'] = $item['amount'];
        $data_insert['price'] = $item['estimate'];
        $data_insert['remark'] = $item['remark'];

        $this->db->insert('coop_loan_guarantee', $data_insert);
    }

    /**
     * ค้นหาข้อมูลสัญญาที่ใช้บุลคลค้ำประกัน
     * @author adisak.sununtha@gmail.com
     */
    public function getGuaranteePerson($id, $member_id){

        $data = array('status' => 500, 'data' => []);

        $dataPerson =  $this->db->select(array('member_id as number',
            "concat(prename_short, firstname_th, ' ', lastname_th) as name",
            'guarantee_person_amount as estimate',
            'guarantee_person_amount_balance as amount'))
            ->from('coop_loan_guarantee_person as tb1')
            ->join('coop_mem_apply as tb2', 'tb1.guarantee_person_id=tb2.member_id', 'inner')
            ->join('coop_prename as tb3','tb3.prename_id=tb2.prename_id', 'inner')
            ->where(array('tb1.loan_id' => $id, 'tb1.guarantee_person_id' => $member_id))
            ->get()->row_array();

        $this->db->select(array('amount', 'price as estimate', 'guarantee_type as typeId', 'remark'));
        $guarantee = $this->db->get_where('coop_loan_guarantee',
            array('loan_id' => $id, 'member_id' => $member_id, 'guarantee_type' => 1), 1)->row_array();

        $guarantee['number'] = $dataPerson['number'];
        $guarantee['name'] = $dataPerson['name'];
        $guarantee['typeName'] = $this->_typeName[0];

        return sizeof($guarantee) ? $guarantee : $data;
    }

    private $_typeName = ['สมาชิกคำประกัน', 'หุ้นค้ำประกัน', 'เงินฝากค้ำประกัน', 'สินทรัพย์ค้ำประกัน'];

    /**
     * ค้นหาข้อมูลสัญญาที่ใช้หุ้นค้ำประกัน
     * @author adisak.sununtha@gmail.com
     */
    public function getGuaranteeShare($loanId){

        //echo $loanId; exit;
        $personal = $this->db->select(array('t1.member_id', 'prename_short', 't1.firstname_th', 't1.lastname_th'))
            ->from('coop_loan as t2')
            ->join('coop_mem_apply as t1', 't2.member_id=t1.member_id','inner')
            ->join('coop_prename as t3','t3.prename_id=t1.prename_id', 'inner')
            ->where(array('t2.id' => $loanId))
            ->get()->row_array();

        $this->db->select(array('amount', 'price as estimate', 'guarantee_type as typeId', 'remark'));
        $guarantee = $this->db->get_where('coop_loan_guarantee',
            array('loan_id' => $loanId, 'guarantee_type' => '2'), 1)->row_array();

        $guarantee['name'] = $personal['prename_short'].$personal['firstname_th']." ".$personal['lastname_th'];
        $guarantee['number'] = $personal['member_id'];
        $guarantee['typeName'] = $this->_typeName[1];

        return $guarantee;
    }

    /**
     * ค้นหาข้อมูลสัญญาที่ใช้หลักทรัพย์ค้ำประกัน
     * @author adisak.sununtha@gmail.com
     */
    public function getGuaranteeRealEstate($loanId){

        $estate = $this->db->select(array('land_number as number', 'estimate_value as estimate', 'amount_value as amount', 'estate_name as name'))
            ->from('coop_loan_guarantee_real_estate')
            ->where(array('loan_id' => $loanId))->get()->row_array();

        $this->db->select(array('amount as estimate', 'price as amount', 'guarantee_type as typeId', 'remark'));
        $guarantee = $this->db->get_where('coop_loan_guarantee',
            array('loan_id' => $loanId, 'guarantee_type' => '4'), 1)->row_array();

        $guarantee['typeName'] = $this->_typeName[3];
        $guarantee['number'] = $estate['number'];
        $guarantee['name'] = $estate['name'];

        if(sizeof($estate)){
            $data = $guarantee;
        }
        return $data;
    }

    /**
     * ค้นหาข้อมูลสัญญาที่ใช้เงินฝากค้ำประกัน
     * @author adisak.sununtha@gmail.com
     */
    public function getGuaranteeDeposit($loanId){

        $this->db->select(array('account_id as number','amount', 'price as estimate', 'guarantee_type as typeId', 'remark'));
        $guarantee = $this->db->get_where('coop_loan_guarantee',
            array('loan_id' => $loanId, 'guarantee_type' => '3'), 1)->row_array();

        $data = $this->db->get_where('coop_maco_account', array('account_id' => $guarantee['number']))->row_array();

        $guarantee['name'] = $data['account_name'];
        $guarantee['typeName'] = $this->_typeName[2];

        return $guarantee;
    }

    public function getLoanDeductList(){
        $this->db->select(array(
            'loan_deduct_list_code','loan_deduct_list','deduct_type','loan_deduct_show', 'loan_deduct_status'
        ));
        $this->db->from('coop_loan_deduct_list');
        $this->db->where(" deduct_type='deduct' AND loan_deduct_status = 1");
        $this->db->order_by('sort asc');
        $rs_loan_deduct_list = $this->db->get()->result_array();
        $loan_deduct_list_odd = array();
        $loan_deduct_list_even = array();
        $i=1;
        foreach($rs_loan_deduct_list as $key => $value){
            if($i==1){
                $loan_deduct_list_odd[] = $value;
                $i++;
            }else{
                $loan_deduct_list_even[] = $value;
                $i = 1;
            }
        }
        $arr_data['loan_deduct_list'] = $rs_loan_deduct_list;
        $arr_data['loan_deduct_list_odd'] = $loan_deduct_list_odd;
        $arr_data['loan_deduct_list_even'] = $loan_deduct_list_even;

        return $arr_data;
    }

    public function getLoanBuyList(){
        $this->db->select(array(
            'loan_deduct_list_code','loan_deduct_list','deduct_type','loan_deduct_show', 'loan_deduct_status'
        ));
        $this->db->from('coop_loan_deduct_list');
        $this->db->where("loan_deduct_list_code != 'deduct_pay_prev_loan' AND deduct_type='buy' AND loan_deduct_status = 1");
        $this->db->order_by('sort asc');
        $rs_loan_buy_list = $this->db->get()->result_array();
        $loan_buy_list_odd = array();
        $loan_buy_list_even = array();
        $i=1;
        foreach($rs_loan_buy_list as $key => $value){
            if($i==1){
                $loan_buy_list_odd[] = $value;
                $i++;
            }else{
                $loan_buy_list_even[] = $value;
                $i = 1;
            }
        }
        $arr_data['loan_buy_list'] = $rs_loan_buy_list;
        $arr_data['loan_buy_list_odd'] = $loan_buy_list_odd;
        $arr_data['loan_buy_list_even'] = $loan_buy_list_even;

        return $arr_data;
    }
	
	//เงินกู้ฉุกเฉินผ่านแอพ
    public function getLoanMobile(){
		$loan_name_id = $this->db->select(array('loan_name_id'))->from('coop_loan_name')->where(" loan_app = '1'")->limit(1)->get()->row_array()['loan_name_id'];
		return $loan_name_id;
	}
	
	//คำนวนสิทธิ์กู้สูงสุด
    public function getMaxLoanLimit($data){		
		$result = 0;
		$salary = $data['salary'];
		$max_limit = $data['max_limit'];
		$multiple_salary = $data['multiple_salary'];
		$estimate_amt = $salary * $multiple_salary;

		if($multiple_salary > 0) { //กู้ได้ x เท่าของเงินเดือน
			$result = $estimate_amt >= $max_limit ? $max_limit : $estimate_amt;
		}

        return $result;
    }
	
	//คำนวณยอดผ่อนต่องวด
    public function getMoneyPerPeriod($data){
		//แบบคงยอด
		$loan_amount = $data['loan_amount'];
		$interest = $data['interest_rate'];
		$period = $data['period_amount'];
		$result = round(($loan_amount * (($interest / 100) / 12)) / (1 - pow(1 / (1 + (($interest / 100) / 12)), $period)));
        return $result;
    }
	
	//ยอดหักกลบหนี้เดิมและดอกเบี้ย ของประเภทเงินกู้ที่ขอกู้
    public function getDeductAmount($member_id, $loan_type){
		ini_set("precision",14);
		$result = array();
		$row = $this->db->select(array('id','contract_number','loan_amount','loan_amount_balance','date_last_interest'))
						->from('coop_loan')
						->where("member_id  = '{$member_id}' AND loan_type = '{$loan_type}'")
						->get()->row_array();
		
		if(!empty($row)){
			$result = $row;			
			$loan_amount = $row['loan_amount_balance'];//เงินกู้
            $loan_id = $row['id'];//ใช้หาเรทดอกเบี้ยใหม่ 26/5/2562
            $date1 = $row['date_last_interest'];//วันคิดดอกเบี้ยล่าสุด
            $date2 = date('Y-m-d');

            //$interest_loan = 0;
            //$interest_loan = $this->loan_libraries->calc_interest_loan($loan_amount, $loan_id, $date1, $date2);
            $calc_interest = array();
            $calc_interest['loan_id'] =  $loan_id;
            $calc_interest['entry_date'] = $date2;
            $calc_interest['loan_type'] = $row['loan_type'];
            $interest_loan = $this->LoanCalc->calc('PL',$calc_interest);

            $interest_loan = ROUND($interest_loan['interest_arrears_bal'], 2);
			$result['interest_loan'] = $interest_loan;
			$result['deduct_amount'] = ROUND(($loan_amount+$interest_loan),2);

		}
		return $result;
    }
	
	public function getMemberLoanMobile($member_id){
		$result = array();
		$row = $this->db->select(array('member_id'))
						->from('coop_setting_member_loan_mobile')
						->where("member_id  = '{$member_id}' AND `status` = '1'")
						->get()->row_array();
		
		if(!empty($row)){
			$result = $row;
		}
		return $result;
    }
	
	//เช็คเงินฝากประเภทหลัก
	public function getMainAccount($member_id){
		$result = array();
		$row = $this->db->select(array('t1.account_id','t2.main_account','t2.type_id'))
						->from('coop_maco_account AS t1')
						->join("coop_deposit_type_setting AS t2","t1.type_id = t2.type_id","inner")
						->where("t1.mem_id = '{$member_id}' AND t2.main_account = '1' AND t1.account_status = 0")
						->get()->row_array();
		
		if(!empty($row)){
			$result = $row;
		}
		return $result;
    }
	
	public function check_status($data_loan){		
		$data_arr = array();
		$member_id = $data_loan['member_id'];
		$check_member_loan = $this->getMemberLoanMobile($member_id);
		$check_bank_main = $this->getMainAccount($member_id);
		//echo '<pre>'; print_r($data_loan); echo '</pre>';
		$data_arr = $data;
		if(empty($check_bank_main)){
			$data_arr['status'] = 'error';
			$data_arr['msg'] = 'ไม่สามารถกู้ได้ กรุณาเปิดเงินฝากบัญชีหลัก';
		}else if(empty($check_member_loan)){
			$data_arr['status'] = 'error';
			$data_arr['msg'] = 'ไม่พบสิทธิ์การกู้ของท่าน กรุณาติดต่อสหกรณ์';
		}else if($data_loan['loan_amount'] > $data_loan['max_loan_limit']){
			$data_arr['status'] = 'error';
			$data_arr['msg'] = 'ไม่สามารถกู้ได้เนื่องจากจำนวนเงินที่ขอกู้มากกว่าสิทธิ์ขอกู้';
		}else if($data_loan['estimate_money'] <= 0){
			$data_arr['status'] = 'error';
			$data_arr['msg'] = 'ไม่สามารถกู้ได้เนื่องจากจำนวนเงินที่จะได้รับเหลือน้อยกว่า 0 บาท กรุณาติดต่อสหกรณ์';
		}else{
			$data_arr['status'] = 'ok';
			$data_arr['msg'] = 'ทำรายการต่อ';
		}
		return $data_arr;
    }
	
	public function getDataLoanMobile($data){		 
		$arr_data = array();
		$member_id = $data['member_no'];
		$loan_name_id = $this->getLoanMobile();
		$term_of_loan = $this->findTermOfLoan($loan_name_id);

		$arr_max_loan = array();
		$arr_max_loan['salary'] = $data['salary'];
		$arr_max_loan['max_limit'] = $term_of_loan['credit_limit'];
		$arr_max_loan['multiple_salary'] = $term_of_loan['less_than_multiple_salary'];
	
		$arr_data['member_id'] = $member_id;
		$arr_data['loan_type'] = $loan_name_id;
		$arr_data['salary'] = $data['salary'];	//salary = เงินเดือน
		$arr_data['max_loan_limit'] = $this->getMaxLoanLimit($arr_max_loan);	//max_loan_limit = สิทธิ์กู้สูงสุด
		$arr_data['max_period'] = $term_of_loan['max_period'];	//max_period = จำนวนงวดสูงสุด
		$arr_data['loan_amount'] = ($data['loan_amount'] > 0)?$data['loan_amount']:$arr_data['max_loan_limit'];	//loan_amount = จำนวนเงินที่ขอกู้
		$arr_data['period_amount'] = ($data['period_amount'] > 0)?$data['period_amount']:$term_of_loan['max_period'];	//period_amount = จำนวนงวด

		$arr_per_period = array();
		$arr_per_period['loan_amount'] = $arr_data['loan_amount'];
		$arr_per_period['interest_rate'] = $term_of_loan['interest_rate'];
		$arr_per_period['period_amount'] = $arr_data['period_amount'];

		$arr_data['money_per_period'] = $this->getMoneyPerPeriod($arr_per_period);	//money_per_period = ผ่อนต่องวด

		$data_deduct = $this->getDeductAmount($member_id, $loan_name_id);
		//echo '<pre>'; print_r($data_deduct); echo '</pre>';
		$arr_data['deduct_amount'] = $data_deduct['deduct_amount'];	//deduct_amount = ยอดหักกลบหนี้เดิมและดอกเบี้ย
		$arr_data['deduct_interest'] = $data_deduct['interest_loan']; //ต้นหนี้เดิม
		$arr_data['deduct_principal'] = $data_deduct['loan_amount_balance']; //ดอกเบี้ยหนี้เดิม
		$arr_data['deduct_loan_id'] = $data_deduct['id']; //loan_id หนี้เดิม
		$arr_data['estimate_money'] = ($arr_data['loan_amount']-$arr_data['deduct_amount']);	//estimate_money = จำนวนเงินที่จะได้รับ
		$arr_data['pay_type'] = 2;	//pay_type = ประเภทการชำระเงิน 1=ชำระต้นเท่ากันทุกงวด 2=ชำระยอดเท่ากันทุกงวด
		$arr_data['period_type'] = 1;	//period_type = ประเภทการคำนวณการชำระเงิน 1=งวดที่ต้องการผ่อน 2=เงินที่ต้องการผ่อนต่องวด
		$arr_data['interest_per_year'] = $term_of_loan['interest_rate'];	//interest_per_year = อัตราดอกเบี้ย

		//echo '<pre>'; print_r($arr_data); echo '</pre>'; exit;
		//loan_type = ประเภทเงินกู้
		//salary = เงินเดือน
		//max_loan_limit = สิทธิ์กู้สูงสุด
		//max_period = จำนวนงวดสูงสุด
		//loan_amount = จำนวนเงินที่ขอกู้
		//period_amount = จำนวนงวด
		//money_per_period = ผ่อนต่องวด
		//deduct_amount = ยอดหักกลบหนี้เดิมและดอกเบี้ย
		//estimate_money = จำนวนเงินที่จะได้รับ
		//pay_type = ประเภทการชำระเงิน 1=ชำระต้นเท่ากันทุกงวด 2=ชำระยอดเท่ากันทุกงวด
		//period_type = ประเภทการคำนวณการชำระเงิน 1=งวดที่ต้องการผ่อน 2=เงินที่ต้องการผ่อนต่องวด
		//interest_per_year = อัตราดอกเบี้ย
		//date_receive_money = วันที่ชำระงวดแรก
		return $arr_data;
	}

	public function gen_arr_insert($data){		 
		$arr_data = array();	
		$arr_data['data']['coop_loan']['loan_type'] = $data['loan_type'];
		$arr_data['data']['coop_loan']['salary'] = $data['salary'];
		$arr_data['data']['coop_loan']['loan_amount'] = $data['loan_amount'];
		$arr_data['data']['coop_loan']['pay_type'] = $data['pay_type'];
		$arr_data['data']['coop_loan']['period_amount'] = $data['period_amount'];
		$arr_data['data']['coop_loan']['createdatetime'] = $this->center_function->mydate2date(date('Y-m-d'));
		$arr_data['data']['coop_loan']['pay_amount'] = $data['money_per_period'];
		$arr_data['data']['coop_loan']['member_id'] = $data['member_id'];
		$arr_data['data']['coop_loan']['period_type'] = $data['period_type'];
		$arr_data['data']['coop_loan']['interest_per_year'] = $data['interest_per_year'];
		$arr_data['data']['coop_loan']['petition_number'] = '';

		$arr_data['data']['loan_deduct_profile']['estimate_receive_money'] = $data['estimate_money'];
		
		$arr_data['data']['loan_deduct']['deduct_pay_prev_loan'] = $data['deduct_amount'];
		
		$arr_data['prev_loan'][0]['interest'] = $data['deduct_interest'];
		$arr_data['prev_loan'][0]['amount'] = $data['deduct_amount'];
		$arr_data['prev_loan'][0]['id'] = $data['deduct_loan_id'];
		$arr_data['prev_loan'][0]['pay_type'] = 'all';
		$arr_data['prev_loan'][0]['type'] = 'loan';
		return $arr_data;
	}
	
	public function getAmountTransfer($loan_id){
		$result = array();
		$row = $this->db->select(array('estimate_receive_money'))
						->from('coop_loan_deduct_profile')
						->where("loan_id = '{$loan_id}'")
						->limit(1)
						->get()->row_array()['estimate_receive_money'];		
		if(!empty($row)){
			$result = $row;
		}
		return $result;
    }
	
	public function get_account_transaction_balance($account_id){		
		$transaction_balance = $this->db->select(array('transaction_balance'))
					->from('coop_account_transaction')
					->where("account_id = '{$account_id}'")
					->order_by('transaction_time DESC,transaction_id DESC')
					->limit(1)
					->get()->row_array()['transaction_balance'];
		return $transaction_balance;
    }

    public function getInstallmentEnable($id){
        return $this->db->select(array("t1.installment_enabled"))->from("coop_term_of_loan as t1")
            ->join("coop_loan as t2", "t2.loan_type=t1.type_id", "inner")
            ->where(array('t2.id' => $id))
            ->order_by("start_date", "desc")
            ->limit(1)
            ->get()->row_array()['installment_enabled'];
    }

}
