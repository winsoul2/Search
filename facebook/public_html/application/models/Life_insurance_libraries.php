<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Life_insurance_libraries extends CI_Model {
	public function __construct()
	{
		parent::__construct();
		//$this->load->database();
		# Load libraries
		//$this->load->library('parser');
		$this->load->helper(array('html', 'url'));
	}

	public function get_deduct_insurance($arr_data,$type){
		$date_receive_money = @$arr_data['start_date_insurance'];
		$year_receive_money = date('Y',strtotime($date_receive_money));
		$month_receive_money = date('m',strtotime($date_receive_money));
		$insurance_new = @$arr_data['insurance_new'];
		$insurance_old = @$arr_data['insurance_old'];
		$start_date_protection = @$arr_data['start_date_insurance'];
		$end_date_insurance = @$arr_data['end_date_insurance'];
		
		$data = array();
		//หาเบี้ย ประกันชีวิต		
		//วันเริ่มกรมธรรม์
		$start_date_insurance = $year_receive_money.'-01-01';
		$data['start_date_insurance'] = $start_date_insurance;
		//วันสิ้นสุดกรมธรรม์
		//$end_date_insurance = $year_receive_money.'-12-31';
		$data['end_date_insurance'] = $end_date_insurance;
		//จำนวนวันทั้งปี
		$num_date_insurance = $this->center_function->get_days_of_year($year_receive_money);
		$data['num_date_insurance'] = $num_date_insurance;
		//วันเริ่มความคุ้มครอง
		//$start_date_protection = $start_date_protection;
		$data['start_date_protection'] = $start_date_protection;
		
		//วันสิ้นสุดความคุ้มครอง
		$end_date_protection = $end_date_insurance;
		$data['end_date_protection'] = $end_date_protection;
		//จำนวนวันที่คุมครอง
		$num_date_protection = $this->center_function->diff_day($start_date_protection,$end_date_protection);
		$data['num_date_protection'] = $num_date_protection;
		//ทุนประกัน
		$deduct_insurance = 0;
		//ทำทุนประกันเพิ่ม 
		$additional_insured = (@$insurance_new - @$insurance_old >0)?@$insurance_new - @$insurance_old:0;
		$data['additional_insured'] = $additional_insured;

		//ข้อมูลการตั้งค่าประกันชีวิต
		$rs_insurance_life = $this->db->select('s_insurance_life_premium,s_insurance_accident_premium,s_insurance_defective_premium')
		->from('coop_setting_life_insurance')
		->where("s_insurance_year = '".($year_receive_money+543)."'")
		->limit(1)
		->get()->result_array();
		$row_insurance_life = @$rs_insurance_life[0];
		//Life	อัตราเบี้ยประกันชีวิต		
		$s_insurance_life_premium = $row_insurance_life['s_insurance_life_premium'];
		$cal_life_premium = ROUND($s_insurance_life_premium*$additional_insured/1000,2);
		$cal_life_premium_all = ROUND($cal_life_premium/$num_date_insurance*$num_date_protection,2);
		$data['s_insurance_life_premium'] = $s_insurance_life_premium;
		$data['cal_life_premium'] = $cal_life_premium;
		$data['cal_life_premium_all'] = $cal_life_premium_all;

		//TPD	อัตราเบี้ยประกันทุพลลภาพ	
		$s_insurance_defective_premium = $row_insurance_life['s_insurance_defective_premium'];
		$cal_defective_premium = ROUND($s_insurance_defective_premium*$additional_insured/1000,2);
		$cal_defective_premium_all = ROUND($cal_defective_premium/$num_date_insurance*$num_date_protection,2);
		$data['s_insurance_defective_premium'] = $s_insurance_defective_premium;
		$data['cal_defective_premium'] = $cal_defective_premium;
		$data['cal_defective_premium_all'] = $cal_defective_premium_all;
		
		//ADD	อัตราเบี้ยประกันอุบัติเหตุ	
		$s_insurance_accident_premium = $row_insurance_life['s_insurance_accident_premium'];
		$cal_accident_premium = ROUND($s_insurance_accident_premium*$additional_insured/1000,2);
		$cal_accident_premium_all = ROUND($cal_accident_premium/$num_date_insurance*$num_date_protection,2);
		$data['s_insurance_accident_premium'] = $s_insurance_accident_premium;
		$data['cal_accident_premium'] = $cal_accident_premium;
		$data['cal_accident_premium_all'] = $cal_accident_premium_all;		

		//เบี้ยประกัน = เบี้ยประกัน 3 อย่าง รวมกัน 		
		$deduct_insurance = @$cal_life_premium_all+@$cal_defective_premium_all+@$cal_accident_premium_all;		
		$data['deduct_insurance'] = (@$deduct_insurance > 0)?@$deduct_insurance:0;	
		
		return $data;
	}
}
