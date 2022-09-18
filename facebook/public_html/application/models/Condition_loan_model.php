<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');
class Condition_loan_model extends CI_Model {
	
	public function __construct()
	{
		parent::__construct();
	}

	public function get_meta_id($txt){
		$id = "";
		$this->db->where("detail_text", $txt);
		$result = $this->db->get("coop_meta_condition")->result_array()[0];
		if(!empty($result)){
			$id = $result['id'];
		}
		return $id;
	}

	//opt['member'] ต้องส่งมา ***
	function get_value_condition_of_loan($type_id, $key_field, $opt){
		// var_dump($opt);
		// echo $key_field."<hr>";
		$this->db->where("start_date <=", "'".date("Y-m-d")."'", false);
		$this->db->order_by("start_date desc");
		$this->db->limit(1);
		$term = $this->db->get_where("coop_term_of_loan", array(
			"type_id" => $type_id
		))->result_array()[0];
		
		if(empty($term)){
			exit;
		}
		
		$used_value 	= "";
		$global_value 	= "";
		$message 		= "";
		$global_value 	= $term[$key_field];
		
		$condition_of_loan = $this->db->get_where("coop_condition_of_loan", array(
			"result_type" 				=> $key_field,
			"term_of_loan_id"			=> $term['id']
		))->result_array();
		foreach ($condition_of_loan as $key => $row) {
			// echo $condition_of_loan[$key]['result_value'];
			// echo "<br>";
			// continue;
			$rs_result_value = $this->db->get_where("coop_condition_detail", array(
				"ccd_id" => $row['result_value']
			))->result_array()[0];

			if($this->is_query(@$rs_result_value['a'])){
				$sql = @$rs_result_value['a'];
				$rs_check = $this->db->query($sql, $opt['required'])->result_array()[0]['value'];
				$a = $rs_check;
			}else
				$a = $rs_result_value['a'];

			if($this->is_query(@$rs_result_value['b'])){
				$sql = @$rs_result_value['b'];
				$rs_check = $this->db->query($sql, $opt['required'])->result_array()[0]['value'];
				$b = $rs_check;
			}else
				$b = $rs_result_value['b'];

			$op = @$rs_result_value['op'];

			// echo $a." |".$op."|- ".$b."<br>";
			if( $this->center_function->operator($a, $b, $op) ){
				$result_value = $this->center_function->operator($a, $b, $op);
			}else{
				$result_value = $a;
			}
			$condition_of_loan[$key]['result_value'] = $result_value;
			// var_dump($rs_result_value);
			// echo $result_value;
			// echo "<br>";
			// echo "SET: ".$condition_of_loan[$key]['result_value'];
			// echo "<br>";
			// continue;
			$condition = $this->db->get_where("coop_condition_list", array(
				"col_id" => $row['col_id']
			))->result_array();
			
			// var_dump($condition);
			$status = true;
			foreach ($condition as $i => $value) {
				/** หาค่า A */
				$a = $this->condition_model->get_op_val($value["ccd_id_a"], array("member_id" => $opt['member_id'], "optional" => $opt['optional']));
				if($a==""){
					$a = $opt['force_a'];
				}
				/** หาค่า B */
				$b = $this->condition_model->get_op_val($value["ccd_id_b"], array("member_id" => $opt['member_id'], "optional" => $opt['optional']));
				if($b==""){
					$b = $opt['force_b'];
				}
				$op = @$value['operation'];
				// echo $a." |".$op."| ".$b."<br>";
				if( $this->center_function->operator($a, $b, $op) ){
					$return_text_garantor = "";
					$result = $value["conn_garantor_id"];
					// echo "TRUEEEE";
				}else{
					$status = false;
					break;
				}
			}

			// if($opt['required']==""){
			// 	$a = $opt['force_a'];
			// }else

			$condition_of_loan[$key]['condition'] = $condition;
			if($status){
				$used_value = $result_value;
				$operator = $row['operator'];
				$message = "";
				break;
			}else{
				$message .= $row['detail_text']."\n";
			}
		}

		// var_dump($condition_of_loan);

		if(sizeof($condition_of_loan) <= 0){
			$used_value = $global_value;
		}

		// echo $used_value;
		// var_dump($rs_condition);
		return $used_value;
	}

	function get_op_val($ccd_id, $opt){
		$rs_ccd_id = $this->db->get_where("coop_condition_detail", array(
			"ccd_id" => $ccd_id
		))->result_array()[0];
		
		if($this->is_query($rs_ccd_id['a'])){
			$sql = $rs_ccd_id['a'];
			$rs = $this->db->query($sql, $opt['member_id'])->result_array()[0]['value'];
			$tmp_a = $rs;
		}else if($rs_ccd_id['a_is_meta']=="1"){
			$tmp_sql_query = $this->db->get_where("coop_meta_condition", array(
				"id" => $rs_ccd_id['a']
			))->result_array()[0];
			$sql_query = $tmp_sql_query['fieldname'];
			$req_field = $tmp_sql_query['req_field'];
			if(!empty($sql_query)){
				$rs = @$this->db->query($sql_query, $opt[$req_field])->result_array()[0]['value'];
			}else{
				$rs = $opt[$req_field];
			}
			$tmp_a = $rs;
		}else{
			$tmp_a = $rs_ccd_id['a'];
		}

		if($this->is_query($rs_ccd_id['b'])){
			$sql = $rs_ccd_id['b'];
			$rs = @$this->db->query($sql, $opt['member_id'])->result_array()[0]['value'];
			$tmp_b = $rs;
		}else if($rs_ccd_id['b_is_meta']=="1"){
			$sql_query = $this->db->get_where("coop_meta_condition", array(
				"id" => $rs_ccd_id['b']
			))->result_array()[0]['fieldname'];
			if(!empty($sql_query)){
				$rs = $this->db->query($sql_query, $opt['member_id'])->result_array()[0]['value'];
			}else{
				$rs = $opt['optional'];
			}
			$tmp_b = $rs;
		}else{
			$tmp_b = $rs_ccd_id['b'];
		}
		
		// echo $tmp_a." tmp ".$tmp_b."<hr>";
		$tmp_op = $rs_ccd_id['op'];

		$val = $tmp_a;
		if(!empty($tmp_op)){
			$val = $this->operator($tmp_a, $tmp_b, $tmp_op);
		}

		return $val;
	}

	function is_query($str){
		return strpos($str, "?") <= -1 ? false : true;
	}

	function operator($a, $b, $op){
		$val = false;
		switch ($op) {
			case '>': $val = ($a > $b) ? true : false;
				break;
			case '>=': $val = ($a >= $b) ? true : false;
				break;
			case '<': $val = ($a < $b) ? true : false;
				break;
			case '<=': $val = ($a <= $b) ? true : false;
				break;
			case '==': $val = ($a == $b) ? true : false;
				break;
			case '=!': $val = ($a =! $b) ? true : false;
				break;
			case '+': $val = ($a + $b);
				break;
			case '-': $val = ($a - $b);
				break;
			case '*': $val = ($a * $b);
				break;
			case '/': $val = ($a / $b);
				break;
			case '^': $val = ($a ^ $b);
				break;
			default:
				$val = false;
				break;
		}
		return $val;
	}

}
