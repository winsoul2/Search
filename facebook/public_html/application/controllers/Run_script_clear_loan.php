<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Run_script_clear_loan extends CI_Controller {
	function __construct()
	{
		parent::__construct();
	}
	public function index(){
		exit;
	}
	function clear_loan_id(){
		$loan_id = @$_GET['loan_id'];
		
		echo '=========coop_loan=========<br>';
		$this->db->select(array('id AS loan_id','deduct_receipt_id'));
		$this->db->from('coop_loan');
		$this->db->where("id = '".$loan_id."'");
		$rs_loan = $this->db->get()->result_array();
		//echo"<pre>";print_r($rs_member);echo"</pre>"; //exit;
		foreach($rs_loan as $key_loan => $row_loan){			
			if($row_loan['loan_id'] != ''){
				$deduct_receipt_id = $row_loan['deduct_receipt_id'];
				echo"<pre>";print_r($row_loan);echo"</pre>";
				if(@$_GET['runscript']=='runscript'){
					$this->db->where("id", $loan_id );
					$this->db->delete("coop_loan");
				}
			}
		}
		echo '=========deduct_receipt=========<br>';
		if($deduct_receipt_id!=''){
			echo $deduct_receipt_id."<br>";
			if(@$_GET['runscript']=='runscript'){
				$this->db->where("receipt_id", $deduct_receipt_id );
				$this->db->delete("coop_receipt");
				
				$this->db->where("receipt_id", $deduct_receipt_id );
				$this->db->delete("coop_receipt_detail");
				
				$this->db->where("receipt_id", $deduct_receipt_id );
				$this->db->delete("coop_finance_transaction");
				
				$this->db->where("share_bill", $deduct_receipt_id );
				$this->db->delete("coop_mem_share");
			}
		}
		echo '=========coop_loan_cost=========<br>';
		$this->db->select(array('loan_id'));
		$this->db->from('coop_loan_cost');
		$this->db->where("loan_id = '".$loan_id."'");
		$rs_cost = $this->db->get()->result_array();
		//echo"<pre>";print_r($rs_member);echo"</pre>"; //exit;
		foreach($rs_cost as $key_cost => $row_cost){			
			if($row_cost['loan_id'] != ''){
				echo"<pre>";print_r($row_cost);echo"</pre>";
				if(@$_GET['runscript']=='runscript'){
					$this->db->where("loan_id", $loan_id );
					$this->db->delete("coop_loan_cost");
				}
			}
		}
		
		echo '=========coop_loan_deduct=========<br>';
		$this->db->select(array('loan_id'));
		$this->db->from('coop_loan_deduct');
		$this->db->where("loan_id = '".$loan_id."'");
		$rs_deduct = $this->db->get()->result_array();
		//echo"<pre>";print_r($rs_member);echo"</pre>"; //exit;
		foreach($rs_deduct as $key_deduct => $row_deduct){			
			if($row_deduct['loan_id'] != ''){
				echo"<pre>";print_r($row_deduct);echo"</pre>";
				if(@$_GET['runscript']=='runscript'){
					$this->db->where("loan_id", $loan_id );
					$this->db->delete("coop_loan_deduct");
				}
			}
		}
		
		echo '=========coop_loan_deduct_profile=========<br>';
		$this->db->select(array('loan_id'));
		$this->db->from('coop_loan_deduct_profile');
		$this->db->where("loan_id = '".$loan_id."'");
		$rs_deduct_profile = $this->db->get()->result_array();
		//echo"<pre>";print_r($rs_member);echo"</pre>"; //exit;
		foreach($rs_deduct_profile as $key_deduct_profile => $row_deduct_profile){			
			if($row_deduct_profile['loan_id'] != ''){
				echo"<pre>";print_r($row_deduct_profile);echo"</pre>";
				if(@$_GET['runscript']=='runscript'){
					$this->db->where("loan_id", $loan_id );
					$this->db->delete("coop_loan_deduct_profile");
				}
			}
		}
		
		echo '=========coop_loan_file_attach=========<br>';
		$this->db->select(array('loan_id'));
		$this->db->from('coop_loan_file_attach');
		$this->db->where("loan_id = '".$loan_id."'");
		$rs_file_attach = $this->db->get()->result_array();
		//echo"<pre>";print_r($rs_member);echo"</pre>"; //exit;
		foreach($rs_file_attach as $key_file_attach => $row_file_attach){			
			if($row_file_attach['loan_id'] != ''){
				echo"<pre>";print_r($row_file_attach);echo"</pre>";
				if(@$_GET['runscript']=='runscript'){
					$this->db->where("loan_id", $loan_id );
					$this->db->delete("coop_loan_file_attach");
				}
			}
		}
		
		echo '=========coop_loan_financial_institutions=========<br>';
		$this->db->select(array('loan_id'));
		$this->db->from('coop_loan_financial_institutions');
		$this->db->where("loan_id = '".$loan_id."'");
		$rs_file_attach = $this->db->get()->result_array();
		//echo"<pre>";print_r($rs_member);echo"</pre>"; //exit;
		foreach($rs_file_attach as $key_file_attach => $row_file_attach){			
			if($row_file_attach['loan_id'] != ''){
				echo"<pre>";print_r($row_file_attach);echo"</pre>";
				if(@$_GET['runscript']=='runscript'){
					$this->db->where("loan_id", $loan_id );
					$this->db->delete("coop_loan_financial_institutions");
				}
			}
		}
		
		echo '=========coop_loan_guarantee=========<br>';
		$this->db->select(array('loan_id'));
		$this->db->from('coop_loan_guarantee');
		$this->db->where("loan_id = '".$loan_id."'");
		$rs_guarantee = $this->db->get()->result_array();
		//echo"<pre>";print_r($rs_member);echo"</pre>"; //exit;
		foreach($rs_guarantee as $key_guarantee => $row_guarantee){			
			if($row_guarantee['loan_id'] != ''){
				echo"<pre>";print_r($row_guarantee);echo"</pre>";
				if(@$_GET['runscript']=='runscript'){
					$this->db->where("loan_id", $loan_id );
					$this->db->delete("coop_loan_guarantee");
				}
			}
		}
		
		echo '=========coop_loan_guarantee_person=========<br>';
		$this->db->select(array('loan_id'));
		$this->db->from('coop_loan_guarantee_person');
		$this->db->where("loan_id = '".$loan_id."'");
		$rs_guarantee_person = $this->db->get()->result_array();
		//echo"<pre>";print_r($rs_member);echo"</pre>"; //exit;
		foreach($rs_guarantee_person as $key_guarantee_person => $row_guarantee_person){			
			if($row_guarantee_person['loan_id'] != ''){
				echo"<pre>";print_r($row_guarantee_person);echo"</pre>";
				if(@$_GET['runscript']=='runscript'){
					$this->db->where("loan_id", $loan_id );
					$this->db->delete("coop_loan_guarantee_person");
				}	
			}
		}
		
		echo '=========coop_loan_guarantee_real_estate=========<br>';
		$this->db->select(array('loan_id'));
		$this->db->from('coop_loan_guarantee_real_estate');
		$this->db->where("loan_id = '".$loan_id."'");
		$rs_guarantee_real_estate = $this->db->get()->result_array();
		//echo"<pre>";print_r($rs_member);echo"</pre>"; //exit;
		foreach($rs_guarantee_real_estate as $key_guarantee_real_estate => $row_guarantee_real_estate){			
			if($row_guarantee_real_estate['loan_id'] != ''){
				echo"<pre>";print_r($row_guarantee_real_estate);echo"</pre>";
				if(@$_GET['runscript']=='runscript'){
					$this->db->where("loan_id", $loan_id );
					$this->db->delete("coop_loan_guarantee_real_estate");
				}
			}
		}
		
		echo '=========coop_loan_period=========<br>';
		$this->db->select(array('loan_id'));
		$this->db->from('coop_loan_period');
		$this->db->where("loan_id = '".$loan_id."'");
		$rs_period = $this->db->get()->result_array();
		//echo"<pre>";print_r($rs_member);echo"</pre>"; //exit;
		foreach($rs_period as $key_period => $row_period){			
			if($row_period['loan_id'] != ''){
				echo"<pre>";print_r($row_period);echo"</pre>";
				if(@$_GET['runscript']=='runscript'){
					$this->db->where("loan_id", $loan_id );
					$this->db->delete("coop_loan_period");
				}	
			}
		}
		
		/*echo '=========coop_loan_prev_deduct=========<br>';
		$this->db->select(array('loan_id'));
		$this->db->from('coop_loan_prev_deduct');
		$this->db->where("loan_id = '".$loan_id."'");
		$rs_period = $this->db->get()->result_array();
		//echo"<pre>";print_r($rs_member);echo"</pre>"; //exit;
		foreach($rs_period as $key_period => $row_period){			
			if($row_period['loan_id'] != ''){
				//ref_id
				echo"<pre>";print_r($row_period);echo"</pre>";
			//$this->db->where("loan_id", $loan_id );
			//$this->db->delete("coop_loan_prev_deduct");
			}
		}
		*/
		
		echo '=========coop_loan_transaction=========<br>';
		$this->db->select(array('loan_id'));
		$this->db->from('coop_loan_transaction');
		$this->db->where("loan_id = '".$loan_id."'");
		$rs_transaction = $this->db->get()->result_array();
		//echo"<pre>";print_r($rs_member);echo"</pre>"; //exit;
		foreach($rs_transaction as $key_transaction => $row_transaction){			
			if($row_transaction['loan_id'] != ''){
				echo"<pre>";print_r($row_transaction);echo"</pre>";
				if(@$_GET['runscript']=='runscript'){
					$this->db->where("loan_id", $loan_id );
					$this->db->delete("coop_loan_transaction");
				}	
			}
		}
		
		echo '=========coop_loan_transfer=========<br>';
		$this->db->select(array('loan_id'));
		$this->db->from('coop_loan_transfer');
		$this->db->where("loan_id = '".$loan_id."'");
		$rs_transfer = $this->db->get()->result_array();
		//echo"<pre>";print_r($rs_member);echo"</pre>"; //exit;
		foreach($rs_transfer as $key_transfer => $row_transfer){			
			if($row_transfer['loan_id'] != ''){
				echo"<pre>";print_r($row_transfer);echo"</pre>";
				if(@$_GET['runscript']=='runscript'){
					$this->db->where("loan_id", $loan_id );
					$this->db->delete("coop_loan_transfer");
				}
			}
		}
		
		/*echo '=========coop_loan_transfer=========<br>';
		$this->db->select(array('loan_id'));
		$this->db->from('coop_loan_transfer');
		$this->db->where("loan_id = '".$loan_id."'");
		$rs_transfer = $this->db->get()->result_array();
		//echo"<pre>";print_r($rs_member);echo"</pre>"; //exit;
		foreach($rs_transfer as $key_transfer => $row_transfer){			
			if($row_transfer['loan_id'] != ''){
				echo"<pre>";print_r($row_transfer);echo"</pre>";
				if(@$_GET['runscript']=='runscript'){
					$this->db->where("loan_id", $loan_id );
					$this->db->delete("coop_loan_transfer");
				}
			}
		}
		
		//มีการหักต่างๆ
		
		/*
		บันทึกการกู้เงิน
		coop_loan
		coop_loan_cost
		coop_loan_deduct
		coop_loan_deduct_profile
		coop_loan_file_attach
		coop_loan_financial_institutions
		coop_loan_guarantee
		coop_loan_guarantee_person
		coop_loan_guarantee_real_estate
		coop_loan_period
		coop_loan_prev_deduct

		coop_loan_transaction ->เข้าตอนโอนเงิน
		coop_loan_transfer

		มีการหักต่างๆ
		coop_receipt
		coop_receipt_detail
		coop_finance_transaction
				*/
		exit;
	}
}
