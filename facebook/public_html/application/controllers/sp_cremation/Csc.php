<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Csc extends CI_Controller {
	function __construct() {
        parent::__construct();
        $this->cremation_group_id = 1;
        $this->path = "csc";
        $this->load->model('Sp_cremation/Cremation', 'cremation');
	}

    public function index() {
        $arr_data = array();

        $result = $this->cremation->get_info_data($this->cremation_group_id);

        $arr_data["cremation"] = $this->cremation->get_cremation_info($this->cremation_group_id);

        $arr_data['provinces'] = $result['provinces'];
        $arr_data['amphurs'] = $result['amphurs'];
        $arr_data['districts'] = $result['districts'];
        $arr_data['member_relations'] = $result['member_relations'];
        $arr_data['prenames'] = $result['prenames'];
        $arr_data["departments"] = $result['departments'];
        $arr_data["factions"] = $result['factions'];
        $arr_data["levels"] =$result['levels'];
        $arr_data["register_periods"] = $result['register_periods'];

        $arr_data['path'] = $this->path;
        $arr_data["cremation_group_id"] = $this->cremation_group_id;

        if(!empty($_POST["register_id"])) {
            $arr_data["data"]["id"] = $_POST["register_id"];
        }

        $this->libraries->template('sp_cremation/index',$arr_data);
    }

    public function ajax_save_register_request() {
        $result = $this->cremation->save_register_request($this->cremation_group_id, $_POST);
        echo json_encode($result);
    }

    public function search_cremation_by_type_jquery() {
        $search_text = @$_POST["search_text"];
		$search_list = @$_POST["search_list"];
		$where = "1=1";
		if(@$_POST['search_list'] == 'member_id'){
			$where = " t2.member_id LIKE '%".$search_text."%'";
		}else if(@$_POST['search_list'] == 'firstname_th'){
			$where = " t3.firstname_th LIKE '%".$search_text."%'";
		}else if(@$_POST['search_list'] == 'lastname_th'){
			$where = " t3.lastname_th LIKE '%".$search_text."%'";
		}else if(@$_POST['search_list'] == 'id_card'){
			$where = " t3.id_card LIKE '%".$search_text."%'";
		}else if(@$_POST['search_list'] == 'cremation_no'){
			$where = " t1.request_id LIKE '%".$search_text."%'";
		}else if(@$_POST['search_list'] == 'member_cremation_id'){
			$where = " t2.cremation_member_id LIKE '%".$search_text."%'";
        }

		$datas = $this->db->select("t1.request_id as cremation_no, t1.id as cremation_request_id, t2.cremation_member_id as member_cremation_id, t2.id, t2.member_id, t3.firstname_th as assoc_firstname, t3.lastname_th as assoc_lastname, t4.prename_full")
							->from("coop_sp_cremation_registration as t1")
                            ->join("coop_sp_cremation_member as t2", "t1.cremation_member_id = t2.id", "inner")
                            ->join("coop_mem_apply as t3", "t2.member_id = t3.member_id", "inner")
							->join("coop_prename as t4", "t3.prename_id = t4.prename_id","left")
							->where($where)
							->get()->result_array();
		$arr_data['datas'] = $datas;

		$this->load->view('cremation/search_cremation_jquery',$arr_data);
    }

    public function period_setting() {
        $arr_data = array();

        if(!empty($_POST)) {
            if(!empty($_POST["start_date"])) {
                $this->cremation->edit_registration_period($this->cremation_group_id, $_POST);
            } else if (!empty($_POST["delete_id"])) {
                $this->cremation->delete_registration_period($this->cremation_group_id, $_POST["delete_id"]);
            }
        }

        $page = !empty($_GET["page"]) ? $_GET["page"] : 1;

        $result = $this->cremation->get_registration_period_page($this->cremation_group_id, $page);

        $arr_data["cremation_group_id"] = $this->cremation_group_id;
        $arr_data["page_start"] = $result['page_start'];
        $arr_data['paging'] = $result["paging"];
        $arr_data['datas'] = $result['datas'];
        $arr_data['path'] = $this->path;

        $this->libraries->template('sp_cremation/period_setting',$arr_data);
    }

    public function get_cremation_member_info() {
        $search_value = "";
        $search_type = "";
        if(!empty($_POST["member_id"])) {
            $search_value = $_POST["member_id"];
            $search_type = 1;
        } else if (!empty($_POST["cremation_member_id"])) {
            $search_value = $_POST["cremation_member_id"];
            $search_type = 2;
        } else if (!empty($_POST["registration_id"])) {
            $search_value = $_POST["registration_id"];
            $search_type = 3;
        } else if (!empty($_POST["cremation_member_raw_id"])) {
            $search_value = $_POST["cremation_member_raw_id"];
            $search_type = 4;
        } else if (!empty($_POST["cremation_register_raw_id"])) {
            $search_value = $_POST["cremation_register_raw_id"];
            $search_type = 5;
        }

        $result = $this->cremation->get_cremation_member_info($this->cremation_group_id, $search_value, $search_type);
        if(!empty($result)) echo json_encode($result);
        exit;
    }

    public function check_cremation_member_id() {
        $result = $this->cremation->check_cremation_member_id($this->cremation_group_id, $_POST);
        if(!empty($result)) echo json_encode($result);
        exit;
    }

    public function registration_payment() {
        $arr_data = array();

        $page = !empty($_GET["page"]) ? $_GET["page"] : 1;
        $datas = $this->cremation->get_register_request_by_status($this->cremation_group_id, 1, null, $page);
        $arr_data["index_start"] = $datas["page_start"];
        $arr_data["datas"] = $datas["datas"];
        $arr_data['path'] = $this->path;

        $this->libraries->template('sp_cremation/registration_payment',$arr_data);
    }

    public function pay_register_fee() {
        $result = $this->cremation->pay_register_fee($this->cremation_group_id, $_POST);
        echo $result;
    }

    public function registration_confirm() {
        $arr_data = array();

        $page = !empty($_GET["page"]) ? $_GET["page"] : 1;
        $datas = $this->cremation->get_register_request_by_status($this->cremation_group_id, null, array(2), $page);
        $arr_data["index_start"] = $datas["page_start"];
        $arr_data["datas"] = $datas["datas"];
        $arr_data['path'] = $this->path;

        $this->libraries->template('sp_cremation/registration_confirm',$arr_data);
    }

    public function receipt() {
        $arr_data = array();
        $receipt_id = !empty($_GET["receipt_id"]) ? $_GET["receipt_id"] : NULL;
        $receipt_no = !empty($_GET["receipt_no"]) ? $_GET["receipt_no"] : NULL;
        $arr_data["data"] = $this->cremation->get_receipt_data($this->cremation_group_id, $receipt_id, $receipt_no);
        $this->load->view('sp_cremation/receipt_pdf',$arr_data);
    }

    public function disapprove_registers() {
        $result = $this->cremation->disapprove_registers($this->cremation_group_id, $_POST);
        echo $result;
    }

    public function approve_registers() {
        $result = $this->cremation->approve_registers($this->cremation_group_id, $_POST);
        echo $result;
    }

    public function resignation() {
        $arr_data = array();

        $page = !empty($_GET["page"]) ? $_GET["page"] : 1;
        $datas = $this->cremation->get_resign_member($this->cremation_group_id, null, $page);
        $arr_data["index_start"] = $datas["page_start"];
        $arr_data["datas"] = $datas["datas"];
        $arr_data['path'] = $this->path;

        $this->libraries->template('sp_cremation/resignation',$arr_data);
    }

    public function save_request_resign() {
        $result = $this->cremation->save_request_resign($this->cremation_group_id, $_POST);
        echo json_encode($result);
    }

    public function resign_approve() {
        $result = $this->cremation->resign_approve($this->cremation_group_id, $_POST["resign_id"], $_POST["payment"]);
        echo json_encode($result);
    }

    public function resign_disapprove() {
        $result = $this->cremation->resign_disapprove($this->cremation_group_id, $_POST["resign_id"]);
        echo json_encode($result);
    }

    public function request_money() {
        $arr_data = array();

        $page = !empty($_GET["page"]) ? $_GET["page"] : 1;
        $datas = $this->cremation->get_request_money_member($this->cremation_group_id, null, $page);
        $settings = $this->cremation->get_request_money_payment_setting($this->cremation_group_id);
        $arr_data["index_start"] = $datas["page_start"];
        $arr_data["datas"] = $datas["datas"];
        $arr_data["commissions"] = $settings["settings"];
        $arr_data['path'] = $this->path;
        $this->libraries->template('sp_cremation/request_money',$arr_data);
    }

    public function save_request_money() {
        $result = $this->cremation->save_request_money($this->cremation_group_id, $_POST["req_id"], $_POST["file_ids"], $_POST["member_cremation_id"], $_POST["reason"], $_FILES);
        echo "<script> document.location.href='".base_url(PROJECTPATH.'/sp_cremation/'.$this->path.'/request_money')."'; </script>";
    }

    public function check_save_request_money() {
        $result = $this->cremation->check_save_request_money($this->cremation_group_id, $_POST["member_cremation_id"]);
        echo json_encode($result);
    }

    public function get_money_request_file() {
        $result = $this->cremation->get_files($this->cremation_group_id, "rev", $_POST["request_id"]);
        echo json_encode($result);
    }

    public function disapprove_request_money() {
        $result = $this->cremation->disapprove_request_money($this->cremation_group_id, $_POST["request_id"]);
    }

    public function download_file() {
        $this->load->helper('download');
        $file = $this->db->select("*")->from("coop_sp_cremation_file")->where("id = '".$_GET["id"]."'")->get()->row();
        force_download($file->name,file_get_contents(FCPATH.$file->path));
    }

    public function request_money_approve() {
        $result = $this->cremation->request_money_approve($this->cremation_group_id, $_POST["req_id"], $_POST["total_payment"], $_POST["commissions"], $_POST["receive_payment"]);
        echo json_encode($result);
    }

    public function request_money_payment_setting() {
        $arr_data = array();
        $result = $this->cremation->get_request_money_payment_setting($this->cremation_group_id);
        $arr_data["settings"] = $result["settings"];
        $arr_data['path'] = $this->path;
        $this->libraries->template('sp_cremation/request_money_payment_setting',$arr_data);
    }

    public function save_request_money_payment_setting() {
        $result = $this->cremation->save_setting($this->cremation_group_id, $_POST["data"]);
        echo json_encode($result);
    }

    public function fee() {
        $arr_data = array();
        if(empty($_POST["year"])) $_POST["year"] = date('Y') + 543;
        $conditions = array();
        $conditions["year"] = $_POST["year"];
        $conditions["status"] = array("1");
        $result = $this->cremation->get_fees($this->cremation_group_id, $conditions);
        $arr_data['datas'] = $result["datas"];

        $conditions = array();
        $status = array();
        $status[] = 1;
        $conditions["status"] = $status;
        $periods = $this->cremation->get_registration_period($this->cremation_group_id, $conditions);

        $arr_data['path'] = $this->path;
        $arr_data['periods'] = $periods["datas"];
        $this->libraries->template('sp_cremation/fees',$arr_data);
    }

    public function check_save_period_fee() {
        $result = $this->cremation->check_save_period_fee($this->cremation_group_id, $_POST);
        echo json_encode($result);
    }

    public function save_period_fee() {
        $result = $this->cremation->save_period_fee($this->cremation_group_id, $_POST["period_id"], $_POST["year"], $_POST["month"], $_POST["fee"], $_POST["assoc_fee"], $_POST["replace"]);
        echo json_encode($result);
    }

    public function delete_period_fee() {
        $conditions = array();
        $conditions["id"] = $_POST["id"];
        $result = $this->cremation->delete_period_fee($this->cremation_group_id, $conditions);
        echo json_encode($result);
    }

    public function fee_charge() {
        $arr_data = array();
        $year = !empty($_POST["year"]) ? $_POST["year"] : date('Y') + 543;
        $conditions = array();
        $conditions["year"] = $year;
        $conditions["status"] = array(1,2,4);
        if(!empty($_POST["cremation_member_id"])) $conditions["cremation_member_id"] = $_POST["cremation_member_id"];
        if(!empty($_POST["period_id"])) $conditions["period_id"] = $_POST["period_id"];
        $datas = $this->cremation->get_debts($this->cremation_group_id, $conditions);

        $conditions = array();
        $status = array();
        $status[] = 1;
        $conditions["status"] = $status;
        $periods = $this->cremation->get_registration_period($this->cremation_group_id, $conditions);
        $arr_data['periods'] = $periods["datas"];

        $arr_data['path'] = $this->path;
        $arr_data['datas'] = $datas["datas"];
        $this->libraries->template('sp_cremation/fee_charge',$arr_data);
    }

    public function save_fee_charge() {
        $result = $this->cremation->save_fee_charge($this->cremation_group_id, $_POST["year"], $_POST["replace"]);
        echo json_encode($result);
    }

    public function edit_fee_charge() {
        $result = $this->cremation->edit_fee_charge($this->cremation_group_id, $_POST);
        echo json_encode($result);
    }

    public function generate_dividend_receipt() {
        $arr_data = array();

        $result = $this->cremation->get_dividend_average_masters($this->cremation_group_id);
        $arr_data['datas'] = $result["datas"];
        $arr_data['path'] = $this->path;
        $this->libraries->template('sp_cremation/generate_dividend_receipt',$arr_data);
    }

    public function save_generate_dividend_receipt() {
        $result = $this->cremation->generate_dividend_receipt($this->cremation_group_id, $_POST["master_id"]);
        echo json_encode($result);
    }

    public function pay_debt() {
        $arr_data = array();
        $arr_data['path'] = $this->path;

        $year = !empty($_POST["year"]) ? $_POST["year"] : date('Y') + 543;

        $conditions = array();
        $conditions["status"] = array(1,2,4);
        $conditions["year"] = $year;
        if(!empty($_POST["cremation_member_id"])) $conditions["cremation_member_id"] = $_POST["cremation_member_id"];
        if(!empty($_POST["period_id"])) $conditions["period_id"] = $_POST["period_id"];
        $result = $this->cremation->get_debts($this->cremation_group_id, $conditions);
        $datas = $result['datas'];
        $arr_data['datas'] = $datas;

        $receipts = array();
        if(!empty($datas)) {
            $debt_ids = array_column($datas, 'debt_id');
            $conditions = array();
            $conditions['debt_ids'] = $debt_ids;
            $receipts = $this->cremation->get_debts_receipt($this->cremation_group_id, $conditions);
        }
        $arr_data['receipts'] = $receipts;

        $conditions = array();
        $status = array();
        $status[] = 1;
        $conditions["status"] = $status;
        $periods = $this->cremation->get_registration_period($this->cremation_group_id, $conditions);
        $arr_data['periods'] = $periods["datas"];

        $this->libraries->template('sp_cremation/pay_debt',$arr_data);
    }

    public function debt_payment() {
        $result = array();
        $receipt_id = $this->cremation->pay_debt($this->cremation_group_id, $_POST["debt_id"], $_POST['amount']);

        if(!empty($receipt_id)) {
            $result['status'] = "success";
            $result['receipt_id'] = $receipt_id;
        } else {
            $result['status'] = "fail";
        }
        echo json_encode($result);
    }
}
