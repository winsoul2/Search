<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Electronic_document extends CI_Controller {
	function __construct()
	{
		parent::__construct();
	}

	public function archives(){
        $arr_data = array();

        if($_POST["action"] == "delete") {
            $this->load->helper("file");

            $files = $this->db->select("*")->from("coop_ele_document_file")->where("document_id = '".$_POST["id"]."'")->get()->result_array();
            foreach($files as $file) {
                unlink(FCPATH.$file['path']);
            }

            //Delete file data
            $this->db->where('document_id',$_POST['id']);
            $this->db->delete('coop_ele_document_file');

            //Delete document data
            $this->db->where('id',$_POST['id']);
            $this->db->delete('coop_ele_document');
        }

		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select('id, name, category_id, updated_at');
		$this->paginater_all->main_table('coop_ele_document');
		$this->paginater_all->where("status = 1");
		$this->paginater_all->page_now($_GET["page"]);
		$this->paginater_all->per_page(20);
		$this->paginater_all->order_by("created_at desc");
		$this->paginater_all->page_link_limit(20);
		$row = $this->paginater_all->paginater_process();
        $paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'], $_GET);

        foreach($row['data'] as $key => $data) {
            $files = $this->db->select("id, name, path")->from("coop_ele_document_file")->where("document_id = '".$data["id"]."'")->get()->result_array();
            $row['data'][$key]["files"] = $files;

            $category = $this->db->select("name")->from("coop_ele_document_category")->where("id = '".$data["category_id"]."'")->get()->row();
            $row['data'][$key]["category_name"] = $category->name;
        }

		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['datas'] = $row['data'];
        $arr_data['runno'] = $row['page_start'];

		$this->libraries->template('electronic_document/archives',$arr_data);
    }

    public function category() {
        $arr_data = array();
        $process_timestamp = date('Y-m-d H:i:s');

        if($_POST["action"] == 'add') {
			$data_insert['name'] = $_POST['name'];
			$data_insert['updated_at'] = $process_timestamp;
			$data_insert['created_at'] = $process_timestamp;
			$this->db->insert('coop_ele_document_category', $data_insert);
		} else if ($_POST["action"] == 'edit') {
			$data_update = array();
			$data_update['name'] = $_POST['name'];
			$data_update['updated_at'] = $process_timestamp;
			$this->db->where('id',$_POST['id']);
			$this->db->update('coop_ele_document_category',$data_update);
		} else if ($_POST["action"] == 'delete') {
			//Delete member
			$this->db->where('id',$_POST['id']);
			$this->db->delete('coop_ele_document_category');
		}

		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select('*');
		$this->paginater_all->main_table('coop_ele_document_category');
		$this->paginater_all->page_now($_GET["page"]);
		$this->paginater_all->per_page(20);
		$this->paginater_all->page_link_limit(20);
		$row = $this->paginater_all->paginater_process();
		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'], $_GET);

		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['datas'] = $row['data'];
        $arr_data['runno'] = $row['page_start'];

		$this->libraries->template('electronic_document/category',$arr_data);
    }

    public function check_delete_category() {
        $document = $this->db->select("id")->from("coop_ele_document")->where("category_id = '".$_GET["id"]."'")->get()->row();
        if(empty($document)) echo "success";
    }

    public function add_archives_document() {
        $arr_data = array();

        if(!empty($_POST)) {
            $process_timestamp = date('Y-m-d H:i:s');
            
            $config = array();
            $config['upload_path'] = FCPATH.'assets/uploads/electronic_document';
            $config['allowed_types'] = '*';
            $config['max_size'] = '0';
            $config['overwrite'] = FALSE;

            $this->load->library('upload', $config);

            if(empty($_POST["document_id"])) {
                // Create document data
                $data_insert = array();
                $data_insert['name'] = $_POST['document_name'];
                $data_insert['category_id'] = $_POST['category_id'];
                $data_insert['status'] = 1;
                $data_insert['user_id'] = $_SESSION['USER_ID'];
                $data_insert['updated_at'] = $process_timestamp;
                $data_insert['created_at'] = $process_timestamp;
                $this->db->insert('coop_ele_document', $data_insert);
                $document_id = $this->db->insert_id();

                $images = array();
                $title = date('Ymdhis').$document_id;
                $files = $_FILES;

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
                        $data_insert["document_id"] = $document_id;
                        $data_insert['name'] = $files['file']['name'][$key];
                        $data_insert['path'] = "/assets/uploads/electronic_document/".$upload_data["orig_name"];
                        $data_insert['created_at'] = $process_timestamp;
                        $this->db->insert('coop_ele_document_file', $data_insert);
                    }
                }
            } else {
                $document_id = $_POST["document_id"];

                // Update document data
                $data_update = array();
                $data_update['name'] = $_POST['document_name'];
                $data_update['category_id'] = $_POST['category_id'];
                $data_update['updated_at'] = $process_timestamp;
                $this->db->where('id',$document_id);
                $this->db->update('coop_ele_document',$data_update);

                //Keep old file data
                $old_files = $this->db->select("*")->from("coop_ele_document_file")->where("document_id = '{$document_id}'")->get()->result_array();

                //Generate new file data
                $images = array();
                $title = date('Ymdhis').$document_id;
                $files = $_FILES;
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
                        $data_insert["document_id"] = $document_id;
                        $data_insert['name'] = $files['file']['name'][$key];
                        $data_insert['path'] = "/assets/uploads/electronic_document/".$upload_data["orig_name"];
                        $data_insert['created_at'] = $process_timestamp;
                        $this->db->insert('coop_ele_document_file', $data_insert);
                    }
                }

                //Delete old file data
                foreach($old_files as $file) {
                    if(!in_array($file["id"], $_POST["file_ids"])) {
                        unlink(FCPATH.$file['path']);
                        $this->db->where('id',$file['id']);
                        $this->db->delete('coop_ele_document_file');
                    }
                }
            }
            echo "<script> document.location.href='".PROJECTPATH."/electronic_document/archives"."' </script>";
        }

        //Check $_GET["id"] for edit
        if(!empty($_GET["id"])) {
            $document = $this->db->select("*")->from("coop_ele_document")->where("id = '".$_GET["id"]."'")->get()->row();
            $arr_data["document"] = $document;

            $files = $this->db->select("*")->from("coop_ele_document_file")->where("document_id = '".$_GET["id"]."'")->get()->result_array();
            $arr_data["files"] = $files;
        }

        $categories = $this->db->select("*")->from("coop_ele_document_category")->get()->result_array();
        $arr_data["categories"] = $categories;
        $this->libraries->template('electronic_document/add_archives_document',$arr_data);
    }

    public function download_file() {
        $this->load->helper('download');
        $file = $this->db->select("*")->from("coop_ele_document_file")->where("id = '".$_GET["id"]."'")->get()->row();
        force_download($file->name,file_get_contents(FCPATH.$file->path));
    }

    public function draft_document() {
        $arr_data = array();
        $process_timestamp = date('Y-m-d H:i:s');

        if ($_POST["action"] == 'delete') {
            $document = $this->db->select("*")->from("coop_ele_document")->where("id = '".$_POST["id"]."'")->get()->row();
            if($document->user_id == $_SESSION['USER_ID']) {
                //Delete file
                $this->load->helper("file");
                $files = $this->db->select("*")->from("coop_ele_document_file")->where("document_id = '".$_POST["id"]."'")->get()->result_array();
                foreach($files as $file) {
                    unlink(FCPATH.$file['path']);
                }

                //Delete file data
                $this->db->where('document_id',$_POST['id']);
                $this->db->delete('coop_ele_document_file');

                //Delete Reviewer data
                $this->db->where('document_id',$_POST['id']);
                $this->db->delete('coop_ele_document_user');

                //Delete comment data
                $this->db->where('document_id',$_POST['id']);
                $this->db->delete('coop_ele_document_comment');

                //Delete document data
                $this->db->where('id',$_POST['id']);
                $this->db->delete('coop_ele_document');
            } else {
                $this->db->where('document_id',$_POST['id']);
                $this->db->where('user_id',$_SESSION['USER_ID']);
                $this->db->delete('coop_ele_document_user');
            }
        } else if ($_POST["action"] == "change_status") {
            $data_update = array();
            $data_update['status'] = $_POST["status"];
            $data_update['updated_at'] = $process_timestamp;
            $this->db->where('id',$_POST['id']);
            $this->db->update('coop_ele_document',$data_update);
        }

        $x=0;
        $join_arr = array();
        $join_arr[$x]['table'] = '(SELECT * FROM coop_ele_document_user WHERE user_id = "'.$_SESSION['USER_ID'].'" AND type = "reviewer") as t2';
        $join_arr[$x]['condition'] = 't1.id = t2.document_id';
        $join_arr[$x]['type'] = 'left';
        $x++;
        $join_arr[$x]['table'] = 'coop_user as t3';
        $join_arr[$x]['condition'] = 't1.user_id = t3.user_id';
        $join_arr[$x]['type'] = 'left';
        $x++;
        $join_arr[$x]['table'] = '(SELECT * FROM coop_ele_document_user WHERE user_id = "'.$_SESSION['USER_ID'].'" AND type = "approver") as t5';
        $join_arr[$x]['condition'] = 't1.id = t5.document_id';
        $join_arr[$x]['type'] = 'left';
        $x++;
        $join_arr[$x]['table'] = '(SELECT * FROM coop_ele_document_user WHERE user_id = "'.$_SESSION['USER_ID'].'" AND type = "approve_draft") as t6';
        $join_arr[$x]['condition'] = 't1.id = t6.document_id';
        $join_arr[$x]['type'] = 'left';

        $this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select('t1.user_id, t1.status, t1.created_at, t1.updated_at, t1.name, t1.id, t1.last_access, t3.user_name, t2.user_id as reviewer, t5.user_id as approver, t6.user_id as approve_draft');
		$this->paginater_all->main_table('coop_ele_document as t1');
        $this->paginater_all->page_now($_GET["page"]);
        $this->paginater_all->where("t1.status in (2,4)
                                        AND (t1.user_id = '".$_SESSION['USER_ID']."'
                                            OR ((t2.user_id = '".$_SESSION['USER_ID']."' OR t6.user_id = '".$_SESSION['USER_ID']."') AND t1.status = 2)
                                            OR (t5.user_id = '".$_SESSION['USER_ID']."' AND t1.status = 4))");
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
        }

		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['datas'] = $row['data'];
        $arr_data['runno'] = $row['page_start'];

        $this->libraries->template('electronic_document/draft_document',$arr_data);
    }

    public function add_draft_document() {
        $arr_data = array();
        $process_timestamp = date('Y-m-d H:i:s');

        if(!empty($_POST)) {
            $config = array();
            $config['upload_path'] = FCPATH.'assets/uploads/electronic_document';
            $config['allowed_types'] = '*';
            $config['max_size'] = '0';
            $config['overwrite'] = FALSE;

            $this->load->library('upload', $config);

            if(empty($_POST["document_id"])) {
                // Create document data
                $data_insert = array();
                $data_insert['name'] = $_POST['document_name'];
                $data_insert['category_id'] = $_POST['category_id'];
                $data_insert['status'] = 2;//Status 2 Waiting for review
                $data_insert['user_id'] = $_SESSION['USER_ID'];
                $data_insert['updated_at'] = $process_timestamp;
                $data_insert['created_at'] = $process_timestamp;
                $this->db->insert('coop_ele_document', $data_insert);
                $document_id = $this->db->insert_id();

                $data_inserts = array();
                if(!empty($_POST["group_id"])) {
                    // $data_insert = array();
                    // $data_insert['document_id'] =  $document_id;
                    // $data_insert['group_id'] = $_POST["group_id"];
                    // $data_insert['user_id'] = null;
                    // $data_insert['created_at'] = $process_timestamp;
                    // $data_inserts[] = $data_insert;

                    // $group_members = $this->db->select("*")->from("coop_user_group_member")->where("id = '".$_POST["group_id"]."'")->get()->result_array();
                    // foreach($group_members as $member) {
                    //     $data_insert = array();
                    //     $data_insert['document_id'] =  $document_id;
                    //     $data_insert['group_id'] = null;
                    //     $data_insert['user_id'] = $member['user_id'];
                    //     $data_insert['created_at'] = $process_timestamp;
                    //     $data_inserts[] = $data_insert;
                    // }
                }

                if(!empty($_POST["user_ids"])) {
                    foreach($_POST["user_ids"] as $user_id) {
                        $data_insert = array();
                        $data_insert['document_id'] =  $document_id;
                        $data_insert['group_id'] = null;
                        $data_insert['user_id'] = $user_id;
                        $data_insert['type'] = "reviewer";
                        $data_insert['created_at'] = $process_timestamp;
                        $data_inserts[] = $data_insert;
                    }
                }
                if(!empty($_POST["approve_draft_user_ids"])) {
                    foreach($_POST["approve_draft_user_ids"] as $user_id) {
                        $data_insert = array();
                        $data_insert['document_id'] =  $document_id;
                        $data_insert['group_id'] = null;
                        $data_insert['user_id'] = $user_id;
                        $data_insert['type'] = "approve_draft";
                        $data_insert['created_at'] = $process_timestamp;
                        $data_inserts[] = $data_insert;
                    }
                }
                if(!empty($_POST["approve_user_ids"])) {
                    foreach($_POST["approve_user_ids"] as $user_id) {
                        $data_insert = array();
                        $data_insert['document_id'] =  $document_id;
                        $data_insert['group_id'] = null;
                        $data_insert['user_id'] = $user_id;
                        $data_insert['type'] = "approver";
                        $data_insert['created_at'] = $process_timestamp;
                        $data_inserts[] = $data_insert;
                    }
                }
                if(!empty($_POST["receive_user_ids"])) {
                    foreach($_POST["receive_user_ids"] as $user_id) {
                        $data_insert = array();
                        $data_insert['document_id'] =  $document_id;
                        $data_insert['group_id'] = null;
                        $data_insert['user_id'] = $user_id;
                        $data_insert['type'] = "receiver";
                        $data_insert['created_at'] = $process_timestamp;
                        $data_inserts[] = $data_insert;
                    }
                }

                if(!empty($data_inserts)) {
                    $this->db->insert_batch('coop_ele_document_user', $data_inserts);
                }

                $images = array();
                $title = date('Ymdhis').$document_id;
                $files = $_FILES;

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
                        $data_insert["document_id"] = $document_id;
                        $data_insert['name'] = $files['file']['name'][$key];
                        $data_insert['path'] = "/assets/uploads/electronic_document/".$upload_data["orig_name"];
                        $data_insert['created_at'] = $process_timestamp;
                        $this->db->insert('coop_ele_document_file', $data_insert);
                    }
                }
            } else {
                $document_id = $_POST["document_id"];

                // Update document data
                $data_update = array();
                $data_update['name'] = $_POST['document_name'];
                $data_update['category_id'] = $_POST['category_id'];
                $data_update['updated_at'] = $process_timestamp;
                $this->db->where('id',$document_id);
                $this->db->update('coop_ele_document',$data_update);

                //Remove reviewer
                $this->db->where('document_id',$document_id);
                $this->db->delete('coop_ele_document_user');

                //Generate new reviewer
                $data_inserts = array();
                if(!empty($_POST["user_ids"])) {
                    foreach($_POST["user_ids"] as $user_id) {
                        $data_insert = array();
                        $data_insert['document_id'] =  $document_id;
                        $data_insert['group_id'] = null;
                        $data_insert['user_id'] = $user_id;
                        $data_insert['type'] = "reviewer";
                        $data_insert['created_at'] = $process_timestamp;
                        $data_inserts[] = $data_insert;
                    }
                }
                if(!empty($_POST["approve_draft_user_ids"])) {
                    foreach($_POST["approve_draft_user_ids"] as $user_id) {
                        $data_insert = array();
                        $data_insert['document_id'] =  $document_id;
                        $data_insert['group_id'] = null;
                        $data_insert['user_id'] = $user_id;
                        $data_insert['type'] = "approve_draft";
                        $data_insert['created_at'] = $process_timestamp;
                        $data_inserts[] = $data_insert;
                    }
                }
                if(!empty($_POST["approve_user_ids"])) {
                    foreach($_POST["approve_user_ids"] as $user_id) {
                        $data_insert = array();
                        $data_insert['document_id'] =  $document_id;
                        $data_insert['group_id'] = null;
                        $data_insert['user_id'] = $user_id;
                        $data_insert['type'] = "approver";
                        $data_insert['created_at'] = $process_timestamp;
                        $data_inserts[] = $data_insert;
                    }
                }
                if(!empty($_POST["receive_user_ids"])) {
                    foreach($_POST["receive_user_ids"] as $user_id) {
                        $data_insert = array();
                        $data_insert['document_id'] =  $document_id;
                        $data_insert['group_id'] = null;
                        $data_insert['user_id'] = $user_id;
                        $data_insert['type'] = "receiver";
                        $data_insert['created_at'] = $process_timestamp;
                        $data_inserts[] = $data_insert;
                    }
                }
                if(!empty($data_inserts)) {
                    $this->db->insert_batch('coop_ele_document_user', $data_inserts);
                }

                //Keep old file data
                $old_files = $this->db->select("*")->from("coop_ele_document_file")->where("document_id = '{$document_id}'")->get()->result_array();

                //Generate new file data
                $images = array();
                $title = date('Ymdhis').$document_id;
                $files = $_FILES;
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
                        $data_insert["document_id"] = $document_id;
                        $data_insert['name'] = $files['file']['name'][$key];
                        $data_insert['path'] = "/assets/uploads/electronic_document/".$upload_data["orig_name"];
                        $data_insert['created_at'] = $process_timestamp;
                        $this->db->insert('coop_ele_document_file', $data_insert);
                    }
                }

                //Delete old file data
                foreach($old_files as $file) {
                    if(!in_array($file["id"], $_POST["file_ids"])) {
                        unlink(FCPATH.$file['path']);
                        $this->db->where('id',$file['id']);
                        $this->db->delete('coop_ele_document_file');
                    }
                }
            }
            echo "<script> document.location.href='".PROJECTPATH."/electronic_document/draft_document"."' </script>";
        }

        //Check $_GET["id"] for edit
        if(!empty($_GET["id"])) {
            $document = $this->db->select("*")->from("coop_ele_document")->where("id = '".$_GET["id"]."'")->get()->row();
            $arr_data["document"] = $document;

            $files = $this->db->select("*")->from("coop_ele_document_file")->where("document_id = '".$_GET["id"]."'")->get()->result_array();
            $arr_data["files"] = $files;

            $review_group = $this->db->select("*")->from("coop_ele_document_user")->where("document_id = '".$_GET["id"]."' AND group_id is not null")->get()->row();
            $arr_data["review_group_id"] = $review_group->group_id;

            $review_users = $this->db->select("t2.user_id, t2.user_name")
                                        ->from("coop_ele_document_user as t1")
                                        ->join("coop_user as t2", "t1.user_id = t2.user_id", "inner")
                                        ->where("t1.document_id = '".$_GET["id"]."' AND t1.user_id is not null AND type = 'reviewer'")->get()->result_array();
            $arr_data["review_users"] = $review_users;

            $approve_draft_users = $this->db->select("t2.user_id, t2.user_name")
                                        ->from("coop_ele_document_user as t1")
                                        ->join("coop_user as t2", "t1.user_id = t2.user_id", "inner")
                                        ->where("t1.document_id = '".$_GET["id"]."' AND t1.user_id is not null AND type = 'approve_draft'")->get()->result_array();
            $arr_data["approve_draft_users"] = $approve_draft_users;

            $approve_draft_users = $this->db->select("t2.user_id, t2.user_name")
                                        ->from("coop_ele_document_user as t1")
                                        ->join("coop_user as t2", "t1.user_id = t2.user_id", "inner")
                                        ->where("t1.document_id = '".$_GET["id"]."' AND t1.user_id is not null AND type = 'approve_draft'")->get()->result_array();
            $arr_data["approve_draft_users"] = $approve_draft_users;

            $approve_users = $this->db->select("t2.user_id, t2.user_name")
                                        ->from("coop_ele_document_user as t1")
                                        ->join("coop_user as t2", "t1.user_id = t2.user_id", "inner")
                                        ->where("t1.document_id = '".$_GET["id"]."' AND t1.user_id is not null AND type = 'approver'")->get()->result_array();
            $arr_data["approve_users"] = $approve_users;

            $receive_users = $this->db->select("t2.user_id, t2.user_name")
                                        ->from("coop_ele_document_user as t1")
                                        ->join("coop_user as t2", "t1.user_id = t2.user_id", "inner")
                                        ->where("t1.document_id = '".$_GET["id"]."' AND t1.user_id is not null AND type = 'receiver'")->get()->result_array();
            $arr_data["receive_users"] = $receive_users;
        }

        $groups = $this->db->select("*")->from("coop_user_group")->get()->result_array();
        $arr_data["groups"] = $groups;

        $users = $this->db->select("*")->from("coop_user")->get()->result_array();
        $arr_data["users"] = $users;

        $this->libraries->template("electronic_document/add_draft_document", $arr_data);
    }

    public function review_draft_document() {
        $arr_data = array();
        $process_timestamp = date('Y-m-d H:i:s');

        if(!empty($_POST)) {
            $_GET["id"] = $_POST["id"];

            if($_POST["action"] == "delete") {
                $this->db->where('id',$_POST['comment_id']);
                $this->db->delete('coop_ele_document_comment');
            } else {
                if(!empty($_POST["comment_id"])) {
                    $data_update = array();
                    $data_update['comment'] = $_POST['comment'];
                    $data_update['updated_at'] = $process_timestamp;
                    $this->db->where('id',$_POST['comment_id']);
                    $this->db->update('coop_ele_document_comment',$data_update);
                    $comment_id = $_POST['comment_id'];
                } else if (!empty($_POST["comment"])) {
                    $data_insert = array();
                    $data_insert['document_id'] = $_POST['id'];
                    $data_insert['user_id'] = $_SESSION['USER_ID'];
                    $data_insert['comment'] = $_POST['comment'];
                    $data_insert['updated_at'] = $process_timestamp;
                    $data_insert['created_at'] = $process_timestamp;
                    $this->db->insert('coop_ele_document_comment', $data_insert);
                    $comment_id = $this->db->insert_id();
                }

                if(!empty($_FILES) && !empty($_FILES['file']['name'][0])) {
                    $config = array();
                    $config['upload_path'] = FCPATH.'assets/uploads/electronic_document';
                    $config['allowed_types'] = '*';
                    $config['max_size'] = '0';
                    $config['overwrite'] = FALSE;

                    $this->load->library('upload', $config);

                    $document_id = $_POST["id"];
                    $document = $this->db->select("*")->from("coop_ele_document")->where("id = '".$document_id."'")->get()->row();

                    //Keep old file data
                    $old_files = $this->db->select("*")->from("coop_ele_document_file")->where("comment_id = '{$comment_id}'")->get()->result_array();

                    $images = array();
                    $title = date('Ymdhis').$new_document_id;
                    $files = $_FILES;

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
                            $data_insert["comment_id"] = $comment_id;
                            $data_insert['name'] = $files['file']['name'][$key];
                            $data_insert['path'] = "/assets/uploads/electronic_document/".$upload_data["orig_name"];
                            $data_insert['created_at'] = $process_timestamp;
                            $this->db->insert('coop_ele_document_file', $data_insert);
                        }
                    }

                    //Delete old file data
                    foreach($old_files as $file) {
                        if(!in_array($file["id"], $_POST["file_ids"])) {
                            unlink(FCPATH.$file['path']);
                            $this->db->where('id',$file['id']);
                            $this->db->delete('coop_ele_document_file');
                        }
                    }
                }
            }
        }

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

        $review_users = $this->db->select("t2.user_id, t2.user_name")
                                    ->from("coop_ele_document_user as t1")
                                    ->join("coop_user as t2", "t1.user_id = t2.user_id", "inner")
                                    ->where("t1.document_id = '".$_GET["id"]."' AND t1.user_id is not null AND t1.type = 'reviewer'")->get()->result_array();
        $arr_data["review_users"] = $review_users;

        $comments = $this->db->select("*")
                                ->from("coop_ele_document_comment as t1")
                                ->join("coop_user as t2", "t1.user_id = t2.user_id", "left")
                                ->where("t1.document_id = '".$document_id."'")
                                ->get()->result_array();
        foreach($comments as $key=>$comment) {
            $files = $this->db->select("*")->from("coop_ele_document_file")->where("comment_id = '".$comment["id"]."'")->get()->result_array();
            $comments[$key]["files"] = $files;
        }
        $arr_data["comments"] = $comments;

        $this->libraries->template("electronic_document/review_draft_document", $arr_data);
    }

    public function get_user_group_members() {
        $members = $this->db->select("t2.user_id, t2.user_name")
                                ->from("coop_user_group_member as t1")
                                ->join("coop_user as t2", "t1.user_id = t2.user_id", "inner")
                                ->where("t1.group_id = '".$_GET["id"]."'")
                                ->get()->result_array();
        echo json_encode($members);
    }

    public function my_document() {
        $arr_data = array();

        if ($_POST["action"] == 'delete') {
            $this->db->where('document_id',$_POST['id']);
            $this->db->where('user_id',$_SESSION['USER_ID']);
            $this->db->delete('coop_ele_document_user');
        }

        $x=0;
        $join_arr = array();
        $join_arr[$x]['table'] = '(SELECT * FROM coop_ele_document_user WHERE user_id = "'.$_SESSION['USER_ID'].'" AND type = "receiver") as t2';
        $join_arr[$x]['condition'] = 't1.id = t2.document_id';
        $join_arr[$x]['type'] = 'left';

        $this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select('t1.user_id, t1.status, t1.created_at, t1.updated_at, t1.name, t1.id');
		$this->paginater_all->main_table('coop_ele_document as t1');
        $this->paginater_all->page_now($_GET["page"]);
        $this->paginater_all->where("t1.status = 5 AND (t1.user_id = '".$_SESSION['USER_ID']."' OR t2.user_id = '".$_SESSION['USER_ID']."')");
        $this->paginater_all->order_by("t1.created_at desc");
        $this->paginater_all->join_arr($join_arr);
		$this->paginater_all->per_page(20);
		$this->paginater_all->page_link_limit(20);
		$row = $this->paginater_all->paginater_process();
		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'], $_GET);

        foreach($row['data'] as $key => $data) {
            $files = $this->db->select("id, name, path")->from("coop_ele_document_file")->where("document_id = '".$data["id"]."'")->get()->result_array();
            $row['data'][$key]["files"] = $files;
        }

		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['datas'] = $row['data'];
        $arr_data['runno'] = $row['page_start'];

        $_SERVER["REQUEST_URI"] = "/electronic_document/my_document";

        $this->libraries->template("electronic_document/my_document", $arr_data);
    }
}
