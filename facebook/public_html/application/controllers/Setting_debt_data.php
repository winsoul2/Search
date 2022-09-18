<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting_debt_data extends CI_Controller {
	function __construct()
	{
		parent::__construct();
	}
	public function coop_debt_letter_setting(){
		$this->db->select('*');
		$this->db->from('coop_debt_letter_setting');
		$rs = $this->db->get()->result_array();
		$arr_data['rows'] = @$rs;
		$this->libraries->template('setting_debt_data/coop_debt_letter_setting',$arr_data);
	}
	
	public function coop_debt_letter_setting_save(){
		$table = "coop_debt_letter_setting";
		$settings = $this->db->select("*")
								->from("coop_debt_letter_setting")
								->get()->result_array();
		foreach($settings as $setting) {
			if(!empty($_POST['num_letter_'.$setting['id']])) {
				$val = $_POST['num_letter_'.$setting['id']];

				$data_insert = array();
				$data_insert['num_letter']	= $val;

				$this->db->where('id', $setting['id'])
							->update("coop_debt_letter_setting", $data_insert);
			}
		}
		
		$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");	
		echo"<script> document.location.href='".PROJECTPATH."/setting_debt_data/coop_debt_letter_setting' </script>"; 
	}	
	
}
