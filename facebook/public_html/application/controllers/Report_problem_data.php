<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_problem_data extends CI_Controller {
	function __construct()
	{
		parent::__construct();
	}
	
	public function report_problem(){
		$arr_data = array();
		
		$x=0;
		$join_arr = array();
		$join_arr[$x]['table'] = 'coop_user';
		$join_arr[$x]['condition'] = 'report_problem.user_id = coop_user.user_id ';
		$join_arr[$x]['type'] = 'left';
		
		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select('report_problem.*,
									coop_user.user_name');
		$this->paginater_all->main_table('report_problem');
		$this->paginater_all->where("report_problem.user_id = '{$_SESSION['USER_ID']}'");
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(10);
		$this->paginater_all->page_link_limit(20);
		$this->paginater_all->order_by('problem_id ASC');
		$this->paginater_all->join_arr($join_arr);
		$row = $this->paginater_all->paginater_process();
		//echo"<pre>";print_r($row);exit;
		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit']);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		$i = $row['page_start'];


		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['rs'] = $row['data'];
		$arr_data['i'] = $i;
		
		$this->libraries->template('report_problem_data/report_problem',$arr_data);
	}
	
	public function report_problem_detail(){
		$arr_data = array();
		$this->db->select(array('*'));
		$this->db->from('report_problem');
		$this->db->where("problem_id = '{$_GET['problem_id']}'");
		$rs = $this->db->get()->result_array();
		$row = @$rs[0];
		$arr_data['row'] = @$row;
		
		$this->db->select(array('*'));
		$this->db->from('report_problem_file');
		$this->db->where("problem_id = '{$row['problem_id']}'");
		$rs_file = $this->db->get()->result_array();
		$arr_data['rs_file'] = @$rs_file;
		//print_r($this->db->last_query());exit;
		
		$this->db->select(array('*'));
		$this->db->from('coop_user');
		$this->db->where("user_id = '{$row['user_id']}'");
		$rs_user = $this->db->get()->result_array();
		$arr_data['row_user'] = @$rs_user[0];
		
		$this->libraries->template('report_problem_data/report_problem_detail',$arr_data);
	}
	
	public function report_problem_add(){
		$arr_data = array();
		if(!empty($_GET['problem_id'])){			
			$this->db->select(array('*'));
			$this->db->from('report_problem');
			$this->db->where("problem_id = '{$_GET['problem_id']}'");
			$rs = $this->db->get()->result_array();
			$row = @$rs[0];
			$arr_data['row'] = @$row;
			
			$this->db->select(array('*'));
			$this->db->from('report_problem_file');
			$this->db->where("problem_id = '{$row['problem_id']}'");
			$rs_file = $this->db->get()->result_array();
			$arr_data['rs_file'] = @$rs_file;
			
			$this->db->select(array('*'));
			$this->db->from('coop_user');
			$this->db->where("user_id = '{$row['user_id']}'");
			$rs_user = $this->db->get()->result_array();
			$arr_data['row_user'] = @$rs_user[0];
		}
		$this->libraries->template('report_problem_data/report_problem_add',$arr_data);
	}

	public function report_problem_save(){
		$mysqli_upbean = new mysqli("report.upbean.co.th", "upbean_report", "aPY9rD3wL");
		$mysqli_upbean->select_db("upbean_report");
		$mysqli_upbean->set_charset("utf8");

		$data_insert = array();

		$finish_date = $this->center_function->ConvertToSQLDate(@$_POST['finish_date']);

		$data_insert['problem_title']	= @$_POST["problem_title"];
		$data_insert['problem_description']	= @$_POST["problem_description"];
		$data_insert['problem_priority']	= @$_POST["problem_priority"];
		$data_insert['finish_date']	= @$finish_date;
		$data_insert['user_id']	= @$_SESSION['USER_ID'];
		
		$id = @$_POST['problem_id'];

		if($_POST['problem_id']!=''){
			// edit
			$this->db->where('problem_id', $id);
			$this->db->update('report_problem', $data_insert);

			$sql = "UPDATE report_problem SET
					problem_title = '".@$_POST['problem_title']."',
					problem_description = '".@$_POST['problem_description']."',
					problem_priority = '".@$_POST['problem_priority']."',
					finish_date = '".@$finish_date."',
					user_id = '".@$_SESSION['USER_ID']."'
				WHERE 
					problem_id = '".@$_POST['problem_id']."'
			";
			$sql_upbean = $sql;
			$sql_upbean .= " AND coop_name = 'freetradecoop'";
			$problem_id = @$_POST['problem_id'];
		}else{

			$data_insert['create_date']	= date('Y-m-d H:i:s');
			$data_insert['problem_status']	= '0';
			$this->db->insert('report_problem', $data_insert);
			$problem_id = $this->db->insert_id();	

			$sql = "INSERT INTO report_problem SET
				problem_title = '".@$_POST['problem_title']."',
				problem_description = '".@$_POST['problem_description']."',
				problem_priority = '".@$_POST['problem_priority']."',
				finish_date = '".@$finish_date."',
				user_id = '".@$_SESSION['USER_ID']."',
				create_date = NOW(),
				problem_status = '0'
			";
			$sql_upbean = $sql;
			$sql_upbean .= ", coop_name = 'freetradecoop'";
			
			$this->db->select(array('*'));
			$this->db->from('coop_user');
			$this->db->where("user_id = '{$_SESSION['USER_ID']}'");
			$rs_user = $this->db->get()->result_array();
			$row_user = @$rs_user[0];
			
			$sql_upbean .= ", user_name = '".@$row_user['user_name']."'";
			$sql_upbean .= ", user_email = '".@$row_user['user_email']."'";
			$sql_upbean .= ", user_tel = '".@$row_user['user_tel']."'";
		}

		$sql_upbean .= ", problem_id = '".$problem_id."'"; 
		$mysqli_upbean->query($sql_upbean);
		
		//echo $problem_id;
		$output_dir = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/report_problem/";
		$file_email = array();
		foreach($_FILES['problem_file']['name'] as $key => $value){
			if($_FILES['problem_file']['name'][$key]!=''){
				$new_filename = $this->center_function->create_file_name($output_dir,$_FILES['problem_file']['name'][$key]);
				copy($_FILES['problem_file']["tmp_name"][$key], $output_dir.$new_filename);
				$data_insert_file = array();
				$data_insert_file['problem_id']	= @$problem_id;
				$data_insert_file['problem_file_old_name']	= @$_FILES['problem_file']['name'][$key];
				$data_insert_file['problem_file_type']	= @$_FILES['problem_file']['type'][$key];
				$data_insert_file['problem_file_name']	= @$new_filename;
				$this->db->insert('report_problem_file', $data_insert_file);
				
				//add file Mysql
				$sql_file = "INSERT INTO report_problem_file SET
					problem_id = '".$problem_id."',
					problem_file_old_name = '".$_FILES['problem_file']['name'][$key]."',
					problem_file_type = '".$_FILES['problem_file']['type'][$key]."',
					problem_file_name = '".$new_filename."'
				";
				//echo $sql_file."<br>";				
				$sql_file .= ", problem_file_path = '".$_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/report_problem/'";
				$sql_file .= ", coop_name = 'freetradecoop'";
				$mysqli_upbean->query($sql_file);
				
				$file_email[] = $new_filename;
			}
		}			

		$date_time_now = $this->center_function->ConvertToThaiDate(date('Y-m-d H:i:s'));
		
		$this->db->select(array('coop_name_th'));
		$this->db->from('coop_profile');
		$this->db->limit(1);
		$rs_profile = $this->db->get()->result_array();
		$row_profile = @$rs_profile[0];

		$this->db->select(array('*'));
		$this->db->from('report_problem_file');
		$this->db->where("problem_id = '{$problem_id}'");
		$rs_file = $this->db->get()->result_array();
		$rs_file = @$rs_file;


		$subject = 'แจ้งปัญหาและข้อเสนอแนะ';
		$mail_detail = "
			<html>
				<head>
					<title>แจ้งปัญหาและข้อเสนอแนะ</title>
				</head>
				<body>
					<p>เรียน Upbean</p>
					<p>".@$row_profile['coop_name_th']." ได้แจ้งปัญหาและข้อเสนอแนะ เมื่อวันที่ ".@$date_time_now." ดังนี้</p>
					<p>หัวข้อ ".@$_POST['problem_title']."</p>
					<p>สถานะ ".@$problem_priority[$_POST['problem_priority']]."</p>
					<p>รายละเอียด ".@$_POST['problem_description']."</p>
		";

		//foreach(@$file_email as $key => $value){
		foreach(@$rs_file as $key => $row_file){
			$mail_detail .= "
			<a class='fancybox' href='".$_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/report_problem/".@$row_file['problem_file_name']."' target='_blank'>
			<img class='img-responsive img-thumbnail' src=".base_url(PROJECTPATH.'/assets/uploads/report_problem/'.@$row_file['problem_file_name'])." style='margin:auto;' />
			</a>";
		}
		$mail_detail .= "</body>
			</html>
		";
	
		$to = 'webmaster@upbean.co.th, support@upbean.co.th';	
		
		$this->center_function->send_mj_mail($subject, $mail_detail, $to);
		//var_dump($result);

		echo"<script> document.location.href='".PROJECTPATH."/report_problem_data/report_problem' </script>";
		exit;
	}
				
	function delete_problem(){	
		$table = @$_POST['table'];
		$table_sub = @$_POST['table_sub'];
		$id = @$_POST['id'];
		$field = @$_POST['field'];
		
		$this->db->select(array('*'));
		$this->db->from('report_problem_file');
		$this->db->where("problem_id = '{$id}'");
		$rs_file = $this->db->get()->result_array();
		
		if(!empty($rs_file)){
			foreach(@$rs_file as $key => $row_file){ 
				if($row_file['problem_file_id'] != ''){
					$output_dir = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/report_problem/";
					unlink(@$output_dir.@$row_file['problem_file_name']);
					
					$this->db->where("problem_file_id", @$row_file['problem_file_id']);
					$this->db->delete("report_problem_file");
					//print_r($this->db->last_query());exit;
				}
			}
		}

		$this->db->where("problem_id", $id );
		$this->db->delete("report_problem");
		$this->center_function->toast("ลบเรียบร้อยแล้ว");
		//print_r($this->db->last_query());exit;		
		//////////////////////////////////////////////////////////////////////////
		
	
		$mysqli_upbean = new mysqli("report.upbean.co.th", "upbean_report", "aPY9rD3wL");
		$mysqli_upbean->select_db("upbean_report");
		$mysqli_upbean->set_charset("utf8");

		$sql_delete_file = "DELETE FROM report_problem_file WHERE problem_id = '".$id."' AND coop_name = 'freetradecoop'";
		$mysqli_upbean->query($sql_delete_file);
		
		$sql_delete = "DELETE FROM report_problem WHERE problem_id = '".$id."' AND coop_name = 'freetradecoop'";
		//echo $sql_delete;
		$mysqli_upbean->query($sql_delete);		
		
		echo true;
		exit;
		
	}

	function delete_file(){	
		$output_dir = @$_SERVER['DOCUMENT_ROOT']."/uploads/report_problem/";
		unlink(@$output_dir.@$_POST['problem_file_name']);
		@$id = @$_POST['problem_file_id'];
		$this->db->where('problem_file_id', $id );
		$this->db->delete('report_problem_file');
		echo 'success';
		exit;	
	
	}

}
