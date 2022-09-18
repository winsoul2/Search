<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Certificate extends CI_Controller {
	function __construct() {
		parent::__construct();
	}
	public function cert_share_loan() {
		$arr_data = array();

		if($_GET['member_id']!=''){
			$member_id = $_GET['member_id'];
		}else{
			$member_id = '';
		}
		$arr_data['member_id'] = $member_id;

		$member_name = '';
		if($member_id != '') {
			$join_arr = array();
			$x=0;
			$join_arr[$x]['table'] = 'coop_prename';
			$join_arr[$x]['condition'] = 'coop_prename.prename_id = coop_mem_apply.prename_id';
			$join_arr[$x]['type'] = 'left';
			$x++;
			$join_arr[$x]['table'] = 'coop_mem_group';
			$join_arr[$x]['condition'] = 'coop_mem_group.id = coop_mem_apply.level';
			$join_arr[$x]['type'] = 'left';

			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select('coop_mem_apply.member_id,
											coop_mem_apply.firstname_th,
											coop_mem_apply.lastname_th,
											coop_prename.prename_full,
											coop_mem_group.mem_group_name');
			$this->paginater_all->main_table('coop_mem_apply');
			$this->paginater_all->where("member_id = '".$member_id."'");
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(20);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->join_arr($join_arr);
			$this->paginater_all->order_by('coop_mem_apply.member_id');
			$row = $this->paginater_all->paginater_process();

			$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'],$_GET);

			$member_name = $row['data'][0]["prename_full"].$row['data'][0]["firstname_th"]." ".$row['data'][0]["lastname_th"];

		}
		$arr_data['member_name'] = $member_name;

		$datas = array();
		if ($_GET["do_search"] == "Y") {

			if(@$_GET['date']){
				$date_arr = explode('/',@$_GET['date']);
				$day = $date_arr[0];
				$month = $date_arr[1];
				$year = $date_arr[2];
				$year -= 543;
				$date = $year.'-'.$month.'-'.$day;
			}

			$member_where = "1=1 ";

			if($_GET["search_type"] == "0") {
				if ($_GET['member_search']) {
					$member_where .= " member_id LIKE '%".$_GET['member_search']."%'";
				}
			} else if ($_GET["search_type"] == "1") {
				if(!empty($_GET['department'])){
					$member_where .= " AND department = '".$_GET['department']."'";
				}
				if(!empty($_GET['faction'])){
					$member_where .= " AND faction = '".$_GET['faction']."'";
				}
				if(!empty($_GET['level'])){
					$member_where .= " AND level = '".$_GET['level']."'";
				}
			}

			$join_arr = array();
			$x=0;
			$join_arr[$x]['table'] = 'coop_prename';
			$join_arr[$x]['condition'] = 'coop_prename.prename_id = coop_mem_apply.prename_id';
			$join_arr[$x]['type'] = 'left';
			$x++;
			$join_arr[$x]['table'] = 'coop_mem_group';
			$join_arr[$x]['condition'] = 'coop_mem_group.id = coop_mem_apply.level';
			$join_arr[$x]['type'] = 'left';

			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select('coop_mem_apply.member_id,
											coop_mem_apply.firstname_th,
											coop_mem_apply.lastname_th,
											coop_prename.prename_full,
											coop_mem_group.mem_group_name');
			$this->paginater_all->main_table('coop_mem_apply');
			$this->paginater_all->where($member_where);
			$this->paginater_all->page_now($_GET["page"]);
			$this->paginater_all->per_page(20);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->join_arr($join_arr);
			$this->paginater_all->order_by('coop_mem_apply.level, coop_mem_apply.member_id');
			$row = $this->paginater_all->paginater_process();

			$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'],$_GET);
		}

		$date = date('Y-m-d');
		if ($_GET['date']) {
			$date_arr = explode("/", $_GET['date']);
			$date = ($date_arr[2] - 543)."-".$date_arr[1]."-".$date_arr[0];
		}
		$arr_data["date"] = $date;

		$arr_data["datas"] = $row["data"];
		$arr_data["paging"] = $paging;

        $this->db->select(array('id','mem_group_name'));
		$this->db->from('coop_mem_group');
		$this->db->where("mem_group_type = '1'");
		$row = $this->db->get()->result_array();
		$arr_data['row_mem_group'] = $row;

		$this->libraries->template('certificate/cert_share_loan',$arr_data);
    }
    
    public function cert_share_loan_pdf() {
		$arr_data = array();

		$whereMember = "1=1 ";
		if(!empty($_GET["member_id"])) {
			$whereMember .= " AND member_id = '".$_GET["member_id"]."'";
		}
		if(!empty($_GET['level'])){
			$whereMember .= " AND level = '".$_GET['level']."'";
		}else if(!empty($_GET['faction'])){
			$whereMember .= " AND faction = '".$_GET['faction']."'";
		}else if(!empty($_GET['department'])){
			$whereMember .= " AND department = '".$_GET['department']."'";
		}

		if($_GET['date']){
			$date_arr = explode('/',@$_GET['date']);
			$day = $date_arr[0];
			$month = $date_arr[1];
			$year = $date_arr[2];
			$year -= 543;
			$date = $year.'-'.$month.'-'.$day;
		}

		//Get last month
		$filter_month = 1;
		$filter_year = 2561;
		if ($month > 1) {
			$filter_month = $month - 1;
			$filter_year = $year + 543;
		} else {
			$filter_month = $month;
			$filter_year = $year + 543 - 1;
		}

		$rs_type_code = $this->db->select(array('coop_loan_name.loan_name_id','coop_loan_type.loan_type_code'))
									->from('coop_loan_name')
									->join('coop_loan_type','coop_loan_name.loan_type_id = coop_loan_type.id','left')
									->get()->result_array();
		$arr_loan_type_code = array();
		foreach($rs_type_code AS $key_type_code=>$row_type_code){
			$arr_loan_type_code[$row_type_code['loan_name_id']] = $row_type_code['loan_type_code'];
		}

		$sql = "SELECT `coop_mem_apply`.`member_id`, `coop_mem_apply`.`prename_id`, `coop_mem_apply`.`firstname_th`, `coop_mem_apply`.`lastname_th`,
				`coop_mem_apply`.`department`, `coop_mem_apply`.`faction`, `coop_mem_apply`.`level`, coop_mem_apply.share_month,
				`coop_prename`.`prename_full`,
				`t1`.`mem_group_id` as `id`, `t1`.`mem_group_name` as `name`,
				`t2`.`mem_group_name` as `sub_name`,
				`t3`.`mem_group_name` as `main_name`,
				`t4`.`share_collect`, `t4`.`share_collect_value`, `t4`.`share_id`, `t4`.`share_period`, `t4`.`share_date`,
				`t5`.`loan_id`, `t5`.`loan_amount_balance`, `t5`.`contract_number`, `t5`.`loan_type`,
				`t6`.`loan_atm_id`, `t6`.`contract_number` AS `contract_number_atm`, `t6`.`loan_amount_balance_atm`,
				t7.account_id
				FROM ((SELECT member_id, `prename_id`, `firstname_th`, `lastname_th`, `department`, `faction`, level, share_month FROM coop_mem_apply WHERE ".$whereMember.") AS coop_mem_apply)
				LEFT JOIN `coop_prename` ON `coop_prename`.`prename_id` = `coop_mem_apply`.`prename_id`
				LEFT JOIN `coop_mem_group` as `t1` ON `t1`.`id` = `coop_mem_apply`.`level`
				LEFT JOIN `coop_mem_group` as `t2` ON `t2`.`id` = `t1`.`mem_group_parent_id`
				LEFT JOIN `coop_mem_group` as `t3` ON `t3`.`id` = `t2`.`mem_group_parent_id`
				LEFT JOIN (SELECT t1.share_id, t1.share_collect, t1.share_collect_value, t1.member_id, t1.share_period, t1.share_date
							FROM coop_mem_share as t1
							WHERE t1.share_date <= '".$date." 23:59:59.000' GROUP BY member_id) AS t4 ON `coop_mem_apply`.`member_id` = `t4`.`member_id`
				LEFT JOIN (SELECT t3.member_id ,t3.contract_number ,t3.period_now ,t3.loan_type 
								,t1.loan_transaction_id
								,t1.loan_id
								,t1.loan_amount_balance
								,t1.transaction_datetime FROM coop_loan_transaction AS t1 LEFT JOIN coop_loan AS t3 ON t1.loan_id = t3.id WHERE t1.transaction_datetime <= '".$date." 23:59:59.000' GROUP BY t1.loan_id ORDER BY t1.loan_id DESC ,t1.loan_transaction_id DESC )
								AS t5 ON `coop_mem_apply`.`member_id` = `t5`.`member_id`
				LEFT JOIN (SELECT t3.member_id ,t3.contract_number
								,t1.loan_atm_transaction_id
								,t1.loan_atm_id
								,t1.loan_amount_balance as loan_amount_balance_atm
								FROM coop_loan_atm_transaction AS t1 LEFT JOIN coop_loan_atm AS t3 ON t1.loan_atm_id = t3.loan_atm_id WHERE t1.transaction_datetime <= '".$date." 23:59:59.000'
								GROUP BY t1.loan_atm_id ORDER BY t1.loan_atm_id DESC ,t1.loan_atm_transaction_id DESC ) AS t6 ON `coop_mem_apply`.`member_id` = `t6`.`member_id`
				LEFT JOIN (SELECT account_id, mem_id FROM coop_maco_account) AS t7 ON t7.mem_id = `coop_mem_apply`.`member_id`
								WHERE (`t5`.`loan_id` != '' OR `t4`.`share_id` != '' OR `t6`.`loan_atm_id` != '' OR t7.account_id != '')";

		$result = $this->db->query($sql)->result_array();

		$member_ids = array_column($result, 'member_id');

		//Get Lastest share
		$share_members == array();
		if (empty($_GET["type"]) || $_GET["type"] == 'share') {
			$shares = $this->db->query("SELECT `t1`.`member_id`, `t1`.`share_date`, `t1`.`share_period`, `t1`.`share_collect_value`, `t1`.`share_collect`
																	FROM `coop_mem_share` as `t1`
																	INNER JOIN (SELECT member_id, share_date, MAX(cast(share_date as Datetime)) as max FROM coop_mem_share
																		WHERE share_date <= '".$date." 23:59:59.000'
																		group by member_id)
																			as t2 ON `t1`.`member_id` = `t2`.`member_id` AND `t1`.`share_date` = `t2`.`max`
																		WHERE `t1`.`share_collect_value` > 0 AND t1.member_id IN (".implode(',',$member_ids).")
																		ORDER BY `t1`.`share_date` DESC, `t1`.`share_id` DESC
																	")->result_array();

			$share_members = array_column($shares, 'member_id');
		}


		//Get Lastest Loan Information
		$loan_members = array();
		$loan_atm_members = array();
		if (empty($_GET["type"]) || $_GET["type"] == 'loan') {
			$loan_ids = array_column($result, 'loan_id');
			$implode_loan = implode(',',array_filter($loan_ids));
			if (!empty($implode_loan)) {
				$loans = $this->db->query("SELECT `t1`.`loan_transaction_id`, `t1`.`loan_id`, `t1`.`loan_amount_balance`, `t1`.`transaction_datetime`
											FROM `coop_loan_transaction` as `t1`
											INNER JOIN (SELECT loan_id, MAX(cast(transaction_datetime as Datetime)) as max FROM coop_loan_transaction WHERE transaction_datetime <= '".$date." 23:59:59.000' group by loan_id)
													as t2 ON `t1`.`loan_id` = `t2`.`loan_id` AND `t1`.`transaction_datetime` = `t2`.`max`
											WHERE `t1`.`loan_amount_balance` > 0 AND t1.loan_id IN  (".implode(',',array_filter($loan_ids)).")
											ORDER BY `t1`.`transaction_datetime`, `t1`.`loan_transaction_id` DESC
											")->result_array();
				$loan_members = array_column($loans, 'loan_id');

				$loan_trans_principal = $this->db->query("SELECT t1.loan_id, t1.pay_amount as principal
													FROM (SELECT * FROM coop_finance_month_detail WHERE loan_id IN (".implode(',',array_filter($loan_ids)).") AND deduct_code = 'LOAN' AND pay_type = 'principal') AS t1
													INNER JOIN (SELECT * FROM coop_finance_month_profile WHERE profile_month = '".$filter_month."' AND profile_year = '".$filter_year."') AS t3 ON t1.profile_id = t3.profile_id
												")->result_array();
				$loan_tran_principal_members = array_column($loan_trans_principal, 'loan_id');

				$loan_trans_interest = $this->db->query("SELECT t2.loan_id, t2.pay_amount as interest
													FROM (SELECT * FROM coop_finance_month_detail WHERE loan_id IN (".implode(',',array_filter($loan_ids)).") AND deduct_code = 'LOAN' AND pay_type = 'interest') AS t2
													INNER JOIN (SELECT * FROM coop_finance_month_profile WHERE profile_month = '".$filter_month."' AND profile_year = '".$filter_year."') AS t3 ON t2.profile_id = t3.profile_id
												")->result_array();
				$loan_tran_interest_members = array_column($loan_trans_interest, 'loan_id');
			}

			//Get Lastest Loan ATM Information
			$loan_atm_ids = array_column($result, 'loan_atm_id');
			$implode_atm = implode(',',array_filter($loan_atm_ids));
			if (!empty($implode_atm)) {
				$loan_atms = $this->db->query("SELECT t1.loan_atm_transaction_id, `t1`.`loan_atm_id`, `t1`.`loan_amount_balance`, `t1`.`transaction_datetime`
											FROM `coop_loan_atm_transaction` as `t1`
											INNER JOIN (SELECT loan_atm_id, MAX(cast(transaction_datetime as Datetime)) as max FROM coop_loan_atm_transaction WHERE transaction_datetime <= '".$date." 23:59:59.000' group by loan_atm_id)
													as t2 ON `t1`.`loan_atm_id` = `t2`.`loan_atm_id` AND `t1`.`transaction_datetime` = `t2`.`max`
													WHERE `t1`.`loan_amount_balance` > 0 AND t1.loan_atm_id IN  (".implode(',',array_filter($loan_atm_ids)).")
											ORDER BY `t1`.`transaction_datetime`, `t1`.`loan_atm_transaction_id` DESC
											")->result_array();
				$loan_atm_members = array_column($loan_atms, 'loan_atm_id');

				$loan_atm_trans_principal = $this->db->query("SELECT t1.loan_atm_id, t1.pay_amount as principal
																FROM (SELECT * FROM coop_finance_month_detail WHERE loan_atm_id IN (".implode(',',array_filter($loan_atm_ids)).") AND deduct_code = 'ATM' AND pay_type = 'principal') AS t1
																INNER JOIN (SELECT * FROM coop_finance_month_profile WHERE profile_month = '".$filter_month."' AND profile_year = '".$filter_year."') AS t3 ON t1.profile_id = t3.profile_id
															")->result_array();
				$loan_atm_tran_principal_members = array_column($loan_atm_trans_principal, 'loan_id');

				$loan_atm_trans_interest = $this->db->query("SELECT t2.loan_atm_id, t2.pay_amount as interest
															FROM (SELECT * FROM coop_finance_month_detail WHERE loan_atm_id IN (".implode(',',array_filter($loan_atm_ids)).") AND deduct_code = 'ATM' AND pay_type = 'interest') AS t2
															INNER JOIN (SELECT * FROM coop_finance_month_profile WHERE profile_month = '".$filter_month."' AND profile_year = '".$filter_year."') AS t3 ON t2.profile_id = t3.profile_id
														")->result_array();
				$loan_atm_tran_interest_members = array_column($loan_atm_trans_interest, 'loan_id');
			}
		}

		//Get Bank Account Info
		$account_members = array();
		if (empty($_GET["type"]) || $_GET["type"] == 'deposit') {
			$account_ids = array_column($result, 'account_id');
			$implode_acc = implode(',',array_filter($account_ids));
			if (!empty($implode_acc)) {
				$accounts = $this->db->query("SELECT t2.transaction_id, `t2`.`account_id`, `t2`.`transaction_balance`, `t2`.`transaction_time`, t4.type_name
												FROM (SELECT account_id, transaction_id, transaction_balance, transaction_time
														FROM coop_account_transaction
														WHERE transaction_time <= '".$date." 23:59:59.000' AND account_id IN  (".implode(',',array_filter($account_ids))."))
														as t2
												LEFT JOIN coop_maco_account as t3 ON t2.account_id = t3.account_id
												LEFT JOIN coop_deposit_type_setting as t4 ON t4.type_id = t3.type_id
												ORDER BY `t2`.`transaction_time` DESC, `t2`.`transaction_id` DESC 
											")->result_array();
							$account_members = array_column($accounts, 'account_id');
			}
		}

		$datas = array();
		$total_data = array();

		foreach($result as $index => $row) {
			$datas[$row["member_id"]]["prename_full"] = $row["prename_full"];
			$datas[$row["member_id"]]["firstname_th"] = $row["firstname_th"];
			$datas[$row["member_id"]]["lastname_th"] = $row["lastname_th"];
			$datas[$row["member_id"]]["member_id"] = $row["member_id"];
			$datas[$row["member_id"]]["name"] = $row["name"];
			$datas[$row["member_id"]]["share_month"] = $row["share_month"];
			if (empty($datas[$row["member_id"]]["share_collect_value"]) && !empty($share_members)) {
				if(in_array($row['member_id'],$share_members)) $datas[$row["member_id"]]["share_collect_value"] = $shares[array_search($row['member_id'],$share_members)]['share_collect_value'];
			}
			if (!empty($row['loan_id']) && empty($datas[$row["member_id"]]["loan"][$row['loan_id']])) {
				if(in_array($row['loan_id'],$loan_members)) {
					$datas[$row["member_id"]]["loan"][$row['loan_id']]['type'] = $arr_loan_type_code[$row['loan_type']];
					$datas[$row["member_id"]]["loan"][$row['loan_id']]["contract_number"] = $row['contract_number'];
					$datas[$row["member_id"]]["loan"][$row['loan_id']]["principal"] = $loan_trans_principal[array_search($row['loan_id'],$loan_tran_principal_members)]['principal'];
					$datas[$row["member_id"]]["loan"][$row['loan_id']]["interest"] = $loan_trans_interest[array_search($row['loan_id'],$loan_tran_interest_members)]['interest'];
					$datas[$row["member_id"]]["loan"][$row['loan_id']]["total"] = $loans[array_search($row['loan_id'],$loan_members)]['loan_amount_balance'];
					$total_data[$row["member_id"]]["loan"] += $datas[$row["member_id"]]["loan"][$row['loan_id']]["total"];
				}
			}
			if (!empty($row['loan_atm_id']) && empty($datas[$row["member_id"]]["loan"][$row['loan_atm_id']])) {
				if(in_array($row['loan_atm_id'],$loan_atm_members)) {
					$datas[$row["member_id"]]["loan"][$row['loan_atm_id']]['type'] = "atm";
					$datas[$row["member_id"]]["loan"][$row['loan_atm_id']]["contract_number"] = $row['contract_number_atm'];
					$datas[$row["member_id"]]["loan"][$row['loan_atm_id']]["principal"] = $loan_atm_trans_principal[array_search($row['loan_atm_id'],$loan_atm_trans_principal)]['principal'];
					$datas[$row["member_id"]]["loan"][$row['loan_atm_id']]["interest"] = $loan_atm_trans_interest[array_search($row['loan_atm_id'],$loan_atm_tran_interest_members)]['interest'];
					$datas[$row["member_id"]]["loan"][$row['loan_atm_id']]["total"] = $loan_atms[array_search($row['loan_atm_id'],$loan_atm_members)]['loan_amount_balance'];
					$total_data[$row["member_id"]]["loan"] += $datas[$row["member_id"]]["loan"][$row['loan_atm_id']]["total"];
				}
			}
			if (!empty($row['account_id']) && empty($datas[$row["member_id"]]["account"][$row['account_id']])) {
				if(in_array($row['account_id'],$account_members) && $accounts[array_search($row['account_id'],$account_members)]['transaction_balance'] > 0) {
					$datas[$row["member_id"]]["account"][$row['account_id']]["type_name"] = $accounts[array_search($row['account_id'],$account_members)]['type_name'];
					$datas[$row["member_id"]]["account"][$row['account_id']]["balance"] = $accounts[array_search($row['account_id'],$account_members)]['transaction_balance'];
					$datas[$row["member_id"]]["account"][$row['account_id']]["account_id"] = $accounts[array_search($row['account_id'],$account_members)]['account_id'];
					$total_data[$row["member_id"]]["account"] += $datas[$row["member_id"]]["account"][$row['account_id']]["balance"];
				}
			}
		}

		$row_profile = $this->db->select(array(
					'coop_name_th',
					'address1',
					'address2',
					'coop_img'
				))
				->from('coop_profile')->limit(1)
		        ->get()->result_array();
		$data_arr['row_profile'] = $row_profile[0];

		$data_arr['member_ids'] = $member_ids;
		$data_arr['member_infos'] = $result;
		$data_arr['datas'] = $datas;
		$data_arr['date'] = $date;

		$this->load->view('certificate/cert_share_loan_pdf',$data_arr);
	}

	public function cert_confirm_share_loan() {
		$arr_data = array();

		if($_GET['member_id']!=''){
			$member_id = $_GET['member_id'];
		}else{
			$member_id = '';
		}
		$arr_data['member_id'] = $member_id;

        $_GET['manager_name'] = str_replace('\\', '', $_GET['manager_name']);
        $_GET['manager_position'] = str_replace('\\', '', $_GET['manager_position']);
        $_GET['date'] = str_replace('\\', '', $_GET['date']);

		$member_name = '';
		if($member_id != '') {
			$join_arr = array();
			$x=0;
			$join_arr[$x]['table'] = 'coop_prename';
			$join_arr[$x]['condition'] = 'coop_prename.prename_id = coop_mem_apply.prename_id';
			$join_arr[$x]['type'] = 'left';
			$x++;
			$join_arr[$x]['table'] = 'coop_mem_group';
			$join_arr[$x]['condition'] = 'coop_mem_group.id = coop_mem_apply.level';
			$join_arr[$x]['type'] = 'left';

			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select('coop_mem_apply.member_id,
											coop_mem_apply.firstname_th,
											coop_mem_apply.lastname_th,
											coop_prename.prename_full,
											coop_mem_group.mem_group_name');
			$this->paginater_all->main_table('coop_mem_apply');
			$this->paginater_all->where("member_id = '".$member_id."'");
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(20);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->join_arr($join_arr);
			$this->paginater_all->order_by('coop_mem_apply.member_id');
			$row = $this->paginater_all->paginater_process();

			$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'],$_GET);

			$member_name = $row['data'][0]["prename_full"].$row['data'][0]["firstname_th"]." ".$row['data'][0]["lastname_th"];

		}
		$arr_data['member_name'] = $member_name;

		$datas = array();
		if ($_GET["do_search"] == "Y") {

			if(@$_GET['date']){
				$date_arr = explode('/',@$_GET['date']);
				$day = $date_arr[0];
				$month = $date_arr[1];
				$year = $date_arr[2];
				$year -= 543;
				$date = $year.'-'.$month.'-'.$day;
			}

			$member_where = "1=1 ";

			if($_GET["search_type"] == "0") {
				if ($_GET['member_search']) {
					$member_where .= " member_id LIKE '%".$_GET['member_search']."%'";
				}
			} else if ($_GET["search_type"] == "1") {
				if(!empty($_GET['department'])){
					$member_where .= " AND department = '".$_GET['department']."'";
				}
				if(!empty($_GET['faction'])){
					$member_where .= " AND faction = '".$_GET['faction']."'";
				}
				if(!empty($_GET['level'])){
					$member_where .= " AND level = '".$_GET['level']."'";
				}
			} else if ($_GET["search_type"] == "2") {
				if(!empty($_GET['mem_group_id'])){
					$member_where .= " AND coop_mem_group.mem_group_id = '".$_GET['mem_group_id']."'";
				}
			}

			$join_arr = array();
			$x=0;
			$join_arr[$x]['table'] = 'coop_prename';
			$join_arr[$x]['condition'] = 'coop_prename.prename_id = coop_mem_apply.prename_id';
			$join_arr[$x]['type'] = 'left';
			$x++;
			$join_arr[$x]['table'] = 'coop_mem_group';
			$join_arr[$x]['condition'] = 'coop_mem_group.id = coop_mem_apply.level';
			$join_arr[$x]['type'] = 'left';

			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select('coop_mem_apply.member_id,
											coop_mem_apply.firstname_th,
											coop_mem_apply.lastname_th,
											coop_prename.prename_full,
											coop_mem_group.mem_group_name');
			$this->paginater_all->main_table('coop_mem_apply');
			$this->paginater_all->where($member_where);
			$this->paginater_all->page_now($_GET["page"]);
			$this->paginater_all->per_page(20);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->join_arr($join_arr);
			$this->paginater_all->order_by('CAST(coop_mem_group.mem_group_id AS int) ASC, coop_mem_apply.member_id');
			$row = $this->paginater_all->paginater_process();

			$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'],$_GET);
		}

		$date = date('Y-m-d');
		if ($_GET['date']) {
			$date_arr = explode("/", $_GET['date']);
			$date = ($date_arr[2] - 543)."-".$date_arr[1]."-".$date_arr[0];
		}
		$arr_data["datas"] = $row["data"];
		$arr_data["date"] = $date;
		$arr_data["paging"] = $paging;

        $this->db->select(array('id','mem_group_name'));
		$this->db->from('coop_mem_group');
		$this->db->where("mem_group_type = '1'");
		$row = $this->db->get()->result_array();
		$arr_data['row_mem_group'] = $row;

		$date_signature = date('Y-m-d');
		$signature = $this->db->select(array('*'))
							->from('coop_signature')
							->where("start_date <= '{$date_signature}'")
							->order_by('start_date DESC')
							->get()->row();
		$arr_data['signature'] = $signature;

		$this->db->select(array('id','mem_group_name', 'mem_group_id'));
		$this->db->from('coop_mem_group');
		$this->db->where("mem_group_type = '3'");
		$this->db->order_by("CAST(coop_mem_group.mem_group_id AS int) ASC");
		$this->db->group_by("mem_group_id");
		$row = $this->db->get()->result_array();
		$arr_data['row_mem_group_id'] = $row;

		$this->libraries->template('certificate/cert_confirm_share_loan',$arr_data);
    }
    
    public function cert_confirm_share_loan_pdf() {
		set_time_limit ( 300 );
		ini_set('memory_limit', -1);
		$arr_data = array();

		$where = '1=1';
		$whereMember = "1=1 AND mem_type NOT IN ('2', '3') AND member_status NOT IN ('2', '3', '4')";
		if(!empty($_GET["member_id"])) {
			$whereMember .= " AND member_id = '".$_GET["member_id"]."'";
		}
		if(!empty($_GET['level'])){
			$whereMember .= " AND level = '".$_GET['level']."'";
		}else if(!empty($_GET['faction'])){
			$whereMember .= " AND faction = '".$_GET['faction']."'";
		}else if(!empty($_GET['department'])){
			$whereMember .= " AND department = '".$_GET['department']."'";
		}
		if(!empty($_GET['mem_group_id'])){
			$where .= " AND t1.mem_group_id = '".$_GET['mem_group_id']."'";
		}

		if($_GET['date']){
			$date_arr = explode('/',@$_GET['date']);
			$day = $date_arr[0];
			$month = $date_arr[1];
			$year = $date_arr[2];
			$year -= 543;
			$date = $year.'-'.$month.'-'.$day;
		}

		//Get last month
		$filter_month = 1;
		$filter_year = 2561;
		if ($month > 1) {
			$filter_month = $month - 1;
			$filter_year = $year + 543;
		} else {
			$filter_month = $month;
			$filter_year = $year + 543 - 1;
		}

		$rs_type_code = $this->db->select(array('coop_loan_name.loan_name_id','coop_loan_type.loan_type_code','coop_term_of_loan.prefix_code'))
									->from('coop_loan_name')
									->join('coop_loan_type','coop_loan_name.loan_type_id = coop_loan_type.id','left')
									->join('coop_term_of_loan','coop_term_of_loan.type_id = coop_loan_name.loan_name_id','left')
									->get()->result_array();
		$arr_loan_type_code = array();
		$arr_loan_prefix_code = array();
		foreach($rs_type_code AS $key_type_code=>$row_type_code){
			$arr_loan_type_code[$row_type_code['loan_name_id']] = $row_type_code['loan_type_code'];
            $arr_loan_prefix_code[$row_type_code['loan_name_id']] = $row_type_code['prefix_code'];
		}

		$sql = "SELECT `coop_mem_apply`.`member_id`, `coop_mem_apply`.`prename_id`, `coop_mem_apply`.`firstname_th`, `coop_mem_apply`.`lastname_th`,
				`coop_mem_apply`.`department`, `coop_mem_apply`.`faction`, `coop_mem_apply`.`level`, coop_mem_apply.share_month, coop_mem_apply.address_send_doc,
				`coop_prename`.`prename_full`,
				`t1`.`mem_group_id` as `id`, `t1`.`mem_group_name` as `name`,
				`t2`.`mem_group_name` as `sub_name`,
				`t3`.`mem_group_name` as `main_name`,
				`t4`.`share_collect`, `t4`.`share_collect_value`, `t4`.`share_id`, `t4`.`share_period`, `t4`.`share_date`,
				`t5`.`loan_id`, `t5`.`loan_amount_balance`, `t5`.`contract_number`, `t5`.`loan_type`,
				`t6`.`loan_atm_id`, `t6`.`contract_number` AS `contract_number_atm`, `t6`.`loan_amount_balance_atm`,
				t7.account_id
				FROM ((SELECT member_id, `prename_id`, `firstname_th`, `lastname_th`, `department`, `faction`, level, share_month, address_send_doc FROM coop_mem_apply WHERE ".$whereMember.") AS coop_mem_apply)
				LEFT JOIN `coop_prename` ON `coop_prename`.`prename_id` = `coop_mem_apply`.`prename_id`
				LEFT JOIN `coop_mem_group` as `t1` ON `t1`.`id` = `coop_mem_apply`.`level`
				LEFT JOIN `coop_mem_group` as `t2` ON `t2`.`id` = `t1`.`mem_group_parent_id`
				LEFT JOIN `coop_mem_group` as `t3` ON `t3`.`id` = `t2`.`mem_group_parent_id`
				LEFT JOIN (SELECT t1.share_id, t1.share_collect, t1.share_collect_value, t1.member_id, t1.share_period, t1.share_date
							FROM coop_mem_share as t1
							WHERE t1.share_date <= '".$date." 23:59:59.000' GROUP BY member_id) AS t4 ON `coop_mem_apply`.`member_id` = `t4`.`member_id`
				LEFT JOIN (SELECT t3.member_id ,t3.contract_number ,t3.period_now ,t3.loan_type 
								,t1.loan_transaction_id
								,t1.loan_id
								,t1.loan_amount_balance
								,t1.transaction_datetime FROM coop_loan_transaction AS t1 LEFT JOIN coop_loan AS t3 ON t1.loan_id = t3.id WHERE t1.transaction_datetime <= '".$date." 23:59:59.000' GROUP BY t1.loan_id ORDER BY t1.loan_id DESC ,t1.loan_transaction_id DESC )
								AS t5 ON `coop_mem_apply`.`member_id` = `t5`.`member_id`
				LEFT JOIN (SELECT t3.member_id ,t3.contract_number
								,t1.loan_atm_transaction_id
								,t1.loan_atm_id
								,t1.loan_amount_balance as loan_amount_balance_atm
								FROM coop_loan_atm_transaction AS t1 LEFT JOIN coop_loan_atm AS t3 ON t1.loan_atm_id = t3.loan_atm_id WHERE t1.transaction_datetime <= '".$date." 23:59:59.000'
								GROUP BY t1.loan_atm_id ORDER BY t1.loan_atm_id DESC ,t1.loan_atm_transaction_id DESC ) AS t6 ON `coop_mem_apply`.`member_id` = `t6`.`member_id`
				LEFT JOIN (SELECT account_id, mem_id FROM coop_maco_account) AS t7 ON t7.mem_id = `coop_mem_apply`.`member_id`
								WHERE (`t5`.`loan_id` != '' OR `t4`.`share_id` != '' OR `t6`.`loan_atm_id` != '' OR t7.account_id != '') AND ".$where."
				ORDER BY CAST(t1.mem_group_id AS int) ASC, member_id";

		$result = $this->db->query($sql)->result_array();

		$member_ids = array_column($result, 'member_id');
        $member_ids = array_unique($member_ids); // ตัดข้อมูลซ้ำใน Array

		//Get Lastest share
		$shares = $this->db->query("SELECT `t1`.`member_id`, `t1`.`share_date`, `t1`.`share_period`, `t1`.`share_collect_value`, `t1`.`share_collect`
									FROM `coop_mem_share` as `t1`
									INNER JOIN (SELECT member_id, share_date, MAX(cast(share_date as Datetime)) as max FROM coop_mem_share
										WHERE share_date <= '".$date." 23:59:59.000' AND share_status IN ('1', '2') AND share_date != '0000-00-00 00:00:00'
										group by member_id)
											as t2 ON `t1`.`member_id` = `t2`.`member_id` AND `t1`.`share_date` = `t2`.`max`
										WHERE t1.member_id IN ('".implode("','",$member_ids)."')
										ORDER BY `t1`.`share_date` DESC, `t1`.`share_id` DESC
									")->result_array();

		$share_members = array_column($shares, 'member_id');

		//Get Lastest Loan Information
		$loan_ids = array_column($result, 'loan_id');
		$implode_loan = implode(',',array_filter($loan_ids));
		if (!empty($implode_loan)) {
		    // เอา `t1`.`loan_amount_balance` > 0 AND ออก
			$loans = $this->db->query("SELECT `t1`.`loan_transaction_id`, `t1`.`loan_id`, `t1`.`loan_amount_balance`, `t1`.`transaction_datetime`
										FROM `coop_loan_transaction` as `t1`
										INNER JOIN (SELECT loan_id, MAX(cast(transaction_datetime as Datetime)) as max FROM coop_loan_transaction WHERE transaction_datetime <= '".$date." 23:59:59.000' group by loan_id)
												as t2 ON `t1`.`loan_id` = `t2`.`loan_id` AND `t1`.`transaction_datetime` = `t2`.`max`
										WHERE t1.loan_id IN  (".implode(',',array_filter($loan_ids)).")
										ORDER BY `t1`.`transaction_datetime`, `t1`.`loan_transaction_id` DESC
										")->result_array();
			$loan_members = array_column($loans, 'loan_id');

			$loan_trans_principal = $this->db->query("SELECT t1.loan_id, t1.pay_amount as principal
												FROM (SELECT * FROM coop_finance_month_detail WHERE loan_id IN (".implode(',',array_filter($loan_ids)).") AND deduct_code = 'LOAN' AND pay_type = 'principal') AS t1
												INNER JOIN (SELECT * FROM coop_finance_month_profile WHERE profile_month = '".$filter_month."' AND profile_year = '".$filter_year."') AS t3 ON t1.profile_id = t3.profile_id
											")->result_array();
			$loan_tran_principal_members = array_column($loan_trans_principal, 'loan_id');

			$loan_trans_interest = $this->db->query("SELECT t2.loan_id, t2.pay_amount as interest
												FROM (SELECT * FROM coop_finance_month_detail WHERE loan_id IN (".implode(',',array_filter($loan_ids)).") AND deduct_code = 'LOAN' AND pay_type = 'interest') AS t2
												INNER JOIN (SELECT * FROM coop_finance_month_profile WHERE profile_month = '".$filter_month."' AND profile_year = '".$filter_year."') AS t3 ON t2.profile_id = t3.profile_id
											")->result_array();
			$loan_tran_interest_members = array_column($loan_trans_interest, 'loan_id');
		}

		//Get Lastest Loan ATM Information
		$loan_atm_ids = array_column($result, 'loan_atm_id');
		$implode_atm = implode(',',array_filter($loan_atm_ids));
		if (!empty($implode_atm)) {
			$loan_atms = $this->db->query("SELECT t1.loan_atm_transaction_id, `t1`.`loan_atm_id`, `t1`.`loan_amount_balance`, `t1`.`transaction_datetime`
										FROM `coop_loan_atm_transaction` as `t1`
										INNER JOIN (SELECT loan_atm_id, MAX(cast(transaction_datetime as Datetime)) as max FROM coop_loan_atm_transaction WHERE transaction_datetime <= '".$date." 23:59:59.000' group by loan_atm_id)
												as t2 ON `t1`.`loan_atm_id` = `t2`.`loan_atm_id` AND `t1`.`transaction_datetime` = `t2`.`max`
												WHERE t1.loan_atm_id IN  (".implode(',',array_filter($loan_atm_ids)).")
										ORDER BY `t1`.`transaction_datetime`, `t1`.`loan_atm_transaction_id` DESC
										")->result_array();
			$loan_atm_members = array_column($loan_atms, 'loan_atm_id');

			$loan_atm_trans_principal = $this->db->query("SELECT t1.loan_atm_id, t1.pay_amount as principal
															FROM (SELECT * FROM coop_finance_month_detail WHERE loan_atm_id IN (".implode(',',array_filter($loan_atm_ids)).") AND deduct_code = 'ATM' AND pay_type = 'principal') AS t1
															INNER JOIN (SELECT * FROM coop_finance_month_profile WHERE profile_month = '".$filter_month."' AND profile_year = '".$filter_year."') AS t3 ON t1.profile_id = t3.profile_id
														")->result_array();
			$loan_atm_tran_principal_members = array_column($loan_atm_trans_principal, 'loan_id');

			$loan_atm_trans_interest = $this->db->query("SELECT t2.loan_atm_id, t2.pay_amount as interest
														FROM (SELECT * FROM coop_finance_month_detail WHERE loan_atm_id IN (".implode(',',array_filter($loan_atm_ids)).") AND deduct_code = 'ATM' AND pay_type = 'interest') AS t2
														INNER JOIN (SELECT * FROM coop_finance_month_profile WHERE profile_month = '".$filter_month."' AND profile_year = '".$filter_year."') AS t3 ON t2.profile_id = t3.profile_id
													")->result_array();
			$loan_atm_tran_interest_members = array_column($loan_atm_trans_interest, 'loan_id');
		}

		//Get Bank Account Info
		$account_ids = array_column($result, 'account_id');
		$implode_acc = implode(',',array_filter($account_ids));
		if (!empty($implode_acc)) {
			$accounts = $this->db->query("SELECT t2.transaction_id, `t2`.`account_id`, `t2`.`transaction_balance`, `t2`.`transaction_time`, t4.type_name, t3.account_status
											FROM (SELECT account_id, transaction_id, transaction_balance, transaction_time
													FROM coop_account_transaction
													WHERE transaction_time <= '".$date." 23:59:59.000' AND account_id IN  (".implode(',',array_filter($account_ids))."))
													as t2
											LEFT JOIN coop_maco_account as t3 ON t2.account_id = t3.account_id
											LEFT JOIN coop_deposit_type_setting as t4 ON t4.type_id = t3.type_id
											WHERE t3.account_status = 0
											ORDER BY `t2`.`transaction_time` DESC, `t2`.`transaction_id` DESC 
										")->result_array();
						$account_members = array_column($accounts, 'account_id');
		}

		$datas = array();
		$total_data = array();

		foreach($result as $index => $row) {
			// if ($index <= 15000) {
			$datas[$row["member_id"]]["prename_full"] = $row["prename_full"];
			$datas[$row["member_id"]]["firstname_th"] = $row["firstname_th"];
			$datas[$row["member_id"]]["lastname_th"] = $row["lastname_th"];
			$datas[$row["member_id"]]["member_id"] = $row["member_id"];
			$datas[$row["member_id"]]["name"] = $row["name"];
			$datas[$row["member_id"]]["sub_name"] = $row["sub_name"];
			$datas[$row["member_id"]]["department"] = $row["department"];
			$datas[$row["member_id"]]["faction"] = $row["faction"];
			$datas[$row["member_id"]]["level"] = $row["level"];
			$datas[$row["member_id"]]["share_month"] = $row["share_month"];
			$datas[$row["member_id"]]["address_send_doc"] = $row["address_send_doc"];
			if (empty($datas[$row["member_id"]]["share_collect_value"])) {
				if(in_array($row['member_id'],$share_members)) $datas[$row["member_id"]]["share_collect_value"] = $shares[array_search($row['member_id'],$share_members)]['share_collect_value'];
			}
			if (!empty($row['loan_id']) && empty($datas[$row["member_id"]]["loan"][$row['loan_id']])) {
				if(in_array($row['loan_id'],$loan_members)) {
					$datas[$row["member_id"]]["loan"][$row['loan_id']]['type'] = $arr_loan_type_code[$row['loan_type']];
					$datas[$row["member_id"]]["loan"][$row['loan_id']]['prefix_code'] = $arr_loan_prefix_code[$row['loan_type']];
					$datas[$row["member_id"]]["loan"][$row['loan_id']]["contract_number"] = $row['contract_number'];
					$datas[$row["member_id"]]["loan"][$row['loan_id']]["principal"] = $loan_trans_principal[array_search($row['loan_id'],$loan_tran_principal_members)]['principal'];
					$datas[$row["member_id"]]["loan"][$row['loan_id']]["interest"] = $loan_trans_interest[array_search($row['loan_id'],$loan_tran_interest_members)]['interest'];
					$datas[$row["member_id"]]["loan"][$row['loan_id']]["total"] = $loans[array_search($row['loan_id'],$loan_members)]['loan_amount_balance'];
					if($datas[$row["member_id"]]["loan"][$row['loan_id']]["total"] > 0){
                        $total_data[$row["member_id"]]["loan"] += $datas[$row["member_id"]]["loan"][$row['loan_id']]["total"];
                    }
				}
			}
			if (!empty($row['loan_atm_id']) && empty($datas[$row["member_id"]]["loan"][$row['loan_atm_id']])) {
				if(in_array($row['loan_atm_id'],$loan_atm_members)) {
					$datas[$row["member_id"]]["loan"][$row['loan_atm_id']]['type'] = "atm";
					$datas[$row["member_id"]]["loan"][$row['loan_atm_id']]['prefix_code'] = $arr_loan_prefix_code[$row['loan_type']];;
					$datas[$row["member_id"]]["loan"][$row['loan_atm_id']]["contract_number"] = $row['contract_number_atm'];
					$datas[$row["member_id"]]["loan"][$row['loan_atm_id']]["principal"] = $loan_atm_trans_principal[array_search($row['loan_atm_id'],$loan_atm_trans_principal)]['principal'];
					$datas[$row["member_id"]]["loan"][$row['loan_atm_id']]["interest"] = $loan_atm_trans_interest[array_search($row['loan_atm_id'],$loan_atm_tran_interest_members)]['interest'];
					$datas[$row["member_id"]]["loan"][$row['loan_atm_id']]["total"] = $loan_atms[array_search($row['loan_atm_id'],$loan_atm_members)]['loan_amount_balance'];
                    if($datas[$row["member_id"]]["loan"][$row['loan_atm_id']]["total"] > 0){
                        $total_data[$row["member_id"]]["loan"] += $datas[$row["member_id"]]["loan"][$row['loan_atm_id']]["total"];
                    }
				}
			}
			if (!empty($row['account_id']) && empty($datas[$row["member_id"]]["account"][$row['account_id']])) {
				if(in_array($row['account_id'],$account_members) && $accounts[array_search($row['account_id'],$account_members)]['transaction_balance'] > 0) {
					$datas[$row["member_id"]]["account"][$row['account_id']]["type_name"] = $accounts[array_search($row['account_id'],$account_members)]['type_name'];
					$datas[$row["member_id"]]["account"][$row['account_id']]["balance"] = $accounts[array_search($row['account_id'],$account_members)]['transaction_balance'];
					$datas[$row["member_id"]]["account"][$row['account_id']]["account_id"] = $accounts[array_search($row['account_id'],$account_members)]['account_id'];
					if($datas[$row["member_id"]]["account"][$row['account_id']]["balance"] > 0){
                        $total_data[$row["member_id"]]["account"] += $datas[$row["member_id"]]["account"][$row['account_id']]["balance"];
                    }
				}
			}

			// }
		}


		// ลบเงินกู้ที่จำนวนเงินกู้ <= 0 ออก
        foreach ($datas as $member_id => $data){
            if(!empty($data['loan'])) {
                foreach ($data['loan'] as $key => $value) {
                    if ($value['total'] <= 0) {
                        unset($datas[$member_id]['loan'][$key]);
                    }
                }
            }
            if(empty($data['share_collect_value']) && empty($datas[$member_id]['loan']) && empty($data['account'])){
                unset($datas[$member_id]);
            }
        }

		$row_profile = $this->db->select(array(
					'coop_name_th',
					'address1',
					'address2',
					'coop_img'
				))
				->from('coop_profile')->limit(1)
		        ->get()->result_array();
		$data_arr['row_profile'] = $row_profile[0];

		$date_signature = date('Y-m-d');
		$signature = $this->db->select(array('*'))
							->from('coop_signature')
							->where("start_date <= '{$date_signature}'")
							->order_by('start_date DESC')
							->get()->row();

        $new_data = array();
//        foreach ($datas as $key => $value){
//            $new_data[$value['faction']][$value['level']][$value['member_id']] = $value;
//        }
//        ksort($new_data);
//        foreach ($new_data as $key => $value){
//            ksort($new_data[$key]);
//        }
//        foreach ($new_data as $key => $value){
//            foreach ($value as $key2 => $value2){
//                ksort($new_data[$key][$key2]);
//            }
//        }
//        $datas = array();
//        $datas_test = array();
//        foreach ($new_data as $key => $value){
//            foreach ($value as $key2 => $value2){
//                foreach ($value2 as $key3 => $value3) {
//                    $datas[$value3['member_id']] = $value3;
//                    $datas_test[$value3['member_id']] = $value3['member_id'];
//                }
//            }
//        }

		$data_arr['member_ids'] = $member_ids;
		$data_arr['member_infos'] = $result;
		$data_arr['datas'] = $datas;
		$data_arr['date'] = $date;
		$data_arr['total_data'] = $total_data;
		$data_arr['signature'] = $signature;

		if($_GET['dev'] == 'dev') {
            echo '<pre>';print_r($data_arr['datas']);exit;
        }
		$this->load->view('certificate/cert_confirm_share_loan_pdf',$data_arr);
	}

	public function check_cert_confirm_share_loan() {
		$whereMember = "1=1 ";
		if(!empty($_GET["member_id"])) {
			$whereMember .= " AND member_id = '".$_GET["member_id"]."'";
		}
		if($_GET['level'] != '' && $_GET['level'] != '0'){
			$whereMember .= " AND level = '".$_GET['level']."'";
		}else if($_GET['faction'] != '' && $_GET['faction'] != '0'){
			$whereMember .= " AND faction = '".$_GET['faction']."'";
		}else if($_GET['department'] != '' && $_GET['department'] != '0'){
			$whereMember .= " AND department = '".$_GET['department']."'";
		}

		if($_GET['date']){
			$date_arr = explode('/',@$_GET['date']);
			$day = $date_arr[0];
			$month = $date_arr[1];
			$year = $date_arr[2];
			$year -= 543;
			$date = $year.'-'.$month.'-'.$day;
		}

		//Get last month
		$filter_month = 1;
		$filter_year = 2561;
		if ($month > 1) {
			$filter_month = $month - 1;
			$filter_year = $year + 543;
		} else {
			$filter_month = $month;
			$filter_year = $year + 543 - 1;
		}

		$rs_type_code = $this->db->select(array('coop_loan_name.loan_name_id','coop_loan_type.loan_type_code'))
									->from('coop_loan_name')
									->join('coop_loan_type','coop_loan_name.loan_type_id = coop_loan_type.id','left')
									->get()->result_array();
		$arr_loan_type_code = array();
		foreach($rs_type_code AS $key_type_code=>$row_type_code){
			$arr_loan_type_code[$row_type_code['loan_name_id']] = $row_type_code['loan_type_code'];
		}

		$sql = "SELECT COUNT(`coop_mem_apply`.`member_id`) as count_row
				FROM ((SELECT member_id FROM coop_mem_apply WHERE ".$whereMember.") AS coop_mem_apply)
				LEFT JOIN (SELECT t1.share_id, t1.member_id
							FROM coop_mem_share as t1
							WHERE t1.share_date <= '".$date." 23:59:59.000' GROUP BY member_id) AS t4 ON `coop_mem_apply`.`member_id` = `t4`.`member_id`
				LEFT JOIN (SELECT t3.member_id, t1.loan_id
                             FROM coop_loan_transaction AS t1 LEFT JOIN coop_loan AS t3 ON t1.loan_id = t3.id WHERE t1.transaction_datetime <= '".$date." 23:59:59.000' GROUP BY t1.loan_id)
								AS t5 ON `coop_mem_apply`.`member_id` = `t5`.`member_id`
				LEFT JOIN (SELECT t3.member_id, t1.loan_atm_id
								FROM coop_loan_atm_transaction AS t1 LEFT JOIN coop_loan_atm AS t3 ON t1.loan_atm_id = t3.loan_atm_id WHERE t1.transaction_datetime <= '".$date." 23:59:59.000'
								GROUP BY t1.loan_atm_id ORDER BY t1.loan_atm_id DESC ,t1.loan_atm_transaction_id DESC ) AS t6 ON `coop_mem_apply`.`member_id` = `t6`.`member_id`
				LEFT JOIN (SELECT account_id, mem_id FROM coop_maco_account) AS t7 ON t7.mem_id = `coop_mem_apply`.`member_id`
								WHERE (`t5`.`loan_id` != '' OR `t4`.`share_id` != '' OR `t6`.`loan_atm_id` != '' OR t7.account_id != '')";

		$result = $this->db->query($sql)->row_array();
		if(!empty($result) && $result['count_row'] > 0){
			echo "success";
		}
		exit;
	}
	public function coop_report_confirmation_share_debt_dept() {
		$arr_data = array();

		$this->db->select(array('id','mem_group_name'));
		$this->db->from('coop_mem_group');
		$this->db->where("mem_group_type = '1'");
		$row = $this->db->get()->result_array();
		$arr_data['row_mem_group'] = $row;

		$this->libraries->template('certificate/coop_report_confirmation_share_debt_dept',$arr_data);
	}

	public function coop_confirmation_share_debt_dept_preview() {
		$arr_data = array();

		if(!empty($_GET['date'])){
			$date_arr = explode('/',@$_GET['date']);
			$day = $date_arr[0];
			$month = $date_arr[1];
			$year = $date_arr[2];
			$year -= 543;
			$date = $year.'-'.$month.'-'.$day;
		} else {
			$_GET['date'] = date("d")."/".date("m").(date("Y")+543);
			$date_arr = explode('/',@$_GET['date']);
			$day = $date_arr[0];
			$month = $date_arr[1];
			$year = $date_arr[2];
			$year -= 543;
			$date = $year.'-'.$month.'-'.$day;
		}
		$arr_data["date"] = $date;

		if($_GET['type_report'] == '2') {
			$datas = $this->get_confirmation_share_debt_dept_person_data($_GET['department'], $_GET['faction'], $_GET['level'], $_GET['date'], $_GET['type_report']);
			$paginate_data = array();
			$page_all = 0;
			$page=1;
			$num_row=1;
			foreach($datas as $key=>$value) {
				$paginate_data[$page][$key] = $value;
				$paginate_data[$page][$key]['num_row'] = $num_row;
				$num_row++;
				if(($page == 1 && $num_row == 37) || ($page > 1 &&$num_row == 42)){
					$page++;
					$page_all++;
					$num_row=1;
				}
			}
			$arr_data["datas"] = $paginate_data;
			$arr_data["page_all"] = $page_all;
			$this->preview_libraries->template_preview('certificate/coop_confirmation_share_debt_dept_person_preview',$arr_data);
		} else {
			$datas = $this->get_confirmation_share_debt_dept_data($_GET['department'], $_GET['faction'], $_GET['level'], $_GET['date'], $_GET['type_report']);
			$paginate_data = array();
			$page_all = 0;
			$page=1;
			$num_row=1;
			foreach($datas as $key=>$value) {
				foreach($value as $key1=>$value1) {
					foreach($value1 as $key2 => $value2) {
						$paginate_data[$page][$key][$key1][$key2] = $value2;
						$paginate_data[$page][$key][$key1][$key2]['num_row'] = $num_row;
						$num_row++;
						if(($page == 1 && $num_row == 17) || ($page > 1 &&$num_row == 19)){
							$page++;
							$page_all++;
							$num_row=1;
							$is_end_department = 1;
						} else {
							$is_end_department = 0;
						}
					}
				}
			}

			$arr_data["datas"] = $paginate_data;
			$arr_data["page_all"] = $page_all;
			$this->preview_libraries->template_preview('certificate/coop_confirmation_share_debt_dept_preview',$arr_data);
		}		
	}

	public function coop_confirmation_share_debt_dept_excel() {
		$arr_data = array();
		if($_GET['date']){
			$date_arr = explode('/',@$_GET['date']);
			$day = $date_arr[0];
			$month = $date_arr[1];
			$year = $date_arr[2];
			$year -= 543;
			$date = $year.'-'.$month.'-'.$day;
		}
		$arr_data["date"] = $date;

		if($_GET['type_report'] == '2') {
			$datas = $this->get_confirmation_share_debt_dept_person_data($_GET['department'], $_GET['faction'], $_GET['level'], $_GET['date'], $_GET['type_report']);
			$arr_data["datas"] = $datas;
			$this->load->view('certificate/coop_confirmation_share_debt_dept_person_excel',$arr_data);
		} else {
			$datas = $this->get_confirmation_share_debt_dept_data($_GET['department'], $_GET['faction'], $_GET['level'], $_GET['date'], $_GET['type_report']);
			$arr_data["datas"] = $datas;
			$this->load->view('certificate/coop_confirmation_share_debt_dept_excel',$arr_data);
		}

		
	}

	public function get_confirmation_share_debt_dept_data($department, $faction, $level, $get_date, $type_report) {
		ini_set('memory_limit', -1);

		$whereMember = "1=1 ";
		if(!empty($level)){
			$whereMember .= " AND t1.level = '".$level."'";
		}else if(!empty($faction)){
			$whereMember .= " AND t1.faction = '".$faction."'";
		}else if(!empty($department)){
			$whereMember .= " AND t1.department = '".$department."'";
		}

		if($get_date){
			$date_arr = explode('/',$get_date);
			$day = $date_arr[0];
			$month = $date_arr[1];
			$year = $date_arr[2];
			$year -= 543;
			$date = $year.'-'.$month.'-'.$day;
		}

		//Get last month
		$filter_month = 1;
		$filter_year = 2561;
		if ($month > 1) {
			$filter_month = $month - 1;
			$filter_year = $year + 543;
		} else {
			$filter_month = $month;
			$filter_year = $year + 543 - 1;
		}

		//Get member
		$members = $this->db->select("t1.member_id,
										t1.firstname_th,
										t1.lastname_th,	
										t1.level,
										t1.faction,
										t1.department,
										t2.prename_full,
										t3.mem_group_id,
										t3.mem_group_name as level_name,
										t4.mem_group_name as faction_name,
										t5.mem_group_name as department_name,
										t6.id as loan_id,
										t7.loan_atm_id,
										t8.account_id
									")
							->from("coop_mem_apply as t1")
							->join("coop_prename as t2", "t1.prename_id = t2.prename_id", "left")
							->join("coop_mem_group as t3", "t1.level = t3.id", "left")
							->join("coop_mem_group as t4", "t1.faction = t4.id", "left")
							->join("coop_mem_group as t5", "t1.department = t5.id", "left")
							->join("coop_loan as t6", "t1.member_id = t6.member_id", "left")
							->join("coop_loan_atm as t7", "t1.member_id = t7.member_id", "left")
							->join("coop_maco_account as t8", "t1.member_id = t8.mem_id", "left")
							->where($whereMember)
							->order_by("t1.department, t1.faction, t1.level")
							->get()->result_array();
		$member_ids = implode(',',array_filter(array_column($members, 'member_id')));
		$account_ids = array_column($members, 'account_id');

		//Get Share
		$shares = $this->db->query("SELECT t1.member_id,
											t1.share_date,
											t1.share_period,
											t1.share_collect_value,
											t1.share_collect
									FROM coop_mem_share as t1
									WHERE member_id IN (".$member_ids.") AND share_date <= '".$date." 23:59:59.000' AND share_status not in (0,3)
									ORDER BY t1.share_date DESC, t1.share_id DESC")
							->result_array();
		$share_members = array_column($shares, 'member_id');

		//Get Loan
		$loans = $this->db->query("SELECT `t1`.`loan_transaction_id`, `t1`.`loan_id`, `t1`.`loan_amount_balance`, `t1`.`transaction_datetime`
										FROM coop_loan as t0
										INNER JOIN `coop_loan_transaction` as `t1` on t0.id = t1.loan_id
										INNER JOIN (SELECT loan_id, MAX(cast(transaction_datetime as Datetime)) as max FROM coop_loan_transaction WHERE transaction_datetime <= '".$date." 23:59:59.000' group by loan_id)
												as t2 ON `t1`.`loan_id` = `t2`.`loan_id` AND `t1`.`transaction_datetime` = `t2`.`max`
										WHERE `t1`.`loan_amount_balance` > 0 AND t0.member_id IN  (".$member_ids.")
										ORDER BY `t1`.`transaction_datetime`, `t1`.`loan_transaction_id` DESC
									")
							->result_array();
		$loan_members = array_column($loans, 'loan_id');

		//Get Loan Atm
		$loan_atms = $this->db->query("SELECT t1.loan_atm_transaction_id, `t1`.`loan_atm_id`, `t1`.`loan_amount_balance`, `t1`.`transaction_datetime`
											FROM coop_loan_atm as t0
											INNER JOIN `coop_loan_atm_transaction` as `t1` on t0.loan_atm_id = t1.loan_atm_id
											INNER JOIN (SELECT loan_atm_id, MAX(cast(transaction_datetime as Datetime)) as max FROM coop_loan_atm_transaction WHERE transaction_datetime <= '".$date." 23:59:59.000' group by loan_atm_id)
													as t2 ON `t1`.`loan_atm_id` = `t2`.`loan_atm_id` AND `t1`.`transaction_datetime` = `t2`.`max`
													WHERE `t1`.`loan_amount_balance` > 0 AND t0.member_id IN  (".$member_ids.")
											ORDER BY `t1`.`transaction_datetime`, `t1`.`loan_atm_transaction_id` DESC
										")
								->result_array();
		$loan_atm_members = array_column($loan_atms, 'loan_atm_id');

		//Get Bank Account
		$implode_acc = implode(',',array_filter($account_ids));
		if (!empty($implode_acc)) {
			$accounts = $this->db->query("SELECT `t2`.`account_id`, `t2`.`transaction_balance`
											FROM (SELECT account_id, transaction_id, transaction_balance, transaction_time
													FROM coop_account_transaction
													WHERE transaction_time <= '".$date." 23:59:59.000' AND account_id IN  (".$implode_acc."))
													as t2
											INNER JOIN (SELECT account_id, MAX(cast(transaction_time as Datetime)) as max FROM coop_account_transaction
														WHERE transaction_time <= '".$date." 23:59:59.000' AND account_id IN  (".$implode_acc.") group by account_id) as t1
													ON t1.max = t2.transaction_time AND t1.account_id = t2.account_id

											ORDER BY `t2`.`transaction_time` DESC, `t2`.`transaction_id` DESC 
										")->result_array();
			$account_members = array_column($accounts, 'account_id');
		}

		$datas = array();
		$loan_ids = array();
		$loan_atm_ids = array();
		$account_ids = array();
		$prev_member_ids = array();
		$prev_member_ids_loan = array();
		$prev_member_ids_account = array();
		foreach($members as $index => $row) {
			$exist = 0;

			if(in_array($row['member_id'],$share_members) && !in_array($row['member_id'], $prev_member_ids)) {
				$share_collect_value = $shares[array_search($row['member_id'],$share_members)]['share_collect_value'];
				if(!empty($share_collect_value)) {
					$datas[$row["department"]][$row["faction"]][$row["level"]]["share_collect_value"] += $share_collect_value;
					$datas[$row["department"]][$row["faction"]][$row["level"]]["share_count"] += 1;
					$prev_member_ids[] = $row['member_id'];
					$exist = 1;
				}
			}

			if (!empty($row['loan_id']) && !in_array($row['loan_id'], $loan_ids) && in_array($row['loan_id'],$loan_members) && $loans[array_search($row['loan_id'],$loan_members)]['loan_amount_balance'] > 0) {
				$balance = $loans[array_search($row['loan_id'],$loan_members)]['loan_amount_balance'];
				if (!empty($balance)) {
					$datas[$row["department"]][$row["faction"]][$row["level"]]["loan"] += $balance;
					if (!in_array($row['member_id'], $prev_member_ids_loan)) {
						$datas[$row["department"]][$row["faction"]][$row["level"]]["loan_count"] += 1;
						$prev_member_ids_loan[] = $row['member_id'];
					}
					$loan_ids[] = $row['loan_id'];
					$exist = 1;
				}
			}

			if (!empty($row['loan_atm_id']) && !in_array($row['loan_atm_id'], $loan_atm_ids) && in_array($row['loan_atm_id'],$loan_atm_members)) {
				$balance = $loan_atms[array_search($row['loan_atm_id'],$loan_atm_members)]['loan_amount_balance'];
				if (!empty($balance)) {
					$datas[$row["department"]][$row["faction"]][$row["level"]]["loan"] += $balance;
					if (!in_array($row['member_id'], $prev_member_ids_loan)) {
						$datas[$row["department"]][$row["faction"]][$row["level"]]["loan_count"] += 1;
						$prev_member_ids_loan[] = $row['member_id'];
					}
					$loan_atm_ids[] = $row['loan_atm_id'];
					$exist = 1;
				}
			}

			if (!empty($row['account_id']) && !in_array($row['account_id'], $account_ids) && in_array($row['account_id'],$account_members)) {
				$balance = $accounts[array_search($row['account_id'],$account_members)]['transaction_balance'];
				if (!empty($balance)) {
					$datas[$row["department"]][$row["faction"]][$row["level"]]["account"] += $balance;
					if (!in_array($row['member_id'], $prev_member_ids_account) && $balance > 0) {
						$datas[$row["department"]][$row["faction"]][$row["level"]]["account_count"] += 1;
						$prev_member_ids_account[] = $row['member_id'];
					}
					$account_ids[] = $row['account_id'];
					$exist = 1;
				}
			}

			if (!empty($exist)) {
				$datas[$row["department"]][$row["faction"]][$row["level"]]['name'] = $row["department_name"].",".$row["faction_name"].",".$row["level_name"];
				$datas[$row["department"]][$row["faction"]][$row["level"]]['level_group_id'] = $row["mem_group_id"];
			}
		}

		return $datas;
	}

	public function get_confirmation_share_debt_dept_person_data($department, $faction, $level, $get_date, $type_report) {
		ini_set('memory_limit', -1);

		$whereMember = "1=1 ";
		if(!empty($level)){
			$whereMember .= " AND t1.level = '".$level."'";
		}else if(!empty($faction)){
			$whereMember .= " AND t1.faction = '".$faction."'";
		}else if(!empty($department)){
			$whereMember .= " AND t1.department = '".$department."'";
		}

		if($get_date){
			$date_arr = explode('/',$get_date);
			$day = $date_arr[0];
			$month = $date_arr[1];
			$year = $date_arr[2];
			$year -= 543;
			$date = $year.'-'.$month.'-'.$day;
		}

		//Get last month
		$filter_month = 1;
		$filter_year = 2561;
		if ($month > 1) {
			$filter_month = $month - 1;
			$filter_year = $year + 543;
		} else {
			$filter_month = $month;
			$filter_year = $year + 543 - 1;
		}

		//Get member
		$members = $this->db->select("t1.member_id,
										t1.firstname_th,
										t1.lastname_th,	
										t1.level,
										t1.faction,
										t1.department,
										t2.prename_full,
										t3.mem_group_name as level_name,
										t4.mem_group_name as faction_name,
										t5.mem_group_name as department_name,
										t6.id as loan_id,
										t7.loan_atm_id,
										t8.account_id
									")
							->from("coop_mem_apply as t1")
							->join("coop_prename as t2", "t1.prename_id = t2.prename_id", "left")
							->join("coop_mem_group as t3", "t1.level = t3.id", "left")
							->join("coop_mem_group as t4", "t1.faction = t4.id", "left")
							->join("coop_mem_group as t5", "t1.department = t5.id", "left")
							->join("coop_loan as t6", "t1.member_id = t6.member_id", "left")
							->join("coop_loan_atm as t7", "t1.member_id = t7.member_id", "left")
							->join("coop_maco_account as t8", "t1.member_id = t8.mem_id", "left")
							->where($whereMember)
							->order_by("t1.member_id")
							->get()->result_array();
		$member_ids = implode(',',array_filter(array_column($members, 'member_id')));
		$account_ids = array_column($members, 'account_id');

		//Get Share
		$shares = $this->db->query("SELECT t1.member_id,
											t1.share_date,
											t1.share_period,
											t1.share_collect_value,
											t1.share_collect
									FROM coop_mem_share as t1
									WHERE member_id IN (".$member_ids.") AND share_date <= '".$date." 23:59:59.000' AND share_status not in (0,3)
									ORDER BY t1.share_date DESC, t1.share_id DESC")
							->result_array();
		$share_members = array_column($shares, 'member_id');

		//Get Loan
		$loans = $this->db->query("SELECT `t1`.`loan_transaction_id`, `t1`.`loan_id`, `t1`.`loan_amount_balance`, `t1`.`transaction_datetime`
										FROM coop_loan as t0
										INNER JOIN `coop_loan_transaction` as `t1` on t0.id = t1.loan_id
										INNER JOIN (SELECT loan_id, MAX(cast(transaction_datetime as Datetime)) as max FROM coop_loan_transaction WHERE transaction_datetime <= '".$date." 23:59:59.000' group by loan_id)
												as t2 ON `t1`.`loan_id` = `t2`.`loan_id` AND `t1`.`transaction_datetime` = `t2`.`max`
										WHERE `t1`.`loan_amount_balance` > 0 AND t0.member_id IN  (".$member_ids.")
										ORDER BY `t1`.`transaction_datetime`, `t1`.`loan_transaction_id` DESC
									")
							->result_array();
		$loan_members = array_column($loans, 'loan_id');

		//Get Loan Atm
		$loan_atms = $this->db->query("SELECT t1.loan_atm_transaction_id, `t1`.`loan_atm_id`, `t1`.`loan_amount_balance`, `t1`.`transaction_datetime`
											FROM coop_loan_atm as t0
											INNER JOIN `coop_loan_atm_transaction` as `t1` on t0.loan_atm_id = t1.loan_atm_id
											INNER JOIN (SELECT loan_atm_id, MAX(cast(transaction_datetime as Datetime)) as max FROM coop_loan_atm_transaction WHERE transaction_datetime <= '".$date." 23:59:59.000' group by loan_atm_id)
													as t2 ON `t1`.`loan_atm_id` = `t2`.`loan_atm_id` AND `t1`.`transaction_datetime` = `t2`.`max`
													WHERE `t1`.`loan_amount_balance` > 0 AND t0.member_id IN  (".$member_ids.")
											ORDER BY `t1`.`transaction_datetime`, `t1`.`loan_atm_transaction_id` DESC
										")
								->result_array();
		$loan_atm_members = array_column($loan_atms, 'loan_atm_id');

		//Get Bank Account
		$implode_acc = implode(',',array_filter($account_ids));
		if (!empty($implode_acc)) {
			$accounts = $this->db->query("SELECT `t2`.`account_id`, `t2`.`transaction_balance`
											FROM (SELECT account_id, transaction_id, transaction_balance, transaction_time
													FROM coop_account_transaction
													WHERE transaction_time <= '".$date." 23:59:59.000' AND account_id IN  (".$implode_acc."))
													as t2
											INNER JOIN (SELECT account_id, MAX(cast(transaction_time as Datetime)) as max FROM coop_account_transaction
														WHERE transaction_time <= '".$date." 23:59:59.000' AND account_id IN  (".$implode_acc.") group by account_id) as t1
													ON t1.max = t2.transaction_time AND t1.account_id = t2.account_id
											ORDER BY `t2`.`transaction_time` DESC, `t2`.`transaction_id` DESC 
										")->result_array();
			$account_members = array_column($accounts, 'account_id');
		}

		$datas = array();
		$loan_ids = array();
		$loan_atm_ids = array();
		$account_ids = array();
		foreach($members as $index => $row) {
			$exist = 0;

			if(in_array($row['member_id'],$share_members)) {
				$balance = $shares[array_search($row['member_id'],$share_members)]['share_collect_value'];
				if (!empty($balance)) {
					$datas[$row["member_id"]]["share_collect_value"] = $balance;
					$exist = 1;
				}
			}

			if (!empty($row['loan_id']) && in_array($row['loan_id'],$loan_members) && !in_array($row['loan_id'], $loan_ids)) {
				$datas[$row["member_id"]]["loan"] += $loans[array_search($row['loan_id'],$loan_members)]['loan_amount_balance'];
				$loan_ids[] = $row['loan_id'];
				$exist = 1;
			}

			if (!empty($row['loan_atm_id']) && in_array($row['loan_atm_id'],$loan_atm_members) && !in_array($row['loan_atm_id'], $loan_atm_ids)) {
				$datas[$row["member_id"]]["loan"] += $loan_atms[array_search($row['loan_atm_id'],$loan_atm_members)]['loan_amount_balance'];
				$loan_atm_ids[] = $row['loan_atm_id'];
				$exist = 1;
			}

			if (!empty($row['account_id']) && in_array($row['account_id'],$account_members) && !in_array($row['account_id'], $account_ids)) {
				$balance = $accounts[array_search($row['account_id'],$account_members)]['transaction_balance'];
				if (!empty($balance)) {
					$datas[$row["member_id"]]["account"] += $balance;
					$account_ids[] = $row['account_id'];
					$exist = 1;
				}
			}

			if (!empty($exist)) {
				$datas[$row["member_id"]]["prename_full"] = $row["prename_full"];
				$datas[$row["member_id"]]["firstname_th"] = $row["firstname_th"];
				$datas[$row["member_id"]]["lastname_th"] = $row["lastname_th"];
				$datas[$row["member_id"]]["member_id"] = $row["member_id"];
				$datas[$row["member_id"]]["level_name"] = $row["level_name"];
				$datas[$row["member_id"]]["faction_name"] = $row["faction_name"];
				$datas[$row["member_id"]]["department_name"] = $row["department_name"];
			}
		}

		return $datas;
	}
}
