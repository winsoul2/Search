<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Interest_modal extends CI_Model {

	public function __construct()
	{
        parent::__construct();
        $this->load->model("Condition_loan_model", "condition_model");
    }

    public function get_interest($loan_type_id, $start_date, $optional = null, $format = null, $max_period=0){
        $this->db->where('start_date <=', $start_date);
        if($format == 'term_of_loan_id'){
            $this->db->where("id", $loan_type_id);
        }else{
            $this->db->where("type_id", $loan_type_id);
        }
        $this->db->order_by("start_date desc, id desc");
        $term_of_loan = $this->db->get("coop_term_of_loan")->result_array()[0];
        $term_of_loan_id = $term_of_loan['id'];
		$result = array();

        if($optional==null){// display
            return $term_of_loan['interest_rate'];
        }else{
            $this->db->where("(result_type = 'interest' or result_type = 'interest_rate')");
            $main_condition_rate_interest = $this->db->get_where("coop_condition_of_loan", array(
                "term_of_loan_id" => $term_of_loan_id
            ))->result_array();
            foreach ($main_condition_rate_interest as $key => $value) {
                $condition = $this->db->get_where("coop_condition_list", array(
                    "col_id" => $value['col_id']
                ))->result_array();
                $check = true;
                foreach ($condition as $i => $value1) {
                    /** หาค่า A */
                    $a = $this->condition_model->get_op_val($value1["ccd_id_a"], $optional);
                    /** หาค่า B */
                    $b = $this->condition_model->get_op_val($value1["ccd_id_b"], $optional);
                    $op = @$value1['operation'];
                    if(@$optional['operation'] == "interest_rate_array"){
						$result[$key]['interest'] = $this->condition_model->get_value($value["result_value"], "a");
						$result[$key]['op'] = $op;
						$result[$key]['round'] = $this->condition_model->get_value($value1["ccd_id_b"], "a");
						$check = false;
					}else {
						if ($this->center_function->operator($a, $b, $op)) {

						} else {
							$check = false;
							break;
						}
					}
                }
                if($check){
                    $result = $this->condition_model->get_op_val($value['result_value'], $optional);
                    return $result;
                }
            }
        }
		if(sizeof($result)){

			return self::meta_rule_conv($result, $max_period);
		}
        return $term_of_loan['interest_rate'];
        
    }

    public function first_interest_payment($loan_id){
	    $contract = $this->db->get_where("coop_loan", array("id" => $loan_id))->row_array();
		$approve_date = date('Y-m-d', strtotime($contract['approve_date']));

	    if(!empty($contract['date_last_interest'])){
	    	$date_last_interest = date('Y-m-d', strtotime($contract['date_last_interest']));

			//echo "approve_date: {$approve_date}, date_last_interest: {$date_last_interest} <br>";

	    	return date_create($approve_date) == date_create($date_last_interest) && date('j', strtotime($approve_date))  <= 5;
		}else{
	    	$data = $this->db->order_by('loan_transaction_id', 'asc')->get_where('coop_loan_transaction', array('loan_id' => $loan_id, ))->result_array();
			$date_last_interest = date('Y-m-d', strtotime($data[0]['transaction_datetime']));

			//echo "approve_date: {$approve_date}, date_last_interest: {$date_last_interest} <br>";
			return date_create($approve_date) == date_create($date_last_interest) && date('j', strtotime($approve_date)) <= 5;
		}
    }

	function is_query($str){
		return strpos($str, "?") <= -1 ? false : true;
	}

    public function operator($a, $b, $operator){
        switch ($operator) {
            case '>':
                return ($a > $b);
            case '>=':
                return ($a >= $b);
            case '<':
                return ($a < $b);
            case '<=':
                return ($a <= $b);
            case '=':
                return ($a = $b);
            case '!=':
                return ($a != $b);
            
            default:
                die("no operator()");
                break;
        }
    }

    public function getCmpDateLastInterest($id, $date_last_interest){
		$last_transaction = self::getLoanTransaction($id);
		return date_create($date_last_interest) >= date_create($last_transaction) ? $date_last_interest : $last_transaction;
	}

	private function getLoanTransaction($id){
		return $this->db->where(array("loan_id" => $id))->order_by("transaction_datetime DESC")
			->get("coop_loan_transaction", 1)->row()->transaction_datetime;
	}

	public function calStepInt($rules, $loan_amount, $start_date, $temp_u9 = 0, $done = false, $counter = 0){

		$period = intval(array_sum(array_column($rules,"round")));
		$max_interest = doubleval(max(array_column($rules, "interest")));


		$u9 = round(($loan_amount * ( ($max_interest/100) / 12 ))/( 1-pow(1/(1+( ($max_interest/100) /12)),$period)), 0);
		$u9 = ceil(($u9)/10)*10;
		if($temp_u9 == null) {
			$temp_u9 = $u9;
		}
		$count_round = 1;
		$temp_start_date = $start_date;
		$temp_m = $loan_amount;
		$int_rate = array_column($rules, "interest");
		$round = array_column($rules, "round");
		$date = $start_date;
		$over_stack = 0;

		for ($size_i = 0 ; $size_i < sizeof($int_rate) ; $size_i++){
			for ($i = 1; $i <= $round[$size_i]; $i++) {
				$start_date = explode("-", $start_date);

				if($count_round == 1) {
					$time = strtotime($date);
					$year = date("Y", $time);
					$month = date("m", $time);
					$day = date("d", $time);
				}else{
					$year = intval($start_date[0]);
					$month = intval($start_date[1]) + 1;
					if ($month > 12) {
						$year += 1;
						$month = 1;
					}
					$day = 1;
				}

				$date = $year . "-" . sprintf("%02d", $month) . "-" . $day;
				$end_date = date("t", strtotime($date));
				$start_date = $date;
				//echo " date ".$count_round.":". $year . "-" . sprintf("%02d", $month) . "-" . $end_date;
				//$interest = round(((($loan_amount * $int_rate[$size_i]) / 100) / 365) * $end_date * 100);

				$interest = ceil(($loan_amount * ($int_rate[$size_i] / (365 / $end_date)) / 100)/1)*1;
				$main_money = $temp_u9 - $interest;


				if ($loan_amount < $main_money) {
					$main_money = $loan_amount;
					$over_stack = $loan_amount;
					$loan_amount = 0;
				} else if ($loan_amount > $temp_u9 && $count_round == $period) {
					$main_money = $loan_amount;
					$over_stack = $loan_amount;
					$loan_amount = 0;

				} else {
					$loan_amount = $loan_amount - $main_money;
				}
				//echo " ->>\t balance ".$loan_amount." principal: ".$main_money."\t\t\t rate int: ".$int_rate[$size_i]."\t\t interest: ".$interest. "\t\t total: ".$temp_u9; echo "<br>";

				if ($loan_amount <= 0) {

					if($count_round < $period &&  $over_stack < $temp_u9) {
						//$stabilise = 10;
						//$restabilize= intval($temp_m) > 1000000 ? 100 : 10;
						$restabilize = ceil(($temp_u9*1/100));
						$temp_u9 -= $restabilize;
						if($_GET['dev'] == 1) {
							echo " 1 temp_u9: " . $temp_u9 . " restabilize: " . $restabilize . " period:" . $count_round . " counter: ".$counter ;
							echo "<hr>";
						}
						return self::CalStepInt($rules, $temp_m, $temp_start_date, $temp_u9, false, $counter+1);
					}else if(($count_round > $period)){
						$restabilize = 10;
						$temp_u9 += $restabilize;
						echo " 4 temp_u9: " . $temp_u9 . " restabilize: " . $restabilize . " period:" . $count_round. " counter: ".$counter;
						echo "<hr>";
						return self::CalStepInt($rules, $temp_m, $temp_start_date, $temp_u9, false, $counter+1);
					}
					break;
				}
				$count_round++;
			}
		}

		if ($done) {
			return $temp_u9;
		} else if ($temp_u9 < $over_stack) {
			$restabilize = $restabilize = ceil(($temp_u9*1/100));;
			$temp_u9 += $restabilize;
			if($_GET['dev'] == 1) {
				echo " 2 temp_u9: ". $temp_u9 ." restabilize: ".$restabilize." period: ".$count_round. " counter: ".$counter;
				echo "<hr>";
			}
			return self::calStepInt($rules, $temp_m, $temp_start_date,  $temp_u9, true, $counter+1);
		} else {
			$restabilize= $restabilize = ceil(($temp_u9*1/100));;
			$temp_u9 -= $restabilize;
			if($_GET['dev'] == 1) {
				echo " 3 temp_u9: ". $temp_u9 ." restabilize: ".$restabilize ." period:" .$count_round. " counter: ".$counter;
				echo "<hr>";
			}
			return self::calStepInt($rules, $temp_m, $temp_start_date, $temp_u9, false, $counter+1);
		}
	}

	public function meta_rule_conv($rules = array(), $max_period = 0){
		$tmp = 0;
		$vRule =  array();
		foreach ($rules as $key => $val){
			if((sizeof($rules)-1) == $key) {
				$vRule[$key]['round'] = self::translateOperator($val['round'], $val['op'], $max_period);
				$vRule[$key]['interest'] = $val['interest'];
			}else{
				$vRule[$key]['round'] = self::translateOperator($tmp, $val['op'], $val['round']);
				$vRule[$key]['interest'] = $val['interest'];
				$tmp = $val['round'];
			}
		}
		return $vRule;

	}

	public function translateOperator($a, $op, $b){
		$val = 0;
		switch ($op) {
			case '>': $val = ($a > $b) ? $a-$b : $b-$a;
				break;
			case '>=': $val = ($a >= $b) ? $a-$b : $b-$a;
				break;
			case '<': $val = ($a < $b) ? $b-$a : $a-$b;
				break;
			case '<=': $val = ($a <= $b) ? $b-$a : $a-$b;
				break;
			case '==': $val = ($a == $b) ? $a : $b;
				break;
			case '=!': $val = ($a =! $b) ? $a : $b;
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
				$val = 0;
				break;
		}
		return $val;
	}

	public function getGroupId($loan_type){
		return $this->db->get_where("coop_loan_name", ['loan_name_id' => $loan_type], 1)->row()->loan_group_id;
	}


    
}
