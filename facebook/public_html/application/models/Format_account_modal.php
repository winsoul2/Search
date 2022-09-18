<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Format_account_modal extends CI_Model {

	public function __construct()
	{
		parent::__construct();
    }

    public function get_format($account_id=''){
		if(@$account_id != ''){
			$this->db->select(array('type_id'));
			$this->db->from('coop_maco_account');
			$this->db->where("account_id = '{$account_id}'");
			$row_account = $this->db->get()->row_array();			
			$type_id = $row_account['type_id'];

			$this->db->select(array('format_account_number'));
			$this->db->from('coop_deposit_type_setting');
			$this->db->where("type_id = '{$type_id}'");
			$row = $this->db->get()->row_array();

			$format = (!empty($row['format_account_number']))?$row['format_account_number']:'';

		}else{
			$format = '';
		}
        return $format;    
    }

    public function get_prefix($account_id){
        if(@$account_id != ''){
            $this->db->select(array('type_id'));
            $this->db->from('coop_maco_account');
            $this->db->where("account_id = '{$account_id}'");
            $row_account = $this->db->get()->row_array();
            $type_id = $row_account['type_id'];

            $this->db->select(array('type_prefix'));
            $this->db->from('coop_deposit_type_setting');
            $this->db->where("type_id = '{$type_id}'");
            $row = $this->db->get()->row_array();

            $prefix = (!empty($row['type_prefix']))?$row['type_prefix']:'';

        }else{
            $prefix = '';
        }
        return $prefix;
    }

    public function check_withdraw_permission($account_id){
	    $acc = $this->db->select("created, type_id")->from("coop_maco_account")->where(array("account_id" => $account_id))->get()->row();
	    $setting =$this->db->select("permission_type, hold_withdraw_month")->from("coop_deposit_type_setting_detail")->where(array('type_id' => $acc->type_id, 'start_date <=' => date("Y-m-d H:i:s")))->order_by("start_date" , "desc")->limit(1)->get()->row();
	    if($setting->permission_type == 1){
            return true;
        }

	    if($setting->permission_type == 2){
            return false;
        }

	    if($setting->hold_withdraw_month){
            $checker = date("Y-m-d", strtotime($acc->created ." +".$setting->hold_withdraw_month." month"));
        }else{
	        $checker = date("Y-m-d");
        }
	    if($_GET['debug'] == 'on') {
	        echo "<pre>"; print_r(array('check' => $checker, 'today' => date("Y-m-d")));
        }

	    if($setting->permission_type == 3){
	        if(date('Y-m-d') >= $checker){
	            return true;
            }else{
                return false;
            }
        }
    }
}
