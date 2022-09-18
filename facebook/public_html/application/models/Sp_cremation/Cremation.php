<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cremation extends CI_Model {
    public function __construct() {
        parent::__construct();
        $this->load->helper(array('html', 'url'));
    }

    function get_cremation_info($cremation_id) {
        $result = array();

        $cremation = $this->db->select("name, full_name")->from("coop_sp_cremation")->where("id = '".$cremation_id."'")->get()->row();
        $result["name"] = $cremation->name;
        $result["full_name"] = $cremation->full_name;

        return $result;
    }

    function get_registration_period_page($cremation_id, $page){
        $result = array();

        $x=0;
		$join_arr = array();
		$join_arr[$x]['table'] = 'coop_sp_cremation_registration as t1';
		$join_arr[$x]['condition'] = 't0.id = t1.period_id AND t1.status = 1';
		$join_arr[$x]['type'] = 'left';
        $this->paginater_all->type(DB_TYPE);
        $this->paginater_all->select('t0.*, t1.id as reg_id');
        $this->paginater_all->main_table('coop_sp_cremation_registration_period as t0');
        $this->paginater_all->where("t0.status = 1 AND t0.cremation_id = '".$cremation_id."'");
        $this->paginater_all->page_now($page);
        $this->paginater_all->per_page(20);
        $this->paginater_all->page_link_limit(20);
        $this->paginater_all->order_by('start_date DESC, end_date ASC');
        $this->paginater_all->join_arr($join_arr);
        $row = $this->paginater_all->paginater_process();

        $paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'],$_GET);
        $result["page_start"] = $row['page_start'];
        $result['paging'] = $paging;
        $result['datas'] = $row['data'];

        return $result;
    }

    public function get_registration_period($cremation_id, $conditions) {
        $result = array();

        $where = "t1.cremation_id = '".$cremation_id."'";
        if(!empty($conditions)) {
            if(!empty($conditions["status"])) {
                $where .= " AND t1.status IN (".implode(',',$conditions["status"]).")";
            }
            if(!empty($conditions["month"])) {
                $where .= " AND MONTH(t1.start_date) <= '".$conditions["month"]."' AND MONTH(t1.end_date) >= '".$conditions["month"]."'";
            }
            if(!empty($conditions["year"])) {
                $where .= " AND YEAR(t1.start_date) <= '".$conditions["year"]."' AND YEAR(t1.end_date) >= '".$conditions["year"]."'";
            }
        }

        $periods = $this->db->select("*")->from("coop_sp_cremation_registration_period as t1")->where($where)->order_by("t1.start_date, t1.end_date")->get()->result_array();
        $result["datas"] = $periods;
        $result["status"] = "success";

        return $result;
    }

    function edit_registration_period($cremation_id, $param) {
        $process_date = date('Y-m-d H:i:s');
        $data_insert = array();
        $data_insert["cremation_id"] = $cremation_id;
        $data_insert["start_date"] = $this->center_function->ConvertToSQLDate($param['start_date']);
        $data_insert["end_date"] = $this->center_function->ConvertToSQLDate($param['end_date']);
        $data_insert["name"] = $param["name"];
        $data_insert["annual_fee"] = str_replace( ',', '', $param["annual_fee"]);
        $data_insert["fee"] = str_replace( ',', '', $param["fee"]);
        $data_insert["assoc_fee"] = str_replace( ',', '', $param["assoc_fee"]);
        $data_insert["other_fee"] = str_replace( ',', '', $param["other_fee"]);
        $data_insert["total"] = ((int)str_replace( ',', '', $param["fee"])) + ((int)str_replace( ',', '', $param["annual_fee"])) + ((int)str_replace( ',', '', $param["assoc_fee"])) + ((int)str_replace( ',', '', $param["other_fee"]));
        $data_insert["status"] = 1;
        if(!empty($param["period_id"])) {
            $data_insert["updated_at"] = $process_date;
            $this->db->where('id',$param["period_id"]);
            $this->db->update('coop_sp_cremation_registration_period',$data_insert);
        } else {
            $data_insert['user_id'] = $_SESSION['USER_ID'];
            $data_insert["created_at"] = $process_date;
            $data_insert["updated_at"] = $process_date;
            $this->db->insert('coop_sp_cremation_registration_period', $data_insert);
        }
    }

    //For delete funciton just change status to NULL
    function delete_registration_period($cremation_id, $period_id) {
        $data_insert = array();
        $data_insert["status"] = NULL;
        $data_insert["updated_at"] = date('Y-m-d H:i:s');
        $this->db->where('id',$period_id);
        $this->db->update('coop_sp_cremation_registration_period',$data_insert);
    }

    //Type = 1 search by member_id coop_mem_apply.member_id
    //Type = 2 search by cremation_member_id coop_sp_cremation_member.cremation_id
    //Type = 3 search by cremation_registration_id coop_sp_cremation_registration.request_id
    //Type = 4 search by coop_sp_cremation_member.id
    //Type = 5 search by coop_sp_cremation_registration.id
    function get_cremation_member_info($cremation_id, $id, $type){
        $result = array();
        $where = "t1.cremation_id = '".$cremation_id."'";
        // $where = "t1.cremation_id = '".$cremation_id."' AND (t2.status IN (0,1,2,4) OR t2.status IS NULL)";

        if($type == 1) {
            $where .= " AND t1.member_id = '".$id."'";
        } else if ($type == 2) {
            $where .= " AND t2.cremation_member_id = '".$id."'";
        } else if ($type == 3) {
            $where .= " AND t1.request_id = '".$id."'";
        } else if ($type == 4) {
            $where .= " AND t2.id = '".$id."'";
        } else if ($type == 5) {
            $where .= " AND t1.id = '".$id."'";
        }

        $cremation = $this->db->select("t1.id, t1.request_date, t1.approve_date, t1.request_id, t1.member_id, t1.status as register_status, t1.period_id, t2.id as cremation_member_raw_id, t2.cremation_member_id, t2.status as member_status,
                                        t3.id as funeral_manager_id, t3.prename_id as funeral_manager_prename_id, t3.firstname as funeral_manager_firstname, t3.lastname as funeral_manager_lastname,
                                        t3.address_no as funeral_manager_address_no, t3.address_moo as funeral_manager_address_moo, t3.address_soi as funeral_manager_address_soi,
                                        t3.address_road as funeral_manager_address_road, t3.address_tambol as funeral_manager_address_tambol, t3.address_amphur as funeral_manager_address_amphur, t3.address_province as funeral_manager_address_province,
                                        t3.phone_number as funeral_manager_phone_number, t3.relate_type_id as funeral_manager_relate_id, t3.zipcode as funeral_manager_zipcode, t3.address_village as funeral_manager_address_village,
                                        t4.id as resign_id, t4.request_date as resign_req_date, t4.approve_date as resign_approve_date, t4.status as resign_status, t4.receipt_id as resign_receipt_id, t4.reason as resign_reason")
                                ->from("coop_sp_cremation_registration as t1")
                                ->join("coop_sp_cremation_member as t2", "t1.cremation_member_id = t2.id", "left")
                                ->join("coop_sp_cremation_funeral_manager as t3", "t1.id = t3.registration_id", "left")
                                ->join("coop_sp_cremation_resign as t4", "t1.cremation_member_id = t4.cremation_member_id", "left")
                                ->where($where)
                                ->get()->row();

        if($type == 1 || !empty($cremation->member_id)) {
            $member_id = !empty($cremation->member_id) ? $cremation->member_id : $id;
            $result["member_id"] = $member_id;

            $members = $this->db->select("*")->from("coop_mem_apply")->where("member_id = '".$member_id."'")->get()->result_array();
            foreach($members as $member) {
                foreach($member as $key=>$value) {
                    if($key == "prename_id") {
                        $result[$key] = (int) $value;
                    } else {
                        $result[$key] = $value;
                    }
                }
            }
        }

        $result["id"] = $cremation->id;
        $result["cremation_member_raw_id"] = $cremation->cremation_member_raw_id;
        $result["period_id"] = $cremation->period_id;
        $result["request_date"] = $cremation->request_date;
        $result["approve_date"] = $cremation->approve_date;
        $result["request_id"] = $cremation->request_id;
        $result["register_status"] = $cremation->register_status;
        $result["cremation_member_id"] = $cremation->cremation_member_id;
        $result["member_status"] = $cremation->member_status;
        $result["funeral_manager_id"] = $cremation->funeral_manager_id;
        $result["funeral_manager_prename_id"] = $cremation->funeral_manager_prename_id;
        $result["funeral_manager_firstname"] = $cremation->funeral_manager_firstname;
        $result["funeral_manager_lastname"] = $cremation->funeral_manager_lastname;
        $result["funeral_manager_address_no"] = $cremation->funeral_manager_address_no;
        $result["funeral_manager_address_moo"] = $cremation->funeral_manager_address_moo;
        $result["funeral_manager_address_village"] = $cremation->funeral_manager_address_village;
        $result["funeral_manager_address_soi"] = $cremation->funeral_manager_address_soi;
        $result["funeral_manager_address_road"] = $cremation->funeral_manager_address_road;
        $result["funeral_manager_address_tambol"] = $cremation->funeral_manager_address_tambol;
        $result["funeral_manager_address_amphur"] = $cremation->funeral_manager_address_amphur;
        $result["funeral_manager_address_province"] = $cremation->funeral_manager_address_province;
        $result["funeral_manager_phone_number"] = $cremation->funeral_manager_phone_number;
        $result["funeral_manager_zipcode"] = $cremation->funeral_manager_zipcode;
        $result["funeral_manager_relate_id"] = $cremation->funeral_manager_relate_id;
        return $result;
    }

    public function get_members($cremation_id, $status_arr) {
        $result = array();

        $where = "t1.status IS NOT NULL";
        if(!empty($status_arr)) {
            $where .= " AND t1.status IN (".implode(',',$status_arr).")";
        }

        $cremations = $this->db->select("t1.id as cremation_member_id")
                                ->from("coop_sp_cremation_member as t1")
                                ->where($where)
                                ->get()->result_array();

        if(!empty($cremations)) {
            $result["datas"] = $cremations;
            $result["status"] = "success";
        } else {
            $result["status"] = "no_mem";
        }
        return $result;
    }
    
    public function get_info_data($cremation_id) {
        $result = array();
        $result['provinces'] = $this->db->select('*')->from('coop_province')->get()->result_array();
        $result['amphurs'] = $this->db->select('*')->from('coop_amphur')->get()->result_array();
        $result['districts'] = $this->db->select('*')->from('coop_district')->get()->result_array();
        $result['member_relations'] = $this->db->select('*')->from('coop_mem_relation')->get()->result_array();
        $result['prenames'] = $this->db->select('*')->from('coop_prename')->get()->result_array();
        $result["departments"] = $this->db->select('*')->from('coop_mem_group')->where("mem_group_type = 1")->get()->result_array();
        $result["factions"] = $this->db->select('*')->from('coop_mem_group')->where("mem_group_type = 2")->get()->result_array();
        $result["levels"] = $this->db->select('*')->from('coop_mem_group')->where("mem_group_type = 3")->get()->result_array();

        $current_timestamp = date('Y-m-d H:i:s');
        $result["register_periods"] = $this->db->select("*")->from("coop_sp_cremation_registration_period")->where("cremation_id  = '".$cremation_id."' AND status = 1 AND start_date <= '".$current_timestamp."' AND end_date >= '".$current_timestamp."'")->get()->result_array();//Please filter start n end date

        return $result;
    }

    public function save_register_request($cremation_id, $param) {
        $result = array();
        $process_timestamp = date('Y-m-d H:i:s');

        $data_insert = array();
        $data_insert["cremation_id"] = $cremation_id;
        $data_insert["member_id"] = $param['member_id'];
        if(empty($param["member_cremation_id"])) {
            $data_insert["created_at"] = $process_timestamp;
            $data_insert["updated_at"] = $process_timestamp;
            $this->db->insert('coop_sp_cremation_member', $data_insert);
            $cremation_member_id = $this->db->insert_id();
        } else {
            $data_insert["updated_at"] = $process_timestamp;
            $this->db->where('id', $param["member_cremation_id"]);
            $this->db->update('coop_sp_cremation_member', $data_insert);
            $cremation_member_id = $param["member_cremation_id"];
        }

        $result["cremation_member_id"] = $cremation_member_id;

        $data_insert = array();
        $data_insert["cremation_id"] = $cremation_id;
        $data_insert["cremation_member_id"] = $cremation_member_id;
        $data_insert["member_id"] = $param['member_id'];
        $data_insert["period_id"] = $param['period_id'];
        $data_insert["status"] = 1;
        $data_insert["request_date"] = !empty($param['request_date']) ? $this->center_function->ConvertToSQLDate($param['request_date']) : $process_timestamp;
        if(empty($param["cremation_request_id"])) {
            $request = $this->db->select("request_id")->from("coop_sp_cremation_registration")->where("cremation_id = '".$cremation_id."'")->order_by("request_date DESC")->get()->row();
            $request_id = '';
            if(!empty($request)) {
                $request_id = sprintf('%08d', ((int) $request->request_id) + 1);
            } else {
                $request_id = sprintf('%08d', 1);
            }

            $data_insert["request_id"] = $request_id;
            $data_insert["created_at"] = $process_timestamp;
            $data_insert["updated_at"] = $process_timestamp;
            $this->db->insert('coop_sp_cremation_registration', $data_insert);
            $register_id = $this->db->insert_id();
        } else {
            $data_insert["updated_at"] = $process_timestamp;
            $this->db->where('id', $param["cremation_request_id"]);
            $this->db->update('coop_sp_cremation_registration', $data_insert);
            $register_id = $param["cremation_request_id"];
        }

        $result["register_id"] = $register_id;
        $result["request_id"] = $request_id;

        $data_insert = array();
        $data_insert["cremation_member_id"] = $cremation_member_id;
        $data_insert["registration_id"] = $register_id;
        $data_insert["prename_id"] = $param['funeral_manage_profile_id'];
        $data_insert["firstname"] = $param['funeral_manage_firstname'];
        $data_insert["lastname"] = $param['funeral_manage_lastname'];
        $data_insert["address_no"] = $param['funeral_manage_address_no'];
        $data_insert["address_moo"] = $param['funeral_manage_address_moo'];
        $data_insert["address_village"] = $param["funeral_manage_address_village"];
        $data_insert["address_soi"] = $param['funeral_manage_address_soi'];
        $data_insert["address_road"] = $param['funeral_manage_address_road'];
        $data_insert["address_tambol"] = $param['funeral_manage_district_id'];
        $data_insert["address_amphur"] = $param['funeral_manage_amphur_id'];
        $data_insert["address_province"] = $param['funeral_manage_province_id'];
        $data_insert["phone_number"] = $param["funeral_manage_phone"];
        $data_insert["zipcode"] = $param["funeral_manage_zipcode"];
        $data_insert["relate_type_id"] = $param["funeral_manage_relate"];
        if(empty($param["cremation_request_id"])) {
            $data_insert["created_at"] = $process_timestamp;
            $data_insert["updated_at"] = $process_timestamp;
            $this->db->insert('coop_sp_cremation_funeral_manager', $data_insert);
        } else {
            $data_insert["updated_at"] = $process_timestamp;
            $this->db->where('registration_id', $param["cremation_request_id"]);
            $this->db->update('coop_sp_cremation_funeral_manager', $data_insert);
        }

        return $result;
    }

    /*
        1=ยื่นคำร้อง
        2=ชำระเงินแล้ว
        3=อนุมัติ
        4=ไม่อนุมัติ
        5=ยกเลิก
    */
    public function get_register_request_by_status($cremation_id, $status, $status_arr, $page) {
        $result = array();

        $where = "";
        if(!empty($status)) {
            $where .= " AND t1.status = ".$status;
        }

        if(!empty($status_arr)) {
            $where .= " AND t1.status in (".implode(',',$status_arr).")";
        }

        $x=0;
		$join_arr = array();
		$join_arr[$x]['table'] = 'coop_mem_apply as t2';
		$join_arr[$x]['condition'] = 't1.member_id = t2.member_id';
        $join_arr[$x]['type'] = 'inner';
        $x++;
        $join_arr[$x]['table'] = 'coop_prename as t3';
		$join_arr[$x]['condition'] = 't2.prename_id = t3.prename_id';
        $join_arr[$x]['type'] = 'left';
        $x++;
        $join_arr[$x]['table'] = 'coop_sp_cremation_registration_period as t4';
		$join_arr[$x]['condition'] = 't1.period_id = t4.id';
        $join_arr[$x]['type'] = 'left';
        $x++;
        $join_arr[$x]['table'] = 'coop_sp_cremation_receipt as t5';
		$join_arr[$x]['condition'] = 't1.receipt_id = t5.id AND t5.status = 1';
		$join_arr[$x]['type'] = 'left';
        $this->paginater_all->type(DB_TYPE);
        $this->paginater_all->select('t1.id,
                                        t1.request_id,
                                        t1.period_id,
                                        t1.request_date,
                                        t1.status as register_status,
                                        t2.firstname_th,
                                        t2.lastname_th,
                                        t2.member_id,
                                        t3.prename_full,
                                        t4.name as period_name,
                                        t4.annual_fee,
                                        t4.fee,
                                        t4.assoc_fee,
                                        t4.other_fee,
                                        t5.receipt_no,
                                        t5.id as receipt_id');
        $this->paginater_all->main_table('coop_sp_cremation_registration as t1');
        $this->paginater_all->where("t1.cremation_id = '".$cremation_id."'".$where);
        $this->paginater_all->page_now($page);
        $this->paginater_all->per_page(20);
        $this->paginater_all->page_link_limit(20);
        $this->paginater_all->order_by('request_date DESC');
        $this->paginater_all->join_arr($join_arr);
        $row = $this->paginater_all->paginater_process();

        $paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'],$_GET);
        $result["page_start"] = $row['page_start'];
        $result['paging'] = $paging;
        $result['datas'] = $row['data'];

        return $result;
    }

    public function pay_register_fee($cremation_id, $param) {
        $receipt_no = "";
        $process_timestamp = date('Y-m-d H:i:s');

        $receipt = $this->db->select("receipt_no")->from("coop_sp_cremation_receipt")->where("cremation_id = '".$cremation_id."' AND type = 1")->order_by("receipt_no DESC")->get()->row();
        if(!empty($receipt)) {
            $receipt_no = sprintf('%08d',(((int) $receipt->receipt_no) + 1));
        } else {
            $receipt_no = '00000001';
        }

        $request = $this->db->select("*")->from("coop_sp_cremation_registration")->where("cremation_id = '".$cremation_id."' AND id = '".$param["register_id"]."'")->get()->row();

        $data_insert = array();
        $data_insert["receipt_no"] = $receipt_no;
        $data_insert["cremation_id"] = $cremation_id;
        $data_insert["register_id"] = $param["register_id"];
        $data_insert["cremation_member_id"] = $request->cremation_member_id;
        $data_insert["status"] = 1;
        $data_insert["type"] = 1;
        $data_insert['user_id'] = $_SESSION['USER_ID'];
        $data_insert['ref'] = $param["register_id"];
        $data_insert["created_at"] = $process_timestamp;
        $data_insert["updated_at"] = $process_timestamp;
        $this->db->insert('coop_sp_cremation_receipt', $data_insert);
        $receipt_id = $this->db->insert_id();

        $total = 0;
        $data_inserts = array();
        if(!empty($param["annual_fee"])) {
            $data_insert = array();
            $data_insert["receipt_id"] = $receipt_id;
            $data_insert["description"] = "เงินค่าสมัคร";
            $data_insert["amount"] = str_replace( ',', '', $param["annual_fee"]);
            $data_insert["data_code"] = "annual_fee";
            $data_insert["created_at"] = $process_timestamp;
            $data_insert["updated_at"] = $process_timestamp;
            $data_inserts[] = $data_insert;
            $total += str_replace( ',', '', $param["annual_fee"]);
        }

        if(!empty($param["fee"])) {
            $data_insert = array();
            $data_insert["receipt_id"] = $receipt_id;
            $data_insert["description"] = "ค่าบำรุงนรายปี";
            $data_insert["amount"] = str_replace( ',', '', $param["fee"]);
            $data_insert["data_code"] = "register_fee";
            $data_insert["created_at"] = $process_timestamp;
            $data_insert["updated_at"] = $process_timestamp;
            $data_inserts[] = $data_insert;
            $total += str_replace( ',', '', $param["fee"]);
        }

        if(!empty($param["assoc_fee"])) {
            $data_insert = array();
            $data_insert["receipt_id"] = $receipt_id;
            $data_insert["description"] = "ค่าสงเคราะห์ศพล่วงหน้า";
            $data_insert["amount"] = str_replace( ',', '', $param["assoc_fee"]);
            $data_insert["data_code"] = "assoc_fee";
            $data_insert["created_at"] = $process_timestamp;
            $data_insert["updated_at"] = $process_timestamp;
            $data_inserts[] = $data_insert;
            $total += str_replace( ',', '', $param["assoc_fee"]);
        }

        if(!empty($param["other_fee"])) {
            $data_insert = array();
            $data_insert["receipt_id"] = $receipt_id;
            $data_insert["description"] = "อื่นๆ";
            $data_insert["amount"] = str_replace( ',', '', $param["other_fee"]);
            $data_insert["data_code"] = "other_fee";
            $data_insert["created_at"] = $process_timestamp;
            $data_insert["updated_at"] = $process_timestamp;
            $data_inserts[] = $data_insert;
            $total += str_replace( ',', '', $param["other_fee"]);
        }

        if (!empty($data_inserts)) {
            $this->db->insert_batch('coop_sp_cremation_receipt_detail', $data_inserts);
        }

        $data_insert = array();
        $data_insert["total"] = $total;
        $this->db->where('id', $receipt_id);
        $this->db->update("coop_sp_cremation_receipt", $data_insert);

        $data_insert = array();
        $data_insert["status"] = 2;
        $data_insert["payment_date"] = $process_timestamp;
        $data_insert["receipt_id"] = $receipt_id;
        $this->db->where('id', $param["register_id"]);
        $this->db->update("coop_sp_cremation_registration", $data_insert);

        return 'success';
    }

    public function get_receipt_data($cremation_id, $receipt_id, $receipt_no) {
        $result = array();
        $where = "";
        if(!empty($receipt_id)) {
            $where .= " AND t1.id = '".$receipt_id."'";
        }
        if (!empty($receipt_no)) {
            $where .= " AND t1.receipt_no = '".$receipt_no."'";
        }

        $receipt = $this->db->select("*")
                            ->from("coop_sp_cremation_receipt as t1")
                            ->where("t1.cremation_id = '".$cremation_id."'".$where)
                            ->get()->result_array()[0];
        $result["receipt"] = $receipt;
        
        if(!empty($receipt["cremation_member_id"])) {
            $member = $this->db->select("t1.cremation_member_id, t1.member_id, t2.firstname_th, t2.lastname_th, t3.prename_full, t4.mem_group_name as level_name, t5.mem_group_name as faction_name")
                                ->from("coop_sp_cremation_member as t1")
                                ->join("coop_mem_apply as t2", "t1.member_id = t2.member_id", "inner")
                                ->join("coop_prename as t3", "t2.prename_id = t3.prename_id", "left")
                                ->join("coop_mem_group as t4", "t2.level = t4.id", "left")
                                ->join("coop_mem_group as t5", "t2.faction = t5.id", "left")
                                ->where("t1.id = '".$receipt["cremation_member_id"]."' AND t1.cremation_id = '".$cremation_id."'")
                                ->get()->result_array()[0];
            $result["member"] = $member;
        } else if (!empty($receipt["register_id"])) {
            $member = $this->db->select("t1.request_id as register_no, t2.firstname_th, t2.lastname_th, t3.prename_full, t4.mem_group_name as level_name, t5.mem_group_name as faction_name")
                                ->from("coop_sp_cremation_registration as t1")
                                ->join("coop_mem_apply as t2", "t1.member_id = t2.member_id", "inner")
                                ->join("coop_prename as t3", "t2.prename_id = t3.prename_id", "left")
                                ->join("coop_mem_group as t4", "t2.level = t4.id", "left")
                                ->join("coop_mem_group as t5", "t2.faction = t5.id", "left")
                                ->where("t1.id = '".$receipt["register_id"]."' AND t1.cremation_id = '".$cremation_id."'")
                                ->get()->result_array()[0];
            $result["member"] = $member;
        }

        $details = $this->db->select("*")->from("coop_sp_cremation_receipt_detail")->where("receipt_id = '".$receipt["id"]."'")->get()->result_array();
        $result["details"] = $details;

        return $result;
    }

    public function approve_registers($cremation_id, $param) {
        $process_timestamp = date('Y-m-d H:i:s');

        $update_arr = array();
        foreach($param["register_ids"] as $register_id) {
            $update = array();
            $update["id"] = $register_id;
            $update['approve_user_id'] = $_SESSION['USER_ID'];
            $update["status"] = 3;
            $update_arr[] = $update;

            //Get last get cremartion no(cremation member id)
            $member = $this->db->select("cremation_member_id")->from("coop_sp_cremation_member")->where("cremation_member_id IS NOT NULL AND cremation_id = '".$cremation_id."'")->order_by("cremation_member_id DESC")->get()->row();
            $cremation_member_id = '';
            if(!empty($member)) {
                $cremation_member_id = sprintf('%08d',(((int) $member->cremation_member_id) + 1));
            } else {
                $cremation_member_id = '00000001';
            }
            $register = $this->db->select("cremation_member_id")->from("coop_sp_cremation_registration")->where("id = '".$register_id."'")->get()->row();
            $update = array();
            $update["cremation_member_id"] = $cremation_member_id;
            $update["status"] = 1;
            $update["status"] = 1;
            $update["member_date"] = $process_timestamp;
            $update["updated_at"] = $process_timestamp;
            $this->db->where('id', $register->cremation_member_id);
            $this->db->update('coop_sp_cremation_member', $update);
        }
        $this->db->update_batch('coop_sp_cremation_registration', $update_arr, 'id');
        return 'success';
    }

    public function disapprove_registers($cremation_id, $param) {
        $process_timestamp = date('Y-m-d H:i:s');

        $update_register_arr = array();
        foreach($param["register_ids"] as $register_id) {
            $update = array();
            $update["id"] = $register_id;
            $update["status"] = 4;
            $update['approve_user_id'] = $_SESSION['USER_ID'];
            $update["approve_date"] = $process_timestamp;
            $update["updated_at"] = $process_timestamp;
            $update_register_arr[] = $update;
        }
        $this->db->update_batch('coop_sp_cremation_registration', $update_register_arr, 'id');
        return 'success';
    }

    public function get_resign_member($cremation_id, $status_arr, $param) {
        if(!empty($status_arr)) {
            $where .= " AND t1.status in (".implode(',',$status_arr).")";
        }

        $x=0;
		$join_arr = array();
		$join_arr[$x]['table'] = 'coop_sp_cremation_member as t2';
		$join_arr[$x]['condition'] = 't1.cremation_member_id = t2.id';
        $join_arr[$x]['type'] = 'inner';
        $x++;
		$join_arr[$x]['table'] = 'coop_mem_apply as t3';
		$join_arr[$x]['condition'] = 't2.member_id = t3.member_id';
        $join_arr[$x]['type'] = 'inner';
        $x++;
		$join_arr[$x]['table'] = 'coop_prename as t4';
		$join_arr[$x]['condition'] = 't4.prename_id = t3.prename_id';
        $join_arr[$x]['type'] = 'left';
        $x++;
		$join_arr[$x]['table'] = 'coop_sp_cremation_registration as t5';
		$join_arr[$x]['condition'] = 't1.cremation_member_id = t5.cremation_member_id';
        $join_arr[$x]['type'] = 'left';
        $x++;
		$join_arr[$x]['table'] = 'coop_sp_cremation_registration_period as t6';
		$join_arr[$x]['condition'] = 't5.period_id = t6.id';
        $join_arr[$x]['type'] = 'left';
        $x++;
		$join_arr[$x]['table'] = 'coop_sp_cremation_receipt as t7';
		$join_arr[$x]['condition'] = 't1.receipt_id = t7.id';
        $join_arr[$x]['type'] = 'left';
        $this->paginater_all->type(DB_TYPE);
        $this->paginater_all->select('t1.id as resign_id,
                                        t1.reason,
                                        t1.cremation_member_id,
                                        t1.request_date,
                                        t1.approve_date,
                                        t1.status as resign_status,
                                        t1.receipt_id,
                                        t2.cremation_member_id as cremation_member_no,
                                        t3.firstname_th,
                                        t3.lastname_th,
                                        t4.prename_full,
                                        t5.id as register_id,
                                        t5.period_id,
                                        t6.name,
                                        t7.receipt_no');
        $this->paginater_all->main_table('coop_sp_cremation_resign as t1');
        $this->paginater_all->where("t1.cremation_id = '".$cremation_id."'".$where);
        $this->paginater_all->page_now($page);
        $this->paginater_all->per_page(20);
        $this->paginater_all->page_link_limit(20);
        $this->paginater_all->order_by('t1.request_date DESC');
        $this->paginater_all->join_arr($join_arr);
        $row = $this->paginater_all->paginater_process();

        $paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'],$_GET);
        $result["page_start"] = $row['page_start'];
        $result['paging'] = $paging;
        $result['datas'] = $row['data'];

        return $result;
    }

    public function check_cremation_member_id($cremation_id, $param) {
        $result = array();
        $cremation = $this->db->select("id, cremation_member_id")->from("coop_sp_cremation_member")->where("cremation_member_id LIKE '%".$param["cremation_member_id"]."' AND cremation_id = '".$cremation_id."'")->get()->row();
        if(!empty($cremation)) {
            $result["cremation_member_id"] = $cremation->cremation_member_id;
            $result["id"] = $cremation->id;
            $result["status"] = 'success';
        } else {
            $result["status"] = 'error';
        }

        return $result;
    }

    public function save_request_resign($cremation_id, $param) {
        $result = array();
        $result["status"] = "success";
        $process_timestamp = date('Y-m-d H:i:s');

        $cremation = $this->db->select("id, status")->from("coop_sp_cremation_member")->where("cremation_member_id LIKE '%".$param["cremation_member_id"]."' AND cremation_id = '".$cremation_id."'")->get()->row();
        if(!empty($cremation)) {
            if($cremation->status != 1) {
                $result["status"] = "error";
                $result["message"] = "สมาชิกไม่อยู่ในสถานะที่สามารถขอลาออกได้";
            } else if(empty($param["resign_id"])) {
                $data_insert = array();
                $data_insert["cremation_id"] = $cremation_id;
                $data_insert["cremation_member_id"] = $cremation->id;
                $data_insert["reason"] = $param["reason"];
                $data_insert["request_date"] = $process_timestamp;
                $data_insert["status"] = 0;
                $data_insert['request_user_id'] = $_SESSION['USER_ID'];
                $data_insert["created_at"] = $process_timestamp;
                $data_insert["updated_at"] = $process_timestamp;
                $this->db->insert('coop_sp_cremation_resign', $data_insert);

                $data_update = array();
                $data_update["status"] = 2;
                $data_update["updated_at"] = $process_timestamp;
                $this->db->where('id', $cremation->id);
                $this->db->update('coop_sp_cremation_member', $data_update);
            } else {
                $data_update = array();
                $data_update["cremation_member_id"] = $cremation->id;
                $data_update["reason"] = $param["reason"];
                $data_update["status"] = 0;
                $data_update["updated_at"] = $process_timestamp;
                $this->db->where('id', $param["resign_id"]);
                $this->db->update('coop_sp_cremation_resign', $data_update);
            }
        } else {
            $result["status"] = "error";
            $result["message"] = "ไม่พบเลขฌาปนกิจ";
        }

        return $result;
    }

    public function resign_approve($cremation_id, $resign_id, $payment) {
        $process_timestamp = date('Y-m-d H:i:s');
        $result = array();

        $request = $this->db->select("cremation_member_id")->from("coop_sp_cremation_resign")->where("id = '".$resign_id."'")->get()->row();
        if(!empty($request)) {
            $data_update = array();
            $data_update["status"] = 3;
            $data_update["updated_at"] = $process_timestamp;
            $this->db->where('id', $request->cremation_member_id);
            $this->db->update('coop_sp_cremation_member', $data_update);
        }

        $receipt_no = "";
        $receipt = $this->db->select("receipt_no")->from("coop_sp_cremation_receipt")->where("cremation_id = '".$cremation_id."' AND type IN (2,3)")->order_by("receipt_no DESC")->get()->row();
        if(!empty($receipt)) {
            $receipt_no = sprintf('%08d',(((int) $receipt->receipt_no) + 1));
        } else {
            $receipt_no = '00000001';
        }

        $data_insert = array();
        $data_insert["receipt_no"] = $receipt_no;
        $data_insert["cremation_id"] = $cremation_id;
        $data_insert["register_id"] = $param["register_id"];
        $data_insert["cremation_member_id"] = $request->cremation_member_id;
        $data_insert["status"] = 1;
        $data_insert["type"] = 2;
        $data_insert["total"] = str_replace( ',', '', $payment);
        $data_insert["receipt_datetime"] = $process_timestamp;
        $data_insert['ref'] = $resign_id;
        $data_insert['user_id'] = $_SESSION['USER_ID'];
        $data_insert["created_at"] = $process_timestamp;
        $data_insert["updated_at"] = $process_timestamp;
        $this->db->insert('coop_sp_cremation_receipt', $data_insert);
        $receipt_id = $this->db->insert_id();

        $total = 0;
        $data_inserts = array();
        if(!empty($payment) && $payment != '0.00') {
            $data_insert = array();
            $data_insert["receipt_id"] = $receipt_id;
            $data_insert["description"] = "เงินคืน";
            $data_insert["amount"] = str_replace( ',', '', $payment);
            $data_insert["data_code"] = "resign_cash";
            $data_insert["created_at"] = $process_timestamp;
            $data_insert["updated_at"] = $process_timestamp;
            $this->db->insert('coop_sp_cremation_receipt_detail', $data_insert);
        }

        $data_update = array();
        $data_update["status"] = 1;
        $data_update["receipt_id"] = $receipt_id;
        $data_update['approve_user_id'] = $_SESSION['USER_ID'];
        $data_update["approve_date"] = $process_timestamp;
        $data_update["updated_at"] = $process_timestamp;
        $this->db->where('id', $resign_id);
        $this->db->update('coop_sp_cremation_resign', $data_update);

        $result["status"] = "success";
        $result["receipt_id"] = $receipt_id;
        return $result;
    }

    public function resign_disapprove($cremation_id, $resign_id) {
        $process_timestamp = date('Y-m-d H:i:s');
        $result = array();

        $data_update = array();
        $data_update["status"] = 2;
        $data_update['approve_user_id'] = $_SESSION['USER_ID'];
        $data_update["approve_date"] = $process_timestamp;
        $data_update["updated_at"] = $process_timestamp;
        $this->db->where('id', $resign_id);
        $this->db->update('coop_sp_cremation_resign', $data_update);

        $request = $this->db->select("cremation_member_id")->from("coop_sp_cremation_resign")->where("id = '".$resign_id."'")->get()->row();
        if(!empty($request)) {
            $data_update = array();
            $data_update["status"] = 1;
            $data_update["updated_at"] = $process_timestamp;
            $this->db->where('id', $request->cremation_member_id);
            $this->db->update('coop_sp_cremation_member', $data_update);
        }

        $result["status"] = "success";
        return $result;
    }


    public function get_request_money_member($cremation_id, $status_arr, $param) {
        if(!empty($status_arr)) {
            $where .= " AND t1.status in (".implode(',',$status_arr).")";
        }

        $x=0;
		$join_arr = array();
		$join_arr[$x]['table'] = 'coop_sp_cremation_member as t2';
		$join_arr[$x]['condition'] = 't1.cremation_member_id = t2.id';
        $join_arr[$x]['type'] = 'inner';
        $x++;
		$join_arr[$x]['table'] = 'coop_mem_apply as t3';
		$join_arr[$x]['condition'] = 't2.member_id = t3.member_id';
        $join_arr[$x]['type'] = 'inner';
        $x++;
		$join_arr[$x]['table'] = 'coop_prename as t4';
		$join_arr[$x]['condition'] = 't4.prename_id = t3.prename_id';
        $join_arr[$x]['type'] = 'left';
        $x++;
		$join_arr[$x]['table'] = 'coop_sp_cremation_registration as t5';
		$join_arr[$x]['condition'] = 't1.cremation_member_id = t5.cremation_member_id';
        $join_arr[$x]['type'] = 'left';
        $x++;
		$join_arr[$x]['table'] = 'coop_sp_cremation_registration_period as t6';
		$join_arr[$x]['condition'] = 't5.period_id = t6.id';
        $join_arr[$x]['type'] = 'left';
        $x++;
		$join_arr[$x]['table'] = 'coop_sp_cremation_receipt as t7';
		$join_arr[$x]['condition'] = 't1.receipt_id = t7.id';
        $join_arr[$x]['type'] = 'left';
        $this->paginater_all->type(DB_TYPE);
        $this->paginater_all->select('t1.id as req_id,
                                        t1.cremation_member_id,
                                        t1.request_date,
                                        t1.approve_date,
                                        t1.status as resign_status,
                                        t1.receipt_id,
                                        t1.reason,
                                        t2.cremation_member_id as cremation_member_no,
                                        t3.firstname_th,
                                        t3.lastname_th,
                                        t4.prename_full,
                                        t5.id as register_id,
                                        t5.period_id,
                                        t6.name,
                                        t7.receipt_no');
        $this->paginater_all->main_table('coop_sp_cremation_request_receive as t1');
        $this->paginater_all->where("t1.cremation_id = '".$cremation_id."'".$where);
        $this->paginater_all->page_now($page);
        $this->paginater_all->per_page(20);
        $this->paginater_all->page_link_limit(20);
        $this->paginater_all->order_by('t1.request_date DESC');
        $this->paginater_all->join_arr($join_arr);
        $row = $this->paginater_all->paginater_process();

        $paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'],$_GET);
        $result["page_start"] = $row['page_start'];
        $result['paging'] = $paging;
        $result['datas'] = $row['data'];

        return $result;
    }

    public function check_save_request_money($cremation_id, $cremation_member_id) {
        $result = array();
        $result["status"] = "success";

        $cremation = $this->db->select("id, status")->from("coop_sp_cremation_member")->where("cremation_member_id LIKE '%".$cremation_member_id."' AND cremation_id = '".$cremation_id."'")->get()->row();

        if(empty($cremation)) {
            $result["status"] = "error";
            $result["message"] = "ไม่พบสมาชิกที่เลือก";
        } else if($cremation->status != 1){
            $result["status"] = "error";
            $result["message"] = "สมาชิกไม่อยู่ในสถานะที่สามารถขอลาออกได้";
        }
        return $result;
    }

    public function save_request_money($cremation_id, $req_id, $file_id_arr, $member_cremation_id, $reason, $file_arr) {
        $result = array();
        $process_timestamp = date('Y-m-d H:i:s');

        $member = $this->db->select("id")->from("coop_sp_cremation_member")->where("cremation_member_id LIKE '%".$cremation_member_id."' AND cremation_id = '".$cremation_id."'")->get()->row();

        if(!empty($member)) {
            if(empty($req_id)) {
                $data_insert = array();
                $data_insert["cremation_id"] = $cremation_id;
                $data_insert["cremation_member_id"] = $member->id;
                $data_insert["reason"] = $reason;
                $data_insert["status"] = 0;
                $data_insert["request_date"] = $process_timestamp;
                $data_insert['request_user_id'] = $_SESSION['USER_ID'];
                $data_insert["created_at"] = $process_timestamp;
                $data_insert["updated_at"] = $process_timestamp;
                $this->db->insert('coop_sp_cremation_request_receive', $data_insert);
                $request_id = $this->db->insert_id();

                $data_update = array();
                $data_update["status"] = 4;
                $data_update["updated_at"] = $process_timestamp;
                $this->db->where('id', $member->id);
                $this->db->update('coop_sp_cremation_member', $data_update);

                $config = array();
                $config['upload_path'] = FCPATH.'assets/uploads/cremation';
                $config['allowed_types'] = '*';
                $config['max_size'] = '0';
                $config['overwrite'] = FALSE;

                $this->load->library('upload', $config);

                $title = date('Ymdhis').$document_id;
                $files = $file_arr;
                foreach ($files['file']['name'] as $key => $name) {
                    if(!empty($files['file']['name'][$key])) {
                        $_FILES['file']['name'] = $files['file']['name'][$key];
                        $_FILES['file']['type'] = $files['file']['type'][$key];
                        $_FILES['file']['tmp_name'] = $files['file']['tmp_name'][$key];
                        $_FILES['file']['error'] = $files['file']['error'][$key];
                        $_FILES['file']['size'] = $files['file']['size'][$key];

                        $fileName = $title.$key;
                        $config['file_name'] = $fileName;

                        $this->upload->initialize($config);
                        $this->upload->do_upload('file');
                        $upload_data = $this->upload->data();

                        //Create file data
                        $data_insert = array();
                        $data_insert["ref"] = $request_id;
                        $data_insert["type"] = "rev";
                        $data_insert['name'] = $files['file']['name'][$key];
                        $data_insert['path'] = "/assets/uploads/cremation/".$upload_data["orig_name"];
                        $data_insert['created_at'] = $process_timestamp;
                        $data_insert['updated_at'] = $process_timestamp;
                        $this->db->insert('coop_sp_cremation_file', $data_insert);
                    }
                }

                $result["status"] = "success";
            } else {
                $data_update = array();
                $data_update["cremation_id"] = $cremation_id;
                $data_update["cremation_member_id"] = $member->id;
                $data_update["reason"] = $reason;
                $data_update["status"] = 0;
                $data_update["updated_at"] = $process_timestamp;
                $this->db->where('id', $req_id);
                $this->db->update('coop_sp_cremation_request_receive', $data_update);
                $request_id = $req_id;

                $data_update = array();
                $data_update["status"] = 4;
                $data_update["updated_at"] = $process_timestamp;
                $this->db->where('id', $member->id);
                $this->db->update('coop_sp_cremation_member', $data_update);

                $config = array();
                $config['upload_path'] = FCPATH.'assets/uploads/cremation';
                $config['allowed_types'] = '*';
                $config['max_size'] = '0';
                $config['overwrite'] = FALSE;

                $this->load->library('upload', $config);

                //Keep old file data
                $old_files = $this->db->select("*")->from("coop_sp_cremation_file")->where("ref = '{$req_id}' AND type = 'rev'")->get()->result_array();

                $title = date('Ymdhis').$document_id;
                $files = $file_arr;
                foreach ($files['file']['name'] as $key => $name) {
                    if(!empty($files['file']['name'][$key])) {
                        $_FILES['file']['name'] = $files['file']['name'][$key];
                        $_FILES['file']['type'] = $files['file']['type'][$key];
                        $_FILES['file']['tmp_name'] = $files['file']['tmp_name'][$key];
                        $_FILES['file']['error'] = $files['file']['error'][$key];
                        $_FILES['file']['size'] = $files['file']['size'][$key];

                        $fileName = $title.$key;
                        $config['file_name'] = $fileName;

                        $this->upload->initialize($config);
                        $this->upload->do_upload('file');
                        $upload_data = $this->upload->data();

                        //Create file data
                        $data_insert = array();
                        $data_insert["ref"] = $request_id;
                        $data_insert["type"] = "rev";
                        $data_insert['name'] = $files['file']['name'][$key];
                        $data_insert['path'] = "/assets/uploads/cremation/".$upload_data["orig_name"];
                        $data_insert['created_at'] = $process_timestamp;
                        $data_insert['updated_at'] = $process_timestamp;
                        $this->db->insert('coop_sp_cremation_file', $data_insert);
                    }
                }

                //Delete old file data
                $file_ids = !empty($file_id_arr) ? $file_id_arr : array();
                foreach($old_files as $file) {
                    if(!in_array($file["id"], $file_ids)) {
                        $data_update = array();
                        $data_update["is_disable"] = 1;
                        $data_update["updated_at"] = $process_timestamp;
                        $this->db->where('id', $file['id']);
                        $this->db->update('coop_sp_cremation_file', $data_update);
                    }
                }

                $result["status"] = "success";
            }
        } else {
            $result["status"] = "error";
            $result["message"] = "ไม่พบสมาชิกที่ทำรายการ";
        }

        return $result;
    }

    public function get_files($cremation_id, $type, $id) {
        $result = array();
        $files = $this->db->select("*")->from("coop_sp_cremation_file")->where("type = '".$type."' AND ref = '".$id."' AND is_disable IS NULL")->get()->result_array();
        $result["files"] = $files;
        return $result;
    }

    public function disapprove_request_money($cremation_id, $request_id) {
        $result = array();
        $request = $this->db->select("cremation_member_id")->from("coop_sp_cremation_request_receive")->where("id = '".$request_id."'")->get()->row();

        $data_update = array();
        $data_update["status"] = 2;
        $data_update['approve_user_id'] = $_SESSION['USER_ID'];
        $data_update['approve_date'] = $process_timestamp;
        $data_update["updated_at"] = $process_timestamp;
        $this->db->where('id', $request_id);
        $this->db->update('coop_sp_cremation_request_receive', $data_update);
        $request_id = $req_id;

        $data_update = array();
        $data_update["status"] = 1;
        $data_update["updated_at"] = $process_timestamp;
        $this->db->where('id', $request->cremation_member_id);
        $this->db->update('coop_sp_cremation_member', $data_update);

        return $result;
    }
    public function request_money_approve($cremation_id, $request_id, $total_payment, $commissions, $receive_payment) {
        $process_timestamp = date('Y-m-d H:i:s');
        $result = array();

        $request = $this->db->select("cremation_member_id")->from("coop_sp_cremation_request_receive")->where("id = '".$request_id."'")->get()->row();
        if(!empty($request)) {
            $data_update = array();
            $data_update["status"] = 5;
            $data_update["updated_at"] = $process_timestamp;
            $this->db->where('id', $request->cremation_member_id);
            $this->db->update('coop_sp_cremation_member', $data_update);
        }

        $receipt_id = null;
        if(!empty($receive_payment) && $receive_payment != '0.00') {
            $receipt_no = "";
            $receipt = $this->db->select("receipt_no")->from("coop_sp_cremation_receipt")->where("cremation_id = '".$cremation_id."' AND type IN (2,3)")->order_by("receipt_no DESC")->get()->row();
            if(!empty($receipt)) {
                $receipt_no = sprintf('%08d',(((int) $receipt->receipt_no) + 1));
            } else {
                $receipt_no = '00000001';
            }

            $data_insert = array();
            $data_insert["receipt_no"] = $receipt_no;
            $data_insert["cremation_id"] = $cremation_id;
            $data_insert["cremation_member_id"] = $request->cremation_member_id;
            $data_insert["status"] = 1;
            $data_insert["type"] = 3;
            $data_insert['ref'] = $request_id;
            $data_insert["total"] = str_replace( ',', '', $receive_payment);
            $data_insert["receipt_datetime"] = $process_timestamp;
            $data_insert["created_at"] = $process_timestamp;
            $data_insert["updated_at"] = $process_timestamp;
            $this->db->insert('coop_sp_cremation_receipt', $data_insert);
            $receipt_id = $this->db->insert_id();

            $data_insert = array();
            $data_insert["receipt_id"] = $receipt_id;
            $data_insert["description"] = "เงินสงเคราะห์";
            $data_insert["amount"] = str_replace( ',', '', $receive_payment);
            $data_insert["data_code"] = "cre_cash";
            $data_insert["created_at"] = $process_timestamp;
            $data_insert["updated_at"] = $process_timestamp;
            $this->db->insert('coop_sp_cremation_receipt_detail', $data_insert);
        }

        $data_update = array();
        $data_update["status"] = 1;
        $data_update["receipt_id"] = $receipt_id;
        $data_update['approve_user_id'] = $_SESSION['USER_ID'];
        $data_update["total_receive"] = str_replace( ',', '', $total_payment);
        $data_update["member_receive"] = str_replace( ',', '', $receive_payment);
        $data_update['approve_date'] = $process_timestamp;
        $data_update["updated_at"] = $process_timestamp;
        $this->db->where('id', $request_id);
        $this->db->update('coop_sp_cremation_request_receive', $data_update);

        $data_inserts = array();
        foreach($commissions as $code=>$commission) {
            $data_insert = array();
            $data_insert["cremation_id"] = $cremation_id;
            $data_insert["code"] = $code;
            $data_insert["type"] = "rev";
            $data_insert["ref"] = $request_id;
            $data_insert["amount"] = str_replace( ',', '', $commission);
            $data_insert["created_at"] = $process_timestamp;
            $data_insert["updated_at"] = $process_timestamp;
            $data_inserts[] = $data_insert;
        }
        if(!empty($data_inserts)) $this->db->insert_batch('coop_sp_cremation_commission', $data_inserts);

        $result["status"] = "success";
        $result["receipt_id"] = $receipt_id;
        return $result;
    }

    public function get_request_money_payment_setting($cremation_id) {
        $result = array();

        $result["settings"] = $this->db->select("*")->from("coop_sp_cremation_setting")->where("cremation_id = '".$cremation_id."' AND code LIKE 'req_rev%'")->get()->result_array();

        return $result;
    }
    public function save_setting($cremation_id, $data_arr) {
        $result = array();
        $update_arr = array();
        $history_arr = array();
        $process_timestamp = date('Y-m-d H:i:s');
        foreach($data_arr as $code=>$value) {
            $setting = $this->db->select("*")->from("coop_sp_cremation_setting")->where("cremation_id = '".$cremation_id."' AND code = '".$code."'")->get()->row();

            if($value != $setting->value) {
                $history = array();
                $history["cremation_id"] = $cremation_id;
                $history["code"] = $code;
                $history["old_value"] = $setting->value;
                $history["new_value"] = $value;
                $history["created_at"] = $process_timestamp;
                $history_arr[] = $history;
    
                $update = array();
                $update["id"] = $setting->id;
                $update["code"] = $code;
                $update["value"] = $value;
                $update["updaed_at"] = $process_timestamp;
                $update_arr[] = $update;
            }
        }

        if(!empty($update_arr)) $this->db->update_batch('coop_sp_cremation_setting', $update_arr, 'id');
        if(!empty($history_arr)) $this->db->insert_batch('coop_sp_cremation_setting_history', $history_arr);
        $result["status"] = "success";
        return $result;
    }

    public function check_registers($cremation_id, $conditions) {
        $result = array();

        $where = "t1.status IS NOT NULL";
        if(!empty($conditions)) {
            if(!empty($conditions["status"])) {
                $where .= " AND t1.status IN (".implode(',',$conditions["status"]).")";
            }
            if(!empty($conditions["register_from_date"])) {
                $where .= " AND t1.request_date >= '".$this->center_function->ConvertToSQLDate($conditions['register_from_date'])."'";
            }
            if(!empty($conditions["register_thru_date"])) {
                $where .= " AND t1.request_date <= '".$this->center_function->ConvertToSQLDate($conditions['register_thru_date'])."'";
            }
            if(!empty($conditions["payment_from_date"])) {
                $where .= " AND t1.payment_date >= '".$this->center_function->ConvertToSQLDate($conditions['payment_from_date'])."'";
            }
            if(!empty($conditions["payment_thru_date"])) {
                $where .= " AND t1.payment_date <= '".$this->center_function->ConvertToSQLDate($conditions['payment_thru_date'])."'";
            }
            if(!empty($conditions["approve_from_date"])) {
                $where .= " AND t1.approve_date >= '".$this->center_function->ConvertToSQLDate($conditions['approve_from_date'])."'";
            }
            if(!empty($conditions["approve_thru_date"])) {
                $where .= " AND t1.approve_date <= '".$this->center_function->ConvertToSQLDate($conditions['approve_thru_date'])."'";
            }
        }

        $cremations = $this->db->select("t1.id")
                                ->from("coop_sp_cremation_registration as t1")
                                ->where($where)
                                ->limit(1)
                                ->get()->result_array();

        if(!empty($cremations)) {
            $result["datas"] = $cremations;
            $result["status"] = "success";
        } else {
            $result["status"] = "no_mem";
        }
        return $result;
    }

    public function check_resigns($cremation_id, $conditions) {
        $result = array();

        $where = "t1.status IS NOT NULL";
        if(!empty($conditions)) {
            if(!empty($conditions["status"])) {
                $where .= " AND t1.status IN (".implode(',',$conditions["status"]).")";
            }
            if(!empty($conditions["request_from_date"])) {
                $where .= " AND t1.request_date >= '".$this->center_function->ConvertToSQLDate($conditions['request_from_date'])."'";
            }
            if(!empty($conditions["request_thru_date"])) {
                $where .= " AND t1.request_date <= '".$this->center_function->ConvertToSQLDate($conditions['request_thru_date'])."'";
            }
            if(!empty($conditions["approve_from_date"])) {
                $where .= " AND t1.approve_date >= '".$this->center_function->ConvertToSQLDate($conditions['approve_from_date'])."'";
            }
            if(!empty($conditions["approve_thru_date"])) {
                $where .= " AND t1.approve_date <= '".$this->center_function->ConvertToSQLDate($conditions['approve_thru_date'])."'";
            }
        }

        $cremations = $this->db->select("t1.id")
                                ->from("coop_sp_cremation_resign as t1")
                                ->where($where)
                                ->limit(1)
                                ->get()->result_array();

        if(!empty($cremations)) {
            $result["datas"] = $cremations;
            $result["status"] = "success";
        } else {
            $result["status"] = "no_mem";
        }
        return $result;
    }

    public function check_request_moneys($cremation_id, $conditions) {
        $result = array();

        $where = "t1.status IS NOT NULL";
        if(!empty($conditions)) {
            if(!empty($conditions["status"])) {
                $where .= " AND t1.status IN (".implode(',',$conditions["status"]).")";
            }
            if(!empty($conditions["request_from_date"])) {
                $where .= " AND t1.request_date >= '".$this->center_function->ConvertToSQLDate($conditions['request_from_date'])."'";
            }
            if(!empty($conditions["request_thru_date"])) {
                $where .= " AND t1.request_date <= '".$this->center_function->ConvertToSQLDate($conditions['request_thru_date'])."'";
            }
            if(!empty($conditions["approve_from_date"])) {
                $where .= " AND t1.approve_date >= '".$this->center_function->ConvertToSQLDate($conditions['approve_from_date'])."'";
            }
            if(!empty($conditions["approve_thru_date"])) {
                $where .= " AND t1.approve_date <= '".$this->center_function->ConvertToSQLDate($conditions['approve_thru_date'])."'";
            }
        }

        $cremations = $this->db->select("t1.id")
                                ->from("coop_sp_cremation_request_receive as t1")
                                ->where($where)
                                ->limit(1)
                                ->get()->result_array();

        if(!empty($cremations)) {
            $result["datas"] = $cremations;
            $result["status"] = "success";
        } else {
            $result["status"] = "no_mem";
        }
        return $result;
    }

    public function check_registration_period($cremation_id, $conditions) {
        $result = array();

        $where = "t1.status = 1";
        if(!empty($conditions)) {
            if(!empty($conditions["start_date"])) {
                $where .= " AND t1.end_date >= '".$this->center_function->ConvertToSQLDate($conditions['start_date'])."'";
            }
            if(!empty($conditions["end_date"])) {
                $where .= " AND t1.start_date <= '".$this->center_function->ConvertToSQLDate($conditions['end_date'])."'";
            }
        }

        $cremations = $this->db->select("t1.id")
                                ->from("coop_sp_cremation_registration_period as t1")
                                ->where($where)
                                ->limit(1)
                                ->get()->result_array();

        if(!empty($cremations)) {
            $result["datas"] = $cremations;
            $result["status"] = "success";
        } else {
            $result["status"] = "no_mem";
        }
        return $result;
    }

    public function check_save_period_fee($cremation_id, $conditions) {
        $result = array();

        $where = "cremation_id = '".$cremation_id."'";
        if(!empty($conditions["period_id"])) {
            $where .= " AND period_id = '".$conditions["period_id"]."'";
        }
        if(!empty($conditions["month"])) {
            $where .= " AND month = '".$conditions["month"]."'";
        }
        if(!empty($conditions["year"])) {
            $where .= " AND year = '".$conditions["year"]."'";
        }

        $period_fee = $this->db->select("*")->from("coop_sp_cremation_fee")->where($where)->get()->result_array();

        if(!empty($period_fee)) {
            $result["datas"] = $period_fee;
            $result["status"] = "success";
        } else {
            $result["status"] = "no_data";
        }

        return $result;
    }

    //Must have "year"
    public function save_period_fee($cremation_id, $period_id, $year, $month, $fee, $assoc_fee, $replace) {
        $process_timestamp = date('Y-m-d H:i:s');

        $result = array();
        if(!empty($period_id)) {
            $where = "cremation_id = '".$cremation_id."' AND period_id = '".$period_id."'";
            if(!empty($month)) {
                $where .= " AND month = '".$month."'";
            }
            if(!empty($year)) {
                $where .= " AND year = '".$year."'";
            }
            $this->db->query("UPDATE coop_sp_cremation_fee SET status = 2 , updated_at = '".$process_timestamp."' WHERE ".$where);

            $data_insert = array();
            $data_insert["cremation_id"] = $cremation_id;
            $data_insert["period_id"] = $period_id;
            $data_insert["month"] = $month;
            $data_insert["year"] = $year;
            $data_insert["fee"] = str_replace( ',', '', $fee);
            $data_insert["status"] = 1;
            $data_insert["assoc_fee"] = str_replace( ',', '', $assoc_fee);
            $data_insert['user_id'] = $_SESSION['USER_ID'];
            $data_insert["created_at"] = $process_timestamp;
            $data_insert["updated_at"] = $process_timestamp;
            $this->db->insert('coop_sp_cremation_fee', $data_insert);
        } else {
            $data_inserts = array();
            $periods = $this->db->select("*")->from("coop_sp_cremation_registration_period")->where("status = 1 AND cremation_id = '".$cremation_id."'")->get()->result_array();
            $where = "cremation_id = '".$cremation_id."' AND status = 1";
            if(!empty($month)) {
                $where .= " AND month = '".$month."'";
            }
            if(!empty($year)) {
                $where .= " AND year = '".$year."'";
            }
            if(!empty($replace)) {
                $this->db->query("UPDATE coop_sp_cremation_fee SET status = 2 , updated_at = '".$process_timestamp."' WHERE ".$where);

                foreach($periods as $period) {
                    $data_insert = array();
                    $data_insert["cremation_id"] = $cremation_id;
                    $data_insert["period_id"] = $period["id"];
                    $data_insert["month"] = $month;
                    $data_insert["year"] = $year;
                    $data_insert["fee"] = str_replace( ',', '', $fee);
                    $data_insert["assoc_fee"] = str_replace( ',', '', $assoc_fee);
                    $data_insert["status"] = 1;
                    $data_insert['user_id'] = $_SESSION['USER_ID'];
                    $data_insert["created_at"] = $process_timestamp;
                    $data_insert["updated_at"] = $process_timestamp;
                    $data_inserts[] = $data_insert;
                }
            } else {
                $exist_periods = $this->db->select("id, period_id")->from("coop_sp_cremation_fee")->where($where)->get()->result_array();
                $check_arr = array();
                foreach($exist_periods as $exist_period) {
                    $check_arr[$exist_period["period_id"]] = $exist_period["id"];
                }

                foreach($periods as $period) {
                    if(empty($check_arr[$period["id"]])) {
                        $data_insert = array();
                        $data_insert["cremation_id"] = $cremation_id;
                        $data_insert["period_id"] = $period["id"];
                        $data_insert["month"] = $month;
                        $data_insert["year"] = $year;
                        $data_insert["fee"] = str_replace( ',', '', $fee);
                        $data_insert["assoc_fee"] = str_replace( ',', '', $assoc_fee);
                        $data_insert["status"] = 1;
                        $data_insert['user_id'] = $_SESSION['USER_ID'];
                        $data_insert["created_at"] = $process_timestamp;
                        $data_insert["updated_at"] = $process_timestamp;
                        $data_inserts[] = $data_insert;
                    }
                }
            }

            if (!empty($data_inserts)) {
                $this->db->insert_batch('coop_sp_cremation_fee', $data_inserts);
            }
        }
        $result["status"] = "success";
        return $result;
    }

    public function get_fees($cremation_id, $conditions) {
        $result = array();
        $where = "t1.cremation_id = '".$cremation_id."'";
        if(!empty($conditions["year"])) {
            $where .= " AND t1.year = '".$conditions["year"]."'";
        }
        if(!empty($conditions["status"])) {
            $where .= " AND t1.status IN (".implode(',',$conditions["status"]).")";
        }

        $datas = $this->db->select("t1.*, t2.name")
                            ->from("coop_sp_cremation_fee as t1")
                            ->join("coop_sp_cremation_registration_period as t2", "t1.period_id = t2.id", "INNER")
                            ->where($where)->get()->result_array();
        $result["datas"] = $datas;
        $result["status"] = "success";
        return $result;
    }

    public function delete_period_fee($cremation_id, $conditions) {
        $result = array();

        $where = "cremation_id = '".$cremation_id."'";
        if(!empty($conditions["id"])) {
            $where .= " AND id = '".$conditions["id"]."'";
        }
        if(!empty($conditions["period_id"])) {
            $where .= " AND period_id = '".$conditions["period_id"]."'";
        }
        if(!empty($conditions["month"])) {
            $where .= " AND month = '".$conditions["month"]."'";
        }
        if(!empty($conditions["year"])) {
            $where .= " AND year = '".$conditions["year"]."'";
        }

        $this->db->query("UPDATE coop_sp_cremation_fee SET status = 2 , updated_at = '".$process_timestamp."' WHERE ".$where);

        $result["status"] = "success";
        return $result;
    }

    public function save_fee_charge($cremation_id, $year, $is_replace) {
        $result = array();
        $process_timestamp = date('Y-m-d H:i:s');

        if(!empty($is_replace)) {
            $where = array('cremation_id ' => $cremation_id , 'year ' => $year, 'status ' => '1');
            $this->db->where($where);
            $this->db->delete('coop_sp_cremation_debt');
        }

        $members = $this->db->select("t1.id as cremation_member_id, t2.status as debt_status, t4.id as fee_id, t4.fee, t4.assoc_fee")
                            ->from("coop_sp_cremation_member as t1")
                            ->join("coop_sp_cremation_debt as t2", "t1.id = t2.cremation_member_id AND t2.year = '".$year."' AND t2.status = 1", "left")
                            ->join("coop_sp_cremation_registration as t3", "t1.id = t3.cremation_member_id", "inner")
                            ->join("coop_sp_cremation_fee as t4", "t4.period_id = t3.period_id AND t4.status = 1", "inner")
                            ->where("t1.status = 1 AND t1.cremation_id = '".$cremation_id."'")
                            ->get()->result_array();
        $data_inserts = array();
        $data_updates = array();
        foreach($members as $member) {
            if(empty($member['debt_status'])) {
                $data_insert = array();
                $data_insert["cremation_id"] = $cremation_id;
                $data_insert["cremation_member_id"] = $member['cremation_member_id'];
                $data_insert["fee_id"] = $member['fee_id'];
                $data_insert["year"] = $year;
                $data_insert["fee"] = $member['fee'];
                $data_insert["assoc_fee"] = $member['assoc_fee'];
                $data_insert["amount"] = $member['fee'] + $member['assoc_fee'];
                $data_insert["balance"] = $member['fee'] + $member['assoc_fee'];
                $data_insert["status"] = 1;
                $data_insert["created_at"] = $process_timestamp;
                $data_insert["updated_at"] = $process_timestamp;
                $data_inserts[] = $data_insert;
            }
        }

        if (!empty($data_inserts)) {
            $this->db->insert_batch('coop_sp_cremation_debt', $data_inserts);
        }

        $result["status"] = "success";
        return $result;
    }

    //condition : status must be array
    public function get_debts($cremation_id, $conditions) {
        $result = array();

        $where = "t1.cremation_id = '".$cremation_id."'";
        if(!empty($conditions["year"])) {
            $where .= " AND t1.year = '".$conditions["year"]."'";
        }
        if(!empty($conditions["cremation_member_id"])) {
            $where .= " AND t7.cremation_member_id like '%".$conditions["cremation_member_id"]."%'";
        }
        if(!empty($conditions["period_id"])) {
            $where .= " AND t2.period_id = '".$conditions["period_id"]."'";
        }
        if(!empty($conditions["status"])) {
            $where .= " AND t1.status IN (".implode(',',$conditions["status"]).")";
        }

        $datas = $this->db->select("t7.cremation_member_id, t7.member_date, t5.firstname_th, t5.lastname_th, t6.prename_full, t3.name as period_name, t1.amount, t1.balance, t1.fee, t1.assoc_fee, t1.year, t1.status, t1.id as debt_id, t1.receipt_id, t8.receipt_no")
                            ->from("coop_sp_cremation_debt as t1")
                            ->join("coop_sp_cremation_registration as t2", "t1.cremation_member_id = t2.cremation_member_id", "inner")
                            ->join("coop_sp_cremation_registration_period as t3", "t2.period_id = t3.id", "inner")
                            ->join("coop_mem_apply as t5", "t2.member_id = t5.member_id", "inner")
                            ->join("coop_prename as t6", "t5.prename_id = t6.prename_id", "left")
                            ->join("coop_sp_cremation_member as t7", "t1.cremation_member_id = t7.id", "inner")
                            ->join("coop_sp_cremation_receipt as t8", "t1.receipt_id = t8.id", "left")
                            ->where($where)
                            ->order_by("t7.member_date, t7.cremation_member_id")
                            ->get()->result_array();
        $result["datas"] = $datas;
        $result["status"] = "success";
        return $result;
    }

    /*
        conditions > debt_id must be array.
    */
    public function get_debts_receipt($cremation_id, $conditions) {
        $result = array();
        $where = "status = 1 AND type = 4";
        if(!empty($conditions['debt_ids'])) {
            $where .= " AND ref IN (".implode(',',$conditions['debt_ids']).")";
        }

        $receipts = $this->db->select("*")->from("coop_sp_cremation_receipt")->where($where)->get()->result_array();
        foreach($receipts as $receipt) {
            $result[$receipt['ref']][] = $receipt;
        }

        return $result;
    }

    public function edit_fee_charge($cremation_id, $param) {
        $result = array();
        $process_timestamp = date('Y-m-d H:i:s');

        $data_insert = array();
        if(!empty($param["year"])) $data_insert["year"] = $param["year"];
        if(!empty($param["fee"])) $data_insert["fee"] = $param["fee"];
        if(!empty($param["assoc_fee"])) $data_insert["assoc_fee"] = $param["assoc_fee"];

        if(!empty($param["debt_id"])) {
            if(empty($param["fee"]) || empty($param["assoc_fee"])) {
                $debt = $this->db->select("fee, assoc_fee")->from("coop_sp_cremation_debt")->where("id = ".$param["debt_id"])->get()->row();
                $fee = !empty($param["fee"]) ? $param["fee"] : $debt->fee;
                $assoc_fee = !empty($param["assoc_fee"]) ? $param["assoc_fee"] : $debt->assoc_fee;
                $data_insert["amount"] = $fee + $assoc_fee;
            } else {
                $data_insert["amount"] = $param["fee"] + $param["assoc_fee"];
            }
            $data_insert["fee_id"] = NULL;
            $data_insert["updated_at"] = $process_timestamp;
            $this->db->where('id',$param["debt_id"]);
            $this->db->update('coop_sp_cremation_debt',$data_insert);
            $result["id"] = $param["debt_id"];
        } else {
            $data_insert["amount"] = $param["fee"] + $param["assoc_fee"];
            $data_insert["created_at"] = $process_timestamp;
            $data_insert["updated_at"] = $process_timestamp;
            $this->db->insert('coop_sp_cremation_debt', $data_insert);
            $result["id"] = $this->db->insert_id();
        }

        $result["status"] = "success";
        return $result;
    }

    public function get_dividend_average_masters($cremation_id) {
        $result = array();
        $cremation = $this->db->select("*")->from("coop_sp_cremation")->where("id = '".$cremation_id."'")->get()->row();
        $datas = $this->db->select("*")
                            ->from("coop_dividend_average_master as t1")
                            ->join("coop_dividend_deduct as t2", "t1.id = t2.master_id", "INNER")
                            ->join("coop_dividend_deduct_type as t3", "t2.deduct_id = t3.deduct_id AND t3.account_list_id = '".$cremation->account_list_id."'")
                            ->where("t1.status = 1")
                            ->get()->result_array();

        if(!empty($datas)) {
            $result["datas"] = $datas;
            $result["status"] = "success";
        } else {
            $result["datas"] = array();
            $result["status"] = "no_data";
        }
        return $result;
    }

    //Need to check divident deduct.
    public function generate_dividend_receipt($cremation_id, $master_id) {
        $result = array();
        $process_timestamp = date('Y-m-d H:i:s');

        $cremation = $this->db->select("account_list_id, name")->from("coop_sp_cremation")->where("id = '".$cremation_id."'")->get()->row();

        $deducts = $this->db->select("t1.year, t2.member_id, t2.transfer_date, t2.amount, t4.cremation_member_id, t4.balance, t4.id as debt_id, t4.fee, t4.assoc_fee, t5.dividend_value, t5.average_return_value,")
                                ->from("coop_dividend_average_master as t1")
                                ->join("coop_dividend_deduct as t2", "t1.id = t2.master_id", "INNER")
                                ->join("coop_dividend_deduct_type as t3", "t2.deduct_id = t3.deduct_id AND t3.account_list_id = '".$cremation->account_list_id."'", "INNER")
                                ->join("coop_dividend_average as t5", "t1.id = t5.master_id", "INNER")
                                ->join("coop_sp_cremation_debt as t4", "t1.year = t4.year AND t2.member_id = t4.member_id AND t4.cremation_id = '".$cremation_id."' AND t4.status = 2", "INNER")
                                ->where("t1.id = '".$master_id."'")
                                ->get()->result_array();
        $data_updates = array();
        foreach($deducts as $deduct) {
            if(empty($deduct["receipt_id"])) {
                $receipt = $this->db->select("receipt_no")->from("coop_sp_cremation_receipt")->where("cremation_id = '".$cremation_id."' AND type = 4")->order_by("receipt_no DESC")->get()->row();
                if(!empty($receipt)) {
                    $receipt_no = sprintf('%08d',(((int) $receipt->receipt_no) + 1));
                } else {
                    $receipt_no = '00000001';
                }
                $data_insert = array();
                $data_insert["cremation_id"] = $cremation_id;
                $data_insert["receipt_no"] = $receipt_no;
                $data_insert["total"] = $deduct["amount"];
                $data_insert["cremation_member_id"] = $deduct["cremation_member_id"];
                $data_insert["status"] = 1;
                $data_insert["type"] = 4;
                $data_insert['ref'] = $debt_id;
                $data_insert["receipt_datetime"] = $process_timestamp;
                $data_insert["user_id"] = $_SESSION['USER_ID'];
                $data_insert["created_at"] = $process_timestamp;
                $data_insert["updated_at"] = $process_timestamp;
                $this->db->insert('coop_sp_cremation_receipt', $data_insert);
                $receipt_id = $this->db->insert_id();

                $data_insert = array();
                $data_insert["receipt_id"] = $receipt_id;
                $data_insert["description"] = "เงินสงเคราะห์รายปี";
                $data_insert["amount"] = $deduct["fee"];
                $data_insert["data_code"] = "debt";
                $data_insert["created_at"] = $process_timestamp;
                $data_insert["updated_at"] = $process_timestamp;
                $this->db->insert('coop_sp_cremation_receipt_detail', $data_insert);

                $data_insert = array();
                $data_insert["receipt_id"] = $receipt_id;
                $data_insert["description"] = "ค่าบำรุงรายปี";
                $data_insert["amount"] = $deduct["assoc_fee"];
                $data_insert["data_code"] = "debt";
                $data_insert["created_at"] = $process_timestamp;
                $data_insert["updated_at"] = $process_timestamp;
                $this->db->insert('coop_sp_cremation_receipt_detail', $data_insert);

                $data_update = array();
                $balance = $deduct["balance"] - $deduct["amount"];
                if($balance < 0) $balance = 0;
                $data_update["balance"] = $balance;
                $data_update["receipt_id"] = $receipt_id;
                if($balance == 0) $data_update["status"] = 4;
                $data_update["dividend_master_id"] = $master_id;
                $data_update["updated_at"] = $process_timestamp;
                $data_update["id"] = $deduct["debt_id"];
                $data_updates[] = $data_update;
            }
        }

        if(!empty($data_updates)) $this->db->update_batch('coop_sp_cremation_debt', $data_updates, 'id');
        
        $result["status"] = "success";
        return $result;
    }

    public function pay_debt($cremation_id, $debt_id, $amount) {
        $process_timestamp = date('Y-m-d H:i:s');

        $debt = $this->db->select("*")->from("coop_sp_cremation_debt")->where("id = '".$debt_id."'")->get()->row_array();
        $balance = $debt['balance'] - $amount;
        $fee_debt = $debt['balance'] > $debt['assoc_fee'] ? $debt['balance'] - $debt['assoc_fee'] : 0;
        $assoc_fee_debt = $debt['assoc_fee'] > $debt['balance'] ? $debt['balance'] : $debt['assoc_fee'];
        $fee_payment = $amount > $fee_debt ? $fee_debt : $amount;
        $assoc_fee_payment = $amount - ($fee_debt + $assoc_fee_debt) == 0 ? $assoc_fee_debt : ($amount > $fee_debt ? $amount - $fee_debt : 0);

        //Generate receipt.
        $receipt = $this->db->select("receipt_no")->from("coop_sp_cremation_receipt")->where("cremation_id = '".$cremation_id."' AND type = 4")->order_by("receipt_no DESC")->get()->row();
        if(!empty($receipt)) {
            $receipt_no = sprintf('%08d',(((int) $receipt->receipt_no) + 1));
        } else {
            $receipt_no = '00000001';
        }

        $data_insert = array();
        $data_insert['cremation_id'] = $cremation_id;
        $data_insert['receipt_no'] = $receipt_no;
        $data_insert['total'] = $amount;
        $data_insert['cremation_member_id'] = $debt['cremation_member_id'];
        $data_insert['status'] = 1;
        $data_insert['type'] = 4;
        $data_insert['ref'] = $debt_id;
        $data_insert['receipt_datetime'] = $process_timestamp;
        $data_insert['user_id'] = $_SESSION['USER_ID'];
        $data_insert['created_at'] = $process_timestamp;
        $data_insert['updated_at'] = $process_timestamp;
        $this->db->insert('coop_sp_cremation_receipt', $data_insert);
        $receipt_id = $this->db->insert_id();

        if(!empty($fee_payment)) {
            $data_insert = array();
            $data_insert["receipt_id"] = $receipt_id;
            $data_insert["description"] = "เงินสงเคราะห์รายปี";
            $data_insert["amount"] = $fee_payment;
            $data_insert["data_code"] = "debt";
            $data_insert["created_at"] = $process_timestamp;
            $data_insert["updated_at"] = $process_timestamp;
            $this->db->insert('coop_sp_cremation_receipt_detail', $data_insert);
        }

        if(!empty($assoc_fee_payment)) {
            $data_insert = array();
            $data_insert["receipt_id"] = $receipt_id;
            $data_insert["description"] = "ค่าบำรุงรายปี";
            $data_insert["amount"] = $assoc_fee_payment;
            $data_insert["data_code"] = "debt";
            $data_insert["created_at"] = $process_timestamp;
            $data_insert["updated_at"] = $process_timestamp;
            $this->db->insert('coop_sp_cremation_receipt_detail', $data_insert);
        }

        $data_insert = array();
        $data_insert["balance"] = $balance;
        if($balance <= 0) {
            $data_insert['status'] = 4;
        }
        $data_insert["updated_at"] = $process_timestamp;
        $this->db->where('id', $debt_id);
        $this->db->update('coop_sp_cremation_debt',$data_insert);

        return $receipt_id;
    }
}
