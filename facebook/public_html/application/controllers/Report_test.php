<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_test extends CI_Controller {
	function __construct()
	{
		parent::__construct();
	}
	
	public function report_test(){
		//set_time_limit(80);
		$arr_data = array();
		
		/*$data_insert = array();
		$max = 20000;
		for($i=0;$i<$max;$i++){
			$data_insert['c1'] = 'ส.001/2561';				
			$data_insert['c2'] = '16/03/2561';				
			$data_insert['c3'] = '13';				
			$data_insert['c4'] = '12345';				
			$data_insert['c5'] = 'นาย';				
			$data_insert['c6'] = 'ภิญญา';				
			$data_insert['c7'] = 'ธนากรถิรพร';				
			$data_insert['c8'] = 'CTEST13';				
			$data_insert['c9'] = '72';				
			$data_insert['c10'] = '20000';	
			$this->db->insert('report_test', $data_insert);
			//echo '<pre>'; print_r($data_insert); echo '</pre>';
		}	
		*/
		//
		/*	
		$this->db->select(array('*'));
		$this->db->from('report_test');
		//$this->db->limit(1, 20000);
		$this->db->limit(4000);
		$rs = $this->db->get()->result_array();
		$arr_data['rs'] = @$rs;
		*/
		//echo '<pre>'; print_r($rs); echo '</pre>';
		
		/*$max = 9000;
		for($i=0;$i<$max;$i++){
			$rs[$i]['c1'] = 'ส.001/2561';				
			$rs[$i]['c2'] = '16/03/2561';				
			$rs[$i]['c3'] = '13';				
			$rs[$i]['c4'] = '12345';				
			$rs[$i]['c5'] = 'นาย';				
			$rs[$i]['c6'] = 'ภิญญา';				
			$rs[$i]['c7'] = 'ธนากรถิรพร';				
			$rs[$i]['c8'] = 'CTEST13';				
			$rs[$i]['c9'] = '72';				
			$rs[$i]['c10'] = '20000';	
		}
		$arr_data['rs'] = @$rs;
		*/
		//echo '<pre>'; print_r($rs); echo '</pre>';
		
		$arr_data = array();
		$this->db->select(array('id','mem_group_name'));
		$this->db->from('coop_mem_group');
		$rs_group = $this->db->get()->result_array();
		$mem_group_arr = array();
		foreach($rs_group as $key => $row_group){
			$mem_group_arr[$row_group['id']] = $row_group['mem_group_name'];
		}
		$arr_data['mem_group_arr'] = $mem_group_arr;
		
		$this->db->select(array('setting_value'));
		$this->db->from('coop_share_setting');
		$this->db->where("setting_id = '1'");
		$row_share_value = $this->db->get()->result_array();
		$share_value = $row_share_value[0]['setting_value'];
		$arr_data['share_value'] = $share_value;
		
		$this->db->select(array('t1.*', 
			't2.prename_short'));
		$this->db->from('coop_mem_apply as t1');
		$this->db->join('coop_prename as t2','t1.prename_id = t2.prename_id','left');
		$this->db->where("member_status = '1'");
		$rs = $this->db->get()->result_array();
		$arr_data['rs'] = $rs;
		$this->libraries->template('report_test/report_test',$arr_data);
	}	
}
