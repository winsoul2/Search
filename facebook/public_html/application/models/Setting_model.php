<?php


class Setting_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	public function get($setting_key = ""){
		if($setting_key){
			return self::findSetting($setting_key)['setting_value'];
		}
		return array_column(self::findSetting(), 'setting_value','setting_name');
	}

	public function findSetting($key = ""){
		if(empty($key)) {
			return $this->db->get('coop_setting')->result_array();
		}else{
			return $this->db->get_where('coop_setting', array('setting_name' => $key))->row_array();
		}
	}
}
