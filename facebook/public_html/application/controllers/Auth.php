<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {
	function __construct()
	{
		parent::__construct();

	}
	public function index()
	{
		if(@$_SESSION['USER_ID']!=''){
			header("location: main_menu");
		}else {
			if ($this->input->post()) {

				$this->db->select(array('user_id', 'user_name'));
				$this->db->from('coop_user');
				$this->db->where("username = " . $this->db->escape($this->input->post('username')) . "");
				$this->db->where("password = " . $this->db->escape($this->input->post('password')) . "");
				$this->db->where("user_status = '1'");
				$user = $this->db->get()->result_array();

				if (!empty($user)) {
					/*@$token = @date("YmdHis") . $user[0]["user_id"] .  @random_char(10);
                    echo"<pre>";print_r($user);exit;

                    $data = array(
                        'user_token' => $token
                    );

                    $this->db->where('user_id', $user_id);
                    $this->db->update('coop_user', $data);*/
					$user_id = (int)@$user[0]["user_id"];
					$user_name = @$user[0]["user_name"];
					$_SESSION['USER_ID'] = $user_id;
					$_SESSION['USER_NAME'] = $user_name;
					
					$this->db->select(array('*'));
					$this->db->from('coop_profile');
					$this->db->where("profile_id = '1'");
					$profile = $this->db->get()->result_array();
					
					$_SESSION['COOP_NAME'] = $profile[0]['coop_name_th'];
					$_SESSION['COOP_NAME_EN'] = $profile[0]['coop_name_en'];
					$_SESSION['COOP_IMG'] = $profile[0]['coop_img'];
					$_SESSION['COOP_SHORT_NAME_EN'] = $profile[0]['coop_short_name_en'];
					
					//$this->session->USER_ID = $user_id;

					header("location: " . (empty($_GET["return_url"]) ? "main_menu" : $_GET["return_url"]));
					exit();
				}

				$err_msg = "ชื่อผู้ใช้/รหัสผ่าน ไม่ถูกต้อง";
			}
			$this->session->sess_destroy();
			$arr_data = array();
			$this->db->select(array('coop_img', 'coop_name_en', 'coop_name_th'));
			$this->db->from('coop_profile');
			$this->db->where("profile_id = '1'");
			$profile = $this->db->get()->result_array();
			//echo"<pre>";print_r($profile);echo"</pre>";
			$arr_data['profile']['coop_img'] = $profile[0]['coop_img'];
			$arr_data['profile']['coop_name_en'] = $profile[0]['coop_name_en'];
			$arr_data['profile']['coop_name_th'] = $profile[0]['coop_name_th'];
			$this->libraries->template('login', $arr_data);
		}
	}
	
	public function authen_confirm_user(){
		if(empty($_SESSION['USER_ID']))
			header('HTTP/1.1 500 Internal Server Error');

		$user = $this->input->post("confirm_user");
		$password = $this->input->post("confirm_pwd");
		$menu_id = $this->input->post("permission_id");
		
		$user_db = $this->db->get_where("coop_user", array(
			"username" => $user,
			"password" => $password,
			"user_status" => 1
		))->result()[0];
		if($user_db){
			$permission = $this->db->get_where("coop_user_permission", array(
				"user_id" => $user_db->user_id,
				"menu_id" => $menu_id,//เมนูสิทธิ์
			))->result_array();
			echo json_encode(array("result" => "true", "permission" => ($permission || $_SESSION['USER_ID']==1 || $user_db->user_type_id==1) ? "true" : "false", "user_id" => $user_db->user_id, "sql" => $this->db->last_query() ) );
		}else{
			echo json_encode(array("result" => "false"));
		}
	}
}
