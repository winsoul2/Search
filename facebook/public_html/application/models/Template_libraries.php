<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Template_libraries extends CI_Model {
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

	public function template($bodyFile='', $arr_data=array()){
		$file = basename($_SERVER['PHP_SELF']);

		$this->db->select(array('coop_img','coop_name_en','coop_name_th'));
		$this->db->from('coop_profile');
		$this->db->where("profile_id = '1'");
		$profile = $this->db->get()->result_array();
		$coop_name_th = $profile[0]['coop_name_th'];

		$name_coop = array(
			"title_name" => $profile[0]["coop_name_th"],
			"title_admin_manage" => "ผู้ดูแลระบบ",
			"file" => $file
		);
		$arr_title['name_coop'] = $name_coop;

		if($bodyFile == 'login'){
            $arr_title['title'] = 'เข้าสู่ระบบ';
			$arr_title['body_class'] = 'login-page';
            $this->load->view('template/template_login_header', $arr_title);
            $this->load->view($bodyFile, $arr_data);//file view show body
			$this->load->view('template/template_login_footer');
		}else{
			$this->db->select('*');
			$this->db->from('coop_user');
			$this->db->where("user_id = '".@$_SESSION["USER_ID"]."' AND user_status = 1");
			$user = $this->db->get()->result_array();
			$user = @$user[0];

			$permissions = array();
			$this->db->select('*');
			$this->db->from('coop_user_permission');
			$this->db->where("user_id = '".@$_SESSION["USER_ID"]."'");
			$permission_arr = $this->db->get()->result_array();
			foreach($permission_arr as $key => $value) {
				$permissions[$value["menu_id"]] = TRUE;
			}
			
			/*$this->db->select('*');
			$this->db->from('coop_user_notification');
			$this->db->where("user_id = '".@$_SESSION["USER_ID"]."'");
			$notification_arr = $this->db->get()->result_array();
			$noti_condition = '';
			foreach($notification_arr as $key => $value) {
				$noti_condition .= "'".$value['notification_id']."',";
			}
			$noti_condition = substr($noti_condition,0,-1);
			if($noti_condition!=''){
				$this->db->select('*');
				$this->db->from('coop_notification');
				$this->db->where("notification_id IN (".$noti_condition.") AND notification_status = '0'");
				$this->db->order_by('notification_datetime DESC');
				$notification = $this->db->get()->result_array();
			}else{*/
				$notification = array();
			//}

			if($user) {
				$arr_title['user'] = $user;
				$arr_title['permissions'] = $permissions;
				$arr_title['notification'] = $notification;
			}else {
				header("location: ".PROJECTPATH."/auth?return_url=".urlencode($_SERVER["REQUEST_URI"]));
				exit();
			}
			$menus = $this->get_menu_arr();
			//echo '<pre>'; print_r($menus); echo '</pre>'; exit;
			
			$self_path = explode("?",$_SERVER["REQUEST_URI"]);
            $self_path = (@$_SERVER["HTTPS"] ? "https://" : "http://").$_SERVER['HTTP_HOST'].$self_path[0];

            if ($self_path == base_url( "/main_menu")) {
                $current_path = ((empty($_GET["section"]) ? "" : base_url( "/main_menu") . "?section=" . $_GET["section"]));
            } else {
                $current_path = (str_replace('/index.php', '', $self_path));
            }

            $current_path_arr = explode('/',$current_path);
			
			$menu_id = $this->get_menu_id($menus, $current_path);
			//echo"<pre>";print_r($menu_id);echo"</pre>";//exit;
			$menu_paths = $this->get_menu_path($menus, $menu_id);
			
			if($menu_id == -1){					
				if(count($current_path_arr)>1){
					//$current_path = '/'.$current_path_arr[0].'/'.$current_path_arr[1];
					$current_path = '/'.$current_path_arr[1];
				}
				$menu_id = $this->get_menu_id($menus, $current_path);
				$menu_paths = $this->get_menu_path($menus, $menu_id);
			}
			
			$arr_title['current_path'] = $current_path;
			$arr_title['menu_id'] = $menu_id;
			$arr_title['menu_paths'] = $menu_paths;
			$arr_data['menu_paths'] = $menu_paths;
				
			$arr_title['menus'] = $menus;
			$arr_data['menus'] = $menus;

			$arr_title['side_menus'] = $menus;
			$arr_data['side_menus'] = $menus;

			$arr_title['title'] = @$coop_name_th;
			$arr_title['body_class'] = 'hold-transition skin-blue fixed sidebar-mini layout layout-header-fixed';
            $this->load->view('template/template_header', $arr_title);
			$this->load->view('template/template_header_body', $arr_title);
			$this->load->view('alert_not_find_modal');
            $this->load->view($bodyFile, $arr_data);//file view show body
			$arr_data = array();
			$arr_data['month_arr'] = @$this->center_function->month_arr();
			
			$this->db->select(array('id','mem_group_name'));
			$this->db->from('coop_mem_group');
			$this->db->where("mem_group_type = '1'");
			$row_mem_group = $this->db->get()->result_array();
			$arr_data['mem_group'] = $row_mem_group;
			
			$this->db->select('mem_type_id, mem_type_name');
			$this->db->from('coop_mem_type');
			$row = $this->db->get()->result_array();
			$arr_data['mem_type'] = $row;
			
            $this->load->view('template/template_footer',$arr_data);
		}
	}	
	
	function get_menu_arr()
	{
		$base_url = rtrim(base_url(), "/");
	
		$this->db->select('menu_id AS id, 
							menu_parent_id AS parent_id,
							menu_name AS name,
							IF(menu_type = "1",CONCAT("'.$base_url.'",menu_url),menu_url) AS url,
							menu_icon AS icon ,
							menu_img AS img , 
							menu_hidden AS hidden,
							menu_active AS active,
							menu_target AS target,
							menu_type,
							order_by');
		$this->db->from('coop_menu');
		$this->db->order_by('order_by ASC');
		$getMenu = $this->db->get()->result_array();
		//echo $this->db->last_query();
		//echo '<hr>';
		
		$arr = $getMenu;
		$menu = []; 
		$byId = []; 
		
		foreach ($arr as $row) {
			$id = $row['id'];
			
			if ( $row['active'] == 1 ){

				$parentId = $row['parent_id'];
				$entry = $row;

				$entry['submenus'] = &$byId[$id]['submenus']; 
				
				if (null === $parentId || 0 === $parentId) {
					$menu[] = &$entry;
				} else {
					$byId[$parentId]['submenus'][] = &$entry;
				}
				$byId[$id] = &$entry;
			
				unset($entry);
			}
			
		}
		
		return $menu;
	}	

	function get_menu_id($menus, $url) {
		$id = -1;		
		foreach($menus as $menu) {			
			if($menu["url"] == $url && $url != '') {	
				return $menu["id"];				
			}
			else if(!empty($menu["submenus"]) && $id == -1) {
				$id = $this->get_menu_id($menu["submenus"], $url);						
			}
		}

		return $id;
	}

	function get_menu($menus, $url) {
		$_menu = array();
		foreach ($menus as $menu) {
			if ($menu["url"] == $url) {
				return $menu;
			} else if (!empty($menu["submenus"]) && empty($_menu)) {
				$_menu = $this->get_menu($menu["submenus"], $url);
			}
		}
		return $_menu;
	}

	function get_menu_path($menus, $id, $is_first = TRUE) {

		if($is_first) {
			$this->menu_path_stack = array();
			$this->is_menu_path_found = FALSE;
		}

		foreach($menus as $menu) {
			if($this->is_menu_path_found == FALSE) {
				array_push($this->menu_path_stack, $menu);
				if($menu["id"] == $id) {
					$this->is_menu_path_found = TRUE;
					return $this->menu_path_stack;
				}
				else {
					if(empty($menu["submenus"])) {
						array_pop($this->menu_path_stack);
					}
					else {
						$result = $this->get_menu_path($menu["submenus"], $id, FALSE);
						if($this->is_menu_path_found != TRUE) {
							array_pop($this->menu_path_stack);
						}
					}
				}
			}
		}

		return $this->menu_path_stack;
	}
}
