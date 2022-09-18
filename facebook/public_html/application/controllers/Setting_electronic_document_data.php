<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting_electronic_document_data extends CI_Controller {
	function __construct()
	{
		parent::__construct();
	}
	
	public function index(){
		$arr_data = array();
		$process_timestamp = date('Y-m-d H:i:s');

        if($_POST["action"] == 'add') {
			$data_insert['group_name'] = $_POST['name'];
			$data_insert['updated_at'] = $process_timestamp;
			$data_insert['created_at'] = $process_timestamp;
			$this->db->insert('coop_user_group', $data_insert);
		} else if ($_POST["action"] == 'edit') {
			$data_update = array();
			$data_update['group_name'] = $_POST['name'];
			$data_update['updated_at'] = $process_timestamp;
			$this->db->where('id',$_POST['id']);
			$this->db->update('coop_user_group',$data_update);
		} else if ($_POST["action"] == 'delete') {
			$this->db->where('id',$_POST['id']);
			$this->db->delete('coop_user_group');
			$this->db->where('group_id',$_POST['id']);
			$this->db->delete('coop_user_group_member');
		}		

		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select('*');
		$this->paginater_all->main_table('coop_user_group');
		$this->paginater_all->page_now($_GET["page"]);
		$this->paginater_all->per_page(20);
		$this->paginater_all->page_link_limit(20);
		$row = $this->paginater_all->paginater_process();
		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'], $_GET);

		foreach($row['data'] as $key=> $data) {
			$member = $this->db->select("id")->from("coop_user_group_member")->where("group_id = '".$data["id"]."'")->get()->result_array();
			$row["data"][$key]["count_member"] = count($member);
		}

		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['datas'] = $row['data'];
		$arr_data['runno'] = $row['page_start'];

		$this->libraries->template('setting_electronic_document_data/user_group',$arr_data);
	}

	public function manage_user_group() {
		$arr_data = array();

		if(!empty($_POST)) {
			$process_timestamp = date('Y-m-d H:i:s');
			$_GET["id"] = $_POST["id"];

			//Clear old data
			$this->db->where('group_id',$_POST['id']);
			$this->db->delete('coop_user_group_member');

			//Generate new data
			$data_inserts = array();
			foreach($_POST["user_ids"] as $user_id) {
				$data_insert = array();
				$data_insert["group_id"] = $_POST["id"];
				$data_insert["user_id"] = $user_id;
				$data_insert['created_at'] = $process_timestamp;
				$data_inserts[] = $data_insert;
			}
			//Insert Guarantee Data
			if (!empty($data_inserts)) {
				$this->db->insert_batch('coop_user_group_member', $data_inserts);
			}
		}

		$users = $this->db->select("t1.user_name, t1.user_id, t2.id")
							->from("coop_user as t1")
							->join("(SELECT * FROM coop_user_group_member WHERE group_id = '".$_GET["id"]."') as t2", "t1.user_id = t2.user_id", "left")
							->order_by("t1.user_id")
							->get()->result_array();
		$arr_data["datas"] = $users;

		$user_group = $this->db->select("group_name")->from("coop_user_group")->where("id = '".$_GET["id"]."'")->get()->row();
		$arr_data["group_name"] = $user_group->group_name;

		$this->libraries->template('setting_electronic_document_data/manage_user_group',$arr_data);
	}
}
