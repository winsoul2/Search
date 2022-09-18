<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Elec_doc_approve extends CI_Controller {

	function __construct() {
		parent::__construct();
	}

    public function index() {
        $arr_data = array();
        $process_timestamp = date('Y-m-d H:i:s');

        if ($_POST["action"] == "change_status") {
            $data_update = array();
            $data_update['status'] = $_POST["status"];
            $data_update['updated_at'] = $process_timestamp;
            $this->db->where('id',$_POST['id']);
            $this->db->update('coop_ele_document',$data_update);
        } else if ($_POST["action"] == "delete_list") {
            foreach($_POST["ids"] as $id) {
                $this->db->where('type',"approver");
                $this->db->where('document_id',$id);
                $this->db->where('user_id',$_SESSION['USER_ID']);
                $this->db->delete('coop_ele_document_user');
            }
        }

        $x=0;
        $join_arr[$x]['table'] = 'coop_user as t3';
        $join_arr[$x]['condition'] = 't1.user_id = t3.user_id';
        $join_arr[$x]['type'] = 'left';
        $x++;
        $join_arr[$x]['table'] = '(SELECT * FROM coop_ele_document_user WHERE user_id = "'.$_SESSION['USER_ID'].'" AND type = "approver") as t5';
        $join_arr[$x]['condition'] = 't1.id = t5.document_id';
        $join_arr[$x]['type'] = 'inner';

        $this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select('t1.user_id, t1.status, t1.created_at, t1.updated_at, t1.name, t1.id, t1.last_access, t3.user_name, t5.user_id as approver, t5.id as doc_user_id, t5.read_at');
		$this->paginater_all->main_table('coop_ele_document as t1');
        $this->paginater_all->page_now($_GET["page"]);
        $this->paginater_all->where("t1.status in (4,5,6)");
        $this->paginater_all->order_by("t1.created_at desc");
        $this->paginater_all->join_arr($join_arr);
		$this->paginater_all->per_page(20);
		$this->paginater_all->page_link_limit(20);
		$row = $this->paginater_all->paginater_process();
		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'], $_GET);

        foreach($row['data'] as $key => $data) {
            $comments = $this->db->select("id, updated_at")->from("coop_ele_document_comment")->where("document_id = '".$data["id"]."'")->order_by("updated_at desc")->get()->result_array();
            $row['data'][$key]["comment_count"] = count($comments);
            if ($data["user_id"] == $_SESSION['USER_ID']) {
                if(!empty($data["last_access"]) && !empty($comments) && $data["last_access"] < $comments[0]["updated_at"]) {
                    $row['data'][$key]["has_unread"] = 1;
                } else if (empty($data["last_access"]) && !empty($comments)) {
                    $row['data'][$key]["has_unread"] = 1;
                } else {
                    $row['data'][$key]["has_unread"] = 0;
                }
            } else {
                $row['data'][$key]["has_unread"] = 0;
            }

            if(empty($data["read_at"])) {
                $data_update = array();
                $data_update['read_at'] = $process_timestamp;
                $this->db->where('id',$data["doc_user_id"]);
                $this->db->update('coop_ele_document_user',$data_update);
            }
        }

		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['datas'] = $row['data'];
        $arr_data['runno'] = $row['page_start'];

        $this->libraries->template("electronic_document/approve", $arr_data);
    }

    public function review_approve_document() {
        $arr_data = array();
        $process_timestamp = date('Y-m-d H:i:s');

        $document_id = $_GET["id"];
        $document = $this->db->select("*")->from("coop_ele_document")->where("id = '".$document_id."'")->get()->row();
        $arr_data["document"] = $document;
        if($document->user_id == $_SESSION['USER_ID']) {
            $data_update = array();
            $data_update['last_access'] = $process_timestamp;
            $this->db->where('id',$document->id);
            $this->db->update('coop_ele_document',$data_update);
        }

        $files = $this->db->select("*")->from("coop_ele_document_file")->where("document_id = '".$_GET["id"]."'")->get()->result_array();
        $arr_data["files"] = $files;

        $review_group = $this->db->select("*")->from("coop_ele_document_user")
                                                ->join("coop_user_group", "coop_ele_document_user.group_id = coop_user_group.id", "inner")
                                                ->where("coop_ele_document_user.document_id = '".$_GET["id"]."' AND coop_ele_document_user.group_id is not null")->get()->row();
        $arr_data["review_group"] = $review_group;

        //Getr relate users
        $review_users = $this->db->select("t2.user_id, t2.user_name")
                                    ->from("coop_ele_document_user as t1")
                                    ->join("coop_user as t2", "t1.user_id = t2.user_id", "inner")
                                    ->where("t1.document_id = '".$_GET["id"]."' AND t1.user_id is not null AND t1.type = 'reviewer'")->get()->result_array();
        $arr_data["review_users"] = $review_users;
        $approve_draft_users = $this->db->select("t2.user_id, t2.user_name")
                                    ->from("coop_ele_document_user as t1")
                                    ->join("coop_user as t2", "t1.user_id = t2.user_id", "inner")
                                    ->where("t1.document_id = '".$_GET["id"]."' AND t1.user_id is not null AND t1.type = 'approve_draft'")->get()->result_array();
        $arr_data["approve_draft_users"] = $approve_draft_users;
        $approver_users = $this->db->select("t2.user_id, t2.user_name")
                                    ->from("coop_ele_document_user as t1")
                                    ->join("coop_user as t2", "t1.user_id = t2.user_id", "inner")
                                    ->where("t1.document_id = '".$_GET["id"]."' AND t1.user_id is not null AND t1.type = 'approver'")->get()->result_array();
        $arr_data["approver_users"] = $approver_users;
        $receiver_users = $this->db->select("t2.user_id, t2.user_name")
                                    ->from("coop_ele_document_user as t1")
                                    ->join("coop_user as t2", "t1.user_id = t2.user_id", "inner")
                                    ->where("t1.document_id = '".$_GET["id"]."' AND t1.user_id is not null AND t1.type = 'receiver'")->get()->result_array();
        $arr_data["receiver_users"] = $receiver_users;

        $this->libraries->template("electronic_document/review_approve_document", $arr_data);
    }

    public function download_file() {
        $this->load->helper('download');
        $file = $this->db->select("*")->from("coop_ele_document_file")->where("id = '".$_GET["id"]."'")->get()->row();
        force_download($file->name,file_get_contents(FCPATH.$file->path));
    }
}
