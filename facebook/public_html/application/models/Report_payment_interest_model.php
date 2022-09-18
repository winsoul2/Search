<?php

use function PHPSTORM_META\type;

if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_payment_interest_model extends CI_Model {
    public function __construct()
    {
        parent::__construct();
    }

    function get_money_type()
    {   
        $this->db->select(array('t1.type_id','t1.type_name','t1.type_code'));
		$this->db->from('coop_deposit_type_setting as t1');
		$row = $this->db->get()->result_array();
		$data['type_id'] = $row;
		return $data;	
	}
    function check_empty_report_payment_interest($start_date,$end_date,$type_id)
    {   
        $row_detail = $this->db->select("t1.account_id,
                        t2.account_name,
                        t1.transaction_time,
                        t1.transaction_deposit,
                        t3.type_name,
                        t1.pay_type
                        ")
                        ->from('coop_account_transaction AS t1')
                        ->join("coop_maco_account AS t2","t1.account_id = t2.account_id","inner")
                        ->join("coop_deposit_type_setting AS t3","t2.type_id = t3.type_id","inner")
                        ->where("t1.transaction_time BETWEEN '{$start_date}' AND '{$end_date}' AND t2.type_id = '{$type_id}'AND t1.transaction_list IN('IN','INT')")
                        ->order_by("t1.transaction_time ASC,t1.transaction_id ASC")
                        ->get()->result_array();
		return $row_detail;	
	}
    function get_data_report_payment_interest($start_date,$end_date,$type_id)
    {   
        $arr_data = array();  
        $pay_type_th=['1'=>'เงินสด','2'=>'รายการโอน','3'=>'อื่น ๆ'];
        
        $style_id=1;
        $style = $this->db->get_where("coop_report_payment_interest_style", array(
            "style_id" => $style_id
        ))->result_array()[0];
        $arr_data['style'] = $style;
        $rows = $this->db->get_where("coop_report_payment_interest_style_setting", array(
            "style_id" => $style_id
        ))->result_array();
        $new_rows = $rows;
    
        $row_detail = $this->db->select("t1.account_id,
                        t2.account_name,
                        t1.transaction_time,
                        t1.transaction_deposit,
                        t3.type_name,
                        t1.pay_type
                        ")
                        ->from('coop_account_transaction AS t1')
                        ->join("coop_maco_account AS t2","t1.account_id = t2.account_id","inner")
                        ->join("coop_deposit_type_setting AS t3","t2.type_id = t3.type_id","inner")
                        ->where("t1.transaction_time BETWEEN '{$start_date}' AND '{$end_date}' AND t2.type_id = '{$type_id}'AND t1.transaction_list IN('IN','INT')")
                        ->order_by("t1.transaction_time ASC,t1.transaction_id ASC")
                        ->get()->result_array();			
        if(!empty($row_detail)){
            foreach($row_detail as $key=>$value){
                foreach ($rows as $keys => $values) {
                    $y=$values['y'];
                    $meta = $values['style_value'];
                    $text = $meta;
                    if($meta == "[account_id]"){
                        $text =$this->center_function->format_account_number($value['account_id']);	
                        $x=$values['x'];
                        $y=$values['y'];
                    }
                    if($meta == "[account_name]"){
                        $text = $value['account_name'];
                        $x=$values['x'];
                        $y=$values['y'];
                    }
                    if($meta == "[transaction_time]"){
                        $text = date('d/m/y ',strtotime($value['transaction_time']."+543 year"));
                        $x=$values['x'];
                        $y=$values['y'];
                    }
                    if($meta == "[transaction_deposit_interest]"){
                        $text =number_format($value['transaction_deposit'],2);
                        $x=$values['x'];
                        $y=$values['y'];
                    }
                    if($meta == "[transaction_deposit_interest_th]"){
                        $text = $this->center_function->convert($value['transaction_deposit']);
                        $x=$values['x'];
                        $y=$values['y'];
                    }
                    if($meta == "[type_name]"){
                        $text = $value['type_name'];
                    }
                    $rows[$keys]['meta'] = $meta;
                    if($meta == "[pay_type]"){
                        if($value['pay_type']==''){
                            $text =  '';                    
                        }else{
                            $text = "X";
                        } 
                        if($value['pay_type']==1){
                            $x=$values['x'];
                            $y=$values['y'];
                        }elseif($value['pay_type']==2){
                            $x=$values['x'];     
                            $y=$values['y']+4;
                        }else{
                            $x=$values['x'];
                            $y=$values['y']+10;
                        }
                        $rows[$keys]['meta4'] = $y;            
                    }
                    if($meta == "[pay_type_th]"){
                        if($value['pay_type']==''){
                            $text =  '';                    
                        }else{
                            $text =  $pay_type_th[$value['pay_type']];
                        }
                             
                        $x=$values['x'];
                        $y=$values['y'];
                    }
                    $new_rows[$keys]['text'] = $text;
                    $new_rows[$keys]['x'] = $x;
                    $new_rows[$keys]['y'] = $y;
                    $data[$value['account_id']]=$new_rows;        
                } 
            }
        } 
        $arr_data['rows'] = $data; 
		return $arr_data;	
	}
	
}
