<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Template_preview_libraries extends CI_Model {
	public function __construct()
	{
		parent::__construct();
		//$this->load->database();
		# Load libraries
		//$this->load->library('parser');
		$this->load->helper(array('html', 'url'));

		$this->menu_path_stack = array();
		$this->is_menu_path_found = FALSE;
	}

	public function template_preview($bodyFile='', $arr_data=array()){
		$file = basename($_SERVER['PHP_SELF']);

		$this->db->select(array('coop_img','coop_name_en','coop_name_th'));
		$this->db->from('coop_profile');
		$this->db->where("profile_id = '1'");
		$profile = $this->db->get()->result_array();

		$name_coop = array(
			"title_name" => $profile[0]["coop_name_th"],
			"title_admin_manage" => "ผู้ดูแลระบบ",
			"file" => $file
		);
		$arr_title['name_coop'] = $name_coop;

		if($bodyFile == 'login' || empty($_SESSION["USER_ID"])){			
			header("location: ".PROJECTPATH."/main_menu/logout?res=".$_SERVER['REQUEST_URI']);
			exit();
		}else{
			$arr_title['title'] = $profile[0]["coop_name_th"];
			$arr_title['body_class'] = 'hold-transition skin-blue fixed sidebar-mini layout layout-header-fixed';
            $this->load->view('template_preview/template_header', $arr_title);
            $this->load->view($bodyFile, $arr_data);//file view show body
			$this->load->view('template_preview/template_footer');
		}
	}

	public function template_preview_non_auth($bodyFile='', $arr_data=array()){
		$file = basename($_SERVER['PHP_SELF']);

		$this->db->select(array('coop_img','coop_name_en','coop_name_th'));
		$this->db->from('coop_profile');
		$this->db->where("profile_id = '1'");
		$profile = $this->db->get()->result_array();

		$name_coop = array(
			"title_name" => $profile[0]["coop_name_th"],
			"title_admin_manage" => "ผู้ดูแลระบบ",
			"file" => $file
		);
		$arr_title['name_coop'] = $name_coop;
		$arr_title['title'] = 'สหกรณ์ออมทรัพย์ครูสมุทรปราการ จำกัด';
		$arr_title['body_class'] = 'hold-transition skin-blue fixed sidebar-mini layout layout-header-fixed';
		$this->load->view('template_preview/template_header', $arr_title);
		$this->load->view($bodyFile, $arr_data);//file view show body
		$this->load->view('template_preview/template_footer');
	}
}
