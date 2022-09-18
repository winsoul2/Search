<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Refund extends CI_Controller {

	function __construct()
	{
		parent::__construct();
    }
    
	public function index()
	{
        $member = $this->db->get_where("coop_mem_apply")->result_array();
        // header('Content-Encoding: UTF-8');
		// header('Content-type: text/csv; charset=UTF-8');
		// header('Content-Disposition: attachment; filename="REFUND | createtime '.date("Y-m-d H:i:s").'.csv"');
		// echo "\xEF\xBB\xBF";
        // $fp = fopen('php://output', 'wb+');

        $str = "รหัสสมาชิก,contract_number,ชื่อ,สกุล,รวมจ่ายดอกเบี้ย,รวมดอกเบี้ยที่คำนวณ,เก็บเพิ่ม";
        // fputcsv($fp, explode(",", $str));
        $c = 1;
        foreach ($member as $key => $value) {
            //$member_id = sprintf("%06d", @$value['member_id']);
            $member_id = $this->center_function->complete_member_id(@$value['member_id']);
            $this->db->join("coop_mem_apply", "coop_loan_atm.member_id = coop_mem_apply.member_id");
            $loan_atm = @$this->db->get_where("coop_loan_atm", array(
                "coop_loan_atm.member_id" => $member_id,
                "loan_atm_status" => 1,
                "mem_type" => "!= 2"
            ))->result_array()[0];
            $loan_atm_id = @$loan_atm['loan_atm_id'];
            if(!$loan_atm_id){
                echo $member_id." NO loan_atm;<br>";
                continue;
            }
                
            $date_month_end = date('Y-m-t',strtotime((2562-543).'-'.sprintf("%02d",2).'-01'));
            $cal_loan_interest = array();
            $cal_loan_interest['loan_atm_id'] = $loan_atm_id;
            $cal_loan_interest['date_interesting'] = $date_month_end;
            $interest = $this->loan_libraries->cal_atm_interest_report_test(
                $cal_loan_interest,
                "echo", 
                array("month" => 2, "year" => (2562-543) )
            );
            echo ($c++)." | ".$member_id."<br>";

            // $str = $member_id.",".$loan_atm['contract_number'].",".$loan_atm['firstname_th'].",".$loan_atm['lastname_th'].",".$interest['sum_real_payment_interest'].",".$interest['sum_collect_interest'].",".($interest['sum_real_payment_interest'] - $interest['sum_collect_interest']);
            // fputcsv($fp, explode(",", $str));	
                
            // var_dump($interest);
            // echo "<hr><br>";
            // if($key>=20){
            //     fclose($fp);
            //     exit;
            // }
        }
		
    }

    public function atm()
	{
        $loan_atm = $this->db->get_where("coop_loan_atm")->result_array();
        header('Content-Encoding: UTF-8');
		header('Content-type: text/csv; charset=UTF-8');
		header('Content-Disposition: attachment; filename="REFUND | createtime '.date("Y-m-d H:i:s").'.csv"');
		echo "\xEF\xBB\xBF";
        $fp = fopen('php://output', 'wb+');

        $str = "รหัสสมาชิก,contract_number,ชื่อ,สกุล,รวมจ่ายดอกเบี้ย,รวมดอกเบี้ยที่คำนวณ,เก็บเพิ่ม";
        // fputcsv($fp, explode(",", $str));
        $c = 1;
        foreach ($loan_atm as $key => $value) {
            $member_id = $value['member_id'];
            $this->db->join("coop_mem_apply", "coop_loan_atm.member_id = coop_mem_apply.member_id");
            $loan_atm = @$this->db->get_where("coop_loan_atm", array(
                "coop_loan_atm.loan_atm_id" => $value['loan_atm_id'],
                "coop_loan_atm.member_id" => $member_id,
                "loan_atm_status" => 1,
                "mem_type !=" => "2"
            ))->result_array()[0];
            $loan_atm_id = @$loan_atm['loan_atm_id'];
            if(!$loan_atm_id){
                continue;
            }
                
            $date_month_end = date('Y-m-t',strtotime((2562-543).'-'.sprintf("%02d",2).'-01'));
            $cal_loan_interest = array();
            $cal_loan_interest['loan_atm_id'] = $loan_atm_id;
            $cal_loan_interest['date_interesting'] = $date_month_end;
            $interest = $this->loan_libraries->cal_atm_interest_report_test(
                $cal_loan_interest,
                "echo", 
                array("month" => 2, "year" => (2562-543) )
            );
            // echo ($c++)." | ".$member_id."<br>";

            $str = $member_id.",".$loan_atm['contract_number'].",".$loan_atm['firstname_th'].",".$loan_atm['lastname_th'].",".$interest['sum_real_payment_interest'].",".$interest['sum_collect_interest'].",=".$interest['sum_real_payment_interest'] ."-". $interest['sum_collect_interest'];
            fputcsv($fp, explode(",", $str));	
                
            // var_dump($interest);
            // echo "<hr><br>";
            // if($key>=20){
            //     fclose($fp);
            //     exit;
            // }
        }
		
    }

}
