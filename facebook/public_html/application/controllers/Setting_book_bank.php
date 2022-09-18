<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting_book_bank extends CI_Controller {
	function __construct()
	{
		parent::__construct();
	}
	
	public function coop_book_bank_style(){
		$arr_data = array();		
			
		$arr_data['items'] = $this->db->get("coop_book_bank_style")->result_array();
			
		$this->libraries->template('Setting_book_bank/coop_book_bank_style',$arr_data);
	}

	public function get_style(){
		$style_id = $_POST['style_id'];
		$this->db->where("style_id", $style_id);
		$style = $this->db->get_where("coop_book_bank_style")->result_array()[0];
		header('Content-Type: application/json');
		echo json_encode(array("result" => $style));
	}

	public function save_coop_book_bank_style(){
		$data = $_POST;
		if($data){
			$this->db->insert("coop_book_bank_style", $data);
		}else{
			die();
		}

		header("location: ".base_url('Setting_book_bank/coop_book_bank_style'));
	}

	public function update_coop_book_bank_style(){
		$data = $_POST;
		if($data){
			$this->db->update("coop_book_bank_style", $data);
		}else{
			die();
		}

		header("location: ".base_url('Setting_book_bank/coop_book_bank_style'));
	}

	public function delete_coop_book_bank_style(){
		$data = $_GET;
		if($data){
			$style_id = $data['style_id'];
			$this->db->where("style_id", $style_id);
			$this->db->delete("coop_book_bank_style");
			$this->db->where("style_id", $style_id);
			$this->db->delete("coop_book_bank_style_setting");
		}else{
			die();
		}

		header("location: ".base_url('Setting_book_bank/coop_book_bank_style'));
	}

	public function coop_book_bank_style_setting(){
		$arr_data = array();	
		$style_id = @$_GET['style_id'];
		if($style_id){
			$this->db->where("style_id", $style_id);	
			$arr_data['items'] = $this->db->get("coop_book_bank_style_setting")->result_array();
		}
			
		$this->libraries->template('Setting_book_bank/coop_book_bank_style_setting',$arr_data);
	}

	public function save_coop_book_bank_style_setting(){
		$data = $_POST;
		$style_id = @$_GET['style_id'];
		$this->db->where("style_id", $style_id);	
		$this->db->delete("coop_book_bank_style_setting");

		foreach ($data['style_value'] as $key => $value) {
			
			if($value!=""){
				$row = array(
					"style_id" 			=> 	$style_id,
					"style_value" 		=> 	$data['style_value'][$key],
					"x" 				=> 	$data['x'][$key],
					"y" 				=> 	$data['y'][$key],
					"font_size" 		=> 	$data['font_size'][$key],
					"width" 			=> 	$data['width'][$key],
					"align" 			=> 	$data['align'][$key]
				);
				// echo "<pre>";
				// var_dump($row);
				$this->db->insert("coop_book_bank_style_setting", $row);
			}
		}
		header("location: ".base_url('Setting_book_bank/coop_book_bank_style'));
	}

	public function coop_book_bank_stagement_row(){
		$arr_data = array();	
		$style_id = @$_GET['style_id'];
		if($style_id){
			$this->db->where("style_id", $style_id);	
			$arr_data['items'] = $this->db->get("coop_book_bank_stagement_row")->result_array();
		}
			
		$this->libraries->template('Setting_book_bank/coop_book_bank_stagement_row',$arr_data);
	}

	public function save_coop_book_bank_stagement_row(){
		$data = $_POST;
		$row_id = @$_GET['row_id'];
		$style_id = @$_GET['style_id'];
		$this->db->where("style_id", $style_id);	
		$this->db->delete("coop_book_bank_stagement_row");
		foreach ($data['no'] as $key => $value) {			
			if($value!=""){
				$row = array(
					"row_id" 		=> 	$data['no'][$key], //ใช่รายการนี้ชั่วคราว
					"no" 			=> 	$data['no'][$key],
					"y" 			=> 	$data['y'][$key],
					"style_id" 		=> 	$style_id
				);
				$this->db->insert("coop_book_bank_stagement_row", $row);
			}
		}
		header("location: ".base_url('Setting_book_bank/coop_book_bank_stagement_row?style_id='.$style_id));
	}

	public function get_detail_in_row(){
		$row_id = $_POST['row_id'];
		$this->db->where("row_id", $row_id);
		$rows = $this->db->get_where("coop_book_bank_stagement_row_setting")->result_array();
		header('Content-Type: application/json');
		echo json_encode(array("result" => $rows));
	}
	
	public function save_coop_book_bank_stagement_row_setting(){
		$data = $_POST;
		$style_id = @$_GET['style_id'];
		
		$this->db->where("style_id", $style_id);		
		if($style_id != '' && @$data['type_save'] == 'row'){			
			$row_id = @$_GET['row_id'];
			$this->db->where("row_id", $row_id);
		}			
		$data_stagement = $this->db->get("coop_book_bank_stagement_row")->result_array();
		
		if(!empty($data_stagement)){
			foreach($data_stagement AS $key_s => $value_s){
				$row_id = $value_s['row_id'];
				$this->db->where("row_id", $row_id);	
				$this->db->delete("coop_book_bank_stagement_row_setting");
				foreach ($data['style_value'] as $key => $value) {
					
					if($value!=""){
						$row = array(
							"row_id" 			=> 	$row_id,
							"style_value" 		=> 	$data['style_value'][$key],
							"x" 				=> 	$data['x'][$key],
							"width" 			=> 	$data['width'][$key],
							"font_size" 		=> 	$data['font_size'][$key],
							"align" 			=> 	$data['align'][$key]
						);
						$this->db->insert("coop_book_bank_stagement_row_setting", $row);
					}
				}			
			}
		}
		header("location: ".base_url('Setting_book_bank/coop_book_bank_stagement_row?style_id='.$style_id));
	}
	
}
