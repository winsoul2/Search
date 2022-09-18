<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tool extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    public function receipt_edit($receipt_id){
        $data_arr = array();
		$receipt_id2 =!empty($receipt_id2)? '/'.$receipt_id2:'';
        
        $data_arr['receipt_id'] = $receipt_id;
		$data_arr['profile'] = $this->db->get("coop_profile")->row_array();

		
		$this->db->select('*');
		$this->db->from("coop_receipt");
		$this->db->join("coop_user", "coop_receipt.admin_id = coop_user.user_id", "left");
		$this->db->where("receipt_id ='".$receipt_id.$receipt_id2."'");
		$row = $this->db->get()->result_array();

		$data_arr['row_receipt'] = $row[0];

		if($data_arr['row_receipt']['from_member_id']!=""){
			$this->db->select(array(
				"CONCAT(prename_short, firstname_th, ' ', lastname_th) as fullname",
			));
			$this->db->join("coop_prename", "coop_mem_apply.prename_id = coop_mem_apply.prename_id", "left");
			$data_arr['row_receipt']['from_member_name'] = $this->db->get_where("coop_mem_apply", array("member_id" => $data_arr['row_receipt']['from_member_id']))->result_array()[0]['fullname'];

		}
		$arr_pay_type = array('0'=>'เงินสด','1'=>'โอนเงิน');
		$data_arr['pay_type'] =  $arr_pay_type[$row[0]['pay_type']];
		//วันที่ใบเสร็จ
		$receipt_datetime = date("Y-m-d", strtotime($data_arr['row_receipt']['receipt_datetime']));

		$this->db->select(array('t1.*','t2.mem_group_name','t3.prename_full'));
		$this->db->from("(SELECT IF (
										(
											SELECT
												level_old
											FROM
												coop_mem_group_move
											WHERE
												date_move >= '".$receipt_datetime."'
											AND coop_mem_group_move.member_id = coop_mem_apply.member_id
											ORDER BY
												date_move ASC
											LIMIT 1
										),
										(
											SELECT
												level_old
											FROM
												coop_mem_group_move
											WHERE
												date_move >= '".$receipt_datetime."'
											AND coop_mem_group_move.member_id = coop_mem_apply.member_id
											ORDER BY
												date_move ASC
											LIMIT 1
										),
										coop_mem_apply. level
									) AS level, member_id, firstname_th, lastname_th, mem_type_id, prename_id FROM coop_mem_apply) as t1");
		$this->db->join("coop_mem_group as t2",'t1.level = t2.id','left');
		$this->db->join("coop_prename as t3",'t1.prename_id = t3.prename_id','left');
		$this->db->where("member_id ='".$data_arr['row_receipt']['member_id']."'");
		$row = $this->db->get()->result_array();
		if(@$_GET['dev']=='dev'){
			echo $this->db->last_query(); exit;
		}

		$data_arr['prename_full'] = @$row[0]['prename_full'];
		$data_arr['name'] = @$row[0]['firstname_th'].' '.@$row[0]['lastname_th'];
		$data_arr['member_data'] = @$row[0];
		$data_arr['member_id'] = @$row[0]['member_id'];
		$data_arr['from_member_id'] = $data_arr['row_receipt']['from_member_id'];
		$data_arr['from_member_name'] = $data_arr['row_receipt']['from_member_name'];
	//		echo"<pre>";print_r($row[0]);exit;

		$this->db->select(array(
			't1.*',
		));
		$this->db->from("coop_finance_transaction as t1");
		$this->db->where("t1.receipt_id = '".$receipt_id.$receipt_id2."'");
		$row = $this->db->get()->result_array();

		$transactions = array();
		$account_list = null;
		foreach($row as $transaction) {
			$account_list = $transaction["account_list_id"];
			if (!empty($transaction['loan_id'])) {
				if(!empty($transaction['principal_payment'])) $transactions[$transaction['loan_id']]['transaction_text_main'] = $transaction['transaction_text'];
				$transactions[$transaction['loan_id']]['transaction_text'] = $transaction['transaction_text'];
				if(!empty($transaction['period_count'])) $transactions[$transaction['loan_id']]['period_count'] = $transaction['period_count'];
				$transactions[$transaction['loan_id']]['principal_payment'] += $transaction['principal_payment'];
				$transactions[$transaction['loan_id']]['interest'] += $transaction['interest'];
				$transactions[$transaction['loan_id']]['loan_interest_remain'] += $transaction['loan_interest_remain'];
				if(!empty($transaction['loan_amount_balance'])) $transactions[$transaction['loan_id']]['loan_amount_balance'] = $transaction['loan_amount_balance'];
			} else if (!empty($transaction['loan_atm_id'])) {
				if(!empty($transaction['principal_payment'])) $transactions["atm_".$transaction['loan_atm_id']]['transaction_text_main'] = $transaction['transaction_text'];
				$transactions["atm_".$transaction['loan_atm_id']]['transaction_text'] = $transaction['transaction_text'];
				if(!empty($transaction['period_count'])) $transactions["atm_".$transaction['loan_atm_id']]['period_count'] = $transaction['period_count'];
				$transactions["atm_".$transaction['loan_atm_id']]['principal_payment'] += $transaction['principal_payment'];
				$transactions["atm_".$transaction['loan_atm_id']]['interest'] += $transaction['interest'];
				$transactions["atm_".$transaction['loan_atm_id']]['loan_interest_remain'] += $transaction['loan_interest_remain'];
				if(!empty($transaction['loan_amount_balance'])) $transactions["atm_".$transaction['loan_atm_id']]['loan_amount_balance'] = $transaction['loan_amount_balance'];
			} else {
				$transactions[] = $transaction;
			}
		}

		$data_arr['transaction_data'] = $transactions;
		$data_arr['account_list'] = $account_list;

		//ลายเซ็นต์
		$date_signature = date('Y-m-d');
		$this->db->select(array('*'));
		$this->db->from('coop_signature');
		$this->db->where("start_date <= '{$date_signature}'");
		$this->db->order_by('start_date DESC');
		$this->db->limit(1);
		$row = $this->db->get()->result_array();
	//		echo"<pre>";print_r($row);exit;
		$data_arr['signature'] = @$row[0];
		
		$this->db->select('*');
		$this->db->from("coop_loan");
		$this->db->where("deduct_receipt_id = '".$receipt_id.$receipt_id2."'");
		$row = $this->db->get()->result_array();
		$data_arr['pay_for_loan']['contract_number'] = @$row[0]['contract_number'];

        
        $this->libraries->template('tool/receipt_edit',$data_arr);
    }

    public function receipt_edit_save(){
        $receipt_id = $_POST['receipt_id'];
        if($receipt_id==""){
            die();
        }
        foreach ($_POST['role'] as $key => $value) {
            $role                       = $value;
            $role_value                 = $_POST['key'][$key];
            $principal_payment          = str_replace(",","", $_POST['principal_payment'][$key]);
            $interest                   = str_replace(",","", $_POST['interest'][$key]);
			$loan_amount_balance        = str_replace(",","", $_POST['loan_amount_balance'][$key]);
			$period_count        		= str_replace(",","", $_POST['period_count'][$key]);
			$change_period        		= $_POST['change_period'][$key];

            if($role=="finance_transaction_id"){
                $this->db->where("finance_transaction_id", $role_value);
                $this->db->where("receipt_id", $receipt_id);
                $old_row = $this->db->get("coop_finance_transaction")->row_array();
                if($old_row['principal_payment']!=$principal_payment){
                    $this->db->set("principal_payment", $principal_payment);
                    $this->db->set("total_amount", "interest + ".$principal_payment, false);
                    $this->db->where("finance_transaction_id", $role_value);
                    $this->db->where("receipt_id", $receipt_id);
                    $this->db->update("coop_finance_transaction");

                    $this->db->set("receipt_count", $principal_payment);
                    $this->db->where("receipt_count", $old_row['principal_payment']);
                    $this->db->where("receipt_list", $old_row['account_list_id']);
                    $this->db->where("receipt_id", $receipt_id);
                    $this->db->update("coop_receipt_detail");

                    $this->db->set('sumcount', '(select sum(receipt_count) from coop_receipt_detail where receipt_id = "'.$receipt_id.'")', false);
                    $this->db->where("receipt_id", $receipt_id);
                    $this->db->update("coop_receipt");
                }
                if($old_row['loan_amount_balance']!=$loan_amount_balance){
                    $this->db->set("loan_amount_balance", $loan_amount_balance);
                    $this->db->where("receipt_list", $old_row['account_list_id']);
                    $this->db->where("finance_transaction_id", $role_value);
                    $this->db->where("receipt_id", $receipt_id);
                    $this->db->update("coop_finance_transaction");
                }
            }else{
				if($role_value=="") continue;
                $this->db->select('account_list_id, sum(principal_payment) as principal_payment, sum(interest) as interest, sum(loan_amount_balance) as loan_amount_balance, period_count');
                $this->db->where("loan_id", $role_value);
                $this->db->where("receipt_id", $receipt_id);
                $this->db->group_by("loan_id");
                $old_row = $this->db->get("coop_finance_transaction")->row_array();
                if($old_row['principal_payment']!=$principal_payment){
                    $this->db->set("principal_payment", $principal_payment);
                    $this->db->set("total_amount", "interest + ".$principal_payment, false);
                    $this->db->where("principal_payment", $old_row['principal_payment']);
                    $this->db->where("loan_id", $role_value);
                    $this->db->where("receipt_id", $receipt_id);
                    $this->db->update("coop_finance_transaction");

                    if($old_row['principal_payment'] > $principal_payment){
                        $this->db->set("loan_amount_balance", $old_row['loan_amount_balance'] - abs($old_row['principal_payment']-$principal_payment));
                    }else{
                        $this->db->set("loan_amount_balance", $old_row['loan_amount_balance'] + abs($old_row['principal_payment']-$principal_payment));
                    }
                    
                    $this->db->where("loan_id", $role_value);
                    $this->db->where("receipt_id", $receipt_id);
                    $this->db->update("coop_loan_transaction");
                    

                    $this->db->set("receipt_count", $principal_payment);
                    $this->db->where("receipt_count", $old_row['principal_payment']);
                    $this->db->where("receipt_list", $old_row['account_list_id']);
                    $this->db->where("receipt_id", $receipt_id);
                    $this->db->update("coop_receipt_detail");

                    $this->db->set('sumcount', '(select sum(receipt_count) from coop_receipt_detail where receipt_id = "'.$receipt_id.'")', false);
                    $this->db->where("receipt_id", $receipt_id);
                    $this->db->update("coop_receipt");
                }
                if($old_row['interest']!=$interest){
                    $this->db->set("interest", $interest);
                    $this->db->set("total_amount", "principal_payment + ".$interest, false);
                    $this->db->where("interest", $old_row['interest']);
                    $this->db->where("loan_id", $role_value);
                    $this->db->where("receipt_id", $receipt_id);
                    $this->db->update("coop_finance_transaction");

                    $this->db->set("receipt_count", $principal_payment);
                    $this->db->where("receipt_count", $old_row['principal_payment']);
                    $this->db->where("receipt_list", $old_row['account_list_id']);
                    $this->db->where("receipt_id", $receipt_id);
                    $this->db->update("coop_receipt_detail");

                    $this->db->set('sumcount', '(select sum(receipt_count) from coop_receipt_detail where receipt_id = "'.$receipt_id.'")', false);
                    $this->db->where("receipt_id", $receipt_id);
                    $this->db->update("coop_receipt");
                }
                if($old_row['loan_amount_balance']!=$loan_amount_balance){
                    $this->db->set("loan_amount_balance", $loan_amount_balance);
                    $this->db->where("loan_amount_balance", $old_row['loan_amount_balance']);
                    $this->db->where("loan_id", $role_value);
                    $this->db->where("receipt_id", $receipt_id);
                    $this->db->update("coop_finance_transaction");

                    $this->db->set("loan_amount_balance", $loan_amount_balance);
                    $this->db->where("loan_id", $role_value);
                    $this->db->where("receipt_id", $receipt_id);
                    $this->db->update("coop_loan_transaction");
				}
				if($period_count!=""){
                    $this->db->set("period_count", $period_count);
                    $this->db->where("loan_id", $role_value);
                    $this->db->where("receipt_id", $receipt_id);
                    $this->db->update("coop_finance_transaction");

					if($change_period=="1"){
						$this->db->set("period_now", $period_count);
						$this->db->where("id", $role_value);
						$this->db->update("coop_loan");
					}
				}
            }
            
           
        }
        $this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
		echo "<script> document.location.href='".base_url(PROJECTPATH.'/tool/receipt_edit/'.$receipt_id)."' </script>";
		exit;
    }



}
