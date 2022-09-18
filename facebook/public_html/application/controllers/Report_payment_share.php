<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_payment_share extends CI_Controller {
	public $month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
	public $month_short_arr = array('1'=>'ม.ค.','2'=>'ก.พ.','3'=>'มี.ค.','4'=>'เม.ย.','5'=>'พ.ค.','6'=>'มิ.ย.','7'=>'ก.ค.','8'=>'ส.ค.','9'=>'ก.ย.','10'=>'ต.ค.','11'=>'พ.ย.','12'=>'ธ.ค.');

	function __construct()
	{
		parent::__construct();
		$this->month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
	}

    public function coop_report_pay_month(){
        $arr_data = array();

        $this->db->select(array('id','mem_group_name'));
        $this->db->from('coop_mem_group');
        $this->db->where("mem_group_type = '1'");
        $row = $this->db->get()->result_array();
        $arr_data['row_mem_group'] = $row;

        $this->libraries->template('report_payment_share/coop_report_pay_month',$arr_data);
    }

	function coop_report_pay_month_preview(){
		set_time_limit ( 180 );
		$arr_data = array();
		$rs_group = $this->db->select(array('id','mem_group_name'))
							->from('coop_mem_group')
							->get()->result_array();
		$mem_group_arr = array();
		foreach($rs_group as $key => $row_group){
			$mem_group_arr[$row_group['id']] = $row_group['mem_group_name'];
		}
		$arr_data['mem_group_arr'] = $mem_group_arr;

		$arr_data['month_arr'] = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		$arr_data['month_short_arr'] = array('1'=>'ม.ค.','2'=>'ก.พ.','3'=>'มี.ค.','4'=>'เม.ย.','5'=>'พ.ค.','6'=>'มิ.ย.','7'=>'ก.ค.','8'=>'ส.ค.','9'=>'ก.ย.','10'=>'ต.ค.','11'=>'พ.ย.','12'=>'ธ.ค.');

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

        $where = " AND t4.profile_month = '".$month."' AND t4.profile_year = '".$year."' AND t3.run_status = '1'";

        $finannce_profile = $this->db->from("coop_finance_month_profile")
            ->where("profile_month = '".$month."' AND profile_year = '".$year."'")
            ->get()->row();
//        $where = " AND t4.profile_month = '".$month."' AND t4.profile_year = '".$year."' AND t3.run_status = '1'";
        $where = "t1.member_status <> 3 ";
        if(!empty($_GET['department'])){
            $where .= " AND t1.department = '".$_GET['department']."'";
        }
        if(!empty($_GET['faction'])){
            $where .= " AND t1.faction = '".$_GET['faction']."'";
        }
//        if(!empty($_GET['level'])){
//            $where .= " AND t1.level = '".$_GET['level']."'";
//        }
//        $where = " AND t4.profile_month = '".$month."' AND t4.profile_year = '".$year."' AND t3.run_status = '1'";

        $members = $this->db->select(array('t1.member_id',
            't1.id_card',
            't1.prename_id',
            't1.firstname_th',
            't1.lastname_th',
            't1.level',
            't2.prename_short',
        ))
            ->from('coop_mem_apply as t1')
            ->join("coop_prename as t2","t1.prename_id = t2.prename_id","left")
//            ->where("t1.member_status <> 3 AND t1.member_id = '05626'")
            ->where($where)
            ->order_by("t1.member_id ASC")
//            ->limit(100)
            ->get()->result_array();
//        echo $this->db->last_query();exit;
        foreach($members as $member) {
            $details = $this->db->select(array(
                't3.pay_amount',
                't3.real_pay_amount',
                't3.member_id',
                't6.contract_number',
                't5.receipt_datetime',
                't3.deduct_code',
                't3.pay_type',
                't3.loan_id',
                't6.loan_type',
                't7.loan_type_id'
            ))
                ->from("(SELECT run_status, member_id, pay_amount, real_pay_amount, deduct_code, pay_type, loan_id FROM coop_finance_month_detail WHERE profile_id = '".$finannce_profile->profile_id."' AND member_id = '".$member['member_id']."') as t3")
                ->join("(SELECT * FROM coop_receipt WHERE finance_month_profile_id = '".$finannce_profile->profile_id."') as t5","t3.member_id = t5.member_id","inner")
                ->join("coop_loan as t6", "t3.loan_id = t6.id", "left")
                ->join("coop_loan_name as t7", "t6.loan_type = t7.loan_name_id", "left")
                ->order_by('t3.member_id')
                ->get()->result_array();
//            echo $this->db->last_query();exit;
            $row = array();
            if(!empty($details)){
                $count_member++;
                foreach(@$details as $key => $detail){
                    if(in_array($detail['loan_type_id'], array('8', '9'))){
                        $loan_type_code = 'normal';
                    }else if(in_array($detail['loan_type_id'], array('7', '10'))){
                        $loan_type_code = 'emergent';
                    }else{
                        $loan_type_code = 'share';
                    }
                    $row[$detail['deduct_code']][$loan_type_code][$detail['loan_id']]['contract_number'] = $detail['contract_number'];
                    $row[$detail['deduct_code']][$loan_type_code][$detail['loan_id']][$detail['pay_type']]['pay_amount'] += $detail["pay_amount"];
                    $row[$detail['deduct_code']][$loan_type_code][$detail['loan_id']][$detail['pay_type']]['real_pay_amount'] += $detail["real_pay_amount"];
                    $row[$detail['deduct_code']][$loan_type_code][$detail['loan_id']][$detail['pay_type']]['receipt_datetime'] = $detail["receipt_datetime"];
                }
                $runno++;
                $row['full_name'] = $member['prename_short'].$member['firstname_th'].'  '.$member['lastname_th'];
                $row['member_id'] = $member['member_id'];
                $row['runno'] = $runno;
                $rs_data[] = $row;
            }

        }
        $arr_data['datas'] = $rs_data;
        if($_GET['dev'] == 'dev'){
            echo '<pre>'; print_r($rs_data); echo '</pre>';
            exit;
        }

		$this->preview_libraries->template_preview('report_payment_share/coop_report_pay_month_preview',$arr_data);
	}

    function check_coop_report_pay_month() {
        echo "success";
    }

}
