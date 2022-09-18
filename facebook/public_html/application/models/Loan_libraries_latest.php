<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Loan_libraries extends CI_Model {
	public function __construct()
	{
		parent::__construct();
		//$this->load->database();
		# Load libraries
		//$this->load->library('parser');
		$this->load->helper(array('html', 'url'));
	}

	public function get_contract_number($year){
		$this->db->select('run_contract_number');
		$this->db->from("coop_loan_contract_number");
		$this->db->where("contract_year = '".$year."'");
		$this->db->order_by("run_id DESC");
		$this->db->limit(1);
		$rs_contact_number = $this->db->get()->result_array();
		$row_contact_number = @$rs_contact_number[0];
		if(@$row_contact_number['run_contract_number']==''){
			$contact_number_now = '1';
		}else{
			$contact_number_now = $row_contact_number['run_contract_number'];
			(int)$contact_number_now++;
		}

		$data_insert = array();
		$data_insert['contract_year'] = $year;
		$data_insert['run_contract_number'] = $contact_number_now;
		$data_insert['createdatetime'] = date('Y-m-d H:i:s');
		$this->db->insert('coop_loan_contract_number',$data_insert);

		return $contact_number_now;
	}

	public function loan_transaction($data){
		$data_insert = array();
		$data_insert['loan_id'] = $data['loan_id'];
		$data_insert['loan_amount_balance'] = $data['loan_amount_balance'];
		$data_insert['transaction_datetime'] = $data['transaction_datetime'];
		if(@$data['receipt_id']!=''){
			$data_insert['receipt_id'] = $data['receipt_id'];
		}
		$this->db->insert('coop_loan_transaction',$data_insert);

		return 'success';
	}

	public function atm_transaction($data){
		$data_insert = array();
		$data_insert['loan_atm_id'] = $data['loan_atm_id'];
		$data_insert['loan_amount_balance'] = $data['loan_amount_balance'];
		$data_insert['transaction_datetime'] = $data['transaction_datetime'];
		if(@$data['receipt_id']!=''){
			$data_insert['receipt_id'] = $data['receipt_id'];
		}
		$this->db->insert('coop_loan_atm_transaction',$data_insert);

		return 'success';
	}

	public function cal_atm_interest($data,$return_type='echo'){
		$this->db->select('*');
		$this->db->from('coop_loan_atm_setting');
		$row = $this->db->get()->result_array();
		$loan_atm_setting = @$row[0];

		$this->db->select('date_last_interest');
		$this->db->from('coop_loan_atm');
		$this->db->where("
			loan_atm_id = '".$data['loan_atm_id']."'
		");
		$row = $this->db->get()->result_array();
		$row_last_transaction = @$row[0];
		if(@$row_last_transaction['date_last_interest']!=''){
			$last_payment_date = $row_last_transaction['payment_date'];
		}else{
			$this->db->select('loan_date');
			$this->db->from('coop_loan_atm_detail');
			$this->db->where("
				loan_atm_id = '".$data['loan_atm_id']."'
			");
			$this->db->order_by("loan_id ASC");
			$this->db->limit(1);
			$row = $this->db->get()->result_array();
			$last_payment_date = @$row[0]['loan_date'];
		}

		$this->db->select('*');
		$this->db->from('coop_loan_atm_transaction');
		$this->db->where("
			loan_atm_id = '".$data['loan_atm_id']."'
			AND transaction_datetime >= '".$last_payment_date."'
		");
		$this->db->order_by("loan_atm_transaction_id ASC");
		$row = $this->db->get()->result_array();
		$atm_transaction = array();
		$i=0;
		foreach($row as $key => $value){
			$atm_transaction[$i]['loan_amount_balance'] = $value['loan_amount_balance'];
			$date_start = explode(' ',$value['transaction_datetime']);
			$atm_transaction[$i]['date_start'] = $date_start[0];
			if(@$row[$key+1]['transaction_datetime']!=''){
				$date_end = explode(' ',$row[$key+1]['transaction_datetime']);
				$atm_transaction[$i]['date_end'] = $date_end[0];
			}else{
				$atm_transaction[$i]['date_end'] = $data['date_interesting'];
			}
			$diff = date_diff(date_create($atm_transaction[$i]['date_start']),date_create($atm_transaction[$i]['date_end']));
			$date_count = $diff->format("%a");
			if($date_count == 0){
				//$date_count = $date_count+1;
			}
			$atm_transaction[$i]['date_count'] = $date_count;
			$interest = ((($atm_transaction[$i]['loan_amount_balance']*$loan_atm_setting['interest_rate'])/100)/365)*$atm_transaction[$i]['date_count'];
			$atm_transaction[$i]['origin_interest'] = $interest;
			$interest = round($interest);
			$atm_transaction[$i]['interest_rate'] = $loan_atm_setting['interest_rate'];
			$atm_transaction[$i]['interest'] = $interest;
			$i++;
		}
		$atm_transaction_tmp = array();
		foreach($atm_transaction as $key => $value){
			$atm_transaction_tmp[$value['date_start']] = $value;
		}
		$atm_transaction = $atm_transaction_tmp;
		$interest_amount = 0;
		foreach($atm_transaction as $key => $value){
			$interest_amount += $value['interest'];
		}
		//echo "<pre>";print_r($atm_transaction);echo"</pre>";exit;
		if($return_type == 'echo'){
			return $interest_amount;
		}else{
			return $atm_transaction;
		}
	}

	/*-----------------------------------------------------------
	// $type_count_date is array("month" => XX, "year" => XX) จะเป็นตัวนับจำนวนวันในเดือนนั้นๆ ใช้ในการคำนวณดอกเบี้ยออกรายการเรียกเก็บประชำเดือน
	------------------------------------------------------------*/
	public function cal_atm_interest_report_test($data,$return_type='echo',$type_count_date="",$is_process=true, $is_counter=false){
		if(@$_GET['debug']){
			var_dump($data);
		}
		if(@$_GET['excel']){
			header('Content-Encoding: UTF-8');
			header('Content-type: text/csv; charset=UTF-8');
			header('Content-Disposition: attachment; filename="'.sprintf("%06d",$_GET['member_id']).' | createtime '.date("Y-m-d H:i:s").'.csv"');
			echo "\xEF\xBB\xBF";
			// $fp = fopen($data["loan_atm_id"].'.csv', 'w');
			$fp = fopen('php://output', 'wb+');
		}
		$this->db->select('*');
		$this->db->from('coop_loan_atm_setting');
		$row = $this->db->get()->result_array();
		$loan_atm_setting = @$row[0];

		$date_interesting = date("Y", strtotime($data['date_interesting'])).'-'.date("m", strtotime($data['date_interesting'])).'-01';
		
		$month = date("m", strtotime("-1 month", strtotime($date_interesting)));
		if($month == "12")
			$year =	date("Y", strtotime("-1 month", strtotime($date_interesting))) + 543;
		else
			$year =	date("Y", strtotime("-0 month", strtotime($date_interesting))) + 543;

		$days_in_year = $this->center_function->get_days_of_year($year-543);
		
		// var_dump($data);
		$i_last = 0;
		//--------------------
		//หาดอกเบี้ยกดระหว่างเดือน
		//--------------------

		$arr_month = explode('-',$data['date_interesting']);
		$last_month = date("Y-m", strtotime("-1 month", strtotime($date_interesting)));
		
		//เพิ่มการหาวันที่ประมลผลผ่านรายการของย้อนหลัง 2 เดือน
		$last_month_2 = date("Y-m", strtotime("-2 month", strtotime($date_interesting)));
		$last_date_month_2 = date("Y-m-t", strtotime("-2 month", strtotime($date_interesting)));
		$prefix_receipt_id1 = date("m", strtotime($last_month_2));
		// $prefix_receipt_id2 = date("m", strtotime("-1 month", strtotime($last_month_2)));
		//-----------------------------
		//หาวันที่ประมวลผลเดือนก่อนหน้า
		//-----------------------------
		$this->db->select("date(transaction_datetime) as transaction_datetime, loan_atm_transaction_id");
		$this->db->limit(1);
		$this->db->where("(receipt_id like '".$prefix_receipt_id1."B%')");
		$this->db->where("(transaction_datetime like '".$last_month_2."%')");
		$this->db->where("loan_atm_id", $data['loan_atm_id']);
		$this->db->order_by("transaction_datetime desc");
		$rs_date_s = $this->db->get("coop_loan_atm_transaction")->result_array()[0];
		if($rs_date_s){
			$this->db->select("date(transaction_datetime) as transaction_datetime, loan_atm_transaction_id, loan_amount_balance");
			$this->db->where("loan_atm_transaction_id >", $rs_date_s['loan_atm_transaction_id']);
			$this->db->where("loan_atm_id", $data['loan_atm_id']);
			$this->db->order_by("transaction_datetime asc");
			$rs_chk = $this->db->get("coop_loan_atm_transaction");
			foreach ($rs_chk->result_array() as $key => $value) {
				if($value['receipt_id']!=""){
					if($value['loan_amount_balance']==0){
						$date_s_calc_atm = $value['transaction_datetime'];
						break;
					}
					continue;
				}
			}
		}
		
		if($date_s_calc_atm == ""){
			//หาวันที่ยังไม่มีการคิดดอกเบี้ย

			$profile_m = date("m", strtotime($last_month));
			$profile_y = date("Y", strtotime($last_month)) + 543;
			$this->db->where("profile_month", $profile_m);
			$this->db->where("profile_year", $profile_y);
			$last_profile_id = $this->db->get("coop_finance_month_profile")->result_array()[0]['profile_id'];

			$this->db->select('date(create_datetime) as create_datetime, COUNT(*) as total');
			$this->db->where("profile_id", $last_profile_id);
			$this->db->group_by('date(create_datetime)'); 
			$this->db->order_by('2 desc'); 
			$this->db->limit(1);
			$date_s_calc_atm = $this->db->get("coop_finance_month_detail")->result_array()[0]['create_datetime'];
			
			if($date_s_calc_atm=="")
				$date_s_calc_atm = $last_date_month_2;
		}
		// echo $date_s_calc_atm;exit;
		//-----------------------------
		//-----------------------------
		//หาวันที่ประมวลล่าสุด
		//-----------------------------
		$this->db->select("transaction_datetime, loan_atm_transaction_id");
		$this->db->limit(1);
		$this->db->where("(receipt_id like '%B%')");
		$this->db->where("loan_atm_id", $data['loan_atm_id']);
		$this->db->order_by("transaction_datetime desc");
		$rs_date_s_latest = $this->db->get("coop_loan_atm_transaction")->result_array()[0];
		if($rs_date_s_latest)
			$date_latest_process = $rs_date_s_latest['transaction_datetime'];
		else
			$date_latest_process = $date_s_calc_atm;
		//-----------------------------
		// echo $date_latest_process;exit;
		// $this->db->select("t1.transaction_datetime");
		// $this->db->from("coop_loan_atm_transaction AS t1");
		// $this->db->join("coop_receipt AS t2","t1.receipt_id = t2.receipt_id","inner");
		// $this->db->where("t1.transaction_datetime LIKE '$last_month_2%' AND t1.loan_atm_id = '".$data['loan_atm_id']."'");
		// $this->db->order_by("t1.transaction_datetime ASC");
		// $this->db->limit(1);
		// $row_last_process = $this->db->get()->result_array();
		// $date_last_process = $row_last_process[0]['transaction_datetime'];		
		// echo 'date_last_process='.$date_last_process.'<br>';
		// echo 'last_date_month_2='.$last_date_month_2.'<br>';
		// exit;
		
		// $this->db->where("loan_atm_id", $data['loan_atm_id']);
		// //$this->db->where("transaction_datetime LIKE '$last_month%'");
		// $this->db->where("transaction_datetime > '".$date_last_process."'");
		// $query = $this->db->get("coop_loan_atm_transaction")->result_array();
		/*-------------------------------
		//ใช้สำหรับ fix ประมวลผลถึงวันที่ตามระบุ
		---------------------------------*/
		$fix_date_end = "";
		$fix_date_end = "and transaction_datetime <= '2019-01-31 23:59:59'";
		/*---------------------------------*/
		$end_of_month = date("Y-m-t", strtotime("-1 month", strtotime($date_interesting)));
		$query = $this->db->query("select * from (SELECT
										*
									FROM
										`coop_loan_atm_transaction`
									WHERE
										`loan_atm_id` = ".$data['loan_atm_id']."
									AND `transaction_datetime` > '".$date_last_process."' $fix_date_end
									) as m
									UNION ALL (select null,null, (select loan_amount_balance from coop_loan_atm_transaction where loan_atm_id = ".$data['loan_atm_id']." and transaction_datetime <= '$end_of_month 23:59:59' order by transaction_datetime desc limit 1),'$end_of_month 00:00:00',NULL)
									ORDER BY transaction_datetime"
		)->result_array();

		// echo $this->db->last_query(); echo '<br>';exit;
		// $this->db->where("loan_atm_id", $data['loan_atm_id']);
		// $this->db->where("transaction_datetime < '".$sub_query[0]['transaction_datetime']."'");
		// $this->db->order_by("transaction_datetime DESC");
		// // $this->db->limit(1);
		// $query = $this->db->get("coop_loan_atm_transaction")->result_array();
		// if(!$query)
		// 	$query = $sub_query;

		//echo $data['date_interesting'].'<hr>';
		//echo $date_interesting.'<hr>';

		// echo $this->db->last_query(); echo '<br>';

		$temp_interest 				= 0;
		$sum_of_interest 			= 0;
		$collect_interest 			= 0;
		$remain_interest 			= 0;
		$subtract_after_payment		= 0;
		$interest_over				= 0;
		$after_payment				= false;
		$is_atm						= false;
		$sum_of_atm					= 0;
		$sum_of_interest_atm		= 0;//ดอกเบี้ยที่ต้องเรียกเก็บ ยอดที่กดระหว่างเดือน
		$last_receipt_id			= "";
		$start_at_transaction_id	= "";
		$is_transfer				= false;
		if($query){
			// echo "<br>";
			$this->db->where("loan_atm_id", $data['loan_atm_id']);
			$this->db->where("transaction_datetime < ", $query[0]['transaction_datetime']);
			$this->db->order_by("transaction_datetime DESC, loan_atm_transaction_id DESC");
			$this->db->limit(1);
			$query_last_transaction 			= $this->db->get("coop_loan_atm_transaction")->result_array()[0];
			// echo $this->db->last_query(); echo '<br>';
			if($query_last_transaction){
				$last_atm_transaction_date 		= date("Y-m-d", strtotime("-0 month", strtotime($query_last_transaction['transaction_datetime'])));
				$bf								= $query_last_transaction['loan_amount_balance'];
				$start_at_transaction_id		= $query_last_transaction['loan_atm_transaction_id'];
				// echo $bf;
				// exit;
			}else{
				// $last_atm_transaction_date 		= date("Y-m-t", strtotime("-2 month", strtotime($date_interesting)));
				$this->db->order_by("transaction_datetime asc");
				$this->db->limit(1);
				$last_atm_transaction_date = $this->db->get_where("coop_loan_atm_transaction", array("loan_atm_id" => $data['loan_atm_id']) )->result_array()[0]['transaction_datetime'];
				$this->db->order_by('transaction_datetime ASC, loan_atm_transaction_id ASC');
				$this->db->limit(1);
				$this->db->where("transaction_datetime < '$last_atm_transaction_date'");
				$this->db->where("loan_atm_id", $data['loan_atm_id']);
				$bf = $this->db->get("coop_loan_atm_transaction")->result()[0]->loan_amount_balance;
			}

			if(@$_GET['excel']){
				$str = "วันที่,คงเหลือ,รายการ/เลขที่ใบเสร็จ,เงินต้น,ดอกเบี้ย,เรทดอกเบี้ย,จำนวนวัน,ดอกเบี้ยสะสม,ดบ.กดระหว่างเดือน,ยอดกดสะสม";
				fputcsv($fp, explode(",", $str));	
				$atm_transaction = @$this->db->get_where("coop_loan_atm_transaction", array(
					"loan_atm_transaction_id" => $start_at_transaction_id
				))->result_array()[0];
				
				$this->db->select(array(
					"sum(principal_payment) as principal", 
					"sum(interest) as interest"
				));
				$receipt_detail = @$this->db->get_where("coop_finance_transaction", array(
					"receipt_id" => $atm_transaction['receipt_id'],
					"loan_atm_id" => $data['loan_atm_id'],
				))->result_array();
				$str = explode(" ",$last_atm_transaction_date)[0].",".$bf.",".@$atm_transaction['receipt_id'].",".$receipt_detail[0]['principal'].",".$receipt_detail[0]['interest'].",,,";
				fputcsv($fp, explode(",", $str));	
			}
			
			foreach ($query as $key => $value) {
				$detail 							= "";
				$withdraw_atm						= 0;
				$temp_interest						= 0;
				$bf 								= ($key==0) ? ($bf==0 ? $value['loan_amount_balance'] : $bf) : $value['loan_amount_balance'];
				$last_bf							= ($key==0) ? $bf : (@$query[$key-1]['loan_amount_balance']);
				if($value['loan_amount_balance']<=0){
					continue;
				}
				$rs_detail = @$this->db->get_where("coop_loan_atm_detail", array(
					"loan_atm_id" 	=> $data['loan_atm_id'],
					"loan_date" 	=> $value['transaction_datetime']
				))->result_array()[0];
				
				if($rs_detail){
					$withdraw_atm						= $rs_detail['loan_amount'];
					$is_atm	= true;
				}

				
				if(@$_GET['debug']){
					echo $value['transaction_datetime']." VS ".$last_atm_transaction_date."<br>";
					echo "<br>";
				}

				if( date("m", strtotime($value['transaction_datetime']) ) != date("m", strtotime($last_atm_transaction_date) ) ){
					$last_atm_transaction_date_part = date("Y-m-t", strtotime($last_atm_transaction_date) );
					$diff 							= date_diff(date_create($last_atm_transaction_date), date_create($last_atm_transaction_date_part));
					$date_count 					= $diff->format("%a");

					// exit;
					/*---------------------*/
					//หาดอกเบี้ย part_1 ส่วนที่คาบเกี่ยวระหว่างเดือน
					$this->db->select('interest_rate');
					$this->db->from('coop_loan_atm_setting_template');
					$this->db->where("start_date <= '".$last_atm_transaction_date_part."'");
					$this->db->order_by("start_date DESC,run_id DESC");
					$this->db->limit(1);
					$row_atm_setting = $this->db->get()->result_array();
					$interest_rate_atm = $row_atm_setting[0]['interest_rate'];
					
					// $temp_interest 					+= $bf * $interest_rate_atm / 100 * $date_count / $days_in_year;
					// $sum_of_interest 				+= $temp_interest;
					// $remain_interest 				+= $temp_interest;
					if(@$_GET['debug']){
						echo $value['transaction_datetime'].($bf-$value['loan_amount_balance']).", ".$date_count.", ".$temp_interest.", ".$value['loan_amount_balance'];
						echo "<br>";
						echo $last_atm_transaction_date." ";
						echo " DIFF ".$date_count;
						echo " INTEREST ".$interest;
						echo "<br>";
						echo "<br> <span style='color: red;'>".$temp_interest." " .$remain_interest. " ".$value['receipt_id']. " </span><br>";
						echo  $bf." * ".$interest_rate_atm." / 100 * ".$date_count." / ".$days_in_year."|<br>";
						echo "collect_interest: $collect_interest<br>";
					}
					/*-------------------------*/
					if(strtotime($last_atm_transaction_date_part) > strtotime($date_s_calc_atm)){
						$temp_interest 					+= $last_bf * $interest_rate_atm / 100 * $date_count / $days_in_year;
						$sum_of_interest 				+= $temp_interest;

						if($is_atm==true)
							$sum_of_interest_atm += round(($sum_of_atm) * $interest_rate_atm / 100 * $date_count / $days_in_year, 2);
					}
					if(@$_GET['excel']){
						$str = $last_atm_transaction_date_part.",".
						$last_bf.","."".","."".","."".",".
						$interest_rate_atm."%".",".
						$date_count.
						",=".$last_bf ."*". $interest_rate_atm ."/ 100 *". $date_count ."/". $days_in_year.
						",=".($is_atm==true ? ($sum_of_atm) ."*". $interest_rate_atm ."/ 100 *". $date_count ."/". $days_in_year : "0" ).
						",".($sum_of_atm + ($value['receipt_id']=="" && $sum_of_atm!=0 ? $withdraw_atm : 0));
						fputcsv($fp, explode(",", $str));
					}

					

					//หาดอกเบี้ย part_2
					$date_calc_diff_day = $value['transaction_datetime'];
					if(strtotime($last_atm_transaction_date_part) <= strtotime($date_s_calc_atm)){
						$date_calc_diff_day = $date_s_calc_atm;
						$last_atm_transaction_date_part = date("Y-m-t", strtotime($date_s_calc_atm));
						
					}else{
						$date_calc_diff_day = $value['transaction_datetime'];
					}

					$this->db->select('interest_rate');
					$this->db->from('coop_loan_atm_setting_template');
					$this->db->where("start_date <= '".$date_calc_diff_day."'");
					$this->db->order_by("start_date DESC,run_id DESC");
					$this->db->limit(1);
					$row_atm_setting = $this->db->get()->result_array();
					$interest_rate_atm = $row_atm_setting[0]['interest_rate'];
					
					$diff 							= date_diff(date_create($date_calc_diff_day), date_create($last_atm_transaction_date_part));
					$date_count 					= $diff->format("%a");
					$bf 							= $value['loan_amount_balance'];
					// $withdraw_atm				= $bf - $last_bf;
					$is_transfer					= true;
				}else{
					$diff 							= date_diff(date_create($value['transaction_datetime']), date_create($last_atm_transaction_date));
					$date_count 					= $diff->format("%a");
					//หาดอกเบี้ย
					$this->db->select('interest_rate');
					$this->db->from('coop_loan_atm_setting_template');
					$this->db->where("start_date <= '".$last_atm_transaction_date."'");
					$this->db->order_by("start_date DESC,run_id DESC");
					$this->db->limit(1);
					$row_atm_setting = $this->db->get()->result_array();
					$interest_rate_atm = $row_atm_setting[0]['interest_rate'];
				}
				//เซ็ตวันที่หาดอกเบี้ยล่าสุด transaction
				$last_atm_transaction_date 		= date("Y-m-d", strtotime($value['transaction_datetime']));


				$temp_interest 					+= $last_bf * $interest_rate_atm / 100 * $date_count / $days_in_year;
				// echo  $bf." * ".$interest_rate_atm." / 100 * ".$date_count." / $days_in_year|<br>";
				$sum_of_interest 				+= $temp_interest;
				$remain_interest 				+= $temp_interest;

				if($is_atm==true && strtotime($last_atm_transaction_date) > strtotime($date_s_calc_atm)){
					$sum_of_interest_atm += round(($sum_of_atm) * $interest_rate_atm / 100 * $date_count / $days_in_year, 2);
				}

				if(@$_GET['debug']){
					echo $value['transaction_datetime'].($bf-$value['loan_amount_balance']).", ".$date_count.", ".$temp_interest.", ".$value['loan_amount_balance'];
					echo "<br>";
					echo $last_atm_transaction_date." ";
					echo " DIFF ".$date_count;
					echo " INTEREST ".$interest;
					echo "<br>";
					echo "<br> <span style='color: red;'>".$temp_interest." " .$remain_interest. " ".$value['receipt_id']. " </span><br>";
					echo  $bf." * ".$interest_rate_atm." / 100 * ".$date_count." / $days_in_year|<br>";
					echo "collect_interest: $collect_interest<br>";
				}
				if(@$_GET['excel']){
					$this->db->select(array(
						"sum(principal_payment) as principal", 
						"sum(interest) as interest"
					));
					$receipt_detail = @$this->db->get_where("coop_finance_transaction", array(
						"receipt_id" => $value['receipt_id'],
						"loan_atm_id" => $data['loan_atm_id'],
						"payment_date" => date("Y-m-d", strtotime($value['transaction_datetime']))
					))->result_array();				
					
					if($is_transfer){
						$detail = @$this->db->get_where("coop_loan_atm_detail", array(
							"loan_atm_id" 	=> $data['loan_atm_id'],
							"loan_date" 	=> $value['transaction_datetime']
						))->result_array()[0]['loan_description'];

						if($value['receipt_id']!="" && $detail=="")
							$detail = $value['receipt_id'];
						else if($detail=="")
							$detail = "ยอดยกมา";
						
						
						$is_transfer = false;
					}else if(@$value['receipt_id']!="" ){

						$detail = @$value['receipt_id'];
					}else{
						// $detail = "ทำรายการกู้ATM";
						
						if($rs_detail){
							$detail = $rs_detail['loan_description'];
						}
						// echo $this->db->last_query();
						if($detail==""){
							$detail = "ยอดยกมา.";
						}
					}
					// $detail = (@$value['receipt_id']!="" || ($withdraw_atm) == 0 ? @$value['receipt_id'] : "ทำรายการกู้ATM");
					$str = explode(" ", $value['transaction_datetime'])[0].",".$bf.",".
						$detail.",".
						(@$value['receipt_id']!="" ? @$receipt_detail[0]['principal'] : $withdraw_atm).",".
						(@$value['receipt_id']!="" ? @$receipt_detail[0]['interest'] : "0").",".
						$interest_rate_atm."%".",".
						$date_count.",".
						"=".$last_bf ."*". $interest_rate_atm ."/ 100 * ". $date_count ."/". $days_in_year.",".
						"=".($is_atm==true ? ($sum_of_atm) ."*". $interest_rate_atm ."/ 100 *". $date_count ."/". $days_in_year : "0" ).",".($sum_of_atm + ($value['receipt_id']=="" && $sum_of_atm!=0 ? $withdraw_atm : 0)).",".$withdraw_atm;
					fputcsv($fp, explode(",", $str));
					
				}
				//echo 'temp_interest='.$temp_interest.'<br>';
				//echo '<pre>'; print_r($value); echo '</pre>';
				if($after_payment)
					$subtract_after_payment += $temp_interest;

				$collect_interest			+= $temp_interest;
				if($value['receipt_id']!=""){
					// $collect_interest			= 0;
					// $remain_interest 			= 0;
					$after_payment				= true;
				}else if(strtotime(date("Y-m-d", strtotime(@$value['transaction_datetime']))) <= strtotime(date("y-m-d", strtotime($last_date_month_2))) ){
					// $collect_interest			+= $temp_interest;
					$remain_interest 			= 0;
				}

				//ทำรายการกู้ATM
				if($value['receipt_id']=="" && $withdraw_atm > 0 && strtotime($last_atm_transaction_date) >= strtotime($date_s_calc_atm)){
					$is_atm = true;
					$sum_of_atm += $withdraw_atm;
				}
				
				$last_receipt_id				= ($value['receipt_id'] != "") ? $value['receipt_id'] : $last_receipt_id;
			}
			/*----------------
			end foreach
			----------------*/



			if(@$_GET['debug']){
				echo "<b><br>BF: ";
				echo $bf."<br>";
				echo "<br>subtract_after_payment: ";
				echo $subtract_after_payment."<br>";
				echo "<br>collect_interest: ";
				echo $collect_interest."<br></b>";
			}
		}else{
			// echo "<br>QUERY NOT FOUND!";
			$this->db->order_by('transaction_datetime DESC, loan_atm_transaction_id DESC');
			$this->db->limit(1);
			$this->db->where("loan_atm_id", $data['loan_atm_id']);
			$bf = $this->db->get("coop_loan_atm_transaction")->result()[0]->loan_amount_balance;
		}

		//หาดอกเบี้ย
		$this->db->select('interest_rate');
		$this->db->from('coop_loan_atm_setting_template');
		$this->db->where("start_date <= '". (date("Y-m-d", strtotime("+1 day", strtotime($last_atm_transaction_date)) ) ) ."'");
		$this->db->order_by("start_date DESC,run_id DESC");
		$this->db->limit(1);
		$row_atm_setting = $this->db->get()->result_array();
		$interest_rate_atm = $row_atm_setting[0]['interest_rate'];
		// echo 'date_interesting ='.$date_interesting .'<br>';
		// echo 'interest_rate_atm ='.$interest_rate_atm .'<br>';

		// echo "<br>";
		$interest = 0;
		//----------------หาดอกเบี้ยส่วนที่ยังไม่มีการคิดดอกเบี้ย
		if($last_atm_transaction_date != ''){
			//$diff = date_diff(date_create( date("Y-m-t", strtotime("-1 month", strtotime($data['date_interesting']) ) ) ), date_create($last_atm_transaction_date));
			if($is_process){
				//ใช้ประมวลผล
				$diff = date_diff(date_create( date("Y-m-t", strtotime("-1 month", strtotime($date_interesting) ) ) ), date_create($last_atm_transaction_date));
				$date_count = $diff->format("%a");
			}else{
				// ใช้หักกลบ
				$diff = date_diff(date_create( date("Y-m-d", strtotime("-0 month", strtotime($data['date_interesting']) ) ) ), date_create($last_atm_transaction_date));
				$date_count = $diff->format("%a");
			}

			//$tmp_interest = $bf * $date_count * $loan_atm_setting['interest_rate'] / 100 / $days_in_year;
			$tmp_interest = $bf * $date_count * $interest_rate_atm / 100 / 365;
			if(@$_GET['debug']){
				//echo  $bf . " * ". $date_count . " * ". $loan_atm_setting['interest_rate'] . " / 100 / $days_in_year||<br>";
				echo $date_interesting." - ".$last_atm_transaction_date."<br>";
				echo  $bf . " * ". $date_count . " * ". $interest_rate_atm . " / 100 / $days_in_year||<br>";
				echo "<br>".$tmp_interest." หาดอกเบี้ยส่วนที่ยังไม่มีการคิดดอกเบี้ย<br>";
				echo "<br>sub total interest: ".$interest."<br>";
			}
			
			if($is_process)
				$interest += $tmp_interest;
		}
		//----------------หา ดบ. เรียกเก็บ
		//$diff = date_diff(date_create( date("Y-m-t", strtotime("-1 month", strtotime($data['date_interesting']) ) ) ), date_create( date('Y-m-t', strtotime($data['date_interesting']) ) ));
		if(!$is_process){
			//ใช้หักกลบ
			$this->db->order_by("transaction_datetime desc");
			$this->db->limit(1);
			$last_transacrion_atm = $this->db->get_where("coop_loan_atm_transaction", array("loan_atm_id" => $data['loan_atm_id']))->result()[0]->transaction_datetime;
			if( date('m', strtotime($date_interesting)) != date("m", strtotime($last_transacrion_atm)) ){
				$last_transacrion_atm = date("Y-m-t", strtotime($last_transacrion_atm));
			}
			$diff = date_diff(date_create( date("Y-m-d", strtotime("-0 month", strtotime($last_transacrion_atm) ) ) ), date_create( date('Y-m-d', strtotime($data['date_interesting']) ) ));
			$date_count = $diff->format("%a");
		}else{
			//ใช้ออกเรียกเก็บประจำเดือน
			$diff = date_diff(date_create( date("Y-m-t", strtotime("-1 month", strtotime($date_interesting) ) ) ), date_create( date('Y-m-t', strtotime($data['date_interesting']) ) ));
			$date_count = $diff->format("%a");
		}


		//หาดอกเบี้ย
		$this->db->select('interest_rate');
		$this->db->from('coop_loan_atm_setting_template');
		$this->db->where("start_date <= '".$date_interesting."'");
		$this->db->order_by("start_date DESC,run_id DESC");
		$this->db->limit(1);
		$row_atm_setting = $this->db->get()->result_array();
		$interest_rate_atm = $row_atm_setting[0]['interest_rate'];

		// echo "<br>".date('Y-m-01', strtotime($data['date_interesting']) );
		//$tmp_interest = $bf * $date_count * $loan_atm_setting['interest_rate'] / 100 / $days_in_year;
		//$tmp_interest = $bf * $date_count * $loan_atm_setting['interest_rate'] / 100 / $days_in_year;
		if($is_process){
			$tmp_interest = ($bf<=0 ? 0 : $bf) * $date_count * $interest_rate_atm / 100 / $days_in_year;
		}else{
			$tmp_interest = ($bf<=0 ? 0 : $bf) * $date_count * $interest_rate_atm / 100 / $days_in_year;
		}
		
		/*------------------------*/
		//skip การเรียกเก็บดอกเบี้ยล่วงหน้า
		$interest = $tmp_interest;
		/*-----------------------*/
		if(@$_GET['debug']){
			
			echo  $bf . " * ". $date_count . " * ". $interest_rate_atm . " / 100 / $days_in_year|||<br>";
			echo $tmp_interest." -หา ดบ. เรียกเก็บ<br>";
			
			echo 'AA'.$interest.'<br>';
			echo "<br>remain_interest ".$remain_interest."<br>";
			// exit;
		}

		if(@$_GET['excel']){
			$receipt_detail = @$this->db->get_where("coop_finance_transaction", array(
				"receipt_id" => $value['receipt_id'],
				"loan_atm_id" => $data['loan_atm_id'],
			))->result_array();				
			$str = explode(" ", $data['date_interesting'])[0].",".$bf.",".
				"-".",".
				"".",".
				"".",".
				$interest_rate_atm."%".",".
				$date_count.",".
				"=".($last_bf<=0 ? 0 : $last_bf) ."*". $interest_rate_atm ."/ 100 *". $date_count ."/". $days_in_year.",".
				"".",";
			fputcsv($fp, explode(",", $str));
		}
		
		//---------------หา ดบ. สะสม
		$this->db->join("coop_finance_month_detail", "coop_finance_month_detail.profile_id = coop_finance_month_profile.profile_id");
		$this->db->where("profile_month", $month);
		$this->db->where("profile_year", $year);
		$this->db->where("loan_atm_id", $data['loan_atm_id']);
		$this->db->where("pay_type", 'interest');
		$this->db->limit(1);
		$query_last_payment = $this->db->get("coop_finance_month_profile");
		$last_payment 		= 0;
		if($query_last_payment)
			foreach ($query_last_payment->result_array() as $key => $value)
				$last_payment += $value['pay_amount'];


		
		/*--------------------------------
		การดอกเบี้ยที่จ่ายจริง
		--------------------------------*/
		$this->db->where("receipt_id != '' AND receipt_id NOT LIKE '%B%'");
		$this->db->where("transaction_datetime like '".$last_month."%'");
		$rs_interest = $this->db->get_where("coop_loan_atm_transaction", array(
			"loan_atm_id" => $data['loan_atm_id'],
		));
		$sum_real_pay_interest = 0;
		if($last_bf > 0){
			foreach ($rs_interest->result_array() as $key => $value) {
				$this->db->select(array("sum(interest) as interest"));
				$this->db->where("month_receipt is null");
				$this->db->where("year_receipt is null");
				$this->db->where("finance_month_profile_id is null");
				$this->db->where("createdatetime < '".$date_latest_process."'");
				$this->db->join("coop_receipt", "coop_receipt.receipt_id = coop_finance_transaction.receipt_id");
				$sum_real_pay_interest += $this->db->get_where("coop_finance_transaction", array(
					"coop_finance_transaction.receipt_id" => $value['receipt_id'],
					"coop_finance_transaction.loan_atm_id" => $data['loan_atm_id'],
				), false)->result_array()[0]['interest'];
			}
		}
		/*--------------------------------*/
		/*--------------------------------
		การหาดอกเบี้ยสะสม
		--------------------------------*/
		$deduct = 0;
		$deduct = $last_payment - ($collect_interest - $subtract_after_payment);
		if(round($last_payment) == 0 || $is_counter){
			$deduct = 0;
		}
		if(@$_GET['debug']){
			echo "deduct = ".$last_payment." - ( ".$collect_interest." - ".$subtract_after_payment.")<br>";
			echo "<span style='color: blue;'>".$deduct."</span><br>";
		}
		
		$deduct = 0;
		/*--------------------------------*/
		if(@$_GET['debug']){
			echo "last_payment: ".$last_payment."<br>";
			echo "collect_interest: ".$collect_interest."<br>";
			echo "<b>deduct = ".$last_payment ."-". "(".$collect_interest ."-". $subtract_after_payment.")<br></b>";
			echo $last_payment.' - '.$collect_interest.'<br>';
			echo $collect_interest.' - '.$last_payment.'<br>';
			echo '-----------------------------<br>';
			echo "INTEREST ".$interest." REMAIN ".$remain_interest;
			echo "<br>".$last_payment." | ".$collect_interest." | ".$deduct."<br><br>";
			echo 'total_interest='.$total_interest.'<br>';
			echo 'deduct='.$deduct.'<br>';
			echo 'collect_interest='.$collect_interest.'<br>';
			echo "<b style='color:green;'><u>(".$interest." + ".$remain_interest." - ".$deduct.")<br></u></b>";
		}

		$total_interest = ($interest);

		if(@$_GET['debug']!=""){
			echo 'total_interest='.$total_interest.'<br>';
		}


		$sql_max_period = "select if( (select max_period from coop_loan_atm where coop_loan_atm.loan_atm_id = ".$data['loan_atm_id']."), (select max_period from coop_loan_atm where coop_loan_atm.loan_atm_id = ".$data['loan_atm_id']."), (select max_period from coop_loan_atm_setting LIMIT 1) ) as max_period";
		$max_period = $this->db->query($sql_max_period)->result()[0]->max_period;
		$sql = "SELECT
		IF (
				ISNULL(
					(
						SELECT
							loan_atm_id
						FROM
							coop_loan_atm_detail
						WHERE
							loan_date like '".($year-543)."-".$month."%'
						AND loan_atm_id = ".$data['loan_atm_id']."  LIMIT 1
					)
				),
				(
					SELECT
						pay_amount
					FROM
						coop_finance_month_detail
					WHERE
						loan_atm_id = ".$data['loan_atm_id']."
					AND pay_type = 'principal'
					ORDER BY
						run_id DESC
					LIMIT 1
				),
				(
					SELECT

					IF (
						MOD (loan_amount_balance / $max_period, 100) > 0,
						loan_amount_balance / $max_period + (
							100 - MOD (loan_amount_balance / $max_period, 100)
						),
						loan_amount_balance / $max_period
					)
					FROM
						coop_loan_atm_transaction
					WHERE
						loan_atm_id = ".$data['loan_atm_id']."
					AND transaction_datetime <= '".date("Y-m-t", strtotime($last_month))." 23:59:59"."'
					ORDER BY
						transaction_datetime DESC
					LIMIT 1
				)
		)  AS principal";

		$arr_loan_interest['principal_month'] = @$this->db->query($sql)->result()[0]->principal;
		//----
		$this->db->where("loan_atm_id", $data['loan_atm_id'] );
		$this->db->where("approve_date < ", $last_month.'-01');
		$rs_chk = $this->db->get("coop_loan_atm")->result_array();
		$used = "";
		if($rs_chk){
			//เคสกู้เก่า ดอกเบี้ยลวงหน้า + ดบ.กดสะสม
			$arr_loan_interest['interest_month'] = round($total_interest + $sum_of_interest_atm);
			$used = "ดอกเบี้ยลวงหน้า + ดบ.กดสะสม";
		}else{
			//เคสกู้เก่าหรือหักกลบ ดอกเบี้ยลวงหน้า + ดบ.สะสม
			$arr_loan_interest['interest_month'] = round($total_interest + $remain_interest);
			$used = "ดอกเบี้ยลวงหน้า + ดบ.สะสม";
		}

		if($sum_real_pay_interest != 0){
			$used .= " - ดอกเบี้ยจ่ายจริง";
		}
		
		//-------------
		$arr_loan_interest['interest_rate'] = $interest_rate_atm;
		if(@$_GET['debug']){
			echo "<pre>";var_dump($arr_loan_interest);echo "</pre><hr>";
			echo "เก็บ ดบ.ยอดที่กดระหว่างเดือน, , , , , , ,".$sum_of_interest_atm;
			exit;
		}
		if(@$_GET['excel']){
			$interest_return = 0;
			$date_ruturn_interest = date("Y-m-t", strtotime( "-1 months", strtotime((@$_GET['year']-543) . "-" . @$_GET['month'] . "-01") ) );
			$days_in_year = $this->center_function->get_days_of_year($_GET['year']-543);

			$sql_return = "SELECT 
					principal_payment
					* (select interest_rate from coop_loan_atm_setting_template where start_date <= coop_finance_transaction.payment_date order by start_date DESC,run_id DESC limit 1)
					/ 100
					* DATEDIFF('$date_ruturn_interest', (select date(transaction_datetime) from coop_loan_atm_transaction where loan_atm_id = ".$data['loan_atm_id']." and transaction_datetime <= '$date_ruturn_interest 23:59:59' order by transaction_datetime desc limit 1) )
					/ $days_in_year as interest_return
					from coop_finance_transaction where loan_atm_id and principal_payment != 0 and receipt_id = (select receipt_id from coop_loan_atm_transaction where loan_atm_id = ".$data['loan_atm_id']." and receipt_id ORDER BY loan_atm_id desc limit 1);";
			$interest_return = round(@$this->db->query($sql_return)->result_array()[0]['interest_return'], 2 );

			$str = ",";
			fputcsv($fp, explode(",", $str));
			$str = "เก็บ ต้น (ประมาณการไม่ถูกนำไปใช้จริง), , , , , , ,".@$arr_loan_interest['principal_month'];
			fputcsv($fp, explode(",", $str));
			$str = "ดบ., , , , , , ,".@$total_interest;
			fputcsv($fp, explode(",", $str));
			$str = "ดบ.ยอดที่กดระหว่างเดือน, , , , , , ,".@$sum_of_interest_atm;
			fputcsv($fp, explode(",", $str));
			$str = "ดบ.สะสม, , , , , , ,".@$remain_interest;
			fputcsv($fp, explode(",", $str));
			$str = "รวมดอกเบี้ย, , , , , , ,=".($arr_loan_interest['interest_month'])."-".$sum_real_pay_interest.",".$used;
			fputcsv($fp, explode(",", $str));
			$str = "เงินคืน., , , , , , ,".@$interest_return;
			fputcsv($fp, explode(",", $str));
			fclose($fp);
		}


		return $arr_loan_interest;
	}
	

	//-----------------------------------------------------
	// $type_count_date is array("month" => XX, "year" => XX) จะเป็นตัวนับจำนวนวันในเดือนนั้นๆ ใช้ในการคำนวณดอกเบี้ยออกรายการเรียกเก็บประชำเดือน
	public function cal_loan_interest($data,$return_type="echo",$type_count_date=""){
		$this->db->select(array('t1.loan_amount_balance','(select interest_rate from coop_term_of_loan where type_id = t1.loan_type and start_date <= CURDATE() ORDER BY start_date desc, id desc LIMIT 1) as interest_per_year','t1.createdatetime','t2.date_transfer','t1.date_last_interest'));
		$this->db->from('coop_loan as t1');
		$this->db->join('coop_loan_transfer as t2','t1.id = t2.loan_id','inner');
		$this->db->where("
			t1.id = '".$data['loan_id']."' AND t2.transfer_status = '0'
		");
		$row = $this->db->get()->result_array();
		$row_loan = @$row[0];

		/*$this->db->select('payment_date');
		$this->db->from('coop_finance_transaction');
		$this->db->where("
			loan_id = '".$data['loan_id']."'
			AND deduct_type = 'all'
		");
		$this->db->order_by("payment_date DESC");
		$this->db->limit(1);
		$row = $this->db->get()->result_array();
		$row_last_transaction = @$row[0];*/
		if($row_loan['date_last_interest']!=''){
			$last_payment_date = $row_loan['date_last_interest'];
		}else{
			$last_payment_date = @$row_loan['date_transfer'];
		}

		$this->db->select('*');
		$this->db->from('coop_loan_transaction');
		$this->db->where("loan_id = '".$data['loan_id']."'");
		$this->db->order_by("transaction_datetime", "desc");
		$this->db->order_by("loan_transaction_id", "desc");
		$this->db->limit(1);
		// $this->db->where("
		// 	loan_id = '".$data['loan_id']."'
		// 	AND transaction_datetime >= '".$last_payment_date."'
		// ");
		// $this->db->order_by("loan_transaction_id ASC");
		$row = $this->db->get()->result_array();
		$loan_transaction = array();
		$i=0;
		if(!empty($row)){
			foreach($row as $key => $value){
				$loan_transaction[$i]['loan_amount_balance'] = $value['loan_amount_balance'];
				$date_start = explode(' ',$value['transaction_datetime']);
				$loan_transaction[$i]['date_start'] = $date_start[0];
				if(@$row[$key+1]['transaction_datetime']!=''){
					$date_end = explode(' ',$row[$key+1]['transaction_datetime']);
					$loan_transaction[$i]['date_end'] = $date_end[0];
				}else{
					$loan_transaction[$i]['date_end'] = $data['date_interesting'];
				}

				//หาจำนวนวัน
				if($type_count_date==""){
					$diff = date_diff(date_create($loan_transaction[$i]['date_start']),date_create($loan_transaction[$i]['date_end']));
					$date_count = $diff->format("%a");
				}else{
					$date_count = cal_days_in_month(CAL_GREGORIAN, $type_count_date['month'], $type_count_date['year']);
				}


				$loan_transaction[$i]['date_count'] = $date_count;
				$interest = ((($loan_transaction[$i]['loan_amount_balance']*$row_loan['interest_per_year'])/100)/365)*$loan_transaction[$i]['date_count'];
				$loan_transaction[$i]['origin_interest'] = $interest;
				$interest = round($interest);
				$loan_transaction[$i]['interest'] = $interest;
				$loan_transaction[$i]['interest_rate'] = $row_loan['interest_per_year'];
				$i++;
			}
			$loan_transaction_tmp = array();
			foreach($loan_transaction as $key => $value){
				$loan_transaction_tmp[$value['date_start']] = $value;
			}
			$loan_transaction = $loan_transaction_tmp;
		}


		$interest_amount = 0;
		foreach(@$loan_transaction as $key => $value){
			$interest_amount += $value['interest'];
		}
		//echo "<pre>";print_r($loan_transaction);echo"</pre>";exit;
		if($return_type == 'echo'){
			return $interest_amount;
		}else{
			return $loan_transaction;
		}

	}


	public function cal_atm_after_process($data,$return_type='echo'){
		$loan_atm_id = @$data['loan_atm_id'];
		$date_interesting = @$data['date_interesting'];
		$arr_date = explode('-',$date_interesting);
		$mm_profile = (int)$arr_date[1];
		$yy_profile = $arr_date[0]+543;

		//หาโปรไฟล์ ก่อน
		$rs_profile = $this->db->select(array('profile_id'))
		->from('coop_finance_month_profile')
		->where("profile_month = '".$mm_profile."' AND profile_year = '".$yy_profile."'")
		->limit(1)
		->get()->result_array();
		$profile = @$rs_profile[0]['profile_id'];

		//เช็คการผ่านรายการ
		$finance_month = $this->db->select('
		coop_finance_month_detail.member_id,
		coop_finance_month_detail.loan_atm_id,
		coop_finance_month_detail.profile_id,
		coop_finance_month_detail.run_status,
		coop_finance_month_detail.pay_type,
		coop_finance_month_detail.pay_amount
		')
		->from('coop_finance_month_detail')
		->where("coop_finance_month_detail.loan_atm_id IS NOT NULL
				AND coop_finance_month_detail.loan_atm_id != ''
				AND coop_finance_month_detail.loan_atm_id = '".$loan_atm_id."'
				AND profile_id = '".$profile."'
				AND pay_type = 'principal'
				AND run_status = '1'")
		->get()->result_array();

		$atm_month_detail = array();
		$finance_month_amount = 0;
		$i=0;
		foreach($finance_month as $key => $value){
			$atm_month_detail[$i]['pay_amount'] = $value['pay_amount'];
			$finance_month_amount += $value['pay_amount'];
			$i++;
		}

		if($return_type == 'echo'){
			return $finance_month_amount;
		}else{
			return $atm_month_detail;
		}

	}

	public function cal_atm_interest_transaction($data,$return_type='echo'){
		$loan_atm_id = @$data['loan_atm_id'];
		$date_interesting = @$data['date_interesting'];
		$arr_date = explode('-',$date_interesting);
		$mm_en = (int)$arr_date[1];
		$yy_en = $arr_date[0];

		$this->db->select('*');
		$this->db->from('coop_loan_atm_setting');
		$row = $this->db->get()->result_array();
		$loan_atm_setting = @$row[0];

		//echo $date_interesting.'<br>';
		//$start_date = $yy_en.'-'.sprintf("%02d",@$mm_en).'-01'.' 00:00:00.000';
		//$end_date = date('Y-m-t',strtotime($start_date)).' 23:59:59.000';

		//echo '<pre>'; print_r($data); echo '</pre>';
		$rs_loan_atm = $this->db->select('member_id')
		->from('coop_loan_atm')
		->where("loan_atm_id = '".$loan_atm_id."'")
		->limit(1)
		->get()->result_array();
		$member_id = @$rs_loan_atm[0]['member_id'];

		$rs_receipt = $this->db->select('receipt_datetime')
		->from('coop_receipt')
		->where("member_id = '".$member_id."' AND month_receipt IS NOT NULL AND year_receipt IS NOT NULL  AND finance_month_profile_id IS NOT NULL ")
		->order_by("receipt_datetime DESC")
		->limit(1)
		->get()->result_array();

		$start_date = @$rs_receipt[0]['receipt_datetime'];
		$end_date = date('Y-m-t',strtotime($date_interesting)).' 23:59:59.000';

		$rs_atm_detail = $this->db->select('loan_date,loan_amount,loan_amount_balance')
		->from('coop_loan_atm_detail')
		->where("
			loan_atm_id = '".$loan_atm_id."'
			AND loan_date BETWEEN '".$start_date."' AND '".$end_date."'
		")
		->get()->result_array();
		$row_atm_detail = @$rs_atm_detail;
		//echo $this->db->last_query(); echo '<br>';
		//exit;
		//echo '<pre>'; print_r($row_atm_detail); echo '</pre>';
		$atm_transaction = array();
		$i=0;
		foreach($row_atm_detail as $key => $value){
			$atm_transaction[$i]['loan_amount_balance'] = $value['loan_amount_balance'];
			$date_start = explode(' ',$value['loan_date']);
			$atm_transaction[$i]['date_start'] = $date_start[0];
			if(@$row[$key+1]['loan_date']!=''){
				$date_end = explode(' ',$row[$key+1]['loan_date']);
				$atm_transaction[$i]['date_end'] = $date_end[0];
			}else{
				$atm_transaction[$i]['date_end'] = $data['date_interesting'];
			}
			$diff = date_diff(date_create($atm_transaction[$i]['date_start']),date_create($atm_transaction[$i]['date_end']));
			$date_count = $diff->format("%a");
			if($date_count == 0){
				//$date_count = $date_count+1;
			}
			$atm_transaction[$i]['date_count'] = $date_count;
			$interest = ((($atm_transaction[$i]['loan_amount_balance']*$loan_atm_setting['interest_rate'])/100)/365)*$atm_transaction[$i]['date_count'];
			$atm_transaction[$i]['origin_interest'] = $interest;
			$interest = number_format($interest, 2, '.', '');
			//echo 'interest='.$interest.'<br>';
			//$interest = round($interest);
			$atm_transaction[$i]['interest_rate'] = $loan_atm_setting['interest_rate'];
			$atm_transaction[$i]['interest'] = $interest;
			$i++;
		}
		//echo '<pre>'; print_r($atm_transaction); echo '</pre>';
		$interest_amount = 0;
		foreach($atm_transaction as $key => $value){
			$interest_amount += $value['interest'];
		}

		if($return_type == 'echo'){
			return number_format($interest_amount,0, '.', '');
		}else{
			return $atm_transaction;
		}
	}

	public function generate_period_loan($loan_id, $pay_type, $period_type, $period, $date_cal ,$interest){
		// var_dump($data);
		// init set
		$this->db->select(array("coop_loan.*", "DAY(date_start_period) AS d", "MONTH(date_start_period) AS m", "YEAR(date_start_period) AS y"));
		$loan_row = $this->db->get_where("coop_loan", array("id" => $loan_id) )->result()[0];
		if($loan_row!=""){

			$this->db->where("loan_id", $loan_id);
			$this->db->delete("coop_loan_period");
		}

		// $interest = $interest; // อัตราดอกเบี้ย
		$loan = $loan_row->loan_amount; // จำนวนเงินกู้
		// $pay_type = $_POST["pay_type"]; // ปรเภท ชำระเท่ากันทุกงวด,ต้นเท่ากันทุกงวด
		// $period = (double)$_POST["period"]; // จำนวน งวด  หรือ เงิน แล้วแต่ type
		$tmp_date = explode("-", $date_cal);
		$day = $tmp_date[2];
		$month = $tmp_date[1]-1;
		$year  = $tmp_date[0];
		// $period_type= 2; // ประเภท งวดหรือจำนวนเงิน
		//1 งวด
		//2 จำนวนเงิน

		if($period_type == '1' && $pay_type=='2'){
			$total_per_period = $loan/$period;

			$date_start = ($year-543)."-".$month."-".$day;
			$date_period_1 = date('Y-m-t',strtotime('+1 month',strtotime($date_start)));
			$diff = date_diff(date_create($date_start),date_create($date_period_1));
			$date_count = $diff->format("%a");
			$date_count = 31;
			$interest_period_1 = ((($loan*$interest)/100)/365)*$date_count;
			//if($interest_period_1 > $total_per_period){
				$per_period = ($loan * ( (6/100) / 12 ))/( 1-pow(1/(1+( (6/100) /12)),$period));
				//$per_period = ceil($interest_period_1/100)*100;
				$period = $per_period;
				$period_type = 2;
			//}
		}

		$pay_period = $loan / $period;
		$a = ceil($pay_period/10)*10;

		$daydiff = date('t') - $day;


		// ---------------------------
				$loan_remain = $loan;
				$is_last = FALSE;
				$total_loan_pri = 0;
				$total_loan_int = 0;
				$total_loan_pay = 0;
				$d = $period - 1;

				$peroid_row = array();
				for ($i=1; $i <= $period; $i++) {

					if($loan_remain <= 0 ){ break; }
					if($pay_type == 1) {
						if ($period_type == 1) {
									if ($month > 12) {
											$month = 1;
											$year += 1;
									}
									//$loan_pri = $a;
									$loan_pri = ceil($a/100)*100;
									$nummonth = cal_days_in_month(CAL_GREGORIAN, $month , $year);
									$summonth = $nummonth;
									$daydiff = 31 - $day;
									if ($i == 1) {
										if ($daydiff >= 0) {
												/*if ($day <= 10) {
													$summonth -=  $day;
													$summonth += 1;
												} else if ($day >= 11 && $day <= 31) {*/
													$month += 1;
													if ($month > 12) {
															$month = 1;
															$year += 1;
													}
													$nummonth = cal_days_in_month(CAL_GREGORIAN, $month , $year);
													$summonth = $nummonth;
													$summonth = $daydiff + 31;
												//}
										 }
									}
									$summonth = $this->force_summonth($summonth,$i);
									//$loan_int = $loan_remain * ($interest / (365 / $summonth)) / 100;
									$loan_int = round($loan_remain * ($interest / (365 / $summonth)) / 100);
									if($loan_pri < 0){
										$loan_pri = 0;
									}
									$loan_pay = $loan_pri + $loan_int;
									$loan_remain -= ceil($loan_pri/100)*100;
									//$loan_remain -= $loan_pri;
						} else if ($period_type == 2) {
							if ($month > 12) {
									$month = 1;
									$year += 1;
							}
							$nummonth = cal_days_in_month(CAL_GREGORIAN, $month , $year);
							$summonth = $nummonth;
							$daydiff = 31 - $day;
							if ($i == 1) {
								if ($daydiff >= 0) {
										/*if ($day <= 10) {
											$summonth -=  $day;
											$summonth += 1;
										} else if ($day >= 11 && $day <= 31) {*/
											$month += 1;
											$nummonth = cal_days_in_month(CAL_GREGORIAN, $month , $year);
											$summonth = $nummonth;
											$summonth = $daydiff + 31;
										//}
								 }
							}
							$summonth = $this->force_summonth($summonth,$i);
							//$loan_pri = $period;
							$loan_pri = ceil($period/100)*100;
							//$loan_int = $loan_remain * ($interest / (365 / $summonth)) / 100;
							$loan_int = round($loan_remain * ($interest / (365 / $summonth)) / 100);
							if($loan_pri < 0){
								$loan_pri = 0;
							}
							$loan_pay = $loan_pri + $loan_int;
							//$loan_remain -= $loan_pri;
							$loan_remain -= ceil($loan_pri/100)*100;
					}
				}else if($pay_type == 2) {
						if ($period_type == 1) {
									if ($month > 12) {
											$month = 1;
											$year += 1;
									}
									//$loan_pri = $a;
									$loan_pri = ceil($a/100)*100;
									$nummonth = cal_days_in_month(CAL_GREGORIAN, $month , $year);
									$summonth = $nummonth;
									$daydiff = 31 - $day;
									if ($i == 1) {
										if ($daydiff >= 0) {
												/*if ($day <= 10) {
													$summonth -=  $day;
													$summonth += 1;
												} else if ($day >= 11 && $day <= 31) {*/
													$month += 1;
													if ($month > 12) {
															$month = 1;
															$year += 1;
													}
													$nummonth = cal_days_in_month(CAL_GREGORIAN, $month , $year);
													$summonth = $nummonth;
													$summonth = $daydiff + 31;
												//}
										 }
									}
									$summonth = $this->force_summonth($summonth,$i);
									//$loan_int = $loan_remain * ($interest / (365 / $summonth)) / 100;
									$loan_int = round($loan_remain * ($interest / (365 / $summonth)) / 100);
									$loan_pri = $loan_pri - $loan_int;
									if($loan_pri < 0){
										$loan_pri = 0;
									}
									$loan_pay = $loan_pri + $loan_int;
									$loan_remain -= ceil($loan_pri/100)*100;
						} else if ($period_type == 2) {
							if ($month > 12) {
									$month = 1;
									$year += 1;
							}
							$nummonth = cal_days_in_month(CAL_GREGORIAN, $month , $year);
							$summonth = $nummonth;
							$daydiff = 31 - $day;
							if ($i == 1) {
								if ($daydiff >= 0) {
										/*if ($day <= 10) {
											$summonth -=  $day;
											$summonth += 1;
										} else if ($day >= 11 && $day <= 31) {*/
											$month += 1;
											$nummonth = cal_days_in_month(CAL_GREGORIAN, $month , $year);
											$summonth = $nummonth;
											$summonth = $daydiff + 31;
										//}
								 }
							}
							$summonth = $this->force_summonth($summonth,$i);
							//$loan_pri = $period;
							$loan_pri = ceil($period/100)*100;
							//$loan_int = $loan_remain * ($interest / (365 / $summonth)) / 100;
							$loan_int = round($loan_remain * ($interest / (365 / $summonth)) / 100);
							$loan_pri = $loan_pri - $loan_int;
							if($loan_pri < 0){
								$loan_pri = 0;
							}
							$loan_pay = $loan_pri + $loan_int;
							//$loan_remain -= $loan_pri;
							$loan_remain -= ceil($loan_pri/100)*100;
					}
				}

					if($loan_remain <= 0) {
						$loan_pri += $loan_remain;
						$loan_pay = $loan_pri + $loan_int;
						$loan_remain = 0;
						@$count = $count + 1;
					}

					$sumloan = $loan_remain + $loan_pri;
					$sumloanarr[] = $loan_remain + $loan_pri;
					$sumint[] = $loan_int;

					if ($i == $period) {
						$loan_pri = $sumloanarr[$d];
						$loan_pay = $loan_pri + $loan_int;
					}

					@$total_loan_int += $loan_int;
					//@$total_loan_pri += $loan_pri;
					@$total_loan_pri += ceil($loan_pri/100)*100;
					@$total_loan_pay += $loan_pay;

					//@$total_loan_pri_m += $loan_pri;
					@$total_loan_pri_m += ceil($loan_pri/100)*100;
					@$total_loan_int_m += $loan_int;
					@$total_loan_pay_m += $loan_pay;

					if((int)$month == '2'){
						$nummonth = '28';
					}
					$peroid_row['period_count']				= $i;
					$peroid_row['outstanding_balance']		= $sumloan;
					$peroid_row['date_period']				= ($year)."-".sprintf('%02d',$month)."-".$nummonth;
					$peroid_row['date_count']				= $summonth;
					$peroid_row['interest']					= $loan_int;
					$peroid_row['principal_payment']		= $loan_pri;
					$peroid_row['total_paid_per_month']		= $loan_pay;
					$peroid_row['loan_id']					= $loan_id;
					// echo "<pre>";
					// var_dump($peroid_row);
					// echo "</pre>";
					// $this->db->insert("coop_loan_period",$peroid_row);

					if(@$period_1==""){
							$period_1 = $peroid_row;
							return $period_1;
					}
					if($is_last) {
						break;
					}
					$month++;
				}
				$update_coop_loan = array();
				$update_coop_loan['money_per_period']	= $period_1['total_paid_per_month'];
				$update_coop_loan['period_amount']		= $i-1;
				$update_coop_loan['updatetimestamp']	= date("Y-m-d H:i:s");
				$update_coop_loan['pay_type']					= $pay_type;
				$update_coop_loan['period_type']			= $period_type;
				$update_coop_loan['money_period_1']		= $period_1['total_paid_per_month'];
				$update_coop_loan['date_period_1']		= $period_1['date_period'];
				$update_coop_loan['period_type']			= $period_type;

				// $this->db->where("id", $loan_id);
				// $this->db->update("coop_loan", $update_coop_loan);
				return $update_coop_loan;
		// var_dump($total_per_period);
				// exit;
	}

	private function force_summonth($summonth,$period){
		if($period=='1'){
			$summonth = $summonth-1;
		}else{
			$summonth = $summonth;
		}
		return $summonth;
	}
	
	public function cal_atm_interest_deduct($data,$return_type='echo',$type_count_date=""){
		$this->db->select('*');
		$this->db->from('coop_loan_atm_setting');
		$row = $this->db->get()->result_array();
		$loan_atm_setting = @$row[0];
		$year =	date("Y", strtotime("-0 month", strtotime($data['date_interesting']))) + 543;
		$month = date("m", strtotime("-1 month", strtotime($data['date_interesting'])));
		// var_dump($data);
		$i_last = 0;
		//--------------------
		//หาดอกเบี้ยกดระหว่างเดือน
		//--------------------
		$arr_month = explode('-',$data['date_interesting']);
		$last_month = date("Y-m", strtotime("-1 month", strtotime($data['date_interesting'])));

		$this->db->where("loan_atm_id", $data['loan_atm_id']);
		$this->db->where("transaction_datetime LIKE '$last_month%'");
		$query = $this->db->get("coop_loan_atm_transaction")->result_array();

		$temp_interest 				= 0;
		$sum_of_interest 			= 0;
		$collect_interest 			= 0;
		$remain_interest 			= 0;

		if($query){
			// echo "<br>";
			$this->db->where("loan_atm_id", $data['loan_atm_id']);
			$this->db->where("transaction_datetime < ", $query[0]['transaction_datetime']);
			$this->db->order_by("transaction_datetime DESC, loan_atm_transaction_id DESC");
			$this->db->limit(1);		
			
			$query_last_transaction 			= $this->db->get("coop_loan_atm_transaction")->result_array()[0];
			if($query_last_transaction){
				$last_atm_transaction_date 		= date("Y-m-d", strtotime("-0 month", strtotime($query_last_transaction['transaction_datetime'])));	
				$bf								= $query_last_transaction['loan_amount_balance'];
				// echo $bf;
				// exit;
			}else{
				$last_atm_transaction_date 		= date("Y-m-t", strtotime("-2 month", strtotime($data['date_interesting'])));
				$this->db->order_by('transaction_datetime ASC, loan_atm_transaction_id ASC');
				$this->db->limit(1);
				$this->db->where("transaction_datetime < '$last_atm_transaction_date'");
				$this->db->where("loan_atm_id", $data['loan_atm_id']);
				$bf = $this->db->get("coop_loan_atm_transaction")->result()[0]->loan_amount_balance;
			}
			
			foreach ($query as $key => $value) {
				// var_dump($value);
				$diff 							= date_diff(date_create($value['transaction_datetime']), date_create($last_atm_transaction_date));
				$date_count 					= $diff->format("%a");
				$last_atm_transaction_date 		= date("Y-m-d", strtotime($value['transaction_datetime']));
				$temp_interest 					= $bf * 6 / 100 * $date_count / 365;
				
				$sum_of_interest 				+= $temp_interest;
				$remain_interest 				+= $temp_interest; 
				$collect_interest				= $temp_interest;
				$bf 							= $value['loan_amount_balance'];					
			}
		}else{
			$this->db->order_by('transaction_datetime DESC, loan_atm_transaction_id DESC');
			$this->db->limit(1);
			$this->db->where("loan_atm_id", $data['loan_atm_id']);
			$bf = $this->db->get("coop_loan_atm_transaction")->result()[0]->loan_amount_balance;
		}
		//echo $this->db->last_query(); echo '<br>';
		// echo "<br>";
		$interest = 0;
		//----------------หาดอกเบี้ยส่วนที่ยังไม่มีการคิดดอกเบี้ย
		if($last_atm_transaction_date != ''){
			$diff = date_diff(date_create( date("Y-m-t", strtotime("-1 month", strtotime($data['date_interesting']) ) ) ), date_create($last_atm_transaction_date));
			$date_count = $diff->format("%a");
			$tmp_interest = $bf * $date_count * $loan_atm_setting['interest_rate'] / 100 / 365;
			// echo "<br>".$tmp_interest." หาดอกเบี้ยส่วนที่ยังไม่มีการคิดดอกเบี้ย<br>";
			$interest += $tmp_interest;
		}
		
		//----------------หา ดบ. เรียกเก็บ
		$diff = date_diff(date_create( date("Y-m-t", strtotime("-1 month", strtotime($data['date_interesting']) ) ) ), date_create( date('Y-m-t', strtotime($data['date_interesting']) ) ));
		$date_count = $diff->format("%a");

		// echo "<br>".date('Y-m-01', strtotime($data['date_interesting']) );
		$tmp_interest = $bf * $date_count * $loan_atm_setting['interest_rate'] / 100 / 365;
		// echo  $bf . " * ". $date_count . " * ". $loan_atm_setting['interest_rate'] . " / 100 / 365<br>";
		// echo $tmp_interest." -หา ดบ. เรียกเก็บ<br>";
		$interest += $tmp_interest;
		// echo "<br>remain_interest ".$remain_interest."<br>";
		
		//---------------หา ดบ. สะสม
		$this->db->join("coop_finance_month_detail", "coop_finance_month_detail.profile_id = coop_finance_month_profile.profile_id");
		$this->db->where("profile_month", $month);
		$this->db->where("profile_year", $year);
		$this->db->where("loan_atm_id", $data['loan_atm_id']);
		$this->db->where("pay_type", 'interest');
		$this->db->limit(1);
		$query_last_payment = $this->db->get("coop_finance_month_profile");
		//echo $this->db->last_query(); echo '<br>';
		$last_payment 		= 0;
		if($query_last_payment)
			foreach ($query_last_payment->result_array() as $key => $value) 
				$last_payment += $value['pay_amount'];
			
			
		
		$deduct = 0;
		$deduct = ($last_payment - $remain_interest);

		 $total_interest = ($interest + $remain_interest);

		$temp_interest 				= 0;
		$sum_of_interest 			= 0;
		$collect_interest 			= 0;
		$remain_interest 			= 0;
		
		$arr_month = explode('-',$data['date_interesting']);
		$last_month = date("Y-m", strtotime("-0 month", strtotime($data['date_interesting'])));

		$this->db->where("loan_atm_id", $data['loan_atm_id']);
		$this->db->where("transaction_datetime LIKE '$last_month%'");
		$query = $this->db->get("coop_loan_atm_transaction")->result_array();
		//echo $this->db->last_query(); echo '<br>';
		//echo '=============coop_loan_atm_transaction=============<br>';
		if($query){
			// echo "<br>";
			$this->db->where("loan_atm_id", $data['loan_atm_id']);
			$this->db->where("transaction_datetime < ", $query[0]['transaction_datetime']);
			$this->db->order_by("transaction_datetime DESC, loan_atm_transaction_id DESC");
			$this->db->limit(1);		
			
			$query_last_transaction 			= $this->db->get("coop_loan_atm_transaction")->result_array()[0];
			if($query_last_transaction){
				$last_atm_transaction_date 		= date("Y-m-d", strtotime("-0 month", strtotime($query_last_transaction['transaction_datetime'])));	
				$bf								= $query_last_transaction['loan_amount_balance'];
			}else{
				$last_atm_transaction_date 		= date("Y-m-t", strtotime("-2 month", strtotime($data['date_interesting'])));
				$this->db->order_by('transaction_datetime ASC, loan_atm_transaction_id ASC');
				$this->db->limit(1);
				$this->db->where("transaction_datetime < '$last_atm_transaction_date'");
				$this->db->where("loan_atm_id", $data['loan_atm_id']);
				$bf = $this->db->get("coop_loan_atm_transaction")->result()[0]->loan_amount_balance;
			}
			
			foreach ($query as $key => $value) {
				// var_dump($value);
				$diff 							= date_diff(date_create($value['transaction_datetime']), date_create($last_atm_transaction_date));
				$date_count 					= $diff->format("%a");
				$last_atm_transaction_date 		= date("Y-m-d", strtotime($value['transaction_datetime']));
				$temp_interest 					= $bf * 6 / 100 * $date_count / 365;
				
				$sum_of_interest 				+= $temp_interest;
				$remain_interest 				+= $temp_interest; 
				$collect_interest				= $temp_interest;
				$loan_amount_balance			= $value['loan_amount_balance'];				
			}			 
		}
		
		//หา ณ วันที่หักกลบ
		$diff 							= date_diff(date_create($data['date_interesting']), date_create($last_atm_transaction_date));
		$date_count 					= $diff->format("%a");
		$last_atm_transaction_date 		= date("Y-m-d", strtotime($data['date_interesting']));
		$temp_interest 					= $loan_amount_balance * 6 / 100 * $date_count / 365;
		
		$sum_of_interest 				+= $temp_interest;
		$remain_interest 				+= $temp_interest; 
		$collect_interest				= $temp_interest;
		// ดบ.ที่ต้องจ่าย เดือนปจจุบัน ลบ ดบ.สะสม ของเดือนก่อน
		$interest_now = $remain_interest - $deduct;
		
		$loan_interest = round($interest_now);
		//echo '===============interest_month===============<br>';
		//echo '<pre>'; print_r($arr_loan_interest); echo '</pre>';
		return $loan_interest;
	}
	
	public function update_loan_atm_setting_now() {
		$this->db->select(array('*'));
		$this->db->from('coop_loan_atm_setting_template');
		$this->db->where("start_date <= '".date('Y-m-d')."'");
		$this->db->order_by('start_date DESC, run_id DESC');
		if($row = $this->db->get()->row_array()) {
			$data_insert = array();
			$data_insert['prefix_code']  = $row["prefix_code"];
			$data_insert['max_loan_amount']  = $row["max_loan_amount"];
			$data_insert['interest_rate']  = $row["interest_rate"];
			$data_insert['use_atm_count']  = $row["use_atm_count"];
			$data_insert['use_atm_over_count_fee']	= $row["use_atm_over_count_fee"];
			$data_insert['min_loan_amount']  = $row["min_loan_amount"];
			$data_insert['min_month_share']  = $row["min_month_share"];
			$data_insert['max_period']  = $row["max_period"];
			$data_insert['max_withdraw_amount_day']  = $row["max_withdraw_amount_day"];
			
			$table = "coop_loan_atm_setting";
			$this->db->where('run_id', '1');
			$this->db->update($table, $data_insert);
		}
	}

	public function calc_interest_loan($loan_amount, $loan_type, $date1, $date2){
		$query = $this->db->query("select calc_loan_interest($loan_amount, $loan_type, '$date1', '$date2' )");
		$tmp_interest = $query->result_array()[0];
		$key = array_keys($tmp_interest);
		$interest = $tmp_interest[$key[0]];
		return $interest;
	}
}
