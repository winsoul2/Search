<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Elec_doc_archives extends CI_Controller {
	function __construct() {
		parent::__construct();
	}

	public function index(){
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
                $file_ids = !empty($_POST["file_ids"]) ? $_POST["file_ids"] : array();
                foreach($old_files as $file) {
                    if(!in_array($file["id"], $file_ids)) {
                        unlink(FCPATH.$file['path']);
                        $this->db->where('id',$file['id']);
                        $this->db->delete('coop_ele_document_file');
                    }
                }
            }
            echo "<script> document.location.href='".PROJECTPATH."/elec_doc_archives"."' </script>";
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
}
