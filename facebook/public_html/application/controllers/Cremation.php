<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cremation extends CI_Controller {

    public $CI;

	function __construct()
	{
		parent::__construct();
		$this->CI =&get_instance();
		$this->month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');

	}

	public function cremation_request(){
		$arr_data = array();

		if(!empty($_POST)) {
			$process_timestamp = date('Y-m-d H:i:s');
			if(empty($_POST["cremation_request_id"])){
				//Add request
				$year_now = (date('Y')+543);
				$last_reqs = $this->db->select(array('MAX(runno) AS last_run'))
										->from('coop_cremation_request')
										->where("yy = '{$year_now}'")
										->get()->result_array();
				$last_req = $last_reqs[0];
				$run_now = 0;
				if($last_req['last_run']) {
					$run_now = $last_req['last_run']+1;
				} else {
					$run_now = 1;
				}

				$cremation_request_id = "000001";
				$lastest_request = $this->db->select("cremation_request_id")->from("coop_cremation_request")->order_by("cremation_request_id DESC")->get()->row();
				if(!empty($lastest_request)) {
					$id = (int)$lastest_request->cremation_request_id;
					$cremation_request_id = sprintf("%06d", $id + 1);
				}

				$this->db->select('*')
							->from('coop_setting_cremation_detail')
							->where('start_date <= now() AND cremation_id = 2')
							->order_by('start_date DESC, cremation_detail_id ASC')
							->limit(1);

				//Cremation ID
				$setting = $this->db->get()->row();
				$setting_cremation_id = $setting->cremation_detail_id;

				//Add member cremation
				$data_insert = array();
				$data_insert["member_id"] = $_POST["member_id"];
				$data_insert["mem_type_id"] = $_POST["type"];
				$data_insert["prename_id"] = $_POST["prename_id"];
				$data_insert["assoc_firstname"] = $_POST["firstname_th"];
				$data_insert["assoc_lastname"] = $_POST["lastname_th"];
				$data_insert["assoc_birthday"] = $this->center_function->ConvertToSQLDate($_POST['birthday']);
				$data_insert["id_card"] = $_POST["id_card"];
				$data_insert["relation"] = $_POST["relation_type"];
				$data_insert["ref_member_id"] = $_POST["cremetion_member_id"];
				$data_insert["ref_cremation_request_id"] = $_POST["relate_member_id"];
				$data_insert["occupation"] = $_POST["career"];
				$data_insert["position"] = $_POST["position"];
				$data_insert["workplace"] = $_POST["workplace"];
				$data_insert["office_phone"] = $_POST["office_tel"];
				$data_insert["addr_no"] = $_POST["address_no"];
				$data_insert["addr_moo"] = $_POST["address_moo"];
				$data_insert["addr_soi"] = $_POST["address_soi"];
				$data_insert["addr_street"] = $_POST["address_road"];
				$data_insert["province_id"] = $_POST["province_id"];
				$data_insert["amphur_id"] = $_POST["amphur_id"];
				$data_insert["district_id"] = $_POST["district_id"];
				$data_insert["zip_code"] = $_POST["zipcode"];
				$data_insert["cur_addr_no"] = $_POST["c_address_no"];
				$data_insert["cur_addr_moo"] = $_POST["c_address_moo"];
				$data_insert["cur_addr_soi"] = $_POST["c_address_soi"];
				$data_insert["cur_addr_street"] = $_POST["c_address_road"];
				$data_insert["cur_province_id"] = $_POST["c_province_id"];
				$data_insert["cur_amphur_id"] = $_POST["c_amphur_id"];
				$data_insert["cur_district_id"] = $_POST["c_district_id"];
				$data_insert["cur_zip_code"] = $_POST["c_zipcode"];
				$data_insert["marry_name"] = $_POST["marry_name"];
				$data_insert["receiver_1"] = $_POST["receiver_1"];
				$data_insert["receiver_2"] = $_POST["receiver_2"];
				$data_insert["receiver_3"] = $_POST["receiver_3"];
				$data_insert["receiver_4"] = $_POST["receiver_4"];
				$data_insert["relate_1"] = $_POST["relate_1"];
				$data_insert["relate_2"] = $_POST["relate_2"];
				$data_insert["relate_3"] = $_POST["relate_3"];
				$data_insert["relate_4"] = $_POST["relate_4"];
				$data_insert["funeral_manager"] = $_POST["funeral_manager"];
				$data_insert["heir_phone"] = $_POST["heir_phone"];
				$data_insert['create_date'] = $process_timestamp;
				$data_insert['status'] = '0';
				$this->db->insert("coop_member_cremation", $data_insert);
				$member_cremation_raw_id = $this->db->insert_id();

				$runno = sprintf("%07d",$run_now);
				$cremation_no = $runno.'/'.$year_now;
				$data_insert = array();
				$data_insert['cremation_no'] = $cremation_no;
				$data_insert['cremation_request_id'] = $cremation_request_id;
				$data_insert['member_cremation_raw_id'] = $member_cremation_raw_id;
				$data_insert['runno'] = $run_now;
				$data_insert['yy'] = $year_now;
				$data_insert["cremation_status"] = 0;
				$data_insert['user_id'] = $_SESSION['USER_ID'];
				$data_insert['user_name'] = $_SESSION['USER_NAME'];
				$data_insert['updatetime'] = $process_timestamp;
				$data_insert['createdatetime'] = $process_timestamp;
				$data_insert['member_id'] = !empty($_POST["member_id"]) ? $_POST["member_id"] : $_POST["cremetion_member_id"];
				$data_insert['cremation_type_id'] = 2;
				$data_insert['cremation_detail_id'] = $setting_cremation_id;
				$this->db->insert("coop_cremation_request", $data_insert);

				//Add bank Account
				$this->db->where("member_cremation_raw_id", $member_cremation_raw_id );
				$this->db->delete("coop_member_cremation_bank_account");
	
				$data_inserts = array();
				foreach($_POST["dividend_bank_id"] as $key => $dividend_bank_id) {
					if(!empty($_POST["dividend_acc_num"][$key])) {
						$data_insert = array();
						$data_insert["member_cremation_raw_id"] = $member_cremation_raw_id;
						$data_insert["dividend_bank_id"] = $dividend_bank_id;
						$data_insert["dividend_bank_branch_id"] = $_POST["dividend_bank_branch_id"][$key];
						$data_insert["dividend_acc_num"] = $_POST["dividend_acc_num"][$key];
						$data_insert['created_at'] = $process_timestamp;
						$data_insert['updated_at'] = $process_timestamp;
						$data_inserts[] = $data_insert;
					}
				}
				if(!empty($data_inserts)) {
					$this->db->insert_batch('coop_member_cremation_bank_account', $data_inserts);
				}

				$_GET["cremation_request_id"] = $cremation_request_id;
			} else {
				//Get cremation request
				$request = $this->db->select("member_cremation_raw_id, cremation_request_id")->from("coop_cremation_request")->where("cremation_request_id = '".$_POST["cremation_request_id"]."'")->get()->row();
				$member_cremation_raw_id = $request->member_cremation_raw_id;
				$cremation_request_id = $request->cremation_request_id;

				//Keep original value
				$member_cremation = $this->db->select("*")->from("coop_member_cremation")->where("id = '".$member_cremation_raw_id."'")->get()->result_array()[0];

				//Update cremation member
				$data_insert = array();
				$data_insert["mem_type_id"] = $_POST["type"];
				$data_insert["prename_id"] = $_POST["prename_id"];
				$data_insert["assoc_firstname"] = $_POST["firstname_th"];
				$data_insert["assoc_lastname"] = $_POST["lastname_th"];
				$data_insert["assoc_birthday"] = $this->center_function->ConvertToSQLDate($_POST['birthday']);
				$data_insert["id_card"] = $_POST["id_card"];
				$data_insert["relation"] = $_POST["relation_type"];
				$data_insert["ref_member_id"] = $_POST["cremetion_member_id"];
				$data_insert["ref_cremation_request_id"] = $_POST["relate_member_id"];
				$data_insert["occupation"] = $_POST["career"];
				$data_insert["position"] = $_POST["position"];
				$data_insert["workplace"] = $_POST["workplace"];
				$data_insert["office_phone"] = $_POST["office_tel"];
				$data_insert["addr_no"] = $_POST["address_no"];
				$data_insert["addr_village"] = $_POST["address_village"];
				$data_insert["addr_moo"] = $_POST["address_moo"];
				$data_insert["addr_soi"] = $_POST["address_soi"];
				$data_insert["addr_street"] = $_POST["address_road"];
				$data_insert["province_id"] = $_POST["province_id"];
				$data_insert["amphur_id"] = $_POST["amphur_id"];
				$data_insert["district_id"] = $_POST["district_id"];
				$data_insert["zip_code"] = $_POST["zipcode"];
				$data_insert["cur_addr_no"] = $_POST["c_address_no"];
				$data_insert["cur_addr_village"] = $_POST["c_address_village"];
				$data_insert["cur_addr_moo"] = $_POST["c_address_moo"];
				$data_insert["cur_addr_soi"] = $_POST["c_address_soi"];
				$data_insert["cur_addr_street"] = $_POST["c_address_road"];
				$data_insert["cur_province_id"] = $_POST["c_province_id"];
				$data_insert["cur_amphur_id"] = $_POST["c_amphur_id"];
				$data_insert["cur_district_id"] = $_POST["c_district_id"];
				$data_insert["cur_zip_code"] = $_POST["c_zipcode"];
				$data_insert["marry_name"] = $_POST["marry_name"];
				$data_insert["receiver_1"] = $_POST["receiver_1"];
				$data_insert["receiver_2"] = $_POST["receiver_2"];
				$data_insert["receiver_3"] = $_POST["receiver_3"];
				$data_insert["receiver_4"] = $_POST["receiver_4"];
				$data_insert["relate_1"] = $_POST["relate_1"];
				$data_insert["relate_2"] = $_POST["relate_2"];
				$data_insert["relate_3"] = $_POST["relate_3"];
				$data_insert["relate_4"] = $_POST["relate_4"];
				$data_insert["funeral_manager"] = $_POST["funeral_manager"];
				$data_insert["heir_phone"] = $_POST["heir_phone"];
				$this->db->where('id', $member_cremation_raw_id);
				$this->db->update("coop_member_cremation", $data_insert);

				//save data history
				$change_datas = array();
				$lastest_change = $this->db->select("ref_id")->from("coop_member_cremation_data_history")->order_by("ref_id DESC")->get()->row();
				foreach($member_cremation as $key => $value) {
					if ($key != "ref_member_id" && $key != "relate_1" && $key != "relate_2" && $key != "relate_3" 
							&& array_key_exists($key ,$data_insert) && $data_insert[$key] != $value
							&& !(($key == "cur_province_id" || $key == "cur_amphur_id" || $key == "cur_district_id") && empty($data_insert[$key]) && empty($value))) {
						$change_data = array();
						$change_data["input_name"] = $key;
						$change_data["ref_id"] = !empty($lastest_change->ref_id) ? $lastest_change->ref_id + 1 : 1;
						$change_data["member_cremation_raw_id"] = $member_cremation_raw_id;
						$change_data["origin_value"] = $value;
						$change_data["new_value"] = $data_insert[$key];
						$change_data["user_id"] = $_SESSION['USER_ID'];
						$change_data["created_at"] = $process_timestamp;
						$change_datas[] = $change_data;
					}
				}
				if(!empty($change_datas)) {
					$this->db->insert_batch('coop_member_cremation_data_history', $change_datas);
				}

				//Remove/Add bank Account
				$this->db->where("member_cremation_raw_id = '".$member_cremation_raw_id."'");
				$this->db->delete("coop_member_cremation_bank_account");

				$data_inserts = array();
				foreach($_POST["dividend_bank_id"] as $key => $dividend_bank_id) {
					if(!empty($_POST["dividend_acc_num"][$key])) {
						$data_insert = array();
						$data_insert["member_cremation_raw_id"] = $member_cremation_raw_id;
						$data_insert["dividend_bank_id"] = $dividend_bank_id;
						$data_insert["dividend_bank_branch_id"] = $_POST["dividend_bank_branch_id"][$key];
						$data_insert["dividend_acc_num"] = $_POST["dividend_acc_num"][$key];
						$data_insert['created_at'] = $process_timestamp;
						$data_insert['updated_at'] = $process_timestamp;
						$data_inserts[] = $data_insert;
					}
				}
				if(!empty($data_inserts)) {
					$this->db->insert_batch('coop_member_cremation_bank_account', $data_inserts);
				}

				$_GET["cremation_request_id"] = $cremation_request_id;
			}
		}

		if(!empty($_GET["cremation_request_id"])) {
			$data = $this->db->select("t1.cremation_no, t1.cremation_request_id, t1.cremation_status, t1.createdatetime, t2.*, t3.prename_full,
										t4.assoc_firstname as firstname_ref, t4.assoc_lastname as lastname_ref, t5.prename_full as prename_full_ref")
								->from("coop_cremation_request as t1")
								->join("coop_member_cremation as t2", "t1.member_cremation_raw_id = t2.id", "inner")
								->join("coop_prename as t3", "t2.prename_id = t3.prename_id", "left")
								->join("coop_member_cremation as t4", "t2.ref_member_id = t4.member_id", "left")
								->join("coop_prename as t5", "t4.prename_id = t5.prename_id", "left")
								->where("t1.cremation_request_id = '".$_GET["cremation_request_id"]."'")
								->get()->result_array();
			$arr_data["data"] = $data[0];

			$balance = $this->db->select("*")
							->from("coop_cremation_advance_payment")
							->where("member_cremation_id = '".$data[0]["member_cremation_id"]."'")
							->get()->row();
			$arr_data["amount_balance"] = $balance->adv_payment_balance;
		}

		if($arr_data['data']["province_id"]!=''){
			$this->db->select('amphur_id, amphur_name');
			$this->db->from('coop_amphur');
			$this->db->where("province_id = '".$arr_data['data']["province_id"]."'");
			$this->db->order_by('amphur_name');
			$row = $this->db->get()->result_array();
			$arr_data['amphur'] = $row;
		}else{
			$arr_data['amphur'] = array();
		}
		if($arr_data['data']["amphur_id"]!=''){
			$this->db->select('district_id, district_name');
			$this->db->from('coop_district');
			$this->db->where("amphur_id = '".$arr_data['data']["amphur_id"]."'");
			$this->db->order_by('district_name');
			$row = $this->db->get()->result_array();
			$arr_data['district'] = $row;
		}else{
			$arr_data['district'] = array();
		}

		if($arr_data['data']["c_province_id"]!=''){
			$this->db->select('amphur_id, amphur_name');
			$this->db->from('coop_amphur');
			$this->db->where("province_id = '".$arr_data['data']["c_province_id"]."'");
			$this->db->order_by('amphur_name');
			$row = $this->db->get()->result_array();
			$arr_data['c_amphur'] = $row;
		}else{
			$arr_data['c_amphur'] = array();
		}
		if(@$arr_data['data']["c_amphur_id"]!=''){
			$this->db->select('district_id, district_name');
			$this->db->from('coop_district');
			$this->db->where("amphur_id = '".@$arr_data['data']["c_amphur_id"]."'");
			$this->db->order_by('district_name');
			$row = $this->db->get()->result_array();
			$arr_data['c_district'] = $row;
		}else{
			$arr_data['c_district'] = array();
		}

		$provinces = $this->db->select('province_id, province_name')
													->from('coop_province')
													->order_by('province_name')
													->get()->result_array();
		$arr_data['province'] = $provinces;
		$prenames = $this->db->select('prename_id, prename_full')
													->from('coop_prename')
													->get()->result_array();
		$arr_data['prename'] = $prenames;

		$row = $this->db->select('act_bank_id, act_bank_name')
						->from('coop_act_bank')
						->get()->result_array();
		$arr_data['act_bank'] = $row;

		$row = $this->db->select('bank_id, bank_name')
						->from('coop_bank')
						->get()->result_array();
		$arr_data['bank'] = $row;

		$row = $this->db->select('branch_id, branch_name')
					->from('coop_bank_branch')
					->where("bank_id = '".@$arr_data['data']["dividend_bank_id"]."'")
					->get()->result_array();
		$arr_data['bank_branch'] = $row;

		$this->libraries->template('cremation/cremation_request',$arr_data);
	}

	public function cremation_request_backup_12_02_2019(){
		$arr_data = array();
		$member_id = @$_GET['id'];
		if($member_id!=''){
			$x=0;
			$join_arr = array();
			$join_arr[$x]['table'] = 'coop_cremation_data';
			$join_arr[$x]['condition'] = 'coop_cremation_request.cremation_type_id = coop_cremation_data.cremation_id';
			$join_arr[$x]['type'] = 'left';
			
			$x++;
			$join_arr[$x]['table'] = 'coop_cremation_transfer';
			$join_arr[$x]['condition'] = 'coop_cremation_request.cremation_request_id = coop_cremation_transfer.cremation_request_id';
			$join_arr[$x]['type'] = 'left';

			$x++;
			$join_arr[$x]['table'] = 'coop_cremation_advance_payment';
            $join_arr[$x]['condition'] = 'coop_cremation_request.member_cremation_id = coop_cremation_advance_payment.member_cremation_id';
            $join_arr[$x]['type'] = 'left';
			
			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select(array('coop_cremation_request.*','coop_cremation_advance_payment.adv_payment_balance','coop_cremation_data.cremation_id','coop_cremation_data.cremation_name_short','coop_cremation_transfer.cremation_transfer_id','coop_cremation_transfer.createdatetime AS record_date','coop_cremation_transfer.transfer_status','coop_cremation_transfer.cremation_receive_id'));
			$this->paginater_all->main_table('coop_cremation_request');
			$this->paginater_all->where("member_id = '".$member_id."'");
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(10);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->order_by('cremation_request_id DESC');
			$this->paginater_all->join_arr($join_arr);
			$row = $this->paginater_all->paginater_process();
			$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'],$_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
			$i = $row['page_start'];


			$arr_data['num_rows'] = $row['num_rows'];
			$arr_data['paging'] = $paging;
			$arr_data['row'] = $row['data'];
			$arr_data['i'] = $i;
			
		}else{
			$arr_data['row'] = array();
		}
		
		
		if($member_id){
			$this->db->select(array('*'));
			$this->db->from('coop_mem_apply');
			$this->db->where("member_id = {$member_id}");
			$rs = $this->db->get()->result_array();
			$arr_data['row_member'] = @$rs[0]; 
			
			$this->db->select('*');
			$this->db->from('coop_mem_req_resign');
			$this->db->where("member_id = '".$member_id."' AND resign_cause_id IN ('9','10') ");
			$this->db->order_by('req_resign_id DESC');
			$rs_resign = $this->db->get()->result_array();
			$row_resign = @$rs_resign[0];	
			
			$arr_data['row_member']['retry_status'] = (empty($row_resign)?'ยังไม่เกษียณ':'เกษียณแล้ว');
		}


        $this->db->select('prename_id, prename_full');
        $this->db->from('coop_prename');
        $row = $this->db->get()->result_array();
        $arr_data['prename'] = $row;

        $this->db->select('province_id, province_name');
        $this->db->from('coop_province');
        $this->db->order_by('province_name');
        $row = $this->db->get()->result_array();
        $arr_data['province'] = $row;

        if(@$arr_data['data']["province_id"]!=''){
            $this->db->select('amphur_id, amphur_name');
            $this->db->from('coop_amphur');
            $this->db->where("province_id = '".@$arr_data['data']["province_id"]."'");
            $this->db->order_by('amphur_name');
            $row = $this->db->get()->result_array();
            $arr_data['amphur'] = $row;
        }else{
            $arr_data['amphur'] = array();
        }

        if(@$arr_data['data']["amphur_id"]!=''){
            $this->db->select('district_id, district_name');
            $this->db->from('coop_district');
            $this->db->where("amphur_id = '".@$arr_data['data']["amphur_id"]."'");
            $this->db->order_by('district_name');
            $row = $this->db->get()->result_array();
            $arr_data['district'] = $row;
        }else{
            $arr_data['district'] = array();
        }

        if(@$arr_data['data']["c_province_id"]!=''){
            $this->db->select('amphur_id, amphur_name');
            $this->db->from('coop_amphur');
            $this->db->where("province_id = '".@$arr_data['data']["c_province_id"]."'");
            $this->db->order_by('amphur_name');
            $row = $this->db->get()->result_array();
            $arr_data['c_amphur'] = $row;
        }else{
            $arr_data['c_amphur'] = array();
        }

        if(@$arr_data['data']["c_amphur_id"]!=''){
            $this->db->select('district_id, district_name');
            $this->db->from('coop_district');
            $this->db->where("amphur_id = '".@$arr_data['data']["c_amphur_id"]."'");
            $this->db->order_by('district_name');
            $row = $this->db->get()->result_array();
            $arr_data['c_district'] = $row;
        }else{
            $arr_data['c_district'] = array();
        }

        if(@$arr_data['data']["m_province_id"]!=''){
            $this->db->select('amphur_id, amphur_name');
            $this->db->from('coop_amphur');
            $this->db->where("province_id = '".@$arr_data['data']["m_province_id"]."'");
            $this->db->order_by('amphur_name');
            $row = $this->db->get()->result_array();
            $arr_data['m_amphur'] = $row;
        }else{
            $arr_data['m_amphur'] = array();
        }

        if(@$arr_data['data']["m_amphur_id"]!=''){
            $this->db->select('district_id, district_name');
            $this->db->from('coop_district');
            $this->db->where("amphur_id = '".@$arr_data['data']["m_amphur_id"]."'");
            $this->db->order_by('district_name');
            $row = $this->db->get()->result_array();
            $arr_data['m_district'] = $row;
        }else{
            $arr_data['m_district'] = array();
        }


        $this->db->select('*');
        $this->db->from('coop_member_cremation');
        $this->db->where(array('ref_member_id' => $member_id, 'mem_type_id' => '2'));
        $arr_data['associates'] = $this->db->get()->result_array();

		//ประเภทฌาปนกิจสงเคราะห์
		$this->db->select(array('*'));
		$this->db->from('coop_cremation_data');
		$row = $this->db->get()->result_array();
		$arr_data['cremation_type'] = @$row;
		//print_r($this->db->last_query());exit;			
		//สถานะ
		$arr_data['cremation_status'] = array('0'=>'รอการอนุมัติ', '1'=>'อนุมัติ', '2'=>'ขอยกเลิก', '3'=>'อนุมัติยกเลิก', '4'=>'ชำระเงินแล้ว', '5'=>'ไม่อนุมัติ', '6'=>'ชำระเงินค่าสมัครแล้ว', '7'=>'ขอรับเงิน', '8'=>'อนุมัติขอรับเงิน', '9'=>'ลาออก', '10'=>'ขอลาออก');
		$arr_data['status_bg_color'] = array('0'=>'#ff9800', '1'=>'#467542', '2'=>'#d50000', '3'=>'#d50000', '4'=>'#467542', '5'=>'#d50000', '6'=>'#467542', '7'=>'#467542', '8'=>'#467542');
		
		$this->db->select('bank_id, bank_name');
		$this->db->from('coop_bank');
		$row = $this->db->get()->result_array();
		$arr_data['bank'] = $row;
		
		$this->libraries->template('cremation/cremation_request',$arr_data);
	}

	function relation($rel_id){
	    if($rel_id == 1){
            return 'คู่สมรส';
        }else if($rel_id == 2){
            return 'บุตร/บุตรบุญธรรม';
        }else if($rel_id == 3){
            return 'บิดา';
        }else if($rel_id == 4){
            return 'มารดา';
        }else{
            return 'ไมระบุ';
        }
    }

	function cremation_request_save(){
		$data_insert = array();
		$data = $this->input->post();

		if($data['mem_type_id'] == 2){

		  $filter = array('mem_type_id' ,'assoc_firstname', 'assoc_lastname' ,'assoc_birthday' ,'id_card' ,'relation' ,'ref_member_id' ,'occupation' ,'position' ,'workplace' ,'office_phone' ,'addr_no' ,'addr_moo' ,'addr_soi' ,'addr_street' ,'province_id' ,'amphur_id' ,'district_id' ,'zip_code' ,'phone' ,'mobile' ,'email' ,'cur_addr_no' ,'cur_addr_moo' ,'cur_addr_soi' ,'cur_addr_street' ,'cur_province_id' ,'cur_amphur_id' ,'cur_district_id' ,'cur_zip_code', 'prename_id');


		  $data['assoc_birthday'] = empty($data['assoc_birthday']) ? "" : date('Y-m-d', strtotime(str_replace('/', '-', $data['assoc_birthday']).' -543 year'));

		  $data_member = array_intersect_key($data, array_flip($filter));

          $data_member['create_date'] = date('Y-m-d H:i:s');
          $data_member['status'] = '0';
          $this->db->insert('coop_member_cremation', $data_member);

          $id = $this->db->insert_id();
          $member_crenation_id = str_pad($id,6, '0',STR_PAD_LEFT);

          $this->db->where('id' , $id);
          $this->db->set('member_cremation_id', $member_crenation_id);
          $this->db->update('coop_member_cremation');

        }else{

            $filter = array('cremation_request_id' ,'cremation_detail_id' ,'member_id' ,'member_name' ,'birthday' ,'age' ,'apply_date' ,'apply_age' ,'retry_date' ,'retry_status' ,'mem_type_id' );

		    //do somthing
            $this->db->select('*')->from('coop_mem_apply')->where('member_id', $data['member_id']);
            $member = $this->db->get()->row();

            $data_insert = array(
                'mem_type_id'       => $data['mem_type_id'],
                'prename_id'        => $member->prename_id,
                'assoc_firstname'   => $member->firstname_th,
                'assoc_lastname'    => $member->lastname_th,
                'assoc_birthday'    => $member->birthday ,
                'id_card'           => $member->id_card,
                'ref_member_id'     => $member->member_id,
                'create_date'       => date('Y-m-d H:i:s'),
                'status'            => '0'
            );

            $this->db->insert('coop_member_cremation', $data_insert);
            unset($data_insert);

            $id = $this->db->insert_id();
            $member_crenation_id = str_pad($id,6, '0',STR_PAD_LEFT);

            $this->db->where('id' , $id);
            $this->db->set('member_cremation_id', $member_crenation_id);
            $this->db->update('coop_member_cremation');
        }
		
		$table = "coop_cremation_request";
		$id_edit = @$data["cremation_request_id"] ;		
		$member_id = $member_crenation_id;
		
		$year_now = (date('Y')+543);
		$this->db->select(array('MAX(runno) AS last_run'));
		$this->db->from('coop_cremation_request');
		$this->db->where("yy = '{$year_now}'");
		$rs = $this->db->get()->result_array();
		$row = @$rs[0];

		$this->db->select('*')
            ->from('coop_setting_cremation_detail')
            ->where('start_date <= now()')
            ->order_by('start_date DESC, cremation_detail_id ASC')
            ->limit(1);

		//Cremation ID
		$setting = $this->db->get()->row();
		$setting_cremation_id = $setting->cremation_detail_id;

		
		$run_now = 0;
		if(empty($id_edit)){
			if(@$row['last_run']){
				$run_now = $row['last_run']+1;	
			}else{
				$run_now = 1;
			}
			$runno = sprintf("%07d",$run_now);
			$cremation_no = $runno.'/'.$year_now;
			$data_insert['cremation_no'] = @$cremation_no;
			$data_insert['runno'] = @$run_now;
		}
		
		$data_insert['member_id'] = @$data['member_id'];	
		$data_insert['member_cremation_id'] = $member_crenation_id;
		$data_insert['yy'] = @$year_now;
		$data_insert['cremation_type_id'] = 2;
		$data_insert['cremation_detail_id'] = $setting_cremation_id;
		$data_insert['cremation_status'] = '0';

		$data_insert['user_id'] = $_SESSION['USER_ID'];
		$data_insert['user_name'] = $_SESSION['USER_NAME'];
		$data_insert['updatetime'] = date('Y-m-d H:i:s');
		
		if($id_edit!=''){			
			$this->db->where('cremation_request_id', $id_edit);
			$this->db->update($table, $data_insert);
			$request_id = $id_edit;
		}else{			
			$data_insert['createdatetime'] = date('Y-m-d H:i:s');
			$this->db->insert($table, $data_insert);
			$request_id = $this->db->insert_id();
		}
		
		$output_dir = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/cremation_request/";
		//echo $output_dir;
		if(!@mkdir($output_dir,0,true)){
		   @chmod($output_dir, 0777);
		}else{
		   @chmod($output_dir, 0777);
		}
		if($_FILES['cremation_request_file']['name'][0]!=''){
			foreach($_FILES['cremation_request_file']['name'] as $key_file => $value_file ){
				$fileName=array();
				$list_dir = array(); 
				$cdir = scandir($output_dir); 
				foreach ($cdir as $key => $value) { 
				   if (!in_array($value,array(".",".."))) { 
					  if (@is_dir(@$dir . DIRECTORY_SEPARATOR . @$value)){ 
						$list_dir[$value] = dirToArray(@$dir . DIRECTORY_SEPARATOR . $value); 
					  }else{
						if(substr($value,0,8) == date('Ymd')){
						$list_dir[] = $value;
						}
					  } 
				   } 
				}
				$explode_arr=array();
				foreach($list_dir as $key => $value){
					$task = explode('.',$value);
					$task2 = explode('_',$task[0]);
					$explode_arr[] = $task2[1];
				}
				$max_run_num = sprintf("%04d",count($explode_arr)+1);
				$explode_old_file = explode('.',$_FILES["cremation_request_file"]["name"][$key_file]);
				$new_file_name = date('Ymd')."_".$max_run_num.".".$explode_old_file[(count($explode_old_file)-1)];
				if(!is_array($_FILES["cremation_request_file"]["name"][$key_file]))
				{
						$fileName['file_name'] = $new_file_name;
						$fileName['file_type'] = $_FILES["cremation_request_file"]["type"][$key_file];
						$fileName['file_old_name'] = $_FILES["cremation_request_file"]["name"][$key_file];
						$fileName['file_path'] = $output_dir.$fileName['file_name'];
						move_uploaded_file($_FILES["cremation_request_file"]["tmp_name"][$key_file],$output_dir.$fileName['file_name']);
						
						$data_insert = array();
						$data_insert['cremation_request_id'] = @$request_id;
						$data_insert['file_name'] = @$fileName['file_name'];
						$data_insert['file_type'] = @$fileName['file_type'];
						$data_insert['file_old_name'] = @$fileName['file_old_name'];
						$data_insert['file_path'] = @$fileName['file_path'];
						$data_insert['cremation_type'] = 'request';
						//add coop_cremation_file_attach
						$this->db->insert('coop_cremation_file_attach', $data_insert);
				}
			}
		}

		$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
		echo"<script> document.location.href='".PROJECTPATH."/cremation/cremation_request?id={$member_id}' </script>";
		exit;
	}
	
	function del_coop_data(){	
		$table = @$_POST['table'];
		$table_sub = @$_POST['table_sub'];
		$id = @$_POST['id'];
		$field = @$_POST['field'];


		if (!empty($table_sub)) {
			$this->db->where($field, $id );
			$this->db->delete($table_sub);	
        }

		$this->db->where($field, $id );
		$this->db->delete($table);
		$this->center_function->toast("ลบเรียบร้อยแล้ว");
		echo true;
		
	}
	
	function get_search_member(){
		$where = "
		 	(member_id LIKE '%".$this->input->post('search_text')."%'
		 	OR firstname_th LIKE '%".$this->input->post('search_text')."%'
			OR lastname_th LIKE '%".$this->input->post('search_text')."%') 
			AND member_status = '1'
		";
		$this->db->select(array('id','member_id','firstname_th','lastname_th','apply_date','mem_apply_id'));
		$this->db->from('coop_mem_apply');
		$this->db->where($where);
		$this->db->order_by('mem_apply_id DESC');
		$row = $this->db->get()->result_array();
		$arr_data['data'] = $row;
		$arr_data['form_target'] = $this->input->post('form_target');
		//echo"<pre>";print_r($arr_data['data']);exit;
		$this->load->view('cremation/get_search_member',$arr_data);
	}
	
	function get_cremation_type(){
		$arr_data = array();
		$arr_data['month_arr'] = $this->month_arr;
		$today = date('Y-m-d');
		//
		$id = @$_POST['id'];
		$this->db->select(array('*'));
		$this->db->from('coop_cremation_data');
		$this->db->join("coop_cremation_data_detail","coop_cremation_data.cremation_id = coop_cremation_data_detail.cremation_id","left");
		$this->db->where("coop_cremation_data_detail.cremation_id = {$id} AND coop_cremation_data_detail.start_date <= '{$today}'");
		$this->db->order_by('coop_cremation_data_detail.start_date DESC');
		$this->db->limit(1);
		$rs = $this->db->get()->result_array();
		$row_detail = @$rs[0];
		$cremation_detail_id = $row_detail['cremation_detail_id'];
		$row_detail['start_date'] = (!empty($row_detail['start_date'])?$this->center_function->mydate2date($row_detail['start_date']):'');
		
		$arr_data['row']  = $row_detail;
			
		$this->db->select(array('coop_cremation_data_detail_mem_type.*','coop_mem_type.mem_type_name'));
		$this->db->from('coop_cremation_data_detail_mem_type');
		$this->db->join("coop_mem_type","coop_mem_type.mem_type_id = coop_cremation_data_detail_mem_type.mem_type_id","left");
		$this->db->where("coop_cremation_data_detail_mem_type.cremation_detail_id = '".@$cremation_detail_id."' AND coop_mem_type.mem_type_status = '1'");
		$row = $this->db->get()->result_array();
		if(!empty($row)){
			foreach($row as $key => $value){
				$arr_data['row_mem_type'][$value['member_type_number']] = $value;
			}
		}
		
		$this->db->select(array('*'));
		$this->db->from('coop_cremation_data_detail_maintenance_fee');
		$this->db->where("cremation_detail_id = '".@$cremation_detail_id."'");
		$row = $this->db->get()->result_array();
		if(!empty($row)){
			foreach($row as $key => $value){
				$row_maintenance_fee[$value['maintenance_fee_number']] = $value;
			}
		}
		if($arr_data['row']['maintenance_fee_type']=='1'){
			$arr_data['row_maintenance_fee'] = $row_maintenance_fee;
		}else if($arr_data['row']['maintenance_fee_type']=='2'){
			$arr_data['row_maintenance_fee_2'] = $row_maintenance_fee;
		}
		
		$this->db->select(array('*'));
		$this->db->from('coop_mem_type');
		$this->db->where("mem_type_status = '1'");
		$row = $this->db->get()->result_array();
		$arr_data['mem_type'] = @$row;
		
		$this->load->view('cremation/cremation_type',$arr_data);
	}
	
	public function get_data_cremation_type(){
		$arr_data = array();	
		$id = @$_POST['id'];
		$member_id = @$_POST['member_id'];
		$cremation_request_id = @$_POST['cremation_request_id'];
		$today = date('Y-m-d');
		
		$id = @$_POST['id'];
		$this->db->select(array('*'));
		$this->db->from('coop_cremation_data');
		$this->db->join("coop_cremation_data_detail","coop_cremation_data.cremation_id = coop_cremation_data_detail.cremation_id","left");
		$this->db->where("coop_cremation_data_detail.cremation_id = {$id} AND coop_cremation_data_detail.start_date <= '{$today}'");
		$this->db->order_by('coop_cremation_data_detail.start_date DESC');
		$this->db->limit(1);
		$rs = $this->db->get()->result_array();
		$row = @$rs[0];
		$arr_data = $row;		
		
		$arr_data['cremation_type_name'] = $row['cremation_name'].'('.$row['cremation_name_short'].')';

		$this->db->select(array('cremation_type_id'));
		$this->db->from('coop_cremation_request');
		$this->db->where("member_id = '{$member_id}' AND cremation_request_id <> '{$cremation_request_id}'");
		$rs_request = $this->db->get()->result_array();
		$row_request = @$rs_request;
		$arr_type_request = array();
		foreach($row_request AS $key=>$val){
			$arr_type_request[$val['cremation_type_id']] = $val['cremation_type_id'];
		}

		if(in_array($id,$arr_type_request)){
			$arr_data['message_alert'] = '1';
		}else{
			$arr_data['message_alert'] = '0';
		}
		echo json_encode($arr_data);
		exit();
	}
	
	function get_cremation_request(){
		$id = @$_POST['id'];
		$today = date('Y-m-d');
		
		$this->db->select(array('coop_cremation_request.*','coop_mem_apply.*','coop_cremation_data_detail.cremation_detail_id'));
		$this->db->from('coop_cremation_request');
		$this->db->join("coop_cremation_data_detail","coop_cremation_request.cremation_type_id = coop_cremation_data_detail.cremation_id","left");
		$this->db->join("coop_mem_apply","coop_mem_apply.member_id = coop_cremation_request.member_id","left");
		$this->db->where("coop_cremation_request.cremation_request_id = {$id} AND coop_cremation_data_detail.start_date <= '{$today}'");
		$this->db->order_by('coop_cremation_data_detail.start_date DESC');
		$this->db->limit(1);
		$rs = $this->db->get()->result_array();
		$row = @$rs[0];
		$row['age'] = (!empty($row['birthday']))?$this->center_function->diff_year($row['birthday'],date('Y-m-d')):'';
		$row['apply_age'] = (!empty($row['apply_date']))?$this->center_function->diff_year($row['apply_date'],date('Y-m-d')):'';
		$row['birthday'] = ((!empty($row['birthday']) && $row['birthday'] != '0000-00-00')?$this->center_function->mydate2date($row['birthday']):'');		
		$row['apply_date'] = ((!empty($row['apply_date']) && $row['apply_date'] != '0000-00-00')?$this->center_function->mydate2date($row['apply_date']):'');		
		$row['retry_date'] = ((!empty($row['retry_date']) && $row['retry_date'] != '0000-00-00')?$this->center_function->mydate2date($row['retry_date']):'');
		$row['retry_status'] = (empty($row['retry_status'])?'ยังไม่เกษียณ':'เกษียณแล้ว');
		
		$this->db->select(array('*'));
		$this->db->from("coop_cremation_file_attach");
		$this->db->where("cremation_request_id = '".$id."' AND cremation_type = 'request'");
		$rs_file = $this->db->get()->result_array();
		@$row['coop_file_attach'] = array();
		if(!empty($rs_file)){
			foreach(@$rs_file as $key => $row_file){
				@$row['coop_file_attach'][] = @$row_file;
			}
		}
		
		echo json_encode($row);
		exit();
	}
	
	function ajax_delete_file_attach(){
		$this->db->select(array('*'));
		$this->db->from("coop_cremation_file_attach");
		$this->db->where("id = '".@$_POST['id']."'");
		$rs = $this->db->get()->result_array();
		$row = @$rs[0];

		//$attach_path = "../uploads/loan_attach/";
		$attach_path = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/cremation_request/";
		$file = @$attach_path.@$row['file_name'];
		unlink($file);

		//$this->db->where("id", @$_POST['id'] );
		$this->db->where("id = '{$_POST['id']}' AND cremation_type = '{$_POST['cremation_type']}' ");
		$this->db->delete("coop_cremation_file_attach");	
		if(@$rs){
			echo "success";
		}else{
			echo "error";
		}
		exit;
	}
	
	public function cremation_approve(){
		if (@$_GET['status_to']) {

			$data_insert = array();
			$data_mem_cremation = array();
			if($_GET['status_to'] == '6') {
				//Generate member_cremation_id if approve
				$year_now = (date('Y')+543);
				$yy = substr($year_now, -2);
				$member_cremation_id = $yy."100001";
				$lastest_member = $this->db->select("member_cremation_id")->from("coop_member_cremation")->order_by("member_cremation_id DESC")->get()->row();
				if(!empty($lastest_member)) {
					$id = (int)substr($lastest_member->member_cremation_id,-6);
					$member_cremation_id = $yy.sprintf("%06d", $id + 1);
				}

				$data_mem_cremation['member_cremation_id'] = $member_cremation_id;
				$data_insert['member_cremation_id'] = $member_cremation_id;

				$request = $this->db->select("*")->from("coop_cremation_request")->where("cremation_request_id = '".$_GET["id"]."'")->get()->row();
				$advance_pay_update = array();
				$advance_pay_update["member_cremation_id"] = $member_cremation_id;
				$this->db->where('cremation_request_id', $_GET['id']);
				$this->db->update('coop_cremation_advance_payment_transaction', $advance_pay_update);

				$this->db->where('member_cremation_raw_id', $request->member_cremation_raw_id);
				$this->db->update('coop_cremation_advance_payment', $advance_pay_update);
			}

			$data_insert['cremation_status'] = @$_GET['status_to'];
			$this->db->where('cremation_request_id', @$_GET['id']);
			$this->db->update('coop_cremation_request', $data_insert);

			$this->db->select('member_cremation_raw_id')
                ->from('coop_cremation_request')
                ->where('cremation_request_id', @$_GET['id']);
			$res = $this->db->get()->row();

			$data_mem_cremation['approve_status'] = $_GET['status_to'] == '6' ? '1' : '2';
            $data_mem_cremation['approve_date'] = date('Y-m-d H:i:s');
			$data_mem_cremation['status'] = $_GET['status_to'] == '6' ? '0' : '2' ;
			$data_mem_cremation['type'] = 1;
            $this->db->where('id', $res->member_cremation_raw_id);
            $this->db->update("coop_member_cremation", $data_mem_cremation);

			$this->center_function->toast('บันทึกข้อมูลเรียบร้อยแล้ว');
			echo "<script> document.location.href='".base_url(PROJECTPATH.'/cremation/cremation_approve')."' </script>";
		}
		$arr_data = array();

		$x=0;
		$join_arr = array();
		$join_arr[$x]['table'] = 'coop_member_cremation';
		$join_arr[$x]['condition'] = 'coop_member_cremation.id = coop_cremation_request.member_cremation_raw_id';
		$join_arr[$x]['type'] = 'left';
		$x++;
		$join_arr[$x]['table'] = 'coop_user';
		$join_arr[$x]['condition'] = 'coop_cremation_request.user_id = coop_user.user_id';
		$join_arr[$x]['type'] = 'left';
		$x++;
		$join_arr[$x]['table'] = 'coop_cremation_data';
		$join_arr[$x]['condition'] = 'coop_cremation_request.cremation_type_id = coop_cremation_data.cremation_id';
		$join_arr[$x]['type'] = 'left';
		
		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select('coop_cremation_request.*, coop_member_cremation.assoc_firstname, coop_member_cremation.assoc_lastname, coop_user.user_name, coop_member_cremation.mem_type_id');
		$this->paginater_all->main_table('coop_cremation_request');
		$this->paginater_all->where("cremation_status IN('1','5')");
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(20);
		$this->paginater_all->page_link_limit(20);
		$this->paginater_all->order_by('createdatetime DESC');
		$this->paginater_all->join_arr($join_arr);
		$row = $this->paginater_all->paginater_process();
		//echo"<pre>";print_r($row);exit;
		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit']);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		$i = $row['page_start'];

		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];
		$arr_data['i'] = $i;

		$this->libraries->template('cremation/cremation_approve',$arr_data);
	}

	public function cremation_multi_approve(){
	    if($this->input->post()){

			$data = $this->input->post("data");

	        if($data[0]['status'] == '5') {
	            $delete = [];

                foreach ($data as $key => $value) {
                    $delete[] = $value['id'];
                }

                $this->db->where_in('cremation_request_id', $delete);
                $this->db->delete('coop_cremation_request');

            }else{
                $update = [];

                $req_id = array_map(function($val){ return $val['id']; }, $data);

                $this->db->select('cremation_request_id, member_cremation_id, member_cremation_raw_id')
                    ->from('coop_cremation_request')
                    ->where_in('cremation_request_id', $req_id);
                $req = $this->db->get()->result_array();

                $req_data = array_map(function($val){ return array($val['cremation_request_id'] => $val['member_cremation_raw_id']); }, $req);
				$id = 100001;
				$year_now = (date('Y')+543);
				$yy = substr($year_now, -2);
				$lastest_member = $this->db->select("member_cremation_id")->from("coop_member_cremation")->order_by("member_cremation_id DESC")->get()->row();
				if(!empty($lastest_member)) {
					$id = (int)substr($lastest_member->member_cremation_id,-6);
				}

                $cremation = [];
                $index = 0;
                foreach ($data as $key => $value) {
                    $update[] = array('cremation_request_id' => $value['id'], 'cremation_status' => $value['status']);

					$member_cremation_id = $yy.sprintf("%06d", $id + $key + 1);

                    $cremation[$index]['member_cremation_id'] = $member_cremation_id;
                    $cremation[$index]['approve_status'] = '1';
                    $cremation[$index]['approve_date'] = date('Y-m-d H:i:s');
					$cremation[$index]['id'] = $req_data[0][$value['id']];
					
					$request = $this->db->select("*")->from("coop_cremation_request")->where("cremation_request_id = '".$value["id"]."'")->get()->row();
					$advance_pay_update = array();
					$advance_pay_update["member_cremation_id"] = $member_cremation_id;
					$this->db->where('cremation_request_id', $value["id"]);
					$this->db->update('coop_cremation_advance_payment_transaction', $advance_pay_update);
	
					$this->db->where('member_cremation_raw_id', $request->member_cremation_raw_id);
					$this->db->update('coop_cremation_advance_payment', $advance_pay_update);
					$index++;
                }

                $this->db->update_batch('coop_cremation_request', $update, 'cremation_request_id');
                $this->db->update_batch('coop_member_cremation', $cremation, 'id');
            }

	        header('content-type: application/json; charset: utf-8');
            echo json_encode(array('status' => true, 'msg' => 'success', 'data' => json_encode($update)));
            exit;

        }
        header('content-type: application/json; charset: utf-8');
        echo json_encode(array('status' => false, 'msg' => 'something failure.'));
        exit;
    }
	
	public function cremation_pay(){
		$arr_data = array();

		$x=0;
		$join_arr = array();
		$join_arr[$x]['table'] = 'coop_member_cremation';
		$join_arr[$x]['condition'] = 'coop_member_cremation.id = coop_cremation_request.member_cremation_raw_id';
		$join_arr[$x]['type'] = 'left';
		$x++;
		$join_arr[$x]['table'] = 'coop_user';
		$join_arr[$x]['condition'] = 'coop_cremation_request.user_id = coop_user.user_id';
		$join_arr[$x]['type'] = 'left';
		$x++;
		$join_arr[$x]['table'] = 'coop_cremation_transfer';
		$join_arr[$x]['condition'] = 'coop_cremation_request.cremation_request_id = coop_cremation_transfer.cremation_request_id';
		$join_arr[$x]['type'] = 'left';		
		$x++;
		$join_arr[$x]['table'] = 'coop_cremation_data';
		$join_arr[$x]['condition'] = 'coop_cremation_request.cremation_type_id = coop_cremation_data.cremation_id';
		$join_arr[$x]['type'] = 'left';
		
		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select('coop_cremation_request.*, coop_member_cremation.assoc_firstname as firstname_th, coop_member_cremation.assoc_lastname as lastname_th, coop_user.user_name,coop_cremation_data.cremation_name_short');
		$this->paginater_all->main_table('coop_cremation_request');
		$this->paginater_all->where("cremation_status IN ('0') OR (coop_cremation_request.receipt_id is not null AND cremation_status IN ('1','6'))");
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(10);
		$this->paginater_all->page_link_limit(20);
		$this->paginater_all->order_by('coop_cremation_request.createdatetime DESC');
		$this->paginater_all->join_arr($join_arr);
		$row = $this->paginater_all->paginater_process();
		//echo"<pre>";print_r($row);exit;
		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit']);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		$i = $row['page_start'];

		//print_r($this->db->last_query());exit;

		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];
		$arr_data['i'] = $i;
		
		$this->db->select('bank_id, bank_name');
		$this->db->from('coop_bank');
		$row = $this->db->get()->result_array();
		$arr_data['bank'] = $row;
		
		$this->libraries->template('cremation/cremation_pay',$arr_data);
	}
	
	public function get_cremation_pay(){
		$arr_data = array();	
		$id = @$_POST['id'];
		$today = date('Y-m-d');
		
		$this->db->select(array('*'));
		$this->db->from('coop_user');
		$rs_user = $this->db->get()->result_array();
		$rs_user = @$rs_user;
		$arr_user = array();
		foreach($rs_user AS $row_user){
			$arr_user[$row_user['user_id']] = $row_user['user_name'];
		}
		
		$this->db->select(array(
			'coop_cremation_request.cremation_no',
			'coop_cremation_request.member_id',
			'coop_cremation_request.cremation_pay_amount',
			'coop_cremation_request.user_id AS admin_request',
			'coop_cremation_request.receipt_id',
			'coop_cremation_request.cremation_detail_id',
			'coop_cremation_data.cremation_id',
			'coop_cremation_data.cremation_name',
			'coop_cremation_data.cremation_name_short',
			'coop_cremation_data_detail.maintenance_fee'
		));
		$this->db->from('coop_cremation_request');
		$this->db->join("coop_cremation_data","coop_cremation_request.cremation_type_id = coop_cremation_data.cremation_id","left");
		$this->db->join("coop_cremation_data_detail","coop_cremation_data_detail.cremation_id = coop_cremation_data.cremation_id","left");
		$this->db->where("coop_cremation_request.cremation_request_id = {$id} AND coop_cremation_data_detail.start_date <= '{$today}'");
		$this->db->order_by('coop_cremation_data_detail.start_date DESC');
		$this->db->limit(1);
		$rs = $this->db->get()->result_array();
		$row = @$rs[0];
		$arr_data = $row;

		$this->db->select('(application_fee + maintenance_fee + advance_pay) as pay_amount')
            ->from('coop_setting_cremation_detail')
            ->where("cremation_detail_id", $row['cremation_detail_id'])
            ->order_by('start_date DESC, cremation_detail_id ASC')->limit('1');
		$setting = $this->db->get()->row();

		$arr_data['cremation_type_name'] = $row['cremation_name'].'('.$row['cremation_name_short'].')';
		//$arr_data['cremation_pay_amount'] = (empty($row['cremation_pay_amount']) ? (int)$setting->application_fee : $row['cremation_pay_amount']);
		$arr_data['cremation_pay_amount'] =  (int)$setting->pay_amount;

		echo json_encode($arr_data);
		exit();
	}
	
	function cremation_pay_save(){
		$data_insert = array();
		$data_post = $this->input->post();
		$process_timestamp = date('Y-m-d H:i:s');

		//Genarate cremation finance month receipt
		//Generate Receipt identification
		$yymm = (date("Y")+543).date("m");
		$this->db->select(array('*'));
		$this->db->from('coop_cremation_receipt');
		$this->db->where("receipt_id LIKE '".$yymm."%'");
		$this->db->order_by("receipt_id DESC");
		$this->db->limit(1);
		$row_receipt = $this->db->get()->result_array();
		$row_receipt = $row_receipt[0];

		if($row_receipt['receipt_id'] != '') {
			$id = (int) substr($row_receipt["receipt_id"], 6);
			$receipt_number = $yymm.sprintf("%06d", $id + 1);
		}else {
			$receipt_number = $yymm."000001";
		}

		$cremation_2_detail = $this->db->select("*")
										->from("coop_setting_cremation_detail")
										->where("start_date <= '".date("Y-m-d")."' AND cremation_id = '2'")
										->order_by("start_date")
										->limit(1)
										->get()->result_array();
		$cremation_2_detail = $cremation_2_detail[0];

		$receipt_insert = array();
		$receipt_insert["receipt_id"] = $receipt_number;
		$receipt_insert["amount"] = $cremation_2_detail['application_fee'];
		$receipt_insert["detail"] = "เงินค่าสมัคร";
		$receipt_insert["status"] = 1;
		$receipt_insert["user_id"] = $_SESSION['USER_ID'];
		$receipt_insert["created_at"] = $process_timestamp;
		$receipt_insert["updated_at"] = $process_timestamp;
		$this->db->insert('coop_cremation_receipt', $receipt_insert);

		$receipt_insert = array();
		$receipt_insert["receipt_id"] = $receipt_number;
		$receipt_insert["amount"] = $cremation_2_detail['maintenance_fee'];
		$receipt_insert["detail"] = "ค่าบำรุงสมาคมฯ";
		$receipt_insert["status"] = 1;
		$receipt_insert["user_id"] = $_SESSION['USER_ID'];
		$receipt_insert["created_at"] = $process_timestamp;
		$receipt_insert["updated_at"] = $process_timestamp;
		$this->db->insert('coop_cremation_receipt', $receipt_insert);

		$receipt_insert = array();
		$receipt_insert["receipt_id"] = $receipt_number;
		$receipt_insert["amount"] = $cremation_2_detail['advance_pay'];
		$receipt_insert["detail"] = "เงินสงเคราะห์ล่วงหน้า";
		$receipt_insert["status"] = 1;
		$receipt_insert["user_id"] = $_SESSION['USER_ID'];
		$receipt_insert["created_at"] = $process_timestamp;
		$receipt_insert["updated_at"] = $process_timestamp;
		$this->db->insert('coop_cremation_receipt', $receipt_insert);

		$cremation_request_id = @$data_post["cremation_request_id"];	
		
		$data_insert['cremation_pay_amount']= @$data_post["cremation_pay_amount"];			
		$data_insert['cremation_pay_date'] = $process_timestamp;
		$data_insert['cremation_status'] = '1';
		$data_insert['user_id_pay'] = $_SESSION['USER_ID'];
		$data_insert['receipt_id'] = $receipt_number;
		$this->db->where('cremation_request_id', @$cremation_request_id);
		$this->db->update('coop_cremation_request', $data_insert);

		$this->cremation_advance_pay($cremation_request_id);
		
		$data_insert = array();
		$data_insert['receipt_id'] = $receipt_number;
		$data_insert['member_id'] = $data_post['member_id'];
		$total = @$data_post['cremation_pay_amount'];

		$this->db->select('member_cremation_raw_id')
            ->from('coop_cremation_request')
            ->where('cremation_request_id', $cremation_request_id);
		$member_cremation = $this->db->get()->row();

		if(!empty($member_cremation)) {
            $data_cremation = [];
            $data_cremation['status'] = '1';
            $this->db->where('id', $member_cremation->member_cremation_raw_id);
            $this->db->update('coop_member_cremation', $data_cremation);
        }

		$this->db->select('account_chart_id');
		$this->db->from('coop_account_match');
		$this->db->where("match_id = '".@$account_list."' AND match_type = 'account_list'");
		$row = $this->db->get()->result_array();
		$row_account_chart = @$row[0];
		$account_chart_id = @$row_account_chart['account_chart_id'];
			
			
		@$data['coop_account_detail'][$key]['account_type'] = 'credit';
		@$data['coop_account_detail'][$key]['account_amount'] = number_format($data_post['cremation_pay_amount'],2,'.','');
		@$data['coop_account_detail'][$key]['account_chart_id'] = $account_chart_id;
		
		$this->account_transaction->account_process($data);

		echo"<script> window.open('".PROJECTPATH."/cremation/receipt_form_pdf/".$receipt_number."','_blank') </script>";
		echo "<script> document.location.href='".base_url(PROJECTPATH.'/cremation/cremation_pay')."' </script>";

		exit;
	}

	function cremation_advance_pay($cremation_request_id){
		$process_timestamp = date('Y-m-d H:i:s');

        $this->db->select('member_cremation_id, member_id, member_cremation_raw_id')->from('coop_cremation_request')->where('cremation_request_id', $cremation_request_id);
        $member_cremation = $this->db->get()->row();

        $this->db->select('advance_pay')
            ->from('coop_setting_cremation_detail')
            ->where("start_date <= ", date('Y-m-d'))
            ->order_by('start_date DESC, cremation_detail_id ASC')->limit('1');
        $setting = $this->db->get()->row();

        if(!empty($member_cremation->member_cremation_id)) $advance_pay['member_cremation_id'] = $member_cremation->member_cremation_id;
        $advance_pay['member_cremation_raw_id'] = $member_cremation->member_cremation_raw_id;
        $advance_pay['ref_member_id'] = $member_cremation->member_id;
        $advance_pay['adv_payment_balance'] = $setting->advance_pay;
        $advance_pay['lastpayment'] = $process_timestamp;
        $advance_pay['createdatetime'] = $process_timestamp;
        $advance_pay['adv_status'] = 1;

		$this->db->insert('coop_cremation_advance_payment', $advance_pay);

		//add transaction history
		$insert_data = array();
		$insert_data["cremation_request_id"] = $cremation_request_id;
		if(!empty($member_cremation->member_cremation_id)) $insert_data["member_cremation_id"] = $member_cremation->member_cremation_id;
		$insert_data["type"] = "CRQP";
		$insert_data["amount"] = $setting->advance_pay;
		$insert_data["total"] = $setting->advance_pay;
		$insert_data["status"] = 1;
		$insert_data["created_at"] = $process_timestamp;
		$insert_data["updated_at"] = $process_timestamp;
		$this->db->insert('coop_cremation_advance_payment_transaction', $insert_data);
    }

	function cremation_receipt_pdf(){
		$arr_data = array();
		$id = @$_GET['id'];
		$this->db->select(array('coop_cremation_request.receipt_id'));
		$this->db->from('coop_cremation_request');
		$this->db->where("coop_cremation_request.cremation_request_id = {$id}");
		$rs = $this->db->get()->result_array();
		$row = @$rs[0];
		$arr_data['receipt_id'] = $row['receipt_id'];
		
		$this->load->view('cremation/cremation_receipt_pdf',$arr_data);
	}

    function create_reqest_cremation_money(){

        $cremation_request_id = $this->input->post('id');

        if(empty($cremation_request_id)){
            header('content-type: application/json; charset: utf-8;');
            echo json_encode(array('status' => false, 'msg' => 'request failure'));
            exit;
        }

        //cremation member
        $this->db->select('t1.cremation_request_id, t1.member_cremation_id, t1.member_id, t1.cremation_type_id, t1.cremation_detail_id')
            ->from('coop_cremation_request t1')
            ->where('t1.cremation_request_id', $cremation_request_id);
        $member_cremation = $this->db->get()->row();

        //setting
        $setting = $this->get_setting_cremation();

        $this->db->select('t1.member_cremation_id')
            ->from('coop_member_cremation t1')
            ->join('coop_cremation_request t2', 't1.member_cremation_id=t2.member_cremation_id', 'inner')
            ->where(array('t1.status' => '1', 't2.user_id_pay' => 1));
        $count_all = $this->db->get()->num_rows();

        $application_fee = $setting->application_fee;

        $recevive_amount =  ($count_all - 1) * $setting->application_fee;
        $action_fee = round(($recevive_amount * $setting->action_fee_percent)/100, 2);
        $total = $recevive_amount - $action_fee;

        $data_insert['cremation_request_id'] = $member_cremation->cremation_request_id;
        $data_insert['member_cremation_id'] = $member_cremation->member_cremation_id;
        $data_insert['member_id'] = $member_cremation->member_id;
        $data_insert['cremation_type_id'] = $member_cremation->cremation_type_id;
        $data_insert['cremation_detail_id'] = $member_cremation->cremation_detail_id;
        $data_insert['cremation_receive_amount'] = $recevive_amount;
        $data_insert['action_fee_percent'] = $action_fee;
        $data_insert['cremation_balance_amount'] = $total;
        $data_insert['cremation_receive_status'] = '0';
        $data_insert['finance_month_status'] = '0';
        $data_insert['admin_id'] = $_SESSION['USER_ID'];
        $data_insert['updatetime'] = date('Y-m-d H:i:s');

        $data_insert['createdatetime'] = date('Y-m-d H:i:s');
        $this->db->insert('coop_cremation_request_receive', $data_insert);
        $receive_id = $this->db->insert_id();

        //Update request member status
        $data_insert = array();
        $data_insert['cremation_status'] = '7';
        $this->db->where('cremation_request_id', $member_cremation->cremation_request_id);
        $this->db->update('coop_cremation_request', $data_insert);

        //Update member cremation status
        $data_insert = array();
        $data_insert['status'] = '2';
        $data_insert['death_date'] = date('Y-m-d');
        $this->db->where('member_cremation_id', $member_cremation->member_cremation_id);
        $this->db->update('coop_member_cremation', $data_insert);

        header('content-type: application/json; charset: utf-8;');
        echo json_encode(array(
            'status' => true, 'msg' => 'request success',
            'data' => array(
                'member_id' => $member_cremation->member_id,
                'cremation_request_id' => $data['cremation_request_id'],
                'cremation_status' => 7,
                'status' => 2
               )
            )
        );
        exit;
    }

    /****************************************
     * Update approve request receive money
     * @comment        : รับรองคำร้องขอรับเงินณาปนกิจสงเคราะห์
     * @method         : POST
     * @post (string)  : cremation_request_id
     * @post (string)  : cremation_receive_id
     * @return (boolean) :  status
     * @default (boolean) : false
     ****************************************/
    function approve_request_money(){
         $data_insert = [];
         $cremation_request_id = $this->input->post('cremation_request_id');
         $cremation_receive_id = $this->input->post('cremation_receive_id');

         if(empty($cremation_receive_id) || empty($cremation_request_id)){
             $this->center_function->toast("เกิดข้อผิดพลาดบางอย่าง");
             header('location: '.base_url('cremation/cremation_approve_receive'));
             exit;
         }

             //update status receive request
             $data = [];
             $data['cremation_receive_status'] = 1;
             $this->db->where('cremation_receive_id', $cremation_receive_id);
             $this->db->update('coop_cremation_request_receive', $data);

             //update status cremation request
             $data_update = [];
             $data_update['cremation_status'] = 8;
             $this->db->where('cremation_request_id', $cremation_request_id);
             $this->db->update('coop_cremation_request', $data_update);

        $this->db->select('*')->from('coop_cremation_transfer')->where('cremation_receive_id', $cremation_receive_id);
        $chk = $this->db->get()->num_rows();

        if($chk == 0) {
             //create transfer
             $transfer = [];
             $transfer['cremation_receive_id'] = $cremation_receive_id;
             $transfer['cremation_request_id'] = $cremation_request_id;
             $transfer['date_transfer'] = date('Y-m-d H:i:s');
             $transfer['transfer_status'] = '0';
             $this->db->insert('coop_cremation_transfer', $transfer);
        }else {
            $transfer = [];
            $transfer['cremation_receive_id'] = $cremation_receive_id;
            $transfer['cremation_request_id'] = $cremation_request_id;
            $transfer['date_transfer'] = date('Y-m-d H:i:s');
            $transfer['transfer_status'] = '0';
            $this->db->where(
                array(
                    'cremation_receive_id' => $cremation_receive_id,
                    'cremation_request_id' => $cremation_request_id
                )
            );
            $this->db->update('coop_cremation_transfer', $transfer);
        }
        $this->center_function->toast("บันทึกข้อมูลสำเร็จ");
        header('location: ' . base_url('cremation/cremation_approve_receive'));
        exit;
    }

	function cremation_receive_save(){
		$data_insert = array();
		$data = $this->input->post();
		
		$table = "coop_cremation_request_receive";
		$id_edit = @$data["cremation_receive_id"];	
		$member_id = @$data['member_id'];
		$request_id = @$data['cremation_request_id'];
		
		$data_insert['cremation_request_id'] = @$data['cremation_request_id'];	
		$data_insert['member_id'] = @$data['member_id'];	
		$data_insert['cremation_type_id'] = @$data['cremation_type_id'];
		$data_insert['cremation_detail_id'] = @$data['cremation_detail_id'];
		$data_insert['pay_type'] = @$data['pay_type'];
		$data_insert['cremation_receive_amount'] = @$data['cremation_receive_amount'];
		$data_insert['action_fee_percent'] = @$data['action_fee_percent'];
		$data_insert['cremation_balance_amount'] = @$data['cremation_balance_amount'];
		$data_insert['cremation_receive_status'] = '0';
		$data_insert['admin_id'] = $_SESSION['USER_ID'];
		$data_insert['updatetime'] = date('Y-m-d H:i:s');
		
		if($id_edit!=''){			
			$this->db->where('cremation_receive_id', $id_edit);
			$this->db->update($table, $data_insert);
			$receive_id = $id_edit;
		}else{	
			$data_insert['finance_month_status'] = '0';		
			$data_insert['createdatetime'] = date('Y-m-d H:i:s');
			$this->db->insert($table, $data_insert);
			$receive_id = $this->db->insert_id();
		}
		
		$data_insert = array();
		$data_insert['cremation_status'] = '7';
		$this->db->where('cremation_request_id', @$data['cremation_request_id']);
		$this->db->update('coop_cremation_request', $data_insert);
		
		$output_dir = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/cremation_request/";
		//echo $output_dir;
		if(!@mkdir($output_dir,0,true)){
		   chmod($output_dir, 0777);
		}else{
		   chmod($output_dir, 0777);
		}
		if($_FILES['cremation_request_file']['name'][0]!=''){
			foreach($_FILES['cremation_request_file']['name'] as $key_file => $value_file ){
				$fileName=array();
				$list_dir = array(); 
				$cdir = scandir($output_dir); 
				foreach ($cdir as $key => $value) { 
				   if (!in_array($value,array(".",".."))) { 
					  if (@is_dir(@$dir . DIRECTORY_SEPARATOR . @$value)){ 
						$list_dir[$value] = dirToArray(@$dir . DIRECTORY_SEPARATOR . $value); 
					  }else{
						if(substr($value,0,8) == date('Ymd')){
						$list_dir[] = $value;
						}
					  } 
				   } 
				}
				$explode_arr=array();
				foreach($list_dir as $key => $value){
					$task = explode('.',$value);
					$task2 = explode('_',$task[0]);
					$explode_arr[] = $task2[1];
				}
				$max_run_num = sprintf("%04d",count($explode_arr)+1);
				$explode_old_file = explode('.',$_FILES["cremation_request_file"]["name"][$key_file]);
				$new_file_name = date('Ymd')."_".$max_run_num.".".$explode_old_file[(count($explode_old_file)-1)];
				if(!is_array($_FILES["cremation_request_file"]["name"][$key_file]))
				{
						$fileName['file_name'] = $new_file_name;
						$fileName['file_type'] = $_FILES["cremation_request_file"]["type"][$key_file];
						$fileName['file_old_name'] = $_FILES["cremation_request_file"]["name"][$key_file];
						$fileName['file_path'] = $output_dir.$fileName['file_name'];
						move_uploaded_file($_FILES["cremation_request_file"]["tmp_name"][$key_file],$output_dir.$fileName['file_name']);
						
						$data_insert = array();
						$data_insert['cremation_request_id'] = @$request_id;
						$data_insert['file_name'] = @$fileName['file_name'];
						$data_insert['file_type'] = @$fileName['file_type'];
						$data_insert['file_old_name'] = @$fileName['file_old_name'];
						$data_insert['file_path'] = @$fileName['file_path'];
						$data_insert['cremation_type'] = 'receive';
						//add coop_cremation_file_attach
						$this->db->insert('coop_cremation_file_attach', $data_insert);
				}
			}
		}
		$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
		echo"<script> document.location.href='".PROJECTPATH."/cremation/cremation_request?id={$member_id}' </script>";
		exit;
	}
	
	public function get_cremation_receive(){
		$arr_data = array();	
		$id = @$_POST['id'];
		$today = date('Y-m-d');
		
		$this->db->select(array('coop_cremation_request.member_id'));
		$this->db->from('coop_cremation_request');
		$this->db->where("coop_cremation_request.cremation_status = '6'");
		$this->db->group_by("coop_cremation_request.member_id");
		$rs = $this->db->get()->result_array();
		$arr_user = @$rs;
		$user_total = 0;
		foreach($arr_user AS $key){
			$user_total++;
		}
		
		$this->db->select(array('*'));
		$this->db->from('coop_user');
		$rs_user = $this->db->get()->result_array();
		$rs_user = @$rs_user;
		$arr_user = array();
		foreach($rs_user AS $row_user){
			$arr_user[$row_user['user_id']] = $row_user['user_name'];
		}
		
		$this->db->select(array(
			'coop_cremation_request.cremation_no',
			'coop_cremation_request.member_id',
			'coop_cremation_request_receive.cremation_receive_id',
			'coop_cremation_request_receive.cremation_balance_amount',
			'coop_cremation_data.cremation_name',
			'coop_cremation_data.cremation_name_short',
			'coop_cremation_data_detail.cremation_id',
			'coop_cremation_data_detail.cremation_detail_id',
			'coop_cremation_data_detail.pay_type',
			'coop_cremation_data_detail.pay_per_person',
			'coop_cremation_data_detail.pay_per_person_stable',
			'coop_cremation_data_detail.action_fee_percent'
		));
		$this->db->from('coop_cremation_request');
		$this->db->join("coop_cremation_data","coop_cremation_request.cremation_type_id = coop_cremation_data.cremation_id","left");
		$this->db->join("coop_cremation_data_detail","coop_cremation_data_detail.cremation_id = coop_cremation_data.cremation_id","left");
		$this->db->join("coop_cremation_request_receive","coop_cremation_request_receive.cremation_request_id = coop_cremation_request.cremation_request_id","left");
		$this->db->where("coop_cremation_request.cremation_request_id = {$id} AND coop_cremation_data_detail.start_date <= '{$today}'");
		$this->db->order_by('coop_cremation_data_detail.start_date DESC');
		$this->db->limit(1);
		$rs = $this->db->get()->result_array();
		$row = @$rs[0];
		$arr_data = $row;		
		
		$arr_data['cremation_type_name'] = $row['cremation_name'].'('.$row['cremation_name_short'].')';
		
		
		if($arr_data['pay_type'] == '1'){
			$cremation_receive_amount = $user_total*@$arr_data['pay_per_person'];
		}else{
			$cremation_receive_amount = @$arr_data['pay_per_person_stable'];
		}
		$action_fee_amount = $cremation_receive_amount*$arr_data['action_fee_percent']/100;
		$arr_data['cremation_receive_amount'] = $cremation_receive_amount;
		$arr_data['cremation_balance_amount'] = $cremation_receive_amount-$action_fee_amount ;
		
		$this->db->select(array('*'));
		$this->db->from("coop_cremation_file_attach");
		$this->db->where("cremation_request_id = '".$id."' AND cremation_type = 'receive'");
		$rs_file = $this->db->get()->result_array();
		@$arr_data['coop_file_attach'] = array();
		if(!empty($rs_file)){
			foreach(@$rs_file as $key => $row_file){
				@$arr_data['coop_file_attach'][] = @$row_file;
			}
		}
		
		echo json_encode($arr_data);
		exit();
	}
	
	public function cremation_approve_receive(){
		$arr_data = array();
		$x=0;
		$join_arr = array();
		$join_arr[$x]['table'] = 'coop_member_cremation';
		$join_arr[$x]['condition'] = 'coop_member_cremation.member_cremation_id = coop_cremation_request_receive.member_cremation_id';
		$join_arr[$x]['type'] = 'inner';
		$x++;
		$join_arr[$x]['table'] = 'coop_user';
		$join_arr[$x]['condition'] = 'coop_cremation_request_receive.admin_id = coop_user.user_id';
		$join_arr[$x]['type'] = 'left';
		$x++;
		$join_arr[$x]['table'] = 'coop_cremation_transfer';
		$join_arr[$x]['condition'] = 'coop_cremation_request_receive.cremation_receive_id = coop_cremation_transfer.cremation_receive_id';
		$join_arr[$x]['type'] = 'left';		
		$x++;
		$join_arr[$x]['table'] = 'coop_cremation_data';
		$join_arr[$x]['condition'] = 'coop_cremation_request_receive.cremation_type_id = coop_cremation_data.cremation_id';
		$join_arr[$x]['type'] = 'left';
		
		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select('
		 coop_cremation_request_receive.*,
		 coop_member_cremation.assoc_firstname as firstname_th,
		 coop_member_cremation.assoc_lastname as lastname_th, 
		 coop_member_cremation.receiver_1, 
		 coop_member_cremation.receiver_2, 
		 coop_member_cremation.receiver_3, 
		 coop_user.user_name, 
		 coop_cremation_request_receive.createdatetime AS record_date,
		 coop_cremation_request_receive.admin_id,
		 coop_user.user_name AS user_name_transfer,
		 coop_cremation_request_receive.cremation_receive_status as transfer_status,
		 coop_cremation_data.cremation_name_short');

		$this->paginater_all->main_table('coop_cremation_request_receive');
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(10);
		$this->paginater_all->page_link_limit(20);
		$this->paginater_all->order_by('createdatetime DESC');
		$this->paginater_all->join_arr($join_arr);
		$row = $this->paginater_all->paginater_process();
		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit']);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		$i = $row['page_start'];
		
		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];
		$arr_data['i'] = $i;
		
		$arr_data['receive_status'] = array('0'=>'รออนุมัติ', '1'=>'อนุมัติ','2'=>'ไม่อนุมัติ');
		
		$this->db->select('bank_id, bank_name');
		$this->db->from('coop_bank');
		$row = $this->db->get()->result_array();
		$arr_data['bank'] = $row;

		$members = $this->db->select("*")->from("coop_member_cremation")->where("approve_status = 1 AND death_date is null")->get()->result_array();
		$arr_data["count_members"] = count($members);

		$settings = $this->db->select("*")->from("coop_setting_cremation_detail")->where("cremation_id = 2 AND start_date <= NOW()")->order_by("start_date")->get()->result_array();
		$arr_data["setting"] = $settings[0];
		
		$this->libraries->template('cremation/cremation_approve_receive',$arr_data);
	}
	
	public function get_cremation_transfer(){
		$id = @$_POST['id'];
		$today = date('Y-m-d');
		$arr_data = array();

		$this->db->select(array('*'));
		$this->db->from('coop_user');
		$rs_user = $this->db->get()->result_array();
		$rs_user = @$rs_user;
		$arr_user = array();
		foreach($rs_user AS $row_user){
			$arr_user[$row_user['user_id']] = $row_user['user_name'];
		}
		
		$this->db->select(array(
			'coop_cremation_request_receive.cremation_request_id',
			'coop_cremation_request_receive.cremation_receive_id',
			'coop_cremation_request_receive.cremation_receive_amount',
			'coop_cremation_request_receive.action_fee_percent',
			'coop_cremation_request_receive.cremation_balance_amount',
			'coop_cremation_request_receive.admin_id AS admin_request',
			'coop_cremation_data.cremation_name',
			'coop_cremation_data.cremation_name_short',
			'coop_cremation_data_detail.cremation_id',
			'coop_cremation_data_detail.cremation_detail_id',
			'coop_cremation_transfer.admin_id AS admin_transfer',
			'coop_cremation_transfer.createdatetime',
			'coop_cremation_transfer.date_transfer',
			'coop_cremation_transfer.date_transfer AS time_transfer',
			'coop_cremation_transfer.bank_type AS bank_type_transfer',
			'coop_cremation_transfer.bank_id',
			'coop_cremation_transfer.bank_branch_id',
			'coop_cremation_transfer.bank_account_no',
			'coop_cremation_transfer.file_name',
			'coop_cremation_transfer.cremation_transfer_id',
			'coop_member_cremation.*'
		));
		$this->db->from('coop_cremation_request_receive');
		$this->db->join("coop_cremation_data","coop_cremation_request_receive.cremation_type_id = coop_cremation_data.cremation_id","left");
		$this->db->join("coop_cremation_data_detail","coop_cremation_data_detail.cremation_id = coop_cremation_data.cremation_id","left");
		$this->db->join("coop_cremation_transfer","coop_cremation_transfer.cremation_receive_id = coop_cremation_request_receive.cremation_receive_id","left");
		$this->db->join("coop_member_cremation","coop_cremation_request_receive.member_cremation_id = coop_member_cremation.member_cremation_id","left");
		$this->db->where("coop_cremation_request_receive.cremation_receive_id = {$id} AND coop_cremation_data_detail.start_date <= '{$today}'");
		$this->db->order_by('coop_cremation_data_detail.start_date DESC');
		$this->db->limit(1);
		$rs = $this->db->get()->result_array();
		$row = @$rs[0];
		$arr_data = $row;		
		//print_r($this->db->last_query());exit;
		$arr_data['cremation_type_name'] = $row['cremation_name'].'('.$row['cremation_name_short'].')';		
		$arr_data['bank_type'] = (!empty($row['bank_type_transfer']))?$row['bank_type_transfer']:$row['bank_type'];
		$arr_data['bank_id'] = (!empty($row['bank_id']))?$row['bank_id']:$row['dividend_bank_id'];
		$arr_data['bank_branch_id'] = (!empty($row['bank_branch_id']))?$row['bank_branch_id']:$row['dividend_bank_branch_id'];
		$arr_data['bank_account_no'] = (!empty($row['bank_account_no']))?$row['bank_account_no']:$row['dividend_acc_num'];
		$arr_data['admin_request'] = $arr_user[$row['admin_request']];
		$arr_data['admin_transfer'] = $arr_user[(empty($row['admin_transfer']))?$_SESSION['USER_ID']:$row['admin_transfer']];
		$arr_data['createdatetime'] = ((!empty($row['createdatetime']) && $row['retry_date'] != '0000-00-00')?$this->center_function->mydate2date($row['createdatetime']):$this->center_function->mydate2date(date('Y-m-d')));
		$arr_data['date_transfer'] = ((!empty($row['date_transfer']) && $row['retry_date'] != '0000-00-00')?$this->center_function->mydate2date($row['date_transfer']):$this->center_function->mydate2date(date('Y-m-d')));
		$arr_data['time_transfer'] = ((!empty($row['time_transfer']) && $row['retry_date'] != '0000-00-00')?date("H:i", strtotime($row['time_transfer'])):date('H:i'));

		//Get bank account if exist
		if(empty($arr_data['bank_type'])) {
			$arr_data['bank_account'] = $this->db->select("*")
												->from("coop_member_cremation_bank_account")
												->where("member_cremation_raw_id = '".$row["id"]."'")
												->get()->result_array()[0];
		}

		echo json_encode($arr_data);
		exit();
	}	

	public function get_cremation_resign_transfer(){
		$id = $_POST['id'];
		$today = date('Y-m-d');
		$arr_data = array();

		$this->db->select(array('*'));
		$this->db->from('coop_user');
		$rs_user = $this->db->get()->result_array();
		$rs_user = @$rs_user;

		$arr_user = array();
		foreach($rs_user AS $row_user){
			$arr_user[$row_user['user_id']] = $row_user['user_name'];
		}
		$this->db->select("coop_cremation_request_resign.id as cremation_resign_id,
							coop_cremation_request_resign.cremation_request_id,
							coop_cremation_request_resign.adv_payment_balance as cremation_balance_amount,
							coop_cremation_request_resign.admin_id AS admin_request,
							coop_cremation_data.cremation_name,
							coop_cremation_data.cremation_name_short,
							coop_cremation_data_detail.cremation_id,
							coop_cremation_data_detail.cremation_detail_id,
							coop_cremation_transfer.cremation_transfer_id,
							coop_cremation_transfer.admin_id AS admin_transfer,
							coop_cremation_transfer.createdatetime,
							coop_cremation_transfer.date_transfer,
							coop_cremation_transfer.date_transfer AS time_transfer,
							coop_cremation_transfer.bank_type AS bank_type_transfer,
							coop_cremation_transfer.bank_id,
							coop_cremation_transfer.bank_branch_id,
							coop_cremation_transfer.bank_account_no,
							coop_cremation_transfer.file_name,
							coop_member_cremation.*
							");
		$this->db->from('coop_cremation_request_resign');
		$this->db->join("coop_cremation_request","coop_cremation_request.cremation_request_id = coop_cremation_request_resign.cremation_request_id","left");
		$this->db->join("coop_cremation_data","coop_cremation_request.cremation_type_id = coop_cremation_data.cremation_id","left");
		$this->db->join("coop_cremation_data_detail","coop_cremation_data_detail.cremation_id = coop_cremation_data.cremation_id","left");
		$this->db->join("coop_cremation_transfer","coop_cremation_transfer.cremation_resign_id = coop_cremation_request_resign.id","left");
		// $this->db->join("coop_mem_apply","coop_mem_apply.member_id = coop_cremation_request.member_id","left");
		$this->db->join("coop_member_cremation","coop_cremation_request_resign.member_cremation_id = coop_member_cremation.member_cremation_id","left");
		$this->db->where("coop_cremation_request_resign.id = {$id} AND coop_cremation_data_detail.start_date <= '{$today}'");
		$this->db->order_by('coop_cremation_data_detail.start_date DESC');
		$this->db->limit(1);
		$rs = $this->db->get()->result_array();

		$row = @$rs[0];
		$arr_data = $row;
		$arr_data['cremation_type_name'] = $row['cremation_name'].'('.$row['cremation_name_short'].')';		
		$arr_data['bank_type'] = (!empty($row['bank_type_transfer']))?$row['bank_type_transfer']:$row['bank_type'];
		$arr_data['bank_id'] = (!empty($row['bank_id']))?$row['bank_id']:$row['dividend_bank_id'];
		$arr_data['bank_branch_id'] = (!empty($row['bank_branch_id']))?$row['bank_branch_id']:$row['dividend_bank_branch_id'];
		$arr_data['bank_account_no'] = (!empty($row['bank_account_no']))?$row['bank_account_no']:$row['dividend_acc_num'];
		$arr_data['admin_request'] = $arr_user[$row['admin_request']];
		$arr_data['admin_transfer'] = $arr_user[(empty($row['admin_transfer']))?$_SESSION['USER_ID']:$row['admin_transfer']];
		$arr_data['createdatetime'] = ((!empty($row['createdatetime']) && $row['retry_date'] != '0000-00-00')?$this->center_function->mydate2date($row['createdatetime']):$this->center_function->mydate2date(date('Y-m-d')));
		$arr_data['date_transfer'] = ((!empty($row['date_transfer']) && $row['retry_date'] != '0000-00-00')?$this->center_function->mydate2date($row['date_transfer']):$this->center_function->mydate2date(date('Y-m-d')));
		$arr_data['time_transfer'] = ((!empty($row['time_transfer']) && $row['retry_date'] != '0000-00-00')?date("H:i", strtotime($row['time_transfer'])):date('H:i'));

		//Get bank account if exist
		if(empty($arr_data['bank_type'])) {
			$arr_data['bank_account'] = $this->db->select("*")
												->from("coop_member_cremation_bank_account")
												->where("member_cremation_raw_id = '".$row["id"]."'")
												->get()->result_array()[0];
		}
		
		echo json_encode($arr_data);
		exit();
	}

	public function get_account_list(){
		$member_id = @$_POST['member_id'];
		$cremation_receive_id = @$_POST['cremation_receive_id'];
		$arr_data = array();
		
		$this->db->select(array('coop_account_id'));
		$this->db->from('coop_mem_apply');
		$this->db->where("member_id = '".$member_id."'");
		$rs_mem = $this->db->get()->result_array();
		$mem_account_id = @$rs_mem[0]['coop_account_id'];
		
		$this->db->select(array('account_id'));
		$this->db->from('coop_cremation_transfer');
		$this->db->where("cremation_receive_id = '".$cremation_receive_id."'");
		$rs_transfer = $this->db->get()->result_array();
		$transfer_account_id = @$rs_transfer[0]['account_id'];
		$arr_data['account_id'] = (!empty($transfer_account_id))?$transfer_account_id :$mem_account_id;
		
		$this->db->select(array('*'));
		$this->db->from('coop_maco_account');
		$this->db->where("mem_id = '".$member_id."' AND account_status = '0'");
		$rs_account = $this->db->get()->result_array();
		$arr_data['rs_account'] = @$rs_account;
		
		$this->load->view('cremation/get_account_list',$arr_data);
	}
	
	function cremation_transfer_save(){
		$data = $this->input->post();

		$table = "coop_cremation_transfer";
		$process_timestamp = date('Y-m-d H:i:s');

		$cremation_transfer_id = $data["cremation_transfer_id"];
		$cremation_receive_id = $data["cremation_receive_id"];
		$cremation_request_id = $data["cremation_request_id"];
		$cremation_resign_id = $data["cremation_resign_id"];
		$data['cremation_balance_amount'] = str_replace( ',', '', $data['cremation_balance_amount']);
		$data['cremation_receive_amount'] = str_replace( ',', '', $data['cremation_receive_amount']);
		$data['action_fee_percent'] = str_replace( ',', '', $data['action_fee_percent']);

		$member_id = @$data['member_id'];

		$this->db->select('t1.member_cremation_id, t1.cremation_status')
            ->from('coop_cremation_request t1')
            // ->join('coop_cremation_request_receive t2', 't1.cremation_request_id=t2.cremation_request_id', 'inner')
            ->where('t1.cremation_request_id', $cremation_request_id);
		$data_member = $this->db->get()->row();

        if($data['bank_type'] == 1) {
            //เงินเข้าบัญชี
            $this->db->select('*');
            $this->db->from('coop_account_transaction');
            $this->db->where("account_id = '" . $data['account_id'] . "'");
            $this->db->order_by('transaction_time, transaction_id DESC');
            $this->db->limit(1);
            $row = $this->db->get()->result_array();
            if (!empty($row)) {
                $balance = $row[0]['transaction_balance'];
            } else {
                $balance = 0;
            }
            $sum = $balance + $data['cremation_balance_amount'];

            //ปิดก่อน โอนเงินเข้าบัญชี
            $data_insert = array();
            $data_insert['transaction_time'] = $this->center_function->ConvertToSQLDate($data['date_transfer']);
            $data_insert['transaction_list'] = 'XD';
            $data_insert['transaction_withdrawal'] = '';
            $data_insert['transaction_deposit'] = $data['cremation_balance_amount'];
            $data_insert['transaction_balance'] = $sum;
            $data_insert['user_id'] = $_SESSION['USER_ID'];
            $data_insert['account_id'] = $data['account_id'];

            if ($this->db->insert('coop_account_transaction', $data_insert)) {
                $data_acc['coop_account']['account_description'] = $data['cremation_type_name'] . " รหัสสมาชิก " . $data['member_id'];
                $data_acc['coop_account']['account_datetime'] = $this->center_function->ConvertToSQLDate($data['date_transfer']);

                $i=0;
                $data_acc['coop_account_detail'][$i]['account_type'] = 'debit';
                $data_acc['coop_account_detail'][$i]['account_amount'] = $data['cremation_balance_amount'];
                $data_acc['coop_account_detail'][$i]['account_chart_id'] = '10100';
                $i++;
                $data_acc['coop_account_detail'][$i]['account_type'] = 'credit';
                $data_acc['coop_account_detail'][$i]['account_amount'] = $data['cremation_balance_amount'];
                $data_acc['coop_account_detail'][$i]['account_chart_id'] = '20100';
                $this->account_transaction->account_process($data_acc);
            }

            $voucher_cond['voucher_no'] = '';

            $key = (date('Y')+543).date('m');
            $this->db->select('MAX(voucher_no) as voucher_no')
                ->from('coop_voucher')
                ->where("voucher_no LIKE '".$key."%'");

            $max_voucher =  $this->db->get()->row();

            if(empty($max_voucher->voucher_no)){
                $number_vc = $key.'000001';
            }else{
                $init = ((int)substr($max_voucher->voucher_no, 6) + 1);
                $number_vc = $key.str_pad($init, 6, '0', STR_PAD_LEFT);
            }

			$cremation_balance_amount = $data['cremation_balance_amount'];
			if(!empty($data['cremation_receive_id'])) {
				$voucher = array();
				$voucher['createdatetime'] = date('Y-m-d H:i:s');
				$voucher['member_id'] = $data['member_id'];
				$voucher['amount'] = $data['cremation_receive_amount']-$data['action_fee_percent'];
				$voucher['user_id'] = $_SESSION['USER_ID'];
				$voucher['voucher_no'] = $number_vc;
				$voucher['detail'] = "โอนเงินเงินสงเคราะห์ สฌ.สอ.สป. ให้สมาชิกเลขณาปากิจ ".$data_member->member_cremation_id;
				$this->db->insert('coop_voucher', $voucher);

				$cremation_balance_amount = $data['cremation_balance_amount']-($data['cremation_receive_amount']-$data['action_fee_percent']);
			}

			$voucher = array();
			$voucher['createdatetime'] = date('Y-m-d H:i:s');
			$voucher['member_id'] = $data['member_id'];
			$voucher['amount'] = $cremation_balance_amount;
			$voucher['user_id'] = $_SESSION['USER_ID'];
			$voucher['voucher_no'] = $number_vc;
			$voucher['detail'] = "โอนเงินเงินสงเคราะห์ล่วงหน้า สฌ.สอ.สป. ให้สมาชิกเลขณาปากิจ ".$data_member->member_cremation_id;
			$this->db->insert('coop_voucher', $voucher);
			

            $data_update = [];
            $data_update['voucher_no'] = $number_vc;
            $this->db->where(array('cremation_transfer_id' => $cremation_transfer_id, 'cremation_request_id' => $cremation_request_id));
            $this->db->update('coop_cremation_transfer', $data_update);

            $this->db->select(array('cremation_transfer_id'));
            $this->db->from('coop_cremation_transfer');
            $this->db->where("cremation_transfer_id = '" . $cremation_transfer_id . "'");
            $rs = $this->db->get()->result_array();
            $row = @$rs[0];
            $cremation_transfer_id = @$row["cremation_transfer_id"];

            $data_insert = array();
            $data_insert['cremation_receive_id'] = !empty($cremation_receive_id) ? $cremation_receive_id : null;
            $data_insert['cremation_request_id'] = $cremation_request_id;
            $data_insert['cremation_resign_id'] = !empty($cremation_resign_id) ? $cremation_resign_id : null;
            $data_insert['account_id'] = @$data['account_id'];

            $data_insert['admin_id'] = $_SESSION['USER_ID'];
            $data_insert['transfer_status'] = '1';
            $data_insert['bank_type'] = @$data['bank_type'];

            $data_insert['date_transfer'] = $this->center_function->ConvertToSQLDate($data['date_transfer']); //วันที่โอนเงิน

            if ($cremation_transfer_id == '') {
                $data_insert['createdatetime'] = date('Y-m-d H:i:s');
                $this->db->insert($table, $data_insert);
                $transfer_id = $this->db->insert_id();
            } else {
                $this->db->where('cremation_transfer_id', @$cremation_transfer_id);
                $this->db->update($table, $data_insert);
            }
        }

        if($data['bank_type'] == 2) {
            $output_dir = $_SERVER["DOCUMENT_ROOT"] . PROJECTPATH . "/assets/uploads/cremation_transfer/";
            $_tmpfile = $_FILES["file_name"];
            if (@$_tmpfile["tmp_name"]['name'] != '') {
                $new_file_name = $this->center_function->create_file_name($output_dir, $_tmpfile["name"]);
                if (!empty($new_file_name)) {
                    copy($_tmpfile["tmp_name"], $output_dir . $new_file_name);
                    @unlink($output_dir . $row['file_name']);
                    $file_name = $new_file_name;
                    $data['file_name'] = $file_name;
                }
			}

			$key = (date('Y')+543).date('m');
            $this->db->select('MAX(voucher_no) as voucher_no')
                ->from('coop_voucher')
                ->where("voucher_no LIKE '".$key."%'");

            $max_voucher =  $this->db->get()->row();

            if(empty($max_voucher->voucher_no)){
                $number_vc = $key.'000001';
            }else{
                $init = ((int)substr($max_voucher->voucher_no, 6) + 1);
                $number_vc = $key.str_pad($init, 6, '0', STR_PAD_LEFT);
            }

			$cremation_balance_amount = $data['cremation_balance_amount'];
			if(!empty($data['cremation_receive_id'])) {
				$voucher = array();
				$voucher['createdatetime'] = date('Y-m-d H:i:s');
				$voucher['member_id'] = $data['member_id'];
				$voucher['amount'] = $data['cremation_receive_amount']-$data['action_fee_percent'];
				$voucher['user_id'] = $_SESSION['USER_ID'];
				$voucher['voucher_no'] = $number_vc;
				$voucher['detail'] = "โอนเงินเงินสงเคราะห์ สฌ.สอ.สป. ให้สมาชิกเลขณาปากิจ ".$data_member->member_cremation_id;
				$this->db->insert('coop_voucher', $voucher);

				$cremation_balance_amount = $data['cremation_balance_amount']-($data['cremation_receive_amount']-$data['action_fee_percent']);
			}

			$voucher = array();
			$voucher['createdatetime'] = date('Y-m-d H:i:s');
			$voucher['member_id'] = $data['member_id'];
			$voucher['amount'] = $cremation_balance_amount;
			$voucher['user_id'] = $_SESSION['USER_ID'];
			$voucher['voucher_no'] = $number_vc;
			$voucher['detail'] = "โอนเงินเงินสงเคราะห์ล่วงหน้า สฌ.สอ.สป. ให้สมาชิกเลขณาปากิจ ".$data_member->member_cremation_id;
			$this->db->insert('coop_voucher', $voucher);

            $data_insert = array();
            $data_insert['cremation_receive_id'] = !empty($cremation_receive_id) ? $cremation_receive_id : null;
            $data_insert['cremation_request_id'] = $cremation_request_id;
            $data_insert['cremation_resign_id'] = !empty($cremation_resign_id) ? $cremation_resign_id : null;
            $data_insert['account_id'] = @$data['account_id'];

            $data_insert['admin_id'] = $_SESSION['USER_ID'];
            $data_insert['transfer_status'] = '1';
            $data_insert['bank_type'] = @$data['bank_type'];
            $data_insert['bank_id'] = @$data['dividend_bank_id'];
            $data_insert['bank_branch_id'] = @$data['dividend_bank_branch_id'];
            $data_insert['bank_account_no'] = @$data['bank_account_no'];
            $data_insert['file_name'] = @$data['file_name']; //ชื่อรูปหลักฐานการโอนเงิน

            $date_arr = explode('/', @$data['date_transfer']);
            $date_transfer = ($date_arr[2] - 543) . "-" . $date_arr[1] . "-" . $date_arr[0] . " " . @$data['time_transfer'];
			$data_insert['date_transfer'] = @$date_transfer; //วันที่โอนเงิน

			$data_insert["voucher_no"] = $number_vc;

            if ($cremation_transfer_id == '') {
                $data_insert['createdatetime'] = date('Y-m-d H:i:s');
                $this->db->insert($table, $data_insert);
                $transfer_id = $this->db->insert_id();
            } else {
                $this->db->where('cremation_transfer_id', @$cremation_transfer_id);
                $this->db->update($table, $data_insert);
            }
        }

		if(!empty($data['cremation_receive_id'])) {
			$data_insert = array();
			$data_insert['cremation_status'] ='8';
			$this->db->where('cremation_request_id', @$cremation_request_id);
			$this->db->update('coop_cremation_request', $data_insert);

			$data_insert = array();
			$data_insert['cremation_receive_status'] ='1';
			$data_insert['admin_id_approve'] = $_SESSION['USER_ID'];
			$this->db->where('cremation_receive_id', @$cremation_receive_id);
			$this->db->update('coop_cremation_request_receive', $data_insert);

			//Add transaction history
			$settings = $this->db->select("*")->from("coop_setting_cremation_detail")->where("cremation_id = 2 AND start_date <= NOW()")->order_by("start_date")->get()->result_array();
			$setting = $settings[0];

			$ad_payment = $this->db->select("*")->from("coop_cremation_advance_payment")->where("member_cremation_id = '".$data_member->member_cremation_id."'")->get()->row();

			$insert_data = array();
			$insert_data["cremation_receive_id"] = $cremation_receive_id;
			$insert_data["member_cremation_id"] = $member_cremation->member_cremation_id;
			$insert_data["type"] = "CTP";
			$insert_data["amount"] = $ad_payment->adv_payment_balance;
			$insert_data["total"] = 0;
			$insert_data["status"] = 1;
			$insert_data["created_at"] = $process_timestamp;
			$insert_data["updated_at"] = $process_timestamp;
			$this->db->insert('coop_cremation_advance_payment_transaction', $insert_data);

			$insert_data = array();
			$insert_data["adv_payment_balance"] = 0;
			$insert_data["updatetime"] = $process_timestamp;
			$this->db->where('adv_id', $ad_payment->adv_id);
			$this->db->update('coop_cremation_advance_payment', $insert_data);

			$cremation_requests = $this->db->select("t1.member_cremation_id, t2.adv_payment_balance")
											->from("coop_cremation_request as t1")
											->join("coop_cremation_advance_payment as t2", "t1.member_cremation_id = t2.member_cremation_id", "inner")
											->where("t1.cremation_status in ('4','6','7','10')")
											->get()->result_array();
			$insert_datas = array();
			foreach($cremation_requests as $cremation_request) {
				$insert_data = array();
				$insert_data["cremation_receive_id"] = $cremation_receive_id;
				$insert_data["member_cremation_id"] = $cremation_request["member_cremation_id"];
				$insert_data["type"] = "CTAP";
				$insert_data["amount"] = $setting["money_received_per_member"];
				$insert_data["total"] = $cremation_request["adv_payment_balance"] - $setting["money_received_per_member"];
				$insert_data["status"] = 1;
				$insert_data["created_at"] = $process_timestamp;
				$insert_data["updated_at"] = $process_timestamp;
				$insert_datas[]  = $insert_data;
			}

			if (!empty($insert_datas)) {
                $this->db->insert_batch('coop_cremation_advance_payment_transaction', $insert_datas);
			}
			
			//deduct
			$this->deduct_advance_pay($data_member->member_cremation_id);

			//เรียกเก็บ
			// $this->send_request_deduct($data_member->member_cremation_id);

			//ส่ง SMS ไปตามเบอร์สมาชิก
			// $mobile = @$data['mobile'];
			// if($mobile){
			// 	$bank_account_no = (@$data['bank_type'] == '1')?@$data['account_id']:@$data['bank_account_no'];
			// 	//$bank_account_no = 'xxx'.substr($bank_account_no, 0, 3).'';
			// 	$msg = "อนุมัติขอรับเงินฌาปนกิจสงเคราะห์เรียบร้อยแล้ว\n";
			// 	$msg .= "โอนเงินเข้า ".$bank_account_no." จำนวน ".number_format(@$data['cremation_balance_amount'],2)."  บาท\n";
			// 	$msg .= '('.date('j/n/Y H:i').')';
			// 	//$status_sms = $this->center_function->send_sms($mobile, $msg);
			// }
		} else if (!empty($data['cremation_resign_id'])) {
			$data_insert = array();
			$data_insert['status'] ='3';
			$this->db->where('id', $cremation_resign_id);
			$this->db->update('coop_cremation_request_resign', $data_insert);
			if ($data_member->cremation_status == 11) {
				$relate_members = $this->db->select("*")
											->from("coop_cremation_advance_payment as t1")
											->where("ref_member_id = '".$member_id."'")
											->get()->result_array();
				$insert_datas = array();
				foreach($relate_members as $member) {
					$insert_data = array();
					$insert_data["cremation_resign_id"] = $cremation_resign_id;
					$insert_data["member_cremation_id"] = $member['member_cremation_id'];
					$insert_data["type"] = "CRP";
					$insert_data["amount"] = $member["adv_payment_balance"];
					$insert_data["total"] = 0;
					$insert_data["status"] = 1;
					$insert_data["created_at"] = $process_timestamp;
					$insert_data["updated_at"] = $process_timestamp;
					$insert_datas[] = $insert_data;

					$insert_data = array();
					$insert_data["adv_payment_balance"] = 0;
					$insert_data["updatetime"] = $process_timestamp;
					$this->db->where('adv_id', $member["adv_id"]);
					$this->db->update('coop_cremation_advance_payment', $insert_data);
				}
				
				if (!empty($insert_datas)) {
					$this->db->insert_batch('coop_cremation_advance_payment_transaction', $insert_datas);
				}
			} else {
				$insert_data = array();
				$insert_data["cremation_resign_id"] = $cremation_resign_id;
				$insert_data["member_cremation_id"] = $member_cremation->member_cremation_id;
				$insert_data["type"] = "CRP";
				$insert_data["amount"] = $data['cremation_receive_amount'];
				$insert_data["total"] = 0;
				$insert_data["status"] = 1;
				$insert_data["created_at"] = $process_timestamp;
				$insert_data["updated_at"] = $process_timestamp;
				$this->db->insert('coop_cremation_advance_payment_transaction', $insert_data);
			}
		}
		$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
		echo"<script> document.location.href='".PROJECTPATH."/cremation/cremation_transfer' </script>";
		exit;
	}
	
	function coop_cremation_cancel(){	
		$cremation_request_id = @$_GET['cremation_request_id'];
		$cremation_transfer_id = @$_GET['cremation_transfer_id'];
		
		$data_insert = array();
		$data_insert['transfer_status'] = @$_GET['status_to'];
		$data_insert['cancel_date'] = date('Y-m-d H:i:s');	 //วันที่ขอยกเลิก	
		$this->db->where('cremation_transfer_id', @$cremation_transfer_id);		
		$this->db->update('coop_cremation_transfer', $data_insert);
		
		$data_insert = array();
		$data_insert['cremation_status'] ='1';
		$this->db->where('cremation_request_id', @$cremation_request_id);
		$this->db->update('coop_cremation_request', $data_insert);

		$this->center_function->toast("ลบข้อมูลเรียบร้อยแล้ว");
		echo true;
		
	}
	public function cremation_pay_all(){
		$arr_data = array();
		$member_id = @$_GET['member_id'];
		$cremation_type_id = @$_GET['cremation_id'];
		
		$this->db->select(array('member_id','firstname_th','lastname_th'));
		$this->db->from('coop_mem_apply');
		$this->db->where("member_id = '{$member_id}'");
		$rs_mem = $this->db->get()->result_array();
		$row_mem = @$rs_mem[0];
		$arr_data['member_id'] = @$row_mem['member_id'];
		$arr_data['member_full_name']= @$row_mem['firstname_th'].' '.@$row_mem['lastname_th'];	
		
		//ประเภทฌาปนกิจสงเคราะห์
		$this->db->select(array('*'));
		$this->db->from('coop_cremation_data');
		$this->db->where("cremation_id = '{$cremation_type_id}'");
		$rs_type = $this->db->get()->result_array();
		$row_type = @$rs_type[0];
		$arr_data['cremation_type_name']= $row_type['cremation_name'].'('.$row_type['cremation_name_short'].')';
		
		
		$this->db->select(array(
								'coop_finance_transaction.member_id',
								'coop_finance_transaction.cremation_type_id',
								'coop_finance_transaction.receipt_id',
								'coop_finance_transaction.account_list_id',
								'coop_finance_transaction.total_amount',
								'coop_receipt.receipt_datetime',
								'coop_account_list.account_list'
						));
		$this->db->from('coop_finance_transaction');
		$this->db->join("coop_account_list","coop_account_list.account_id = coop_finance_transaction.account_list_id","inner");
		$this->db->join("coop_receipt","coop_receipt.receipt_id = coop_finance_transaction.receipt_id","inner");
		$this->db->where("coop_finance_transaction.member_id = '{$member_id}' AND coop_finance_transaction.cremation_type_id = '{$cremation_type_id}'");
		$rs = $this->db->get()->result_array();
		$arr_data['data'] = @$rs;
		//print_r($this->db->last_query());
		//echo '<pre>'; print_r($_GET); echo '</pre>';
		
		$this->load->view('cremation/cremation_pay_all',$arr_data);
	}

	public function get_check_request(){

        $result = false;

        if(!empty($this->input->get('member_id')) || !empty($this->input->get('type'))){
            if($this->input->get('type') == 1) {
                $cond['ref_member_id'] = $this->input->get('member_id');
                $cond['mem_type_id'] = $this->input->get('type');
                $this->db->select('*')->from('coop_member_cremation')->where($cond);
                $chk_has_member = $this->db->get()->row();

                if(!empty($chk_has_member)){
                    header('content-type: application/json; charset: utf-8;');
                    echo json_encode(array(
                            'status' => $result,
                            'msg' => 'ท่านเป็นสมาชิกฌาปนกิจสงเคราะห์แล้ว'
                        )
                    );
                    exit;
                }
            }
        }

        if(!empty($this->input->get('id_card')) || !empty($this->input->get('type'))){
            if($this->input->get('type') == 2) {
                $cond['ref_member_id'] = $this->input->get('member_id');
                $cond['id_card'] = $this->input->get('id_card');
                $this->db->select('*')->from('coop_member_cremation')->where($cond);
                $chk_has_member = $this->db->get()->row();

                if(!empty($chk_has_member)){
                    header('content-type: application/json; charset: utf-8;');
                    echo json_encode(array(
                            'status' => $result,
                            'msg' => 'ท่านเป็นสมาชิกฌาปนกิจสงเคราะห์แล้ว'
                        )
                    );
                    exit;
                }
            }
        }

	    $this->db->select('*')
            ->from('coop_setting_cremation_detail')
            ->where('start_date <= now()')
            ->order_by('start_date DESC, cremation_detail_id ASC')
            ->limit(1);
        $setting = $this->db->get()->row();

        $birthday = $this->input->get('birthday');
        $mem_cremotion_type = $this->input->get('type');

        if($birthday == ""){
            header('content-type: application/json; charset: utf-8;');
            echo json_encode(array(
                    'status' => $result,
                    'msg' => 'กรุณาระบุวันเกิด'
                )
            );
            exit;
        }

        //convert date
        $birthday = date("Y-m-d", strtotime(str_replace('/', '-', $birthday)." -543 year"));
        $age = $this->center_function->diff_year($birthday,date('Y-m-d'));

        //ตรวจสอบประเภทสมาชิก
        if($mem_cremotion_type == 1){
            $result = (int)$setting->ordinary_member_age_limit > (int)$age;
            $msg = 'สมาชิกสามัญอายุต้องไม่เกิน ' . $setting->ordinary_member_age_limit . ' ปี';
        }

        if($mem_cremotion_type <> 1){
            $result = (int) $setting->associate_member_age_limit > (int) $age;
            $msg = 'สมาชิกสมทบอายุต้องไม่เกิน '.$setting->associate_member_age_limit.' ปี';
        }

        header('content-type: application/json; charset: utf-8;');
        echo json_encode(array(
            'status' => $result,
            'msg' => $msg)
        );
        exit;
    }

    public function get_check_mem_request(){

        $result = false;
        $member_id = $this->input->get('member_id');
        $this->db->select('*')->from('coop_mem_apply')->where('member_id', str_pad($member_id, 6,'0',STR_PAD_LEFT));
        $member = $this->db->get()->row();

        $age = $this->center_function->diff_year($member->birthday,date('Y-m-d'));

        //ตรวจสอบประเภทสมาชิก
        if($member->apply_type_id <> 1){
            $msg = 'ต้องสมัครในนามสมาชิกสามัญเท่านั้น';
        }else{
            $result = true;
            $msg = 'ผ่านจ้า';
        }

        header('content-type: application/json; charset: utf-8;');
        echo json_encode(array(
                'status' => $result,
                'msg' => $msg)
        );
        exit;
    }

	public function get_check_approved(){

	    $this->db->select('*')->from('coop_cremation_request')->where(array('cremation_status' => 1, 'member_id' => $this->input->get('id')));

	    if($this->db->get()->num_rows() > 0 ){
	        header('content-type: application/json; charset: utf-8;');
	        echo json_encode(array('status' => true));
	        exit;
        }else{
            header('content-type: application/json; charset: utf-8;');
            echo json_encode(array('status' => false));
            exit;
        }

    }

    public function cremation_transfer(){
	    $arr_data = [];

        $x=0;
        $join_arr = array();
        $join_arr[$x]['table'] = 'coop_cremation_request_receive';
        $join_arr[$x]['condition'] = 'coop_cremation_transfer.cremation_receive_id = coop_cremation_request_receive.cremation_receive_id';
        $join_arr[$x]['type'] = 'left';
		$x++;
        $join_arr[$x]['table'] = 'coop_cremation_request_resign';
        $join_arr[$x]['condition'] = 'coop_cremation_transfer.cremation_resign_id = coop_cremation_request_resign.id';
		$join_arr[$x]['type'] = 'left';
		$x++;
        $join_arr[$x]['table'] = 'coop_member_cremation';
		$join_arr[$x]['condition'] = 'coop_member_cremation.member_cremation_id = coop_cremation_request_receive.member_cremation_id
										OR coop_member_cremation.member_cremation_id = coop_cremation_request_resign.member_cremation_id';
		$join_arr[$x]['type'] = 'inner';

        $this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select(array('coop_cremation_transfer.voucher_no','coop_cremation_request_receive.cremation_receive_id','coop_member_cremation.assoc_firstname',
											'coop_member_cremation.assoc_lastname', 'coop_member_cremation.member_cremation_id', 'coop_cremation_transfer.transfer_status',
											'coop_member_cremation.receiver_1','coop_member_cremation.receiver_2','coop_member_cremation.receiver_3','coop_member_cremation.receiver_4',
											'coop_member_cremation.heir_phone',
											'coop_cremation_request_receive.receiver','coop_cremation_request_receive.cremation_balance_amount',
											'coop_cremation_request_resign.id as resign_id', 'coop_cremation_request_resign.adv_payment_balance','coop_cremation_transfer.cremation_transfer_id'));
        $this->paginater_all->main_table('coop_cremation_transfer');
		$this->paginater_all->where("!((coop_cremation_transfer.voucher_no is null OR coop_cremation_transfer.voucher_no = '') AND coop_cremation_transfer.transfer_status = 1)
										AND ((coop_cremation_request_receive.cremation_receive_id is not null AND coop_cremation_request_receive.cremation_receive_status = 1)
											OR (coop_cremation_request_resign.id is not null AND coop_cremation_request_resign.status in (1,3)))");
        $this->paginater_all->page_now(@$_GET["page"]);
        $this->paginater_all->per_page(10);
        $this->paginater_all->page_link_limit(20);
        $this->paginater_all->order_by('coop_cremation_request_receive.cremation_request_id DESC');
        $this->paginater_all->join_arr($join_arr);
        $row = $this->paginater_all->paginater_process();
        //echo"<pre>";print_r($row);exit;
        $paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'],$_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
        $i = $row['page_start'];

        $arr_data['num_rows'] = $row['num_rows'];
        $arr_data['paging'] = $paging;
        $arr_data['row'] = $row['data'];
        $arr_data['i'] = $i;

        $arr_data['transfer_type'] = ['รอโอนเงิน', 'โอนเงินแล้ว'];

        $this->db->select('bank_id, bank_name');
        $this->db->from('coop_bank');
        $row = $this->db->get()->result_array();
        $arr_data['bank'] = $row;


        $this->libraries->template('cremation/cremation_transfer',$arr_data);

    }

    function get_setting_cremation(){
        $this->db->select('*')
            ->from('coop_setting_cremation_detail')
            ->where("start_date <= ", date('Y-m-d'))
            ->order_by('start_date DESC, cremation_detail_id ASC')->limit('1');
        return $this->db->get()->row();
    }

    function send_request_deduct($member_cremation_id){


        $this->db->select('*')->from('coop_member_cremation')->where('member_cremation_id', $member_cremation_id);
        $member_death = $this->db->get()->row();

        $this->deduct_advance_pay($member_death->member_cremation_id);

        $cond_profile_id = array(
            'profile_year' => date('Y')+543,
            'profile_month' => date('n')+1
        );

        $this->db->select('profile_id')
            ->from('coop_finance_month_profile')
            ->where($cond_profile_id);
        $profile = $this->db->get()->row();

        //Check has profile id
        if(empty($profile->profile_id)){

            $data_insert['profile_year']  = date('Y')+543;
            $data_insert['profile_month'] = date('n')+1;
            $this->db->insert('coop_finance_month_profile',$data_insert);

            $profile_id = $this->db->insert_id();

        }else {

            $profile_id = $profile->profile_id;
        }

        //Preparing data
        $raw = [];

        $wh_pre['t1.status'] = 1;
        $wh_pre['t1.approve_date <='] = $member_death->death_date;
        $wh_pre['t4.user_id_pay'] = 1;
        $wh_pre['t1.status'] = 1;
        $wh_pre['t1.status'] = 1;

        $this->db->select(array(
            't1.ref_member_id',
            'count(t1.ref_member_id) * 100 as pay_amount',
            't3.department',
            't3.faction',
            't3.`level`'
        ))
            ->from('coop_member_cremation t1')
            ->join('coop_cremation_request t4', 't1.member_cremation_id=t4.member_cremation_id', 'inner')
            ->join('coop_cremation_advance_payment t2', 't1.member_cremation_id=t2.member_cremation_id', 'inner')
            ->join('coop_mem_apply t3', 't1.ref_member_id=t3.member_id', 'inner')
            ->where(array('t1.status' => 1, 't1.approve_date <=' => $member_death->death_date , 't4.user_id_pay' => 1))
            ->group_by('t1.ref_member_id');
        $raw = $this->db->get()->result_array();

        $cond_where_in['member_id'] = [];

        $member_cremation_data = [];
        foreach ($raw as $key => $value){
            $cond_where_in['member_id'][] = $value['ref_member_id'];
            $member_cremation_data[$value['ref_member_id']] = $value['pay_amount'];
        }

        $cond_month_detail['profile_id'] = $profile_id;
        $cond_month_detail['deduct_code'] = 'CREMATION';
        $cond_month_detail['deduct_id'] = 16;

        $this->db->select('*')
            ->from('coop_finance_month_detail')
            ->where($cond_month_detail)
            ->where_in($cond_where_in);

        $check_month_detail = $this->db->get()->num_rows();

        $this->db->select('*')
            ->from('coop_finance_month_detail')
            ->where($cond_month_detail)
            ->where_in($cond_where_in);

        $prepare = $this->db->get()->result_array();

        if($check_month_detail == 0) {
            $data = [];
            $index = 0;
            foreach ($raw as $key => $item) {
                $data[$index]['profile_id'] = $profile_id;
                $data[$index]['member_id'] = $item['ref_member_id'];
                $data[$index]['deduct_code'] = 'CREMATION';
                $data[$index]['pay_amount'] = $item['pay_amount'];
                $data[$index]['real_pay_amount'] = $item['pay_amount'];
                $data[$index]['pay_type'] = 'principal';
                $data[$index]['deduct_id'] = 16;
                $data[$index]['run_status'] = 0;
                $data[$index]['finance_month_type'] = 1;
                $data[$index]['create_datetime'] = date('Y-m-d H:i:s');
                $data[$index]['update_datetime'] = date('Y-m-d H:i:s');
                $data[$index]['department'] = $item['department'];
                $data[$index]['faction'] = $item['faction'];
                $data[$index]['level'] = $item['level'];
                $data[$index]['create_by'] = $_SESSION['USER_ID'];
                $index++;
            }

            if (!empty($data)) {
                $this->db->insert_batch('coop_finance_month_detail', $data);
            }

        }else{

            $update = [];
            $member_in = [];
            $index = 0;
            foreach ($prepare as $key => $item){

                $pay_amount = $item['pay_amount'] + $member_cremation_data[$item['member_id']];
                $update[$index]['pay_amount'] = $pay_amount ;
                $update[$index]['real_pay_amount'] = $pay_amount;
                $update[$index]['run_id'] = $item['run_id'];
                $index++;

                $member_in[] =  $item['member_id'];

            }

            if(!empty($member_in)){
                $raw = [];
                $this->db->select(array(
                    't1.ref_member_id',
                    'count(t1.ref_member_id) * 100 as pay_amount',
                    't3.department',
                    't3.faction',
                    't3.`level`'
                ))
                    ->from('coop_member_cremation t1')
                    ->join('coop_cremation_request t4', 't1.member_cremation_id=t4.member_cremation_id', 'inner')
                    ->join('coop_cremation_advance_payment t2', 't1.member_cremation_id=t2.member_cremation_id', 'inner')
                    ->join('coop_mem_apply t3', 't1.ref_member_id=t3.member_id', 'inner')
                    ->where(array('t1.status' => 1, 't1.approve_date <=' => $member_death->death_date, 't4.user_id_pay' => 1))
                    ->where_not_in('t4.member_id', $member_in)
                    ->group_by('t1.ref_member_id');
                $raw = $this->db->get()->result_array();

                $data = [];
                $index = 0;
                foreach ($raw as $key => $item) {
                    $data[$index]['profile_id'] = $profile_id;
                    $data[$index]['member_id'] = $item['ref_member_id'];
                    $data[$index]['deduct_code'] = 'CREMATION';
                    $data[$index]['pay_amount'] = $item['pay_amount'];
                    $data[$index]['real_pay_amount'] = $item['pay_amount'];
                    $data[$index]['pay_type'] = 'principal';
                    $data[$index]['deduct_id'] = 16;
                    $data[$index]['run_status'] = 0;
                    $data[$index]['finance_month_type'] = 1;
                    $data[$index]['create_datetime'] = date('Y-m-d H:i:s');
                    $data[$index]['update_datetime'] = date('Y-m-d H:i:s');
                    $data[$index]['department'] = $item['department'];
                    $data[$index]['faction'] = $item['faction'];
                    $data[$index]['level'] = $item['level'];
                    $data[$index]['create_by'] = $_SESSION['USER_ID'];
                    $index++;
                }

                if (!empty($data)) {
                    $this->db->insert_batch('coop_finance_month_detail', $data);
                }
            }

            if (!empty($update)) {
                $this->db->update_batch('coop_finance_month_detail', $update, 'run_id');
            }
        }
    }

    function deduct_advance_pay($member_cremation_id){

        $this->db->select('*')->from('coop_member_cremation')->where('member_cremation_id', $member_cremation_id);
		$member_death = $this->db->get()->row();

		$request = $this->db->select("*")->from("coop_cremation_request_receive")->where('member_cremation_id = "'.$member_cremation_id.'" AND cremation_receive_status = 1')->get()->row();
		
		$settings = $this->db->select("*")->from("coop_setting_cremation_detail")->where("cremation_id = 2 AND start_date <= NOW()")->order_by("start_date")->get()->result_array();
		$setting = $settings[0];

        $this->db->select(array('(t2.adv_payment_balance - '.$setting["money_received_per_member"].') as adv_payment_balance', 't2.adv_id'))
            ->from('coop_member_cremation t1')
            ->join('coop_cremation_request t3', 't1.member_cremation_id=t3.member_cremation_id', 'inner')
            ->join('coop_cremation_advance_payment t2', 't1.member_cremation_id=t2.member_cremation_id', 'inner')
            ->where(array(
                't1.status' => 1,
				't1.approve_date <= ' => $request->createdatetime
				));

        $res = $this->db->get()->result_array();

        if(!empty($res)){
            $this->db->update_batch('coop_cremation_advance_payment', $res, 'adv_id');
        }

    }

    public function check_generator(){
        $key = (date('Y')+543).date('m');
        $this->db->select('MAX(voucher_no) as voucher_no')
            ->from('coop_voucher')
            ->where("voucher_no LIKE '".$key."%'");

        $max_voucher =  $this->db->get()->row();

        if(empty($max_voucher->voucher_no)){
            $number_vc = $key.'000001';
        }else{
            $init = ((int)substr($max_voucher->voucher_no, 6) + 1);
            echo $init;
            die;
            $number_vc = $key.str_pad($init, 6, '0', STR_PAD_LEFT);
        }

        echo $number_vc;
    }

	public function search_cremation_by_type_jquery() {
		$search_text = @$_POST["search_text"];
		$search_list = @$_POST["search_list"];
		$where = "1=1";
		if(@$_POST['search_list'] == 'member_id'){
			$where = " t2.member_id LIKE '%".$search_text."%'";
		}else if(@$_POST['search_list'] == 'firstname_th'){
			$where = " t2.assoc_firstname LIKE '%".$search_text."%'";
		}else if(@$_POST['search_list'] == 'lastname_th'){
			$where = " t2.assoc_lastname LIKE '%".$search_text."%'";
		}else if(@$_POST['search_list'] == 'id_card'){
			$where = " t2.id_card LIKE '%".$search_text."%'";
		}else if(@$_POST['search_list'] == 'cremation_no'){
			$where = " t1.cremation_no LIKE '%".$search_text."%'";
		}else if(@$_POST['search_list'] == 'member_cremation_id'){
			$where = " t1.member_cremation_id LIKE '%".$search_text."%'";
		}
		if(!empty($_POST["status"])) {
			$where .= " AND t1.cremation_status in (".$_POST["status"].")";
		} else {
			$where .= " AND t1.cremation_status in (0,1,2,4,6,7,8)";
		}
		if(!empty($_POST["is_member"])) {
			$where .= " AND t2.member_id is not null";
		}
		$datas = $this->db->select("t1.cremation_no, t1.cremation_request_id, t1.member_cremation_id, t2.id, t2.member_id, t2.assoc_firstname, t2.assoc_lastname, t3.prename_full")
							->from("coop_cremation_request as t1")
							->join("coop_member_cremation as t2", "t1.member_cremation_id = t2.member_cremation_id", "inner")
							->join("coop_prename as t3", "t2.prename_id = t3.prename_id","left")
							->where($where)
							->get()->result_array();
		$arr_data['datas'] = $datas;

		$this->load->view('cremation/search_cremation_jquery',$arr_data);
	}

	public function get_cremation_info() {
		$where = "";
		if(!empty($_GET["id"])) {
			$where .= "t1.member_cremation_raw_id = ".$_GET["id"];
		} else if (!empty($_GET["member_cremation_id"])) {
			$where .= "t1.member_cremation_id = ".$_GET["member_cremation_id"];
		}

		if ($where == "") {
			$datas = array();
		} else {
			$datas = $this->db->select("t1.cremation_no, t1.cremation_request_id, t1.cremation_status, t1.createdatetime, t2.*, t3.prename_full,
											t4.assoc_firstname as firstname_ref, t4.assoc_lastname as lastname_ref, t5.prename_full as prename_full_ref,
											t6.adv_payment_balance")
								->from("coop_cremation_request as t1")
								->join("coop_member_cremation as t2", "t1.member_cremation_raw_id = t2.id", "inner")
								->join("coop_prename as t3", "t2.prename_id = t3.prename_id", "left")
								->join("coop_member_cremation as t4", "t2.ref_member_id = t4.member_id", "left")
								->join("coop_prename as t5", "t4.prename_id = t5.prename_id", "left")
								->join("coop_cremation_advance_payment as t6", "t1.member_cremation_id = t6.member_cremation_id", "left")
								->where($where)
								->get()->result_array();

			$member_cremation_raw_id = $datas[0]["id"];
			$banks = $this->db->select("t1.*, t2.branch_name")
								->from("coop_member_cremation_bank_account as t1")
								->join("coop_bank_branch as t2", "t1.dividend_bank_branch_id = t2.branch_code AND t1.dividend_bank_id = t2.bank_id", "left")
								->where("member_cremation_raw_id = '".$member_cremation_raw_id."'")
								->get()->result_array();
			$datas[0]["banks"] = $banks;
		}
		echo json_encode($datas[0]);
	}

	public function get_cremation_request_receive() {
		$datas = $this->db->select("t1.*, t2.file_old_name as testament, t3.file_old_name as evidence")
							->from("coop_cremation_request_receive as t1")
							->join("coop_cremation_receive_file_attach as t2", "t1.cremation_receive_id = t2.cremation_receive_id AND t2.type = 'testament'", "left")
							->join("coop_cremation_receive_file_attach as t3", "t1.cremation_receive_id = t3.cremation_receive_id AND t3.type = 'evidence'", "left")
							->where("t1.cremation_receive_id = '".$_GET["id"]."'")->get()->result_array();
		echo json_encode($datas[0]);
	}

	public function check_request_id() {
		$datas = $this->db->select("t1.cremation_request_id")
									->from("coop_cremation_request as t1")
									->join("coop_member_cremation as t2", "t1.member_cremation_id = t2.member_cremation_id", "inner")
									->where("t1.cremation_request_id = '".$_GET["id"]."'")
									->get()->result_array();
		echo json_encode($datas[0]);
	}

	public function check_member_cremation_id() {
		$datas = $this->db->select("t1.member_cremation_id, t2.id")
									->from("coop_cremation_request as t1")
									->join("coop_member_cremation as t2", "t1.member_cremation_id = t2.member_cremation_id", "inner")
									->where("t1.member_cremation_id = ".$_GET["id"])
									->get()->result_array();
		echo json_encode($datas[0]);
	}

	function check_member_id(){
		//$member_id = sprintf("%06d", @$_POST['member_id']);
		$member_id = $this->center_function->complete_member_id(@$_POST['member_id']);
		$cremation_members = $this->db->select("t1.member_id, t1.member_cremation_id, t1.id")
										->from("coop_member_cremation as t1")
										->join("coop_cremation_request as t2", "t1.id = t2.member_cremation_raw_id AND cremation_status in (0,1,2,4,6,7,8,10)")
										->where("t1.member_id = '".$member_id."'")
										->get()->result_array();
		if(!empty($cremation_members)) {
			echo json_encode($cremation_members[0]);
		} else {
			$arr_data = array();
			$this->db->select(array('member_id'));
			$this->db->from('coop_mem_apply');
			$this->db->where("member_id LIKE '%".$member_id."%'");
			$this->db->limit(1);
			$rs_member = $this->db->get()->result_array();
			//echo $this->db->last_query();exit;
			$row_member = $rs_member[0];
			if(!empty($row_member)){
				$arr_data = @$row_member;
			}else{
				$arr_data = array();
			}
			//echo '<pre>'; print_r($arr_data); echo '</pre>';
			echo json_encode($arr_data);
		}
		exit;
	}

	public function get_convert_to_thai_date() {
		echo $this->center_function->ConvertToThaiDate($_GET["date"],'1','0');
	}

	public function cremation_request_money() {
		$arr_data = array();

		if(!empty($_POST)) {
			if(empty($_POST["cremation_receive_id"])) {
				$cremation_request_id = $_POST["cremation_request_id"];
				//cremation member
				$member_cremation = $this->db->select('t1.cremation_request_id, t1.member_cremation_id, t1.member_id, t1.cremation_type_id, t1.cremation_detail_id')
												->from('coop_cremation_request t1')
												->where('t1.cremation_request_id = "'.$cremation_request_id.'"')
												->get()->row();
				$data_insert['cremation_request_id'] = $member_cremation->cremation_request_id;
				$data_insert['member_cremation_id'] = $member_cremation->member_cremation_id;
				$data_insert['member_id'] = $member_cremation->member_id;
				$data_insert['cremation_type_id'] = $member_cremation->cremation_type_id;
				$data_insert['cremation_detail_id'] = $member_cremation->cremation_detail_id;
				$data_insert['cremation_receive_amount'] = str_replace(',','',$_POST["cremation_receive_amount"]);
				$data_insert['action_fee_percent'] = str_replace(',','',$_POST["action_fee_percent"]);
				$data_insert['cremation_balance_amount'] = str_replace(',','',$_POST["cremation_balance_amount"]);
				$data_insert['adv_payment_balance'] = str_replace(',','',$_POST["adv_payment_balance"]);
				$data_insert['reason'] = $_POST["reason"];
				$data_insert['money_received_per_member'] = $_POST["money_received_per_member"];
				$data_insert['member_amount'] = $_POST["member_amount"];
				if(!empty($_POST["receiver"])) $data_insert['receiver'] = "receiver_".$_POST["receiver"];
				$data_insert['cremation_receive_status'] = '0';
				$data_insert['finance_month_status'] = '0';
				$data_insert['admin_id'] = $_SESSION['USER_ID'];
				$data_insert['updatetime'] = date('Y-m-d H:i:s');
				$data_insert['createdatetime'] = date('Y-m-d H:i:s');
				$this->db->insert('coop_cremation_request_receive', $data_insert);
				$receive_id = $this->db->insert_id();

				//Update request member status
				$data_insert = array();
				$data_insert['cremation_status'] = '7';
				$this->db->where('cremation_request_id', $member_cremation->cremation_request_id);
				$this->db->update('coop_cremation_request', $data_insert);

				//Update member cremation status
				$data_insert = array();
				$data_insert['status'] = '2';
				$death_date = date('Y-m-d');
				if($_POST['death_date']){
					$death_date_arr = explode('/',@$_POST['death_date']);
					$death_day = $death_date_arr[0];
					$death_month = $death_date_arr[1];
					$death_year = $death_date_arr[2];
					$death_year -= 543;
					$death_date = $death_year.'-'.$death_month.'-'.$death_day;
				}
				$data_insert['death_date'] = $death_date;
				$this->db->where('member_cremation_id', $member_cremation->member_cremation_id);
				$this->db->update('coop_member_cremation', $data_insert);

				$output_dir = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/cremation_request/";
				if(!@mkdir($output_dir,0,true)){
				chmod($output_dir, 0777);
				}else{
				chmod($output_dir, 0777);
				}

				if($_FILES['testament']['name']!='') {
					$value_file = $_FILES['testament']['name'];
					$fileName = array();
					$list_dir = array(); 
					$cdir = scandir($output_dir); 
					foreach ($cdir as $key => $value) { 
						if (!in_array($value,array(".",".."))) { 
							if (is_dir($dir . DIRECTORY_SEPARATOR . $value)){ 
							$list_dir[$value] = dirToArray(@$dir . DIRECTORY_SEPARATOR . $value); 
							}else{
							if(substr($value,0,8) == date('Ymd')){
							$list_dir[] = $value;
							}
							} 
						} 
					}
					$explode_arr=array();
					foreach($list_dir as $key => $value) {
						$task = explode('.',$value);
						$task2 = explode('_',$task[0]);
						$explode_arr[] = $task2[1];
					}
					$max_run_num = sprintf("%04d",count($explode_arr)+1);
					$explode_old_file = explode('.',$_FILES["testament"]["name"]);
					$new_file_name = date('Ymd')."_".$max_run_num.".".$explode_old_file[(count($explode_old_file)-1)];
					if(!is_array($_FILES["testament"]["name"])) {
						$fileName['file_name'] = $new_file_name;
						$fileName['file_type'] = $_FILES["testament"]["type"];
						$fileName['file_old_name'] = $_FILES["testament"]["name"];
						$fileName['file_path'] = $output_dir.$fileName['file_name'];
						move_uploaded_file($_FILES["testament"]["tmp_name"],$output_dir.$fileName['file_name']);
						
						$data_insert = array();
						$data_insert['cremation_receive_id'] = $receive_id;
						$data_insert['file_name'] = $fileName['file_name'];
						$data_insert['file_type'] = $fileName['file_type'];
						$data_insert['file_old_name'] = $fileName['file_old_name'];
						$data_insert['file_path'] = $fileName['file_path'];
						$data_insert['type'] = 'testament';
						$this->db->insert('coop_cremation_receive_file_attach', $data_insert);
					}
				}

				if($_FILES['evidence']['name']!='') {
					$value_file = $_FILES['evidence']['name'];
					$fileName = array();
					$list_dir = array(); 
					$cdir = scandir($output_dir); 
					foreach ($cdir as $key => $value) { 
						if (!in_array($value,array(".",".."))) { 
							if (is_dir($dir . DIRECTORY_SEPARATOR . $value)){ 
							$list_dir[$value] = dirToArray(@$dir . DIRECTORY_SEPARATOR . $value); 
							}else{
							if(substr($value,0,8) == date('Ymd')){
							$list_dir[] = $value;
							}
							}
						} 
					}
					$explode_arr=array();
					foreach($list_dir as $key => $value) {
						$task = explode('.',$value);
						$task2 = explode('_',$task[0]);
						$explode_arr[] = $task2[1];
					}
					$max_run_num = sprintf("%04d",count($explode_arr)+1);
					$explode_old_file = explode('.',$_FILES["evidence"]["name"]);
					$new_file_name = date('Ymd')."_".$max_run_num.".".$explode_old_file[(count($explode_old_file)-1)];
					if(!is_array($_FILES["evidence"]["name"])) {
						$fileName['file_name'] = $new_file_name;
						$fileName['file_type'] = $_FILES["evidence"]["type"];
						$fileName['file_old_name'] = $_FILES["evidence"]["name"];
						$fileName['file_path'] = $output_dir.$fileName['file_name'];
						move_uploaded_file($_FILES["evidence"]["tmp_name"],$output_dir.$fileName['file_name']);
						
						$data_insert = array();
						$data_insert['cremation_receive_id'] = $receive_id;
						$data_insert['file_name'] = $fileName['file_name'];
						$data_insert['file_type'] = $fileName['file_type'];
						$data_insert['file_old_name'] = $fileName['file_old_name'];
						$data_insert['file_path'] = $fileName['file_path'];
						$data_insert['type'] = 'evidence';
						$this->db->insert('coop_cremation_receive_file_attach', $data_insert);
					}
				}
			} else {
				$cremation_receive_id = $_POST["cremation_receive_id"];
				$request_receive = $this->db->select("*")
											->from("coop_cremation_request_receive")
											->where("cremation_receive_id = '".$cremation_receive_id."'")
											->get()->row();

				//Update receive request
				$data_insert = array();
				$data_insert['cremation_receive_amount'] = str_replace(',','',$_POST["cremation_receive_amount"]);
				$data_insert['action_fee_percent'] = str_replace(',','',$_POST["action_fee_percent"]);
				$data_insert['cremation_balance_amount'] = str_replace(',','',$_POST["cremation_balance_amount"]);
				$data_insert['adv_payment_balance'] = str_replace(',','',$_POST["adv_payment_balance"]);
				$data_insert['reason'] = $_POST["reason"];
				if(!empty($_POST["receiver"])) $data_insert['receiver'] = "receiver_".$_POST["receiver"];
				$data_insert['cremation_receive_status'] = '0';
				$data_insert['admin_id'] = $_SESSION['USER_ID'];
				$data_insert['updatetime'] = date('Y-m-d H:i:s');
				$this->db->where('cremation_receive_id', $cremation_receive_id);
				$this->db->update("coop_cremation_request_receive", $data_insert);

				//Update member cremation status
				$data_insert = array();
				$data_insert['status'] = '2';
				$death_date = date('Y-m-d');
				if($_POST['death_date']){
					$death_date_arr = explode('/',@$_POST['death_date']);
					$death_day = $death_date_arr[0];
					$death_month = $death_date_arr[1];
					$death_year = $death_date_arr[2];
					$death_year -= 543;
					$death_date = $death_year.'-'.$death_month.'-'.$death_day;
				}
				$data_insert['death_date'] = $death_date;
				$this->db->where('member_cremation_id', $_POST["member_cremation_id"]);
				$this->db->update('coop_member_cremation', $data_insert);

				$output_dir = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/cremation_request/";
				if(!@mkdir($output_dir,0,true)){
					chmod($output_dir, 0777);
				}else{
					chmod($output_dir, 0777);
				}

				if($_FILES['testament']['name']!='') {
					//Delete old file
					$files = $this->db->select("*")->from("coop_cremation_receive_file_attach")
										->where("cremation_receive_id = '".$cremation_receive_id."' AND type = 'testament'")
										->get()->result_array();
					foreach($files as $row) {
						$file = $output_dir.$row['file_name'];
						unlink($file);
						//Delete file data
						$this->db->where("id", $row["id"]);
						$this->db->delete("coop_cremation_receive_file_attach");
					}

					$value_file = $_FILES['testament']['name'];
					$fileName = array();
					$list_dir = array(); 
					$cdir = scandir($output_dir); 
					foreach ($cdir as $key => $value) { 
						if (!in_array($value,array(".",".."))) { 
							if (is_dir($dir . DIRECTORY_SEPARATOR . $value)){ 
							$list_dir[$value] = dirToArray(@$dir . DIRECTORY_SEPARATOR . $value); 
							}else{
							if(substr($value,0,8) == date('Ymd')){
							$list_dir[] = $value;
							}
							} 
						} 
					}
					$explode_arr=array();
					foreach($list_dir as $key => $value) {
						$task = explode('.',$value);
						$task2 = explode('_',$task[0]);
						$explode_arr[] = $task2[1];
					}
					$max_run_num = sprintf("%04d",count($explode_arr)+1);
					$explode_old_file = explode('.',$_FILES["testament"]["name"]);
					$new_file_name = date('Ymd')."_".$max_run_num.".".$explode_old_file[(count($explode_old_file)-1)];
					if(!is_array($_FILES["testament"]["name"])) {
						$fileName['file_name'] = $new_file_name;
						$fileName['file_type'] = $_FILES["testament"]["type"];
						$fileName['file_old_name'] = $_FILES["testament"]["name"];
						$fileName['file_path'] = $output_dir.$fileName['file_name'];
						move_uploaded_file($_FILES["testament"]["tmp_name"],$output_dir.$fileName['file_name']);
						
						$data_insert = array();
						$data_insert['cremation_receive_id'] = $cremation_receive_id;
						$data_insert['file_name'] = $fileName['file_name'];
						$data_insert['file_type'] = $fileName['file_type'];
						$data_insert['file_old_name'] = $fileName['file_old_name'];
						$data_insert['file_path'] = $fileName['file_path'];
						$data_insert['type'] = 'testament';
						$this->db->insert('coop_cremation_receive_file_attach', $data_insert);
					}
				}

				if($_FILES['evidence']['name']!='') {
					//Delete old file
					$files = $this->db->select("*")->from("coop_cremation_receive_file_attach")
										->where("cremation_receive_id = '".$cremation_receive_id."' AND type = 'evidence'")
										->get()->result_array();
					foreach($files as $row) {
						$file = $output_dir.$row['file_name'];
						unlink($file);
						//Delete file data
						$this->db->where("id", $row["id"]);
						$this->db->delete("coop_cremation_receive_file_attach");
					}

					$value_file = $_FILES['evidence']['name'];
					$fileName = array();
					$list_dir = array(); 
					$cdir = scandir($output_dir); 
					foreach ($cdir as $key => $value) { 
						if (!in_array($value,array(".",".."))) { 
							if (is_dir($dir . DIRECTORY_SEPARATOR . $value)){ 
							$list_dir[$value] = dirToArray(@$dir . DIRECTORY_SEPARATOR . $value); 
							}else{
							if(substr($value,0,8) == date('Ymd')){
							$list_dir[] = $value;
							}
							}
						} 
					}
					$explode_arr=array();
					foreach($list_dir as $key => $value) {
						$task = explode('.',$value);
						$task2 = explode('_',$task[0]);
						$explode_arr[] = $task2[1];
					}
					$max_run_num = sprintf("%04d",count($explode_arr)+1);
					$explode_old_file = explode('.',$_FILES["evidence"]["name"]);
					$new_file_name = date('Ymd')."_".$max_run_num.".".$explode_old_file[(count($explode_old_file)-1)];
					if(!is_array($_FILES["evidence"]["name"])) {
						$fileName['file_name'] = $new_file_name;
						$fileName['file_type'] = $_FILES["evidence"]["type"];
						$fileName['file_old_name'] = $_FILES["evidence"]["name"];
						$fileName['file_path'] = $output_dir.$fileName['file_name'];
						move_uploaded_file($_FILES["evidence"]["tmp_name"],$output_dir.$fileName['file_name']);
						
						$data_insert = array();
						$data_insert['cremation_receive_id'] = $cremation_receive_id;
						$data_insert['file_name'] = $fileName['file_name'];
						$data_insert['file_type'] = $fileName['file_type'];
						$data_insert['file_old_name'] = $fileName['file_old_name'];
						$data_insert['file_path'] = $fileName['file_path'];
						$data_insert['type'] = 'evidence';
						$this->db->insert('coop_cremation_receive_file_attach', $data_insert);
					}
				}
			}
		}
		$x=0;
		$join_arr = array();
		$join_arr[$x]['table'] = 'coop_member_cremation as t2';
		$join_arr[$x]['condition'] = 't2.member_cremation_id = t1.member_cremation_id';
		$join_arr[$x]['type'] = 'inner';
		$x++;
		$join_arr[$x]['table'] = 'coop_user as t3';
		$join_arr[$x]['condition'] = 't1.admin_id = t3.user_id';
		$join_arr[$x]['type'] = 'left';
		$x++;
		$join_arr[$x]['table'] = 'coop_prename as t6';
		$join_arr[$x]['condition'] = 't2.prename_id = t6.prename_id';
		$join_arr[$x]['type'] = 'left';

		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select('t1.cremation_receive_id, t1.cremation_request_id, t1.member_cremation_id, t1.cremation_balance_amount, t1.cremation_receive_status, t1.createdatetime, t1.receiver,
										t2.assoc_firstname, t2.assoc_lastname, t2.receiver_1, t2.receiver_2, t2.receiver_3, t3.user_name, t6.prename_full');
		$this->paginater_all->main_table('coop_cremation_request_receive as t1');
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(10);
		$this->paginater_all->page_link_limit(20);
		$this->paginater_all->order_by('t1.createdatetime');
		$this->paginater_all->join_arr($join_arr);
		$this->paginater_all->where("t1.cremation_receive_status IN ('0')");
		$row = $this->paginater_all->paginater_process();
		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit']);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		$i = $row['page_start'];

		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];
		$arr_data['i'] = $i;

		$arr_data['receive_status'] = array('0'=>'รออนุมัติ', '1'=>'อนุมัติ','2'=>'ไม่อนุมัติ');

		$members = $this->db->select("*")->from("coop_member_cremation")->where("approve_status = 1 AND death_date is null")->get()->result_array();
		$members = $this->db->select("*")
							->from("coop_member_cremation as t1")
							->join("coop_cremation_request_receive as t2", "t1.member_cremation_id = t2.member_cremation_id AND t2.cremation_receive_status = 1", "left")
							->join("coop_cremation_request_resign as t3", "t1.member_cremation_id = t3.member_cremation_id AND t3.status IN (1,3)", "left")
							->where("t1.member_cremation_id is not null AND t2.cremation_receive_id is null AND t3.id IS NULL")->get()->result_array();
		$arr_data["count_members"] = count($members) - 1;

		$settings = $this->db->select("*")->from("coop_setting_cremation_detail")->where("cremation_id = 2 AND start_date <= NOW()")->order_by("start_date")->get()->result_array();
		$arr_data["setting"] = $settings[0];

		$this->db->select('bank_id, bank_name');
		$this->db->from('coop_bank');
		$row = $this->db->get()->result_array();
		$arr_data['bank'] = $row;
		$this->libraries->template('cremation/cremation_request_money',$arr_data);
	}

	public function delete_cremation_request_receive() {
		$cremation_receive_id = $_POST["id"];
		$cremation_request_receive = $this->db->select("*")
												->from("coop_cremation_request_receive")
												->where("cremation_receive_id = '".$cremation_receive_id."'")
												->get()->row();
		$member_cremation_id = $cremation_request_receive->member_cremation_id;
		$cremation_request_id = $cremation_request_receive->cremation_request_id;

		//Delete file
		$files = $this->db->select("*")->from("coop_cremation_receive_file_attach")
										->where("cremation_receive_id = '".$cremation_receive_id."'")
										->get()->result_array();
		$attach_path = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/cremation_request/";
		foreach($files as $file) {
			$file = $attach_path.$file['file_name'];
			unlink($file);
		}

		//Delete file data
		$this->db->where("cremation_receive_id", $cremation_receive_id);
		$this->db->delete("coop_cremation_receive_file_attach");

		//Delete receive data
		$this->db->where("cremation_receive_id", $cremation_receive_id);
		$this->db->delete("coop_cremation_request_receive");

		//Re-status of cremation if do not have any request receive
		$cremation_request_receive = $this->db->select("*")
												->from("coop_cremation_request_receive")
												->where("cremation_receive_id = '".$cremation_receive_id."'")
												->get()->row();
		if(empty($cremation_request_receive)) {
			$data_update = array();
			$data_update["death_date"] = null;
			$this->db->where('member_cremation_id', $member_cremation_id);
			$this->db->update("coop_member_cremation", $data_update);

			$data_update = array();
			$data_update["cremation_status"] = 6;
			$this->db->where('cremation_request_id', $cremation_request_id);
			$this->db->update("coop_cremation_request", $data_update);
		}
		echo "success";
	}

	public function cremation_request_resign() {
		$arr_data = array();

		if(!empty($_POST)) {
			$process_timestamp = date('Y-m-d H:i:s');
			if(empty($_POST["cremation_resign_id"])) {
				$data_insert = array();
				$data_insert['cremation_request_id'] = $_POST["cremation_request_id"];
				$data_insert['member_cremation_id'] = $_POST["member_cremation_id"];
				$data_insert['reason'] = $_POST["reason"];
				$data_insert['adv_payment_balance'] = $_POST["adv_payment_balance"];
				$data_insert["status"] = 0;
				$data_insert["type"] = 1;
				$data_insert['admin_id'] = $_SESSION['USER_ID'];
				$data_insert["created_at"] = $process_timestamp;
				$data_insert["updated_at"] = $process_timestamp;
				$this->db->insert('coop_cremation_request_resign', $data_insert);
				$req_id = $this->db->insert_id();

				if($_FILES['file']['name']!='') {
					$output_dir = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/cremation_request/";
					$value_file = $_FILES['file']['name'];
					$fileName = array();
					$list_dir = array(); 
					$cdir = scandir($output_dir); 
					foreach ($cdir as $key => $value) { 
						if (!in_array($value,array(".",".."))) { 
							if (is_dir($dir . DIRECTORY_SEPARATOR . $value)){ 
							$list_dir[$value] = dirToArray(@$dir . DIRECTORY_SEPARATOR . $value); 
							}else{
							if(substr($value,0,8) == date('Ymd')){
							$list_dir[] = $value;
							}
							} 
						} 
					}
					$explode_arr=array();
					foreach($list_dir as $key => $value) {
						$task = explode('.',$value);
						$task2 = explode('_',$task[0]);
						$explode_arr[] = $task2[1];
					}
					$max_run_num = sprintf("%04d",count($explode_arr)+1);
					$explode_old_file = explode('.',$_FILES["file"]["name"]);
					$new_file_name = date('Ymd')."_".$max_run_num.".".$explode_old_file[(count($explode_old_file)-1)];
					if(!is_array($_FILES["file"]["name"])) {
						$fileName['file_name'] = $new_file_name;
						$fileName['file_type'] = $_FILES["file"]["type"];
						$fileName['file_old_name'] = $_FILES["file"]["name"];
						$fileName['file_path'] = $output_dir.$fileName['file_name'];
						move_uploaded_file($_FILES["file"]["tmp_name"],$output_dir.$fileName['file_name']);
						$data_insert = array();
						$data_insert['cremation_resign_id'] = $req_id;
						$data_insert['file_name'] = $fileName['file_name'];
						$data_insert['file_type'] = $fileName['file_type'];
						$data_insert['file_old_name'] = $fileName['file_old_name'];
						$data_insert['file_path'] = $fileName['file_path'];
						$this->db->insert('coop_cremation_resign_file_attach', $data_insert);
					}
				}

				$data_update = array();
				$data_update["cremation_status"] = 10;
				$data_insert["updatetime"] = $process_timestamp;
				$this->db->where('cremation_request_id', $_POST["cremation_request_id"]);
				$this->db->update('coop_cremation_request', $data_update);
			} else {
				$data_update = array();
				$data_update['reason'] = $_POST["reason"];
				$data_insert["updated_at"] = $process_timestamp;
				$this->db->where('id', $_POST["cremation_resign_id"]);
				$this->db->update('coop_cremation_request_resign', $data_update);

				if($_FILES['file']['name']!='') {
					$output_dir = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/cremation_request/";
					if(!@mkdir($output_dir,0,true)){
						@chmod($output_dir, 0777);
					}else{
						@chmod($output_dir, 0777);
					}

					$old_file = $this->db->select("*")->from("coop_cremation_resign_file_attach")->where("cremation_resign_id = '".$_POST["cremation_resign_id"]."'")->get()->row();
					if(!empty($old_file)) {
						unlink($output_dir.$old_file->file_name);
					}
					$this->db->where("cremation_resign_id", $_POST["cremation_resign_id"]);
					$this->db->delete("coop_cremation_resign_file_attach");

					$value_file = $_FILES['file']['name'];
					$fileName = array();
					$list_dir = array(); 
					$cdir = scandir($output_dir); 
					foreach ($cdir as $key => $value) { 
						if (!in_array($value,array(".",".."))) { 
							if (is_dir($dir . DIRECTORY_SEPARATOR . $value)){ 
								$list_dir[$value] = dirToArray(@$dir . DIRECTORY_SEPARATOR . $value); 
							}else{
								if(substr($value,0,8) == date('Ymd')){
									$list_dir[] = $value;
								}
							} 
						} 
					}
					$explode_arr=array();
					foreach($list_dir as $key => $value) {
						$task = explode('.',$value);
						$task2 = explode('_',$task[0]);
						$explode_arr[] = $task2[1];
					}
					$max_run_num = sprintf("%04d",count($explode_arr)+1);
					$explode_old_file = explode('.',$_FILES["file"]["name"]);
					$new_file_name = date('Ymd')."_".$max_run_num.".".$explode_old_file[(count($explode_old_file)-1)];
					if(!is_array($_FILES["file"]["name"])) {
						$fileName['file_name'] = $new_file_name;
						$fileName['file_type'] = $_FILES["file"]["type"];
						$fileName['file_old_name'] = $_FILES["file"]["name"];
						$fileName['file_path'] = $output_dir.$fileName['file_name'];
						move_uploaded_file($_FILES["file"]["tmp_name"],$output_dir.$fileName['file_name']);
						$data_insert = array();
						$data_insert['cremation_resign_id'] = $_POST["cremation_resign_id"];
						$data_insert['file_name'] = $fileName['file_name'];
						$data_insert['file_type'] = $fileName['file_type'];
						$data_insert['file_old_name'] = $fileName['file_old_name'];
						$data_insert['file_path'] = $fileName['file_path'];
						$this->db->insert('coop_cremation_resign_file_attach', $data_insert);
					}
				}
			}
		}

		$x=0;
		$join_arr = array();
		$join_arr[$x]['table'] = 'coop_member_cremation as t2';
		$join_arr[$x]['condition'] = 't1.member_cremation_id = t2.member_cremation_id';
		$join_arr[$x]['type'] = 'inner';
		$x++;
		$join_arr[$x]['table'] = 'coop_cremation_request as t3';
		$join_arr[$x]['condition'] = 't1.cremation_request_id = t3.cremation_request_id';
		$join_arr[$x]['type'] = 'inner';
		$x++;
		$join_arr[$x]['table'] = 'coop_prename as t4';
		$join_arr[$x]['condition'] = 't2.prename_id = t4.prename_id';
		$join_arr[$x]['type'] = 'left';
		$x++;
		$join_arr[$x]['table'] = 'coop_user as t5';
		$join_arr[$x]['condition'] = 't1.admin_id = t5.user_id';
		$join_arr[$x]['type'] = 'left';
	
		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select('t1.id as resign_request_id, t1.adv_payment_balance, t1.reason, t1.member_cremation_id, t1.created_at, t2.assoc_firstname, t2.assoc_lastname, t3.cremation_status, t4.prename_full, t5.user_name');
		$this->paginater_all->main_table('coop_cremation_request_resign as t1');
		$this->paginater_all->where("t1.status IN ('0')");
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(20);
		$this->paginater_all->page_link_limit(20);
		$this->paginater_all->order_by('createdatetime DESC');
		$this->paginater_all->join_arr($join_arr);
		$row = $this->paginater_all->paginater_process();
		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit']);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		$i = $row['page_start'];

		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];
		$arr_data['i'] = $i;
		
		$this->libraries->template('cremation/cremation_resign',$arr_data);
	}

	public function get_cremation_request_resign() {
		$result = $this->db->select("t1.id, t1.adv_payment_balance, t1.member_cremation_id, t1.cremation_request_id, t1.reason, t2.assoc_firstname, t2.assoc_lastname, t3.prename_full, t4.file_old_name as file_name")
							->from("coop_cremation_request_resign as t1")
							->join("coop_member_cremation as t2", "t1.member_cremation_id = t2.member_cremation_id", "inner")
							->join("coop_prename as t3", "t2.prename_id = t3.prename_id", "left")
							->join("coop_cremation_resign_file_attach as t4", "t1.id = t4.cremation_resign_id", "left")
							->where("t1.id = '".$_GET["id"]."'")
							->get()->row();
		echo json_encode($result);
		exit();
	}

	public function delete_cremation_request_resign() {
		$request_resign = $this->db->select("*")->from("coop_cremation_request_resign")->where("id = '".$_POST["id"]."'")->get()->row();
		$this->db->where("id", $_POST["id"]);
		$this->db->delete("coop_cremation_request_resign");

		$old_file = $this->db->select("*")->from("coop_cremation_resign_file_attach")->where("cremation_resign_id = '".$_POST["id"]."'")->get()->row();
		if(!empty($old_file)) {
			unlink($output_dir.$old_file->file_name);
		}
		$this->db->where("cremation_resign_id", $_POST["id"]);
		$this->db->delete("coop_cremation_resign_file_attach");

		$req = $this->db->select("*")->from("coop_cremation_request_resign")->where("cremation_request_id = '".$request_resign->cremation_request_id."'")->get()->row();
		if(empty($req)) {
			$data_update = array();
			$data_update['cremation_status'] = 6;
			$data_update["updatetime"] = date('Y-m-d H:i:s');
			$this->db->where('cremation_request_id', $request_resign->cremation_request_id);
			$this->db->update('coop_cremation_request', $data_update);
		}
	}

	public function cremation_approve_resign(){
		$arr_data = array();

		if(!empty($_POST)) {
			$process_timestamp = date('Y-m-d H:i:s');
			if($_POST["action"] == "approve") {
				$request = $this->db->select("*")->from("coop_cremation_request_resign")->where("id = '".$_POST["id"]."'")->get()->row();
				$data_update = array();
				$data_update['status'] = 1;
				$data_update['admin_id_approve'] = $_SESSION['USER_ID'];
				$data_update["updated_at"] = $process_timestamp;
				$this->db->where('id', $request->id);
				$this->db->update('coop_cremation_request_resign', $data_update);

				$data_update = array();
				$data_update['cremation_status'] = 9;
				$data_update["updatetime"] = $process_timestamp;
				$this->db->where('cremation_request_id', $request->cremation_request_id);
				$this->db->update('coop_cremation_request', $data_update);

				//create transfer
				$transfer = array();
				$transfer['cremation_resign_id'] = $request->id;
				$transfer['cremation_request_id'] = $request->cremation_request_id;
				$transfer['date_transfer'] = $process_timestamp;
				$transfer['transfer_status'] = '0';
				$this->db->insert('coop_cremation_transfer', $transfer);
			} else if ($_POST["action"] == "reject") {
				$request = $this->db->select("*")->from("coop_cremation_request_resign")->where("id = '".$_POST["id"]."'")->get()->row();
				$data_update = array();
				$data_update['status'] = 2;
				$data_update["updated_at"] = $process_timestamp;
				$this->db->where('id', $request->id);
				$this->db->update('coop_cremation_request_resign', $data_update);

				$data_update = array();
				$advance_pay = $this->db->select("*")->from("coop_cremation_advance_payment")->where("member_cremation_id = '".$request->member_cremation_id."'")->get()->row();
				$data_update['cremation_status'] = !empty($advance_pay) ? 6 : 1;
				$data_update["updatetime"] = $process_timestamp;
				$this->db->where('cremation_request_id', $request->cremation_request_id);
				$this->db->update('coop_cremation_request', $data_update);
			} else if ($_POST["action"] == "approve_all") {
				$requests = $this->db->select("*")->from("coop_cremation_request_resign")->where("id in (".implode(',',$_POST["ids"]).")")->get()->result_array();
				$resign_updates = array();
				$request_updates = array();
				$transfer_inserts = array();
				foreach($requests as $request) {
					$resign_update = array();
					$resign_update["id"] = $request["id"];
					$resign_update['status'] = 1;
					$resign_update["updated_at"] = $process_timestamp;
					$resign_updates[] = $resign_update;

					$request_update = array();
					$request_update["cremation_request_id"] = $request["cremation_request_id"];
					$request_update['cremation_status'] = 9;
					$request_update["updatetime"] = $process_timestamp;
					$request_updates[] = $request_update;

					$transfer_insert = array();
					$transfer_insert["cremation_resign_id"] = $request["id"];
					$transfer_insert["cremation_request_id"] = $request["cremation_request_id"];
					$transfer_insert['transfer_status'] = '0';
					$transfer_insert["date_transfer"] = $process_timestamp;
					$transfer_inserts[] = $transfer_insert;
				}
				$this->db->update_batch('coop_cremation_request_resign', $resign_updates, 'id');
				$this->db->update_batch('coop_cremation_request', $request_updates, 'cremation_request_id');
				if (!empty($transfer_inserts)) {
					$this->db->insert_batch('coop_cremation_transfer', $transfer_inserts);
				}
			} else if ($_POST["action"] == "reject_all") {
				$requests = $this->db->select("*")->from("coop_cremation_request_resign")->where("id in (".implode(',',$_POST["ids"]).")")->get()->result_array();
				$resign_updates = array();
				$request_updates = array();
				foreach($requests as $request) {
					$resign_update = array();
					$resign_update["id"] = $request["id"];
					$resign_update['status'] = 2;
					$resign_update["updated_at"] = $process_timestamp;
					$resign_updates[] = $resign_update;

					$request_update = array();
					$request_update["cremation_request_id"] = $request["cremation_request_id"];
					$advance_pay = $this->db->select("*")->from("coop_cremation_advance_payment")->where("member_cremation_id = '".$request["member_cremation_id"]."'")->get()->row();
					$request_update['cremation_status'] = !empty($advance_pay) ? 6 : 1;
					$request_update["updatetime"] = $process_timestamp;
					$request_updates[] = $request_update;
				}
				$this->db->update_batch('coop_cremation_request_resign', $resign_updates, 'id');
				$this->db->update_batch('coop_cremation_request', $request_updates, 'cremation_request_id');
			}
		}

		$x=0;
		$join_arr = array();
		$join_arr[$x]['table'] = 'coop_member_cremation as t2';
		$join_arr[$x]['condition'] = 't1.member_cremation_id = t2.member_cremation_id';
		$join_arr[$x]['type'] = 'inner';
		$x++;
		$join_arr[$x]['table'] = 'coop_prename as t3';
		$join_arr[$x]['condition'] = 't3.prename_id = t2.prename_id';
		$join_arr[$x]['type'] = 'left';
		$x++;
		$join_arr[$x]['table'] = 'coop_user as t4';
		$join_arr[$x]['condition'] = 't4.user_id = t1.admin_id';
		$join_arr[$x]['type'] = 'left';
		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select('t1.id, t1.created_at, t1.member_cremation_id, t1.status, t2.mem_type_id, t2.assoc_firstname, t2.assoc_lastname, t3.prename_full, t4.user_name');
		$this->paginater_all->main_table('coop_cremation_request_resign as t1');
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(10);
		$this->paginater_all->page_link_limit(20);
		$this->paginater_all->order_by('created_at desc');
		$this->paginater_all->join_arr($join_arr);
		$this->paginater_all->where("t1.status in (0,1,2)");
		$row = $this->paginater_all->paginater_process();
		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit']);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		$i = $row['page_start'];

		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];
		$arr_data['i'] = $i;

		$arr_data['receive_status'] = array('0'=>'รออนุมัติ', '1'=>'อนุมัติ','2'=>'ไม่อนุมัติ');

		$this->db->select('bank_id, bank_name');
		$this->db->from('coop_bank');
		$row = $this->db->get()->result_array();
		$arr_data['bank'] = $row;

		$this->libraries->template('cremation/cremation_approve_resign',$arr_data);
	}

	public function debt() {
		$data_arr = array();
		$where = "t1.pay_amount != t1.real_pay_amount AND ((t1.profile_id IS NOT null AND t2.profile_id IS NOT null) OR t1.year IS NOT NULL)";
		$on_t2 = "";

		if(!empty($_GET["month"])) {
			$on_t2 = " AND profile_month = '".$_GET["month"]."'";
		}
		if(!empty($_GET["year"])) {
			// $on_t2 .= " AND profile_year = '".$_GET["year"]."'";
			$where .= " AND ((t1.profile_id IS NOT null AND t2.profile_year = '".$_GET["year"]."') OR t1.year = '".$_GET["year"]."')";
		}
		if(!empty($_GET["search_text"])) {
			$where .= " AND (t3.firstname_th LIKE '%".$_GET["search_text"]."%' OR t3.lastname_th  LIKE '%".$_GET["search_text"]."%')";
		}

		$x=0;
		$join_arr = array();
		$join_arr[$x]['table'] = 'coop_finance_month_profile as t2';
		$join_arr[$x]['condition'] = 't1.profile_id = t2.profile_id'.$on_t2;
		$join_arr[$x]['type'] = 'left';
		$x++;
		$join_arr[$x]['table'] = 'coop_member_cremation as t3';
		$join_arr[$x]['condition'] = 't1.member_cremation_id = t3.member_cremation_id';
		$join_arr[$x]['type'] = 'inner';
		$x++;
		$join_arr[$x]['table'] = 'coop_cremation_debt_letter as t4';
		$join_arr[$x]['condition'] = 't4.member_cremation_id = t1.member_cremation_id AND t4.runno = 1 AND t4.status = 1 AND t4.month = t2.profile_month AND t4.year = t2.profile_year';
		$join_arr[$x]['type'] = 'left';
		$x++;
		$join_arr[$x]['table'] = 'coop_cremation_debt_letter as t5';
		$join_arr[$x]['condition'] = 't5.member_cremation_id = t1.member_cremation_id AND t5.runno = 2 AND t5.status = 1 AND t5.month = t2.profile_month AND t5.year = t2.profile_year';
		$join_arr[$x]['type'] = 'left';
		$x++;
		$join_arr[$x]['table'] = 'coop_prename as t6';
		$join_arr[$x]['condition'] = 't3.prename_id = t6.prename_id';
		$join_arr[$x]['type'] = 'left';
		$x++;
		$join_arr[$x]['table'] = 'coop_cremation_request as t7';
		$join_arr[$x]['condition'] = 't1.member_cremation_id = t7.member_cremation_id';
		$join_arr[$x]['type'] = 'inner';

		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select('t1.id, t1.member_cremation_id, t1.pay_amount, t1.real_pay_amount, t1.year as debt_year,
										t2.profile_month as month, t2.profile_year as year, t6.prename_full,
										t3.assoc_firstname as firstname_th, t3.assoc_lastname as lastname_th, t3.member_id,
										t4.id as first_letter_id, t5.id as second_letter_id, t7.cremation_status');
		$this->paginater_all->main_table('coop_cremation_finance_month as t1');
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(20);
		$this->paginater_all->page_link_limit(20);
		$this->paginater_all->order_by('t1.member_cremation_id');
		$this->paginater_all->join_arr($join_arr);
		$this->paginater_all->where($where);
		$row = $this->paginater_all->paginater_process();
		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'], $_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		$i = $row['page_start'];

		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['datas'] = $row['data'];
		$arr_data['i'] = $i;
		$arr_data["page_start"] = $row["page_start"];
		$arr_data['month_arr'] = $this->month_arr;

		$this->libraries->template('cremation/cremation_debt_non_member',$arr_data);
	}

	public function save_debt_letter() {
		$process_timestamp = date('Y-m-d H:i:s');
		$non_pay_ids = array();

		$prev_letter = $this->db->select("print_ref")->from("coop_cremation_debt_letter")->where("print_ref is not null")->order_by("print_ref desc")->get()->result_array();
		$print_ref = !empty($prev_letter) ? $prev_letter[0]["print_ref"] + 1 : 1 ;

		if(!empty($_POST['non_pay_ids'])) {
			$non_pay_ids = $_POST['non_pay_ids'];
		} else if(!empty($_POST["non_pay_id"])) {
			$non_pay_ids[] = $_POST["non_pay_id"];
		}

		$non_pays = $this->db->select("t1.non_pay_id, t1.non_pay_month, t1.non_pay_year, t1.member_id, t2.non_pay_amount, t2.non_pay_amount_balance, t3.pay_amount")
								->from("coop_non_pay as t1")
								->join("coop_non_pay_detail as t2", "t1.non_pay_id = t2.non_pay_id AND t2.deduct_code = 'CREMATION'", "inner")
								->join("coop_finance_month_detail as t3", "t2.finance_month_detail_id = t3.run_id", "left")
								->where("t1.non_pay_id in (".implode(",",$non_pay_ids).")")
								->get()->result_array();

		$data_inserts = array();
		foreach($non_pays as $non_pay) {
			$letters = $this->db->select("*")->from("coop_cremation_debt_letter")->where("non_pay_id = '".$non_pay["non_pay_id"]."' AND status = 1")->get()->result_array();
			$runno = count($letters) + 1;
			$cremations = $this->db->select("member_cremation_id")->from("coop_member_cremation")
									->where("approve_status = 1 AND (death_date < now() OR death_date is null)
												AND (ref_member_id = '".$non_pay["member_id"]."' OR member_id = '".$non_pay["member_id"]."')")->get()->result_array();
			$member_cremation_ids = array_column($cremations, "member_cremation_id");

			$data_insert = array();
			$data_insert["non_pay_id"] = $non_pay["non_pay_id"];
			$data_insert["member_id"] = $non_pay["member_id"];
			$data_insert["total"] = !empty($non_pay["pay_amount"]) ? $non_pay["pay_amount"] : $non_pay["non_pay_amount"];
			$data_insert["debt"] = $non_pay["non_pay_amount_balance"];
			$data_insert["runno"] = $runno;
			$data_insert["status"] = 1;
			$data_insert["member_cremation_ids"] = json_encode($member_cremation_ids);
			$data_insert["print_ref"] = $print_ref;
			$data_insert["date"] = $process_timestamp;
			$data_insert["month"] = $non_pay["non_pay_month"];
			$data_insert["year"] = $non_pay["non_pay_year"];
			$data_insert["created_at"] = $process_timestamp;
			$data_insert["updated_at"] = $process_timestamp;
			$data_inserts[] = $data_insert;
		}
		if (!empty($data_inserts)) {
			$this->db->insert_batch('coop_cremation_debt_letter', $data_inserts);
		}
		echo"<script> window.open('".PROJECTPATH."/cremation/print_debt_letter?print_ref=".$print_ref."','_blank') </script>";
		echo"<script> document.location.href='".PROJECTPATH."/cremation/debt?".$get_param."' </script>";
	}

	public function print_debt_letter() {
		$where = "t1.status = 1";
		if(!empty($_GET["print_ref"])) {
			$where .= " AND t1.print_ref = '".$_GET["print_ref"]."'";
		}
		if(!empty($_GET["letter_id"])) {
			$where .= " AND t1.id = '".$_GET["letter_id"]."'";
		}
		$letters = $this->db->select("t1.*, t3.assoc_firstname as firstname_th, t3.assoc_lastname as lastname_th, t4.prename_full")
							->from("coop_cremation_debt_letter as t1")
							->join("coop_member_cremation as t3", "t1.member_cremation_id = t3.member_cremation_id", "inner")
							->join("coop_prename as t4", "t3.prename_id = t4.prename_id", "left")
							->where($where)
							->get()->result_array();

		foreach($letters as $key => $letter) {
			$year = $letter["year"] - 543;
			$end_date = date("Y-m-t", strtotime($year."-".$letter["month"]."-01"));
			$start_date = $year."-".$letter["month"]."-01";
			$payments = $this->db->select("t3.assoc_firstname, t3.assoc_lastname, t4.prename_full")
									->from("coop_cremation_transfer as t1")
									->join("coop_cremation_request_receive as t2", "t1.cremation_receive_id = t2.cremation_receive_id AND t2.cremation_receive_status = '1'", "inner")
									->join("coop_member_cremation as t3", "t2.member_cremation_id = t3.member_cremation_id", "inner")
									->join("coop_prename as t4", "t3.prename_id = t4.prename_id", "left")
									->where("t1.date_transfer BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000' AND t1.cancel_date is null")
									->get()->result_array();
			$letters[$key]["cremation_receivers"] = $payments;
		}

		$data_arr["datas"] = $letters;
		$date_signature = date('Y-m-d');
		$row = $this->db->select(array('*'))
						->from('coop_signature')
						->where("start_date <= '{$date_signature}'")
						->order_by('start_date DESC')
						->limit(1)
						->get()->result_array();
		$data_arr['signature'] = @$row[0];
		$row_profile = $this->db->from('coop_profile')
								->limit(1)
								->get()->result_array();
		$data_arr['row_profile'] = $row_profile[0];
		$data_arr['month_arr'] = $this->month_arr;

		$this->preview_libraries->template_preview('cremation/debt_letter_perview',$data_arr);
	}

	public function save_debt_letter_non_member(){
		$process_timestamp = date('Y-m-d H:i:s');
		$non_pay_ids = array();

		$prev_letter = $this->db->select("print_ref")->from("coop_cremation_debt_letter")->where("print_ref is not null")->order_by("print_ref desc")->get()->result_array();
		$print_ref = !empty($prev_letter) ? $prev_letter[0]["print_ref"] + 1 : 1 ;
		$datas = array();
		$finance_months = $this->db->select("*")
									->from("coop_cremation_finance_month as t1")
									->join("coop_finance_month_profile as t2", "t1.profile_id = t2.profile_id", "left")
									->where("t1.id in (".implode(',',$_POST["set_datas"]).")")
									->get()->result_array();

		foreach($finance_months as $finance_month) {
			if(!empty($finance_month->profile_id)) {
				$letters = $this->db->select("*")->from("coop_cremation_debt_letter")
									->where("member_cremation_id = '".$finance_month["member_cremation_id"]."' AND status = 1 AND month = '".$finance_month["profile_month"]."' AND year = '".$finance_month["profile_year"]."'")
									->get()->result_array();
				$runno = count($letters) + 1;

				$data_insert = array();
				$data_insert["total"] = $finance_month["pay_amount"];
				$data_insert["debt"] = $finance_month["pay_amount"] - $finance_month["real_pay_amount"];
				$data_insert["runno"] = $runno;
				$data_insert["status"] = 1;
				$data_insert["member_cremation_id"] = $finance_month["member_cremation_id"];
				$data_insert["print_ref"] = $print_ref;
				$data_insert["date"] = $process_timestamp;
				$data_insert["month"] = (int) $finance_month["profile_month"];
				$data_insert["year"] = $finance_month["profile_year"];
				$data_insert["finance_month_id"] = $finance_month["id"];
				$data_insert["created_at"] = $process_timestamp;
				$data_insert["updated_at"] = $process_timestamp;
				$data_inserts[] = $data_insert;
			} else {
				$letters = $this->db->select("*")->from("coop_cremation_debt_letter")
									->where("member_cremation_id = '".$finance_month["member_cremation_id"]."' AND status = 1 AND debt_year = '".$finance_month["year"]."'")
									->get()->result_array();
				$runno = count($letters) + 1;

				$data_insert = array();
				$data_insert["total"] = $finance_month["pay_amount"];
				$data_insert["debt"] = $finance_month["pay_amount"] - $finance_month["real_pay_amount"];
				$data_insert["runno"] = $runno;
				$data_insert["status"] = 1;
				$data_insert["member_cremation_id"] = $finance_month["member_cremation_id"];
				$data_insert["print_ref"] = $print_ref;
				$data_insert["date"] = $process_timestamp;
				$data_insert["debt_year"] = $finance_month["year"];
				$data_insert["finance_month_id"] = $finance_month["id"];
				$data_insert["created_at"] = $process_timestamp;
				$data_insert["updated_at"] = $process_timestamp;
				$data_inserts[] = $data_insert;
			}
		}

		if (!empty($data_inserts)) {
			$this->db->insert_batch('coop_cremation_debt_letter', $data_inserts);
		}
		echo"<script> window.open('".PROJECTPATH."/cremation/print_debt_letter?print_ref=".$print_ref."','_blank') </script>";
		echo"<script> document.location.href='".PROJECTPATH."/cremation/debt?".$get_param."' </script>";
	}

	public function get_relate_cremation_info() {
		$member_id = $_GET["member_id"];
		$cremations = $this->db->select("t1.member_cremation_id, t1.assoc_firstname as firstname, t1.assoc_lastname as lastname, t3.prename_full, t2.adv_payment_balance as balance")
										->from("coop_member_cremation as t1")
										->join("coop_cremation_advance_payment as t2", "t1.member_cremation_id = t2.member_cremation_id", "left")
										->join("coop_prename as t3", "t1.prename_id = t3.prename_id", "left")
										->where("t1.approve_status = 1 AND (t1.death_date < now() OR t1.death_date is null)
													AND (t1.ref_member_id = '".$member_id."' OR t1.member_id = '".$member_id."')")
										->get()->result_array();
		echo json_encode($cremations);
	}

	public function fire_member() {
		$process_timestamp = date('Y-m-d H:i:s');
		$member_id = $_POST["member_id"];
		$member_cremation_id = $_POST["member_cremation_id"];

		$cremation_infos = $this->db->select("t1.member_cremation_id, t2.cremation_request_id")
									->from("coop_member_cremation as t1")
									->join("coop_cremation_request as t2", "t1.member_cremation_id = t2.member_cremation_id", "inner")
									->where("t1.member_cremation_id = '".$member_cremation_id."' AND approve_status = '1'")
									->get()->result_array();

		if(!empty($cremation_infos)) {
			$cremation_info = $cremation_infos[0];

			//Change Status
			$data_update = array();
			$data_update["cremation_status"] = 11;
			$data_insert["updatetime"] = $process_timestamp;
			$this->db->where('cremation_request_id', $cremation_info["cremation_request_id"]);
			$this->db->update('coop_cremation_request', $data_update);

			//Change Status
			$data_update = array();
			$data_update["status"] = 2;
			$data_insert["updatetime"] = $process_timestamp;
			$this->db->where('member_cremation_id', $cremation_info["member_cremation_id"]);
			$this->db->update('coop_member_cremation', $data_update);

			//create transfer
			$data_insert = array();
			$data_insert['cremation_request_id'] = $cremation_info["cremation_request_id"];
			$data_insert['member_cremation_id'] = $cremation_info["member_cremation_id"];
			$data_insert['reason'] = "ให้ออกเนื่องจากไม่ได้ชำระหนี้";
			$data_insert['adv_payment_balance'] = $total_balance;
			$data_insert["status"] = 1;
			$data_insert["type"] = 2;
			$data_insert['admin_id'] = $_SESSION['USER_ID'];
			$data_insert['admin_id_approve'] = $_SESSION['USER_ID'];
			$data_insert["created_at"] = $process_timestamp;
			$data_insert["updated_at"] = $process_timestamp;
			$this->db->insert('coop_cremation_request_resign', $data_insert);
			$req_id = $this->db->insert_id();

			$transfer = array();
			$transfer['cremation_resign_id'] = $req_id;
			$transfer['cremation_request_id'] = $cremation_info["cremation_request_id"];
			$transfer['date_transfer'] = $process_timestamp;
			$transfer['transfer_status'] = '0';
			$this->db->insert('coop_cremation_transfer', $transfer);
		}
		echo"<script> document.location.href='".PROJECTPATH."/cremation/debt'</script>";
	}

	public function import_thaiftsc() {
		$arr_data = array();

		if(!empty($_FILES)) {
			$this->load->library('myexcel');
			$datas = $this->read_excel($_FILES);
			if(gettype($datas) == "string") {
				$this->center_function->toastDanger($datas);
				echo "<script> document.location.href='".base_url(PROJECTPATH.'/cremation/import_thaiftsc')."' </script>";
			} else {
				if(count($datas[2]) != 9) {
					$this->center_function->toastDanger("รูปแบบเอกสารไม่ถูกต้อง");
					echo "<script> document.location.href='".base_url(PROJECTPATH.'/cremation/import_thaiftsc')."' </script>";
				} else {
					$data_inserts = array();
					$process_timestamp = date('Y-m-d H:i:s');
					foreach($datas as $key => $data) {
						if($key > 2) {
							$member_date_arr = explode('/',$data["G"]);
							$member_year = $member_date_arr[2] - 543;
							$member_date = $member_year."-".$member_date_arr[1]."-".$member_date_arr[0].' 00:00:00.000';

							$approve_date_arr = explode('/',$data["H"]);
							$approve_year = $approve_date_arr[2] - 543;
							$approve_date = $approve_year."-".$approve_date_arr[1]."-".$approve_date_arr[0].' 00:00:00.000';

							$data_insert = array();
							$data_insert["import_cremation_no"] = $data["B"];
							$data_insert["member_id"] = $data["C"];
							$data_insert["prename_full"] = $data["D"];
							$data_insert["firstname_th"] = $data["E"];
							$data_insert["lastname_th"] = $data["F"];
							$data_insert["import_start_date"] = $member_date;
							$data_insert["import_protection_date"] = $approve_date;
							$data_insert["import_total_amount_balance"] = $data["I"];
							$data_insert["import_cremation_year"] = $_POST["year"];
							$data_insert["import_cremation_type"] = 2;
							$data_insert["admin_id"] = $_SESSION['USER_ID'];
							$data_insert["createdatetime"] = $process_timestamp;
							$data_insert["updatetime"] = $process_timestamp;
							$data_inserts[] = $data_insert;
						}
					}
					if (!empty($data_inserts)) {
						$this->db->insert_batch('coop_import_data_cremation', $data_inserts);
					}
				}
			}
		}

		if(!empty($_POST["ids"])) {
			$sql = "DELETE FROM coop_import_data_cremation WHERE import_cremation_id in (".implode(',',$_POST["ids"]).");";
			$this->db->query($sql);
		}

		$where = "import_cremation_type = 2";
		if(!empty($_GET["year"])) {
			$where .= " AND import_cremation_year = '".$_GET["year"]."'";
		}
		if(!empty($_GET["member_id"])) {
			$where .= " AND member_id LIKE '%".$_GET["member_id"]."%'";
		}
		if(!empty($_GET["import_cremation_no"])) {
			$where .= " AND import_cremation_no LIKE '%".$_GET["import_cremation_no"]."%'";
		}
		if(!empty($_GET["name"])) {
			$name_arr = explode(' ',$_GET["name"]);
			$where .= " AND (";
			foreach($name_arr as $key => $name) {
				$where .= $key == 0 ? " firstname_th LIKE '%".$name."%'" : " OR firstname_th LIKE '%".$name."%'";
				$where .= " OR lastname_th LIKE '%".$name."%'";
			}
			$where .= ")";
		}
		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select("*");
		$this->paginater_all->main_table('coop_import_data_cremation');
		$this->paginater_all->where($where);
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(20);
		$this->paginater_all->page_link_limit(20);
		$row = $this->paginater_all->paginater_process();

		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'],$_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20

		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['datas'] = $row['data'];
		$arr_data["page_start"] = $row["page_start"];

		$this->libraries->template('cremation/import_thaiftsc',$arr_data);
	}

	public function import_fscct() {
		$arr_data = array();

		if(!empty($_FILES)) {
			$this->load->library('myexcel');
			$datas = $this->read_excel($_FILES);
			if(gettype($datas) == "string") {
				$this->center_function->toastDanger($datas);
				echo "<script> document.location.href='".base_url(PROJECTPATH.'/cremation/import_fscct')."' </script>";
			} else {
				if(count($datas[1]) != 10) {
					$this->center_function->toastDanger("รูปแบบเอกสารไม่ถูกต้อง");
					echo "<script> document.location.href='".base_url(PROJECTPATH.'/cremation/import_fscct')."' </script>";
				} else {
					$data_inserts = array();
					$process_timestamp = date('Y-m-d H:i:s');
					foreach($datas as $key => $data) {
						if($key > 1) {
							$prename = $this->db->select("*")->from("coop_prename")->where("prename_full = '".$data["D"]."' OR prename_short = '".$data["D"]."'")->get()->row();

							$member_date_arr = explode('/',$data["G"]);
							$member_year = $member_date_arr[2] - 543;
							$member_date = $member_year."-".$member_date_arr[1]."-".$member_date_arr[0].' 00:00:00.000';

							$approve_date_arr = explode('/',$data["H"]);
							$approve_year = $approve_date_arr[2] - 543;
							$approve_date = $approve_year."-".$approve_date_arr[1]."-".$approve_date_arr[0].' 00:00:00.000';

							$data_insert = array();
							$data_insert["import_cremation_no"] = $data["B"];
							$data_insert["member_id"] = $data["C"];
							$data_insert["prename_full"] = $data["D"];
							$data_insert["firstname_th"] = $data["E"];
							$data_insert["lastname_th"] = $data["F"];
							$data_insert["import_start_date"] = $member_date;
							$data_insert["import_protection_date"] = $approve_date;
							$data_insert["import_total_amount_balance"] = $data["I"];
							$data_insert["import_amount_balance"] = $data["J"];
							$data_insert["import_cremation_year"] = $_POST["year"];
							$data_insert["import_cremation_type"] = 1;
							$data_insert["admin_id"] = $_SESSION['USER_ID'];
							$data_insert["createdatetime"] = $process_timestamp;
							$data_insert["updatetime"] = $process_timestamp;
							$data_inserts[] = $data_insert;
						}
					}
					if (!empty($data_inserts)) {
						$this->db->insert_batch('coop_import_data_cremation', $data_inserts);
					}
				}
			}
		}

		if(!empty($_POST["ids"])) {
			$sql = "DELETE FROM coop_import_data_cremation WHERE import_cremation_id in (".implode(',',$_POST["ids"]).");";
			$this->db->query($sql);
		}

		$where = "import_cremation_type = 1";
		if(!empty($_GET["year"])) {
			$where .= " AND import_cremation_year = '".$_GET["year"]."'";
		}
		if(!empty($_GET["member_id"])) {
			$where .= " AND member_id LIKE '%".$_GET["member_id"]."%'";
		}
		if(!empty($_GET["import_cremation_no"])) {
			$where .= " AND import_cremation_no LIKE '%".$_GET["import_cremation_no"]."%'";
		}
		if(!empty($_GET["name"])) {
			$name_arr = explode(' ',$_GET["name"]);
			$where .= " AND (";
			foreach($name_arr as $key => $name) {
				$where .= $key == 0 ? " firstname_th LIKE '%".$name."%'" : " OR firstname_th LIKE '%".$name."%'";
				$where .= " OR lastname_th LIKE '%".$name."%'";
			}
			$where .= ")";
		}

		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select("*");
		$this->paginater_all->main_table('coop_import_data_cremation');
		$this->paginater_all->where($where);
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(20);
		$this->paginater_all->page_link_limit(20);
		$row = $this->paginater_all->paginater_process();

		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'],$_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20

		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['datas'] = $row['data'];
		$arr_data["page_start"] = $row["page_start"];

		$this->libraries->template('cremation/import_fscct',$arr_data);
	}

	public function download_file() {
		$this->load->helper('download');
		$path = "";
		$name = "";
		if($_GET["type"]=="thai_ftsc") {
			$path = "assets/document/cremation/ex_thai_ftsc.xlsx";
			$name = "สมาชิกฌาปนกิจสสอค.xlsx";
		} else if ($_GET["type"]=="fscct") {
			$path = "assets/document/cremation/ex_fscct.xlsx";
			$name = "สมาชิกฌาปนกิจชสอ.xlsx";
		} else if ($_GET["type"]=="testament") {
			$info = $this->db->select("*")->from("coop_cremation_receive_file_attach")->where("cremation_receive_id = '".$_GET["id"]."' AND type = 'testament'")->get()->row();
			$path = "assets/uploads/cremation_request/".$info->file_name;
			$name = $info->file_old_name;
		} else if ($_GET["type"]=="evidence") {
			$info = $this->db->select("*")->from("coop_cremation_receive_file_attach")->where("cremation_receive_id = '".$_GET["id"]."' AND type = 'evidence'")->get()->row();
			$path = "assets/uploads/cremation_request/".$info->file_name;
			$name = $info->file_old_name;
		}

        force_download($name,file_get_contents(FCPATH.$path));
	}

	public function read_excel($files) {
		if($files['file']['name']!='') {
			$output_dir = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/document/cremation/";
			if(!@mkdir($output_dir,0,true)){
				@chmod($output_dir, 0777);
			}else{
				@chmod($output_dir, 0777);
			}

			$value_file = $files['file']['name'];
			$fileName = array();
			$list_dir = array();
			$cdir = scandir($output_dir);
			foreach ($cdir as $key => $value) {
				if (!in_array($value,array(".",".."))) {
					if (is_dir($dir . DIRECTORY_SEPARATOR . $value)){
						$list_dir[$value] = dirToArray(@$dir . DIRECTORY_SEPARATOR . $value);
					}else{
						if(substr($value,0,8) == date('Ymd')){
							$list_dir[] = $value;
						}
					}
				}
			}
			$explode_arr=array();
			foreach($list_dir as $key => $value) {
				$task = explode('.',$value);
				$task2 = explode('_',$task[0]);
				$explode_arr[] = $task2[1];
			}
			$max_run_num = sprintf("%04d",count($explode_arr)+1);
			$explode_old_file = explode('.',$files["file"]["name"]);
			$new_file_name = date('Ymd')."_".$max_run_num.".".$explode_old_file[(count($explode_old_file)-1)];
			if(!is_array($files["file"]["name"])) {
				$fileName['file_name'] = $new_file_name;
				$fileName['file_type'] = $files["file"]["type"];
				$fileName['file_old_name'] = $files["file"]["name"];
				$fileName['file_path'] = $output_dir.$fileName['file_name'];
				move_uploaded_file($files["file"]["tmp_name"],$output_dir.$fileName['file_name']);

				$types = array('Excel2007', 'Excel5');
				foreach ($types as $type) {
					$reader = PHPExcel_IOFactory::createReader($type);
					if ($reader->canRead($fileName['file_path'])) {
						$valid = true;
						break;
					}
				}

				if(!empty($valid)) {
					$objPHPExcel = PHPExcel_IOFactory::load($fileName['file_path']);
					$cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();

					$datas = array();
					foreach ($cell_collection as $cell) {
						$column = $objPHPExcel->getActiveSheet()->getCell($cell)->getColumn();
						$row = $objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
						$data_value = $objPHPExcel->getActiveSheet()->getCell($cell)->getValue();
						$datas[$row][$column] = $data_value;
					}
					unlink($fileName['file_path']);
					return $datas;
				}
				unlink($fileName['file_path']);
				return "ไฟล์ไม่ถูกต้อง";
			}
		}
		exit;
	}

	public function check_request_receive() {
		$request = $this->db->select("*")->from("coop_cremation_request")->where("member_cremation_id = '".$_POST["id"]."' AND cremation_status in (0,2,3,5,7,8,9,10,11)")->get()->row();
		if(empty($request)) {
			echo "success";
		} else {
			$has_req_status = array(7, 8);
			$non_member_status = array(0,2,5,9,11);
			if(in_array($request->cremation_status, $has_req_status)) {
				echo " - สมาชิกได้ยืนคำร้องขอรับเงินไปแล้ว";
			} else if (in_array($request->cremation_status, $non_member_status)) {
				echo " - เลขฌาปนกิจสงเคราะห์นี้ไม่อยู่ในสถานะเป็นสมาชิก";
			} else if ($request->cremation_status == 9) {
				echo " - สมาชิกได้ยืนคำร้องขอลาออก";
			}
		}
	}

	public function check_request_resign() {
		$request = $this->db->select("*")->from("coop_cremation_request")->where("member_cremation_id = '".$_POST["id"]."' AND cremation_status in (0,2,3,5,7,8,9,10,11)")->get()->row();
		if(empty($request)) {
			echo "success";
		} else {
			$has_req_status = array(7, 8);
			$non_member_status = array(0,2,3,5,9,11);
			if(in_array($request->cremation_status, $has_req_status)) {
				echo " - สมาชิกได้ยืนคำร้องขอรับเงิน";
			} else if (in_array($request->cremation_status, $non_member_status)) {
				echo " - เลขฌาปนกิจสงเคราะห์นี้ไม่อยู่ในสถานะเป็นสมาชิก";
			} else if ($request->cremation_status == 9) {
				echo " - สมาชิกได้ยืนคำร้องขอลาออกไปแล้ว";
			}
		}
	}

	public function creamation_request_validation() {
		$id_card = $_POST["personal_id"];
		$warning_text = "";
		if(!empty($id_card)) {
			$member = $this->db->select("t1.id_card")
								->from("coop_member_cremation as t1")
								->join("coop_cremation_request as t2", "t1.id = t2.member_cremation_raw_id AND t2.cremation_status in (0,1,2,4,5,6,7,8,10)", "inner")
								->where("t1.id_card = '".$id_card."'")
								->get()->row();
			if(!empty($member)) {
				$warning_text .= "- เลขที่บัตรประจำตัวประชาชนถูกใช้งานแล้ว";
			}
		}

		if($warning_text == "") {
			echo "success";
		} else {
			echo $warning_text;
		}
	}

	public function finance_month() {
		if(!empty($_POST["month"] && !empty($_POST["year"]))) {
			//Get month profile/create new if not exist
			$this->db->select('profile_id');
			$this->db->from('coop_finance_month_profile');
			$this->db->where("profile_month = '".(int)$_POST["month"]."' AND profile_year = '".$_POST["year"]."' ");
			$row = $this->db->get()->result_array();
			$row_profile = $row[0];
			if($row_profile['profile_id'] == ''){
				$data_insert = array();
				$data_insert['profile_month'] = (int)$_POST["month"];
				$data_insert['profile_year'] = $_POST["year"];
				$this->db->insert('coop_finance_month_profile', $data_insert);
				$profile_id = $this->db->insert_id();
			}else{
				$profile_id = $row_profile['profile_id'];
			}

			$year = $_POST["year"] - 543;//Change to AD
			$month = $_POST["month"] - 1;//For get last month
			$process_timestamp = date('Y-m-d H:i:s');

			$setting = $this->db->select("*")
								->from("coop_setting_cremation_detail")
								->where("start_date <= now()")
								->order_by("start_date")
								->get()->row();

			if($setting->finance_collect_type == 1) {
				//Get money request
				$end_date = date("Y-m-t", strtotime($year."-".$month."-01"))." 23:59:59.000";

				$deduct_per_members = $this->db->select("t3.member_cremation_id, SUM(money_received_per_member) as deduct_per_member, t4.member_id, t4.ref_member_id, t2.cremation_receive_id")
												->from("coop_cremation_transfer as t1")
												->join("coop_cremation_request_receive as t2", "t1.cremation_receive_id = t2.cremation_receive_id AND t2.finance_month_status = 0 AND t2.cremation_receive_status = 1", "inner")
												->join("coop_cremation_advance_payment_transaction as t3", "t2.cremation_receive_id = t3.cremation_receive_id AND t3.type = 'CTAP' AND t3.status = 1", "inner")
												->join("coop_member_cremation as t4", "t3.member_cremation_id = t4.member_cremation_id", "inner")
												->where("t1.cremation_receive_id is not null AND t1.date_transfer <= '".$end_date."'")
												->group_by("t3.member_cremation_id")
												->get()->result_array();

				$cremation_finances = array();
				$finance_months = array();
				$receive_ids = array();//For change cremation request receive status
				foreach($deduct_per_members as $deduct_per_member) {
					$member_id = !empty($deduct_per_member["ref_member_id"]) ? $deduct_per_member["ref_member_id"] : $deduct_per_member["member_id"];
					$member = $this->db->select("department, faction, level")->from('coop_mem_apply')->where("member_id = '".$member_id."'")->get()->row();
					$cremation_finance = array();
					$cremation_finance["profile_id"] = $profile_id;
					$cremation_finance["ref_member_id"] = $member_id;
					$cremation_finance["member_cremation_id"] = $deduct_per_member["member_cremation_id"];
					$cremation_finance["pay_amount"] = $deduct_per_member["deduct_per_member"];
					$cremation_finance["real_pay_amount"] = 0;
					$cremation_finance["user_id"] = $_SESSION['USER_ID'];
					$cremation_finance["status"] = 1;
					$cremation_finance["created_at"] = $process_timestamp;
					$cremation_finance["updated_at"] = $process_timestamp;
					$cremation_finances[] = $cremation_finance;

					$finance_months[$member_id]["profile_id"] = $profile_id;
					$finance_months[$member_id]["member_id"] = $member_id;
					$finance_months[$member_id]["deduct_code"] = "CREMATION";
					$finance_months[$member_id]["pay_amount"] += $deduct_per_member["deduct_per_member"];
					$finance_months[$member_id]["real_pay_amount"] += $deduct_per_member["deduct_per_member"];
					$finance_months[$member_id]["pay_type"] = "principal";
					$finance_months[$member_id]["deduct_id"] = "21";
					$finance_months[$member_id]["cremation_type_id"] = "2";
					$finance_months[$member_id]["run_status"] = "0";
					$finance_months[$member_id]["finance_month_type"] = 1;
					$finance_months[$member_id]['create_datetime'] = $process_timestamp;
					$finance_months[$member_id]['department'] = $member->department;
					$finance_months[$member_id]['faction'] = $member->faction;
					$finance_months[$member_id]['level'] = $member->level;
					$finance_months[$member_id]['create_by'] = $_SESSION['USER_ID'];

					if (!in_array($deduct_per_member["cremation_receive_id"], $receive_ids)) {
						$receive_ids[] = $deduct_per_member["cremation_receive_id"];
					}
				}

				if (!empty($cremation_finances)) {
					$this->db->insert_batch('coop_cremation_finance_month', $cremation_finances);
				}

				if (!empty($cremation_finances)) {
					$this->db->insert_batch('coop_finance_month_detail', $finance_months);
				}

				if(!empty($receive_ids)) {
					$data_update = array();
					$data_update["finance_month_status"] = 1;
					$this->db->where("cremation_receive_id IN (".implode(',',$receive_ids).")");
					$this->db->update("coop_cremation_request_receive", $data_update);
				}
			} else {
				$members = $this->db->select("t1.*")
									->from("coop_member_cremation as t1")
									->join("coop_cremation_request as t2", "t2.cremation_status IN (6,7,10) AND t1.member_cremation_id = t2.member_cremation_id", "inner")
									->get()->result_array();
				$pay_amount = $setting->finance_amount;
				foreach($members as $member) {
					$member_id = !empty($member["ref_member_id"]) ? $member["ref_member_id"] : $member["member_id"];
					$member_detail = $this->db->select("department, faction, level")->from('coop_mem_apply')->where("member_id = '".$member_id."'")->get()->row();
					$cremation_finance = array();
					$cremation_finance["profile_id"] = $profile_id;
					$cremation_finance["ref_member_id"] = $member_id;
					$cremation_finance["member_cremation_id"] = $member["member_cremation_id"];
					$cremation_finance["pay_amount"] = $pay_amount;
					$cremation_finance["real_pay_amount"] = 0;
					$cremation_finance["user_id"] = $_SESSION['USER_ID'];
					$cremation_finance["status"] = 1;
					$cremation_finance["created_at"] = $process_timestamp;
					$cremation_finance["updated_at"] = $process_timestamp;
					$cremation_finances[] = $cremation_finance;

					$finance_months[$member_id]["profile_id"] = $profile_id;
					$finance_months[$member_id]["member_id"] = $member_id;
					$finance_months[$member_id]["deduct_code"] = "CREMATION";
					$finance_months[$member_id]["pay_amount"] += $pay_amount;
					$finance_months[$member_id]["real_pay_amount"] += $pay_amount;
					$finance_months[$member_id]["pay_type"] = "principal";
					$finance_months[$member_id]["deduct_id"] = "21";
					$finance_months[$member_id]["cremation_type_id"] = "2";
					$finance_months[$member_id]["run_status"] = "0";
					$finance_months[$member_id]["finance_month_type"] = 1;
					$finance_months[$member_id]['create_datetime'] = $process_timestamp;
					$finance_months[$member_id]['department'] = $member_detail->department;
					$finance_months[$member_id]['faction'] = $member_detail->faction;
					$finance_months[$member_id]['level'] = $member_detail->level;
					$finance_months[$member_id]['create_by'] = $_SESSION['USER_ID'];
				}

				if (!empty($cremation_finances)) {
					$this->db->insert_batch('coop_cremation_finance_month', $cremation_finances);
				}

				$data_update = array();
				$data_update["finance_month_status"] = 1;
				$this->db->where("cremation_receive_status = 1");
				$this->db->update("coop_cremation_request_receive", $data_update);
			}
			$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
			echo "<script> document.location.href='".base_url(PROJECTPATH.'/cremation/finance_month')."' </script>";
		}
		$this->libraries->template('cremation/finance_month',$arr_data);
	}

	public function check_finance_month_process() {
		$profile_month = $this->db->select("*")->from("coop_finance_month_profile")->where("profile_month = '".$_POST["month"]."' AND profile_year = '".$_POST["year"]."'")->get()->row();
		if(!empty($profile_month)) {
			$finance_month = $this->db->select("*")->from("coop_cremation_finance_month")->where("profile_id = '".$profile_month->profile_id."'")->get()->row();
			if(!empty($finance_month)) {
				echo "- มีการทำรายการเรียกเก็บในเดือนนี้แล้ว";
				exit;
			}
		}
		echo "success";
		exit;
	}

	public function cashier() {
		$arr_data = array();
		$where = "1=1";

		//Filter member if member_cremation_id exist
		if(!empty($_GET["member_cremation_id"])) {
			$member = $this->db->select("*")->from("coop_member_cremation")->where("member_cremation_id = '".$_GET["member_cremation_id"]."'")->get()->result_array();
			$where .= " AND t1.member_cremation_id = '".$_GET["member_cremation_id"]."'";
		}
		if(!empty($_GET["month"])) {
			$where .= " AND t3.profile_month = '".$_GET["month"]."'";
		}
		if(!empty($_GET["year"])) {
			$where .= " AND ((t1.profile_id IS NOT null AND t3.profile_year = '".$_GET["year"]."') OR t1.year = '".$_GET["year"]."')";
		}
		if(!empty($_GET["type"])) {
			$where .= $_GET["type"] == "paid" ? " AND t1.pay_amount = t1.real_pay_amount" : " AND t1.pay_amount > t1.real_pay_amount";
		}

		$join_arr = array();
		$x = 0;
		$join_arr[$x]['table'] = 'coop_member_cremation as t2';
		$join_arr[$x]['condition'] = 't1.member_cremation_id = t2.member_cremation_id';
		$join_arr[$x]['type'] = 'inner';
		$x++;
		$join_arr[$x]['table'] = 'coop_finance_month_profile as t3';
		$join_arr[$x]['condition'] = 't1.profile_id = t3.profile_id';
		$join_arr[$x]['type'] = 'left';
		$x++;
		$join_arr[$x]['table'] = 'coop_prename as t4';
		$join_arr[$x]['condition'] = 't2.prename_id = t4.prename_id';
		$join_arr[$x]['type'] = 'inner';

		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select("t1.id, t1.ref_member_id, t1.member_cremation_id, t1.pay_amount, t1.real_pay_amount, t1.year as debt_year, t1.profile_id, t2.assoc_firstname as firstname_th, t2.assoc_lastname as lastname_th, t4.prename_full
									,t3.profile_month as month, t3.profile_year as year");
		$this->paginater_all->main_table('coop_cremation_finance_month as t1');
		$this->paginater_all->where($where);
		$this->paginater_all->page_now($_GET["page"]);
		$this->paginater_all->per_page(20);
		$this->paginater_all->page_link_limit(20);
		$this->paginater_all->order_by('t1.created_at DESC');
		$this->paginater_all->join_arr($join_arr);
		$row = $this->paginater_all->paginater_process();
		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'],$_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		$i = $row['page_start'];

		//Add receipt
		foreach($row['data'] as $key => $data) {
			$receipts = $this->db->select("t2.receipt_id, t2.created_at, t3.user_name")
									->from("coop_cremation_finance_month_receipt as t1")
									->join("coop_cremation_receipt as t2", "t1.receipt_id = t2.id AND t2.status = 1", "inner")
									->join("coop_user as t3", "t2.user_id = t3.user_id", "left")
									->where("t1.finance_month_id = '".$data["id"]."'")
									->order_by("t2.created_at")
									->get()->result_array();
			$row["data"][$key]["receipts"] = $receipts;
		}

		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['row'] = $row['data'];
		$arr_data['i'] = $i;
		$this->libraries->template('cremation/cremation_cashier',$arr_data);
	}

	public function save_debt_payment() {
		$process_timestamp = date('Y-m-d H:i:s');
		$finance_month = $this->db->select("*")
									->from("coop_cremation_finance_month")
									->where("id = '".$_POST["id"]."'")
									->get()->row();

		$member_cremation = $this->db->select("*")
										->from("coop_member_cremation")
										->where("member_cremation_id = '".$finance_month->member_cremation_id."'")
										->get()->row();

		$amount = $finance_month->pay_amount - $finance_month->real_pay_amount;

		//Update finance month
		$data_update = array();
		$data_update["real_pay_amount"] = $finance_month->pay_amount;
		$data_update["updated_at"] = $process_timestamp;
		$this->db->where('id', $_POST["id"]);
		$this->db->update("coop_cremation_finance_month", $data_update);

		//Create receipt
		if(!empty($finance_month->profile_id)) {
			$profile_month = $this->db->select("*")->from("coop_finance_month_profile")->where("profile_id = '".$finance_month->profile_id."'")->get()->row();
			//Generate Receipt identification
			$yy_check = (date("Y")+543);
			$yymm = sprintf("%02d", $profile_month->profile_month);
			$yy_full = (date("Y")+543);
			$yy = substr($profile_month->profile_year,2);
			$current_month = (int)date("m");
			$current_year = date("Y") + 543;

			$text = 'B';
			if(!empty($finance_month->ref_member_id)) {
				$main_finance_month = $this->db->select("*")
												->from("coop_finance_month_detail")
												->where("profile_id = '".$finance_month->profile_id."' AND member_id = '".$finance_month->ref_member_id."' AND deduct_code = 'CREMATION'")
												->get()->row();
				if(!empty($main_finance_month)) {
					if($main_finance_month->run_status == 1) $text = 'C';
				} else {
					if((int)$profile_month->profile_month != $current_month || (int)$profile_month->profile_year != $current_year) {
						$text = 'C';
					}
				}
			} else {
				if((int)$profile_month->profile_month != $current_month || (int)$profile_month->profile_year != $current_year) {
					$text = 'C';
				}
			}

			$this->db->select('*');
			$this->db->from('coop_cremation_receipt');
			$this->db->where("receipt_id LIKE '".'%__'.$text.'__'.$yy_check."%'");
			$this->db->order_by("receipt_id DESC");
			$this->db->limit(1);
			$row = $this->db->get()->result_array();
			if(!empty($row)) {
				$id = (int) substr($row[0]["receipt_id"], 9);
				$receipt_number = $yymm.''.$text.''.$yy.$yy_check.sprintf("%06d", $id + 1);
			} else {
				$receipt_number = $yymm.''.$text.''.$yy.$yy_check."000001";
			}
		} else {
			//Generate Receipt identification
			$text = 'B';
			$yy_check = (date("Y")+543);
			$yymm = sprintf("%02d", date("m"));
			$yy_full = (date("Y")+543);
			$yy = substr(date("Y") + 543,2);
			$current_month = (int)date("m");
			$current_year = date("Y") + 543;

			$this->db->select('*');
			$this->db->from('coop_cremation_receipt');
			$this->db->where("receipt_id LIKE '".'%__'.$text.'__'.$yy_check."%'");
			$this->db->order_by("receipt_id DESC");
			$this->db->limit(1);
			$row = $this->db->get()->result_array();
			if(!empty($row)) {
				$id = (int) substr($row[0]["receipt_id"], 9);
				$receipt_number = $yymm.''.$text.''.$yy.$yy_check.sprintf("%06d", $id + 1);
			} else {
				$receipt_number = $yymm.''.$text.''.$yy.$yy_check."000001";
			}
		}

		$data_insert = array();
		$data_insert["receipt_id"] = $receipt_number;
		$data_insert["member_cremation_id"] = $finance_month->member_cremation_id;
		$data_insert["amount"] = $amount;
		$data_insert["detail"] = "ชำระเงินฌาปนกิจสงเคราะห์";
		$data_insert["status"] = 1;
		$data_insert["user_id"] = $_SESSION['USER_ID'];
		$data_insert["created_at"] = $process_timestamp;
		$data_insert["updated_at"] = $process_timestamp;
		$this->db->insert('coop_cremation_receipt', $data_insert);
		$id_receipt = $this->db->insert_id();

		$data_insert = array();
		$data_insert["receipt_id"] = $id_receipt;
		$data_insert["finance_month_id"] = $finance_month->id;
		$data_insert["created_at"] = $process_timestamp;
		$data_insert["updated_at"] = $process_timestamp;
		$this->db->insert('coop_cremation_finance_month_receipt', $data_insert);

		//Update Advance Payment
		$advance_pay = $this->db->select("*")->from("coop_cremation_advance_payment")->where("member_cremation_id = '".$finance_month->member_cremation_id."'")->get()->row();

		$data_update = array();
		$data_update["adv_payment_balance"] = $advance_pay->adv_payment_balance + $amount;
		$data_update["lastpayment"] = $process_timestamp;
		$data_update["updatetime"] = $process_timestamp;
		$this->db->where('member_cremation_id', $finance_month->member_cremation_id);
		$this->db->update("coop_cremation_advance_payment", $data_update);

		$data_insert = array();
		$data_insert["member_cremation_id"] = $finance_month->member_cremation_id;
		$data_insert["type"] = "FMP";
		$data_insert["cremation_finance_month_id"] = $finance_month->id;
		$data_insert["amount"] = $amount;
		$data_insert["total"] = $data_update["adv_payment_balance"];
		$data_insert["status"] = 1;
		$data_insert["created_at"] = $process_timestamp;
		$data_insert["updated_at"] = $process_timestamp;
		$this->db->insert("coop_cremation_advance_payment_transaction", $data_insert);

		if(!empty($finance_month->profile_id)) {
			//Update main finance transaction
			$coop_finance_month = $this->db->select("*")
											->from("coop_finance_month_detail")
											->where("profile_id = '".$finance_month->profile_id."' AND member_id = '".$finance_month->ref_member_id."' AND deduct_code = 'CREMATION'")
											->get()->row();
			$data_update = array();
			$data_update["update_datetime"] = $process_timestamp;
			if($coop_finance_month->run_status == '1') {
				//Update non pay
				$non_pay_detail = $this->db->select("*")
											->from("coop_non_pay_detail")
											->where("finance_month_detail_id = '".$coop_finance_month->run_id."'")
											->get()->row();
				if(!empty($non_pay_detail)) {
					$non_pay_update = array();
					$non_pay_update["non_pay_amount_balance"] = $non_pay_detail->non_pay_amount_balance - $amount;
					$this->db->where('run_id', $non_pay_detail->run_id);
					$this->db->update("coop_non_pay_detail", $non_pay_update);

					$non_pay = $this->db->select("SUM(non_pay_amount_balance) as sum_balance")
										->from("coop_non_pay_detail")
										->where("non_pay_id = '".$non_pay_detail->non_pay_id."'")
										->get()->row();
					$non_pay_update = array();
					$non_pay_update["non_pay_amount_balance"] = $sum_balance;
					if($sum_balance == 0) {
						$non_pay_update["non_pay_status"] = 2;
					}
					$this->db->where('non_pay_id', $non_pay_detail->non_pay_id);
					$this->db->update("coop_non_pay", $non_pay_update);
				}
			} else {
				//Delete
				$this->db->where("run_id", $coop_finance_month->run_id);
				$this->db->delete("coop_finance_month_detail");
			}
		}

		$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
		echo "<script> document.location.href='".base_url(PROJECTPATH.'/cremation/cashier')."' </script>";
	}

	public function receipt_form_pdf($receipt_id,$receipt_id2=null) {
		$receipt_id2 = !empty($receipt_id2)? '/'.$receipt_id2:'';

		//Get receipt info
		$receipts = $this->db->select("*")
							->from("coop_cremation_receipt as t1")
							->where("receipt_id = '".$receipt_id.$receipt_id2."'")
							->get()->result_array();
		$transaction_data = array();
		foreach($receipts as $receipt) {
			$transaction = array();
			$transaction["transaction_text"] = $receipt["detail"];
			$transaction["amount"] = $receipt["amount"];
			$transaction_data[] = $transaction;
			$data_arr["receipt_datetime"] = $receipt["created_at"];
			$member_cremation_id = $receipt['member_cremation_id'];
		}
		$data_arr["transaction_data"] = $transaction_data;
		$data_arr['receipt_id'] = $receipt_id.$receipt_id2;

		//get member info
		if(empty($member_cremation_id)) {
			$request = $this->db->select("*")->from("coop_cremation_request")->where("receipt_id = '".$data_arr['receipt_id']."'")->get()->row();
			$member = $this->db->select("*")
								->from("coop_member_cremation as t1")
								->join("coop_prename as t2", "t1.prename_id = t2.prename_id", "left")
								->where("t1.id = '".$request->member_cremation_raw_id."'")
								->get()->result_array()[0];
			$data_arr["prename_full"] = $member["prename_full"];
			$data_arr["name"] = $member["assoc_firstname"]." ".$member["assoc_lastname"];
			$data_arr["member_id"] = $member["member_id"];
			$data_arr["member_cremation_id"] = $member["member_cremation_id"];
		} else {
			$member = $this->db->select("*")
								->from("coop_member_cremation as t1")
								->join("coop_prename as t2", "t1.prename_id = t2.prename_id", "left")
								->where("t1.member_cremation_id = '".$member_cremation_id."'")
								->get()->result_array()[0];
			$data_arr["prename_full"] = $member["prename_full"];
			$data_arr["name"] = $member["assoc_firstname"]." ".$member["assoc_lastname"];
			$data_arr["member_id"] = $member["member_id"];
			$data_arr["member_cremation_id"] = $member["member_cremation_id"];
		}

		if(!empty($member["member_id"])) {
			$member_apply = $this->db->select("*")
										->from("coop_mem_apply as t1")
										->join("coop_mem_group as t2", "t1.level = t2.id", "inner")
										->get()->row();
			$data_arr["group_name"] = $member_apply->mem_group_name;
		}

		//Logo
		$setting = $this->db->select("*")->from("coop_setting_cremation_detail")->where("cremation_id = 2 AND start_date <= NOW()")->order_by("start_date DESC")->get()->row();
		$data_arr["logo_path"] = $setting->logo_path;
		$this->load->view('cremation/receipt_form_pdf',$data_arr);
	}

	public function cremation_refund_resgister_payment() {
		if(!empty($_POST)) {
			$process_timestamp = date('Y-m-d H:i:s');

			$data_update = array();
			$data_update["refund_datetime"] = $process_timestamp;
			$data_update["updatetime"] = $process_timestamp;
			$this->db->where('cremation_request_id', $_POST["cremation_request_id"]);
			$this->db->update("coop_cremation_request", $data_update);

			$data_insert = array();
			$data_insert["cremation_request_id"] = $_POST["cremation_request_id"];
			$data_insert["transfer_status"] = 1;
			$data_insert["bank_type"] = $_POST["bank_type"];
			$data_insert["admin_id"] = $_SESSION['USER_ID'];
			if($_POST["bank_type"] == 1) {
				$data_insert["account_id"] = $_POST["account_id"];
				$data_insert["date_transfer"] = $process_timestamp;
			} else {
				$output_dir = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/cremation_transfer/";
				$_tmpfile = $_FILES["file_name"];
				if (@$_tmpfile["tmp_name"]['name'] != '') {
					$new_file_name = $this->center_function->create_file_name($output_dir, $_tmpfile["name"]);
					if (!empty($new_file_name)) {
						copy($_tmpfile["tmp_name"], $output_dir . $new_file_name);
						@unlink($output_dir . $row['file_name']);
						$file_name = $new_file_name;
						$data['file_name'] = $file_name;
					}
				}

				$data_insert["bank_id"] = $_POST["dividend_bank_id"];
				$data_insert["file_name"] = $file_name;
				$data_insert["bank_branch_id"] = $_POST["dividend_bank_branch_id"];
				$data_insert["bank_account_no"] = $_POST["bank_account_no"];
				$date_arr = explode('/', @$data['date_transfer']);
				$data_insert["date_transfer"] = ($date_arr[2] - 543) . "-" . $date_arr[1] . "-" . $date_arr[0] . " " . @$data['time_transfer'];
			}

			$this->db->insert("coop_cremation_transfer", $data_insert);
		}

		$arr_data = array();
		$x=0;
		$join_arr = array();
		$join_arr[$x]['table'] = 'coop_member_cremation as t2';
		$join_arr[$x]['condition'] = 't1.member_cremation_raw_id = t2.id';
		$join_arr[$x]['type'] = 'inner';
		$x++;
		$join_arr[$x]['table'] = 'coop_prename as t3';
		$join_arr[$x]['condition'] = 't2.prename_id = t3.prename_id';
		$join_arr[$x]['type'] = 'left';

		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select('t1.*, t2.assoc_firstname, t2.assoc_lastname, t3.prename_full');
		$this->paginater_all->main_table('coop_cremation_request as t1');
		$this->paginater_all->where("t1.cremation_status IN('5')");
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(20);
		$this->paginater_all->page_link_limit(20);
		$this->paginater_all->order_by('createdatetime DESC');
		$this->paginater_all->join_arr($join_arr);
		$row = $this->paginater_all->paginater_process();
		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit']);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		$i = $row['page_start'];

		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];
		$arr_data['i'] = $i;

		$this->db->select('*');
        $this->db->from('coop_bank');
        $row = $this->db->get()->result_array();
        $arr_data['banks'] = $row;

		$this->libraries->template('cremation/cremation_refund_resgister_payment',$arr_data);
	}

	public function get_cremation_member_bank_account() {
		$arr_data = array();
		$member = $this->db->select("*")
							->from("coop_member_cremation")
							->where("id = '".$_GET["member_cremation_raw_id"]."'")
							->get()->row();
		$member_id = !empty($member->member_id) ? $member->member_id : $member->ref_member_id;
		$bank_account = $this->db->select("*")->from("coop_member_cremation_bank_account")->where("member_cremation_raw_id = '".$_GET["member_cremation_raw_id"]."'")->get()->result_array();
		$arr_data["bank_account"] = $bank_account[0];

		if(!empty($member_id)) {
			$coop_accounts = $this->db->select("account_name, account_id")->from("coop_maco_account")->where("mem_id = '".$member_id."'")->get()->result_array();
			$arr_data["coop_accounts"] = $coop_accounts;
		} else {
			$arr_data["coop_accounts"] = array();
		}

		echo json_encode($arr_data);
		exit;
	}

	public function get_cremation_request_from_cremation_no() {
		$arr_data = array();
		$cremation_no = $_GET["cremation_no"];
		$requests = $this->db->select("*")->from("coop_cremation_request")->where("cremation_no = '{$cremation_no}'")->get()->result_array();
		if(!empty($requests)) {
			$arr_data["message"] = "success";
			$arr_data["data"] = $requests[0];
		} else {
			$arr_data["message"] = "ไม่พบเลขที่คำร้อง";
			$arr_data["data"] = array();
		}
		echo json_encode($arr_data);
		exit;
	}

	public function finance_year() {
		$arr_data = array();

		if(!empty($_POST["year"])) {
			$setting = $this->db->select("*")
								->from("coop_setting_cremation_detail")
								->where("start_date <= now()")
								->order_by("start_date")
								->get()->row();

			$process_timestamp = date('Y-m-d H:i:s');
			if($setting->finance_collect_type == 1) {
				//Get money request
				$year = $_POST["year"] - 543;//Change to AD
				$period = $this->db->select("*")->from("coop_account_period_setting")->get()->row();
				$end_date = date("Y-m-t", strtotime($year."-".$period->accm_month_ini."-01"))." 23:59:59.000";

				$deduct_per_members = $this->db->select("t3.member_cremation_id, SUM(money_received_per_member) as deduct_per_member, t4.member_id, t4.ref_member_id, t2.cremation_receive_id")
												->from("coop_cremation_transfer as t1")
												->join("coop_cremation_request_receive as t2", "t1.cremation_receive_id = t2.cremation_receive_id AND t2.finance_month_status = 0 AND t2.cremation_receive_status = 1", "inner")
												->join("coop_cremation_advance_payment_transaction as t3", "t2.cremation_receive_id = t3.cremation_receive_id AND t3.type = 'CTAP' AND t3.status = 1", "inner")
												->join("coop_member_cremation as t4", "t3.member_cremation_id = t4.member_cremation_id", "inner")
												->join("coop_cremation_request as t5", "t4.member_cremation_id = t5.member_cremation_id AND t5.cremation_status IN (6,7,10)")
												->where("t1.cremation_receive_id is not null AND t1.date_transfer <= '".$end_date."'")
												->group_by("t3.member_cremation_id")
												->get()->result_array();

				$cremation_finances = array();
				$finance_months = array();
				$receive_ids = array();//For change cremation request receive status
				foreach($deduct_per_members as $deduct_per_member) {
					$member_id = !empty($deduct_per_member["ref_member_id"]) ? $deduct_per_member["ref_member_id"] : $deduct_per_member["member_id"];
					$member = $this->db->select("department, faction, level")->from('coop_mem_apply')->where("member_id = '".$member_id."'")->get()->row();
					$cremation_finance = array();
					$cremation_finance["year"] = $_POST["year"];
					$cremation_finance["ref_member_id"] = $member_id;
					$cremation_finance["member_cremation_id"] = $deduct_per_member["member_cremation_id"];
					$cremation_finance["pay_amount"] = $deduct_per_member["deduct_per_member"];
					$cremation_finance["real_pay_amount"] = 0;
					$cremation_finance["user_id"] = $_SESSION['USER_ID'];
					$cremation_finance["status"] = 1;
					$cremation_finance["created_at"] = $process_timestamp;
					$cremation_finance["updated_at"] = $process_timestamp;
					$cremation_finances[] = $cremation_finance;

					if (!in_array($deduct_per_member["cremation_receive_id"], $receive_ids)) {
						$receive_ids[] = $deduct_per_member["cremation_receive_id"];
					}
				}

				if (!empty($cremation_finances)) {
					$this->db->insert_batch('coop_cremation_finance_month', $cremation_finances);
				}

				if(!empty($receive_ids)) {
					$data_update = array();
					$data_update["finance_month_status"] = 1;
					$this->db->where("cremation_receive_id IN (".implode(',',$receive_ids).")");
					$this->db->update("coop_cremation_request_receive", $data_update);
				}
			} else {
				$members = $this->db->select("t1.*")
									->from("coop_member_cremation as t1")
									->join("coop_cremation_request as t2", "t2.cremation_status IN (6,7,10) AND t1.member_cremation_id = t2.member_cremation_id", "inner")
									->get()->result_array();
				$pay_amount = $setting->finance_amount;
				foreach($members as $member) {
					$member_id = !empty($member["ref_member_id"]) ? $member["ref_member_id"] : $member["member_id"];
					$cremation_finance = array();
					$cremation_finance["year"] = $_POST["year"];
					$cremation_finance["ref_member_id"] = $member_id;
					$cremation_finance["member_cremation_id"] = $member["member_cremation_id"];
					$cremation_finance["pay_amount"] = $pay_amount;
					$cremation_finance["real_pay_amount"] = 0;
					$cremation_finance["user_id"] = $_SESSION['USER_ID'];
					$cremation_finance["status"] = 1;
					$cremation_finance["created_at"] = $process_timestamp;
					$cremation_finance["updated_at"] = $process_timestamp;
					$cremation_finances[] = $cremation_finance;
				}

				if (!empty($cremation_finances)) {
					$this->db->insert_batch('coop_cremation_finance_month', $cremation_finances);
				}

				$data_update = array();
				$data_update["finance_month_status"] = 1;
				$this->db->where("cremation_receive_status = 1");
				$this->db->update("coop_cremation_request_receive", $data_update);
			}
			$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
			echo "<script> document.location.href='".base_url(PROJECTPATH.'/cremation/finance_year')."' </script>";
		}

		$this->libraries->template('cremation/cremation_finance_year',$arr_data);
	}

	public function check_finance_year_process() {
		$finance_year = $this->db->select("*")->from("coop_cremation_finance_month")->where("year = '".$_POST["year"]."'")->get()->row();
		if(!empty($finance_year)) {
			echo "- มีการทำรายการเรียกเก็บในปีนี้แล้ว";
			exit;
		}

		echo "success";
		exit;
	}
}
