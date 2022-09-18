<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cremation extends CI_Controller {

    public $CI;

	function __construct()
	{
		parent::__construct();
        $this->CI =&get_instance();
	}

	public function cremation_request(){
		$arr_data = array();

		if(!empty($_POST)) {
			if(empty($_POST["cremation_no"])){
				$process_timestamp = date('Y-m-d H:i:s');
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

				$member_cremation_id = "000001";
				$lastest_member = $this->db->select("member_cremation_id")->from("coop_member_cremation")->order_by("member_cremation_id DESC")->get()->row();
				if(!empty($lastest_member)) {
					$id = (int)$lastest_member->member_cremation_id;
					$member_cremation_id = sprintf("%06d", $id + 1);
				}

				$this->db->select('*')
							->from('coop_setting_cremation_detail')
							->where('start_date <= now() AND cremation_id = 2')
							->order_by('start_date DESC, cremation_detail_id ASC')
							->limit(1);

				//Cremation ID
				$setting = $this->db->get()->row();
				$setting_cremation_id = $setting->cremation_detail_id;

				$runno = sprintf("%07d",$run_now);
				$cremation_no = $runno.'/'.$year_now;
				$data_insert = array();
				$data_insert['cremation_no'] = $cremation_no;
				$data_insert['cremation_request_id'] = $cremation_request_id;
				$data_insert['member_cremation_id'] = $member_cremation_id;
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

				//Add member cremation
				$data_insert = array();
				$data_insert['member_cremation_id'] = $member_cremation_id;
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
				$data_insert["relate_1"] = $_POST["relate_1"];
				$data_insert["relate_2"] = $_POST["relate_2"];
				$data_insert["relate_3"] = $_POST["relate_3"];
				$data_member['create_date'] = $process_timestamp;
				$data_member['status'] = '0';
				$this->db->insert("coop_member_cremation", $data_insert);

				$_GET["cremation_request_id"] = $cremation_request_id;
			} else {
				//Get cremation request
				$request = $this->db->select("member_cremation_id, cremation_request_id")->from("coop_cremation_request")->where("cremation_no = '".$_POST["cremation_no"]."'")->get()->row();
				$member_cremation_id = $request->member_cremation_id;
				$cremation_request_id = $request->cremation_request_id;

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
				$data_insert["zip_code"] = $_POST["c_zipcode"];
				$data_insert["marry_name"] = $_POST["marry_name"];
				$data_insert["receiver_1"] = $_POST["receiver_1"];
				$data_insert["receiver_2"] = $_POST["receiver_2"];
				$data_insert["receiver_3"] = $_POST["receiver_3"];
				$data_insert["relate_1"] = $_POST["relate_1"];
				$data_insert["relate_2"] = $_POST["relate_2"];
				$data_insert["relate_3"] = $_POST["relate_3"];
				$this->db->where('member_cremation_id', $member_cremation_id);
				$this->db->update("coop_member_cremation", $data_insert);

				$_GET["cremation_request_id"] = $cremation_request_id;
			}
		}

		if(!empty($_GET["cremation_request_id"])) {
			$data = $this->db->select("t1.cremation_no, t1.cremation_request_id, t1.cremation_status, t1.createdatetime, t2.*, t3.prename_full,
										t4.assoc_firstname as firstname_ref, t4.assoc_lastname as lastname_ref, t5.prename_full as prename_full_ref")
								->from("coop_cremation_request as t1")
								->join("coop_member_cremation as t2", "t1.member_cremation_id = t2.member_cremation_id", "inner")
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
			//echo"<pre>";print_r($row);exit;
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
		$arr_data['month_arr'] = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
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
			$data_insert['cremation_status'] = @$_GET['status_to'];
			$this->db->where('cremation_request_id', @$_GET['id']);
			$this->db->update('coop_cremation_request', $data_insert);

			$this->db->select('member_cremation_id')
                ->from('coop_cremation_request')
                ->where('cremation_request_id', @$_GET['id']);
			$res = $this->db->get()->row();

			$data_mem_cremation['approve_status'] = $_GET['status_to'] == '1' ? '1' : '2';
            $data_mem_cremation['approve_date'] = date('Y-m-d H:i:s');
            $data_mem_cremation['status'] = $_GET['status_to'] == '1' ? '0' : '2' ;
            $this->db->where('member_cremation_id', $res->member_cremation_id);
            $this->db->update("coop_member_cremation", $data_mem_cremation);

			$this->center_function->toast('บันทึกข้อมูลเรียบร้อยแล้ว');
			echo "<script> document.location.href='".base_url(PROJECTPATH.'/cremation/cremation_approve')."' </script>";
		}
		$arr_data = array();

		$x=0;
		$join_arr = array();
		$join_arr[$x]['table'] = 'coop_member_cremation';
		$join_arr[$x]['condition'] = 'coop_member_cremation.member_cremation_id = coop_cremation_request.member_cremation_id';
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
		$this->paginater_all->where("cremation_status IN('0','1','5')");
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

	        if($data[0]['status'] == 6) {
	            $delete = [];

                foreach ($data as $key => $value) {
                    $delete[] = $value['id'];
                }

                $this->db->where_in('cremation_request_id', $delete);
                $this->db->delete('coop_cremation_request');

            }else{
                $update = [];

                $req_id = array_map(function($val){ return $val['id']; }, $data);

                $this->db->select('cremation_request_id, member_cremation_id')
                    ->from('coop_cremation_request')
                    ->where_in('cremation_request_id', $req_id);
                $req = $this->db->get()->result_array();

                $req_data = array_map(function($val){ return array($val['cremation_request_id'] => $val['member_cremation_id']); }, $req);

                $cremation = [];
                $index = 0;
                foreach ($data as $key => $value) {
                    $update[] = array('cremation_request_id' => $value['id'], 'cremation_status' => $value['status']);

                    $cremation[$index]['approve_status'] = '1';
                    $cremation[$index]['approve_date'] = date('Y-m-d H:i:s');
                    $cremation[$index]['member_cremation_id'] = $req_data[$value['id']];
                    $index++;
                }

                $this->db->update_batch('coop_cremation_request', $update, 'cremation_request_id');
                $this->db->update_bathch('coop_member_cremation', $cremation, 'member_cremation_id');
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
		$join_arr[$x]['table'] = 'coop_mem_apply';
		$join_arr[$x]['condition'] = 'coop_mem_apply.member_id = coop_cremation_request.member_id';
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
		$this->paginater_all->select('coop_cremation_request.*, coop_mem_apply.firstname_th, coop_mem_apply.lastname_th, coop_user.user_name,coop_cremation_data.cremation_name_short');
		$this->paginater_all->main_table('coop_cremation_request');
		$this->paginater_all->where("cremation_status IN ('1','4','6')");
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
		
		$yymm = (date("Y")+543).date("m");
		
		$this->db->select('*');
		$this->db->from('coop_receipt');
		$this->db->where("receipt_id LIKE '".$yymm."%'");
		$this->db->order_by("receipt_id DESC");
		$this->db->limit(1);
		$row = $this->db->get()->result_array();
		
		if(!empty($row)) {
			$id = (int) substr($row[0]["receipt_id"], 6);
			$receipt_number = $yymm.sprintf("%06d", $id + 1);
		}
		else {
			$receipt_number = $yymm."000001";
		}
		
		$cremation_request_id = @$data_post["cremation_request_id"];	
		
		$data_insert['cremation_pay_amount']= @$data_post["cremation_pay_amount"];			
		$data_insert['cremation_pay_date'] = date('Y-m-d H:i:s');
		$data_insert['cremation_status'] = '6';
		$data_insert['user_id_pay'] = $_SESSION['USER_ID'];
		$data_insert['receipt_id'] = $receipt_number;
		$this->db->where('cremation_request_id', @$cremation_request_id);
		$this->db->update('coop_cremation_request', $data_insert);

		$this->cremation_advance_pay($cremation_request_id);
		
		$data_insert = array();
		$data_insert['receipt_id'] = $receipt_number;
		$data_insert['member_id'] = $data_post['member_id'];
		$total = @$data_post['cremation_pay_amount'];

		$this->db->select('member_cremation_id')
            ->from('coop_cremation_request')
            ->where('cremation_request_id', $cremation_request_id);
		$member_cremation = $this->db->get()->row();

		if(!empty($member_cremation)) {
            $data_cremation = [];
            $data_cremation['status'] = '1';
            $this->db->where('member_cremation_id', $member_cremation->member_cremation_id);
            $this->db->update('coop_member_cremation', $data_cremation);
        }
		
		$data_insert['sumcount'] = number_format($total,2,'.','');
		$data_insert['receipt_datetime'] = date('Y-m-d H:i:s');
		$data_insert['admin_id'] = $_SESSION['USER_ID'];
		$this->db->insert('coop_receipt', $data_insert);

		$data = array();
		$data['coop_account']['account_description'] = "ชำระเงินค่าสมัครฌาปนกิจสงเคราะห์";
		$data['coop_account']['account_datetime'] = date('Y-m-d H:i:s');
		
		$data['coop_account_detail'][10100]['account_type'] = 'debit';
		$data['coop_account_detail'][10100]['account_amount'] = $total;
		$data['coop_account_detail'][10100]['account_chart_id'] = '10100';
		
		$account_list = '27'; //รหัสการชำระเงิน
		$data_insert = array();
		$data_insert['receipt_id'] = @$receipt_number;
		$data_insert['receipt_list'] = @$account_list;
		$data_insert['receipt_count'] = number_format($data_post['cremation_pay_amount'],2,'.',''); //จำนวนเงิน
		$this->db->insert('coop_receipt_detail', $data_insert);		
		
		$data_insert = array();
		$data_insert['transaction_text'] = "ชำระเงินค่าสมัครฌาปนกิจสงเคราะห์";
		$data_insert['member_id'] = @$data_post['member_id'];
		$data_insert['receipt_id'] = @$receipt_number;
		$data_insert['loan_id'] = '';
		$data_insert['account_list_id'] = $account_list;
		$data_insert['principal_payment'] = number_format($data_post['cremation_pay_amount'],2,'.',''); //เงินต้น
		$data_insert['interest'] = '';
		$data_insert['total_amount'] = @$data_post['cremation_pay_amount'];
		$data_insert['loan_amount_balance'] = '';
		$data_insert['payment_date'] = date('Y-m-d H:i:s');
		$data_insert['createdatetime'] = date('Y-m-d H:i:s');
		$data_insert['cremation_type_id'] = @$data_post['cremation_type_id'];
		$this->db->insert('coop_finance_transaction', $data_insert);			
			
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
		
		echo "<script> document.location.href='".base_url(PROJECTPATH.'/cremation/cremation_pay')."' </script>";
		exit;
	}

	function cremation_advance_pay($cremation_request_id){

        $this->db->select('member_cremation_id, member_id')->from('coop_cremation_request')->where('cremation_request_id', $cremation_request_id);
        $member_cremation = $this->db->get()->row();

        $this->db->select('advance_pay')
            ->from('coop_setting_cremation_detail')
            ->where("start_date <= ", date('Y-m-d'))
            ->order_by('start_date DESC, cremation_detail_id ASC')->limit('1');
        $setting = $this->db->get()->row();

        $advance_pay['member_cremation_id'] = $member_cremation->member_cremation_id;
        $advance_pay['ref_member_id'] = $member_cremation->member_id;
        $advance_pay['adv_payment_balance'] = $setting->advance_pay;
        $advance_pay['lastpayment'] = date('Y-m-d H:i:s');
        $advance_pay['createdatetime'] = date('Y-m-d H:i:s');
        $advance_pay['adv_status'] = 1;

        $this->db->insert('coop_cremation_advance_payment', $advance_pay);
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
//		$join_arr[$x]['table'] = 'coop_user AS coop_user_transfer';
//		$join_arr[$x]['condition'] = 'coop_cremation_transfer.admin_id = coop_user_transfer.user_id';
//		$join_arr[$x]['type'] = 'left';
//		$x++;
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
		//$this->paginater_all->where("cremation_status IN ('1','4')");
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(10);
		$this->paginater_all->page_link_limit(20);
		$this->paginater_all->order_by('createdatetime DESC');
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
		
		$arr_data['receive_status'] = array('0'=>'รออนุมัติ', '1'=>'อนุมัติ','2'=>'ไม่อนุมัติ');
		
		$this->db->select('bank_id, bank_name');
		$this->db->from('coop_bank');
		$row = $this->db->get()->result_array();
		$arr_data['bank'] = $row;
		
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
			'coop_mem_apply.*'
		));
		$this->db->from('coop_cremation_request_receive');
		$this->db->join("coop_cremation_data","coop_cremation_request_receive.cremation_type_id = coop_cremation_data.cremation_id","left");
		$this->db->join("coop_cremation_data_detail","coop_cremation_data_detail.cremation_id = coop_cremation_data.cremation_id","left");
		$this->db->join("coop_cremation_transfer","coop_cremation_transfer.cremation_receive_id = coop_cremation_request_receive.cremation_receive_id","left");
		$this->db->join("coop_mem_apply","coop_mem_apply.member_id = coop_cremation_request_receive.member_id","left");
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
							coop_mem_apply.*
							");
		$this->db->from('coop_cremation_request_resign');
		$this->db->join("coop_cremation_request","coop_cremation_request.cremation_request_id = coop_cremation_request_resign.cremation_request_id","left");
		$this->db->join("coop_cremation_data","coop_cremation_request.cremation_type_id = coop_cremation_data.cremation_id","left");
		$this->db->join("coop_cremation_data_detail","coop_cremation_data_detail.cremation_id = coop_cremation_data.cremation_id","left");
		$this->db->join("coop_cremation_transfer","coop_cremation_transfer.cremation_resign_id = coop_cremation_request_resign.id","left");
		$this->db->join("coop_mem_apply","coop_mem_apply.member_id = coop_cremation_request.member_id","left");
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
		$cremation_transfer_id = $data["cremation_transfer_id"];
		$cremation_receive_id = $data["cremation_receive_id"];
		$cremation_request_id = $data["cremation_request_id"];
		$cremation_resign_id = $data["cremation_resign_id"];
		$member_id = @$data['member_id'];

		$this->db->select('t1.member_cremation_id')
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
											'coop_member_cremation.receiver_1','coop_member_cremation.receiver_2','coop_member_cremation.receiver_3',
											'coop_cremation_request_receive.receiver','coop_cremation_request_receive.cremation_balance_amount',
											'coop_cremation_request_resign.id as resign_id', 'coop_cremation_request_resign.adv_payment_balance','coop_cremation_transfer.cremation_transfer_id'));
        $this->paginater_all->main_table('coop_cremation_transfer');
		$this->paginater_all->where("(coop_cremation_request_receive.cremation_receive_id is not null AND coop_cremation_request_receive.cremation_receive_status = 1)
										OR (coop_cremation_request_resign.id is not null AND coop_cremation_request_resign.status in (1,3))");
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

        $this->db->select(array('(t2.adv_payment_balance - 100) as adv_payment_balance', 't2.adv_id'))
            ->from('coop_member_cremation t1')
            ->join('coop_cremation_request t3', 't1.member_cremation_id=t3.member_cremation_id', 'inner')
            ->join('coop_cremation_advance_payment t2', 't1.member_cremation_id=t2.member_cremation_id', 'inner')
            ->where(array(
                't1.status' => 1,
                't1.approve_date <= ' => $member_death->death_date,
                't3.user_id_pay' => 1));

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
		$where = "";
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
		}else if(@$_POST['member_cremation_id'] == 'id_card'){
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
		$datas = $this->db->select("t1.cremation_no, t1.cremation_request_id, t1.member_cremation_id, t2.member_id, t2.assoc_firstname, t2.assoc_lastname, t3.prename_full")
							->from("coop_cremation_request as t1")
							->join("coop_member_cremation as t2", "t1.member_cremation_id = t2.member_cremation_id", "inner")
							->join("coop_prename as t3", "t2.prename_id = t3.prename_id","left")
							->where($where)
							->get()->result_array();
		$arr_data['datas'] = $datas;

		$this->load->view('cremation/search_cremation_jquery',$arr_data);
	}

	public function get_cremation_info() {
		$datas = $this->db->select("t1.cremation_no, t1.cremation_request_id, t1.cremation_status, t1.createdatetime, t2.*, t3.prename_full,
										t4.assoc_firstname as firstname_ref, t4.assoc_lastname as lastname_ref, t5.prename_full as prename_full_ref,
										t6.adv_payment_balance")
							->from("coop_cremation_request as t1")
							->join("coop_member_cremation as t2", "t1.member_cremation_id = t2.member_cremation_id", "inner")
							->join("coop_prename as t3", "t2.prename_id = t3.prename_id", "left")
							->join("coop_member_cremation as t4", "t2.ref_member_id = t4.member_id", "left")
							->join("coop_prename as t5", "t4.prename_id = t5.prename_id", "left")
							->join("coop_cremation_advance_payment as t6", "t1.member_cremation_id = t6.member_cremation_id", "left")
							->where("t1.member_cremation_id = '".$_GET["id"]."'")
							->get()->result_array();
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
		$datas = $this->db->select("t1.member_cremation_id")
									->from("coop_cremation_request as t1")
									->join("coop_member_cremation as t2", "t1.member_cremation_id = t2.member_cremation_id", "inner")
									->where("t1.member_cremation_id = '".$_GET["id"]."'")
									->get()->result_array();
		echo json_encode($datas[0]);
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
				$data_insert['cremation_receive_amount'] = $_POST["cremation_receive_amount"];
				$data_insert['action_fee_percent'] = $_POST["action_fee_percent"];
				$data_insert['cremation_balance_amount'] = $_POST["cremation_balance_amount"];
				$data_insert['adv_payment_balance'] = $_POST["adv_payment_balance"];
				$data_insert['reason'] = $_POST["reason"];
				if(!empty($_POST["receiver"])) $data_insert['receiver'] = "receiver_".$_POST["receiver"];
				$data_insert['cremation_receive_status'] = '0';
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
				$data_insert['cremation_receive_amount'] = $_POST["cremation_receive_amount"];
				$data_insert['action_fee_percent'] = $_POST["action_fee_percent"];
				$data_insert['cremation_balance_amount'] = $_POST["cremation_balance_amount"];
				$data_insert['adv_payment_balance'] = $_POST["adv_payment_balance"];
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
		$arr_data["count_members"] = count($members);

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
			$data_update["cremation_status"] = 1;
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
}
