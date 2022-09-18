<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Paginater_all extends CI_Model {
	
	private $main_table;
	private $page_now;
	private $type;
	private $per_page;
	private $order_by;
	private $where;
	private $select;
	private $page_link_limit;
	private $join_arr;
	private $group_by;
	private $field_count='';
	
	public $debug = false;
	
	public function __construct()
	{
		parent::__construct();
		//$this->load->database();
		# Load libraries
		//$this->load->library('parser');
		$this->load->helper(array('html', 'url'));
	}
	
	public function main_table($main_table){
		$this->main_table = $main_table;
	}
	public function page_now($page_now){
		$this->page_now = $page_now;
	}
	public function type($type){
		$this->type = $type;
	}
	public function per_page($per_page){
		$this->per_page = $per_page;
	}
	public function order_by($order_by){
		$this->order_by = $order_by;
	}
	public function where($where){
		$this->where = $where;
	}
	public function select($select){
		$this->select = $select;
	}
	public function page_link_limit($page_link_limit){
		$this->page_link_limit = $page_link_limit;
	}
	public function join_arr($join_arr){
		$this->join_arr = $join_arr;
	}
	public function group_by($group_by) {
		$this->group_by = $group_by;
	}
	public function field_count($field_count){
		$this->field_count = $field_count;
	}

	public function paginater_process(){
		$main_table = $this->main_table;
		$page_now = $this->page_now;
		$type = $this->type;
		$per_page = $this->per_page;
		$order_by = $this->order_by;
		$where = $this->where;
		$select = $this->select;
		$page_link_limit = $this->page_link_limit;
		$join_arr = $this->join_arr;
		$group_by =	$this->group_by;
		$field_count = $this->field_count;
		
		if(substr(trim($where),0,3) != 'AND' && substr(trim($where),0,3) != 'and' && trim($where) !=''){
			$where = 'AND '.$where;
		}
		
		if($field_count!=''){
			$this->db->select('COUNT('.$field_count.') as count_row');
		}else{
			$this->db->select($select);
		}
		$this->db->from($main_table);
		if(!empty($join_arr)){
			foreach($join_arr as $key => $value){
				$this->db->join($value['table'], $value['condition'], $value['type']);
			}
		}
		$this->db->where('1=1 '.$where);
		
		if($this->debug) { echo "SQL Count: ".$this->db->get_compiled_select(null, false)."<br>"; }
		
		$row = $this->db->get()->result_array();
		if($field_count!=''){
			$num_rows = $row[0]['count_row'];
		}else{
			$num_rows = count($row) ;
		}
		
		$page = $page_now ? ((int) $page_now) : 1;
		
		

		if($type == 'sql_server'){
			$page_start = (($per_page * $page) - $per_page)+1;
			
			$this->db->select($select);
			$this->db->from('( SELECT *, ROW_NUMBER() OVER (ORDER BY '.$order_by.') as row FROM '.$main_table.' WHERE 1=1 '.$where.') as '.$main_table);
			$this->db->where("row >= ".$page_start." AND row <= ".($page_start+$per_page-1));
			if(!empty($join_arr)){
				foreach($join_arr as $key => $value){
					$this->db->join($value['table'], $value['condition'], $value['type']);
				}
			}
			$this->db->order_by($order_by);
			if (!empty($group_by)) {
				$this->db->group_by($group_by); 
			}
		}else{
			$page_start = (($per_page * $page) - $per_page);
			$this->db->select($select);
			$this->db->from($main_table);
			if(!empty($join_arr)){
				foreach($join_arr as $key => $value){
					$this->db->join($value['table'], $value['condition'], $value['type']);
				}
			}
			$this->db->where("1=1 ".$where);
			$this->db->order_by($order_by);
			if (!empty($group_by)) {
				$this->db->group_by($group_by); 
			}
			$this->db->limit($per_page, $page_start);
			$page_start++;
		}
		
		if($this->debug) { echo "SQL List: ".$this->db->get_compiled_select(null, false)."<br>"; }
		
		$row = $this->db->get()->result_array();
		$data_return['data'] = $row;
		$data_return['page'] = $page;
		$data_return['page_start'] = $page_start;
		$data_return['num_rows'] = $num_rows;
		$data_return['per_page'] = $per_page;
		$data_return['page_link_limit'] = $page_link_limit;
		return $data_return;
	}
}
