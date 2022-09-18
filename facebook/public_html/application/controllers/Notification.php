<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notification extends CI_Controller {
	function __construct()
	{
		parent::__construct();

	}
	public function index()
	{
		$arr_data = array();
		$this->db->select('*');
		$this->db->from('coop_user_notification');
		$this->db->where("user_id = '".@$_SESSION["USER_ID"]."'");
		$notification_arr = $this->db->get()->result_array();
		$noti_condition = '';
		foreach($notification_arr as $key => $value) {
			$noti_condition .= ",'".$value['notification_id']."'";
		}
		
		$join_arr = array();
		$x=0;
		$join_arr = array();
		$join_arr[$x]['table'] = "coop_notification_session t2";
		$join_arr[$x]['condition'] = "t1.id = t2.ref_id";
		$join_arr[$x]['type'] = 'left';
			
		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select('t1.*, t2.ref_id');
		$this->paginater_all->main_table('coop_notification t1');
		$this->paginater_all->where("t1.notification_id IN ('0'".$noti_condition.")");
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(10);
		$this->paginater_all->page_link_limit(20);
		$this->paginater_all->order_by('t1.notification_datetime DESC');
		$this->paginater_all->join_arr($join_arr);
		$row = $this->paginater_all->paginater_process();
		//echo $this->db->last_query();exit;
		//echo"<pre>";print_r($row);exit;
		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'], $_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		
		$i = $row['page_start'];

		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['rs'] = $row['data'];
		$arr_data['i'] = $i;	
		
		$this->libraries->template('notification/index',$arr_data);
	}
	public function update_notification()
	{
		$this->db->select('*');
		$this->db->from('coop_notification_session');
		$this->db->where("user_id = '".$_SESSION["USER_ID"]."' AND ref_id = '".$_GET['id']."'");
		if(!($row = $this->db->get()->row_array())) {
			$this->db->insert('coop_notification_session', array(
				"user_id" => $_SESSION["USER_ID"],
				"ref_id" => $_GET['id']
			));
		}
		
		$this->db->select('*');
		$this->db->from('coop_notification');
		$this->db->where("id = '".$_GET['id']."'");
		$row = $this->db->get()->row_array();
		if($row['notification_link']!=''){
			echo"<script> document.location.href='".PROJECTPATH.$row['notification_link']."' </script>";
		}else{
			echo"<script> document.location.href='".PROJECTPATH."/main_menu' </script>";
		}
		
	}
	
	public function call_notification(){
		$arr_data = array();
		$this->db->select('*');
		$this->db->from('coop_user_notification');
		$this->db->where("user_id = '".@$_SESSION["USER_ID"]."'");
		$notification_arr = $this->db->get()->result_array();
		$noti_condition = '';
		foreach($notification_arr as $key => $value) {
			$noti_condition .= "'".$value['notification_id']."',";
		}
		$noti_condition = substr($noti_condition,0,-1);
		if($noti_condition != ''){
			$this->db->select('*');
			$this->db->from('coop_notification');
			$this->db->where("notification_id IN (".$noti_condition.") AND id NOT IN (SELECT ref_id FROM coop_notification_session WHERE user_id = '".$_SESSION["USER_ID"]."')");
			$this->db->order_by('notification_datetime DESC');
			$notification = $this->db->get()->result_array();
			
			//$arr_data['notification'] = $notification;
			
			$notification_body = "";
			if(count($notification)>0){
			$notification_body .= "<div class=\"dropdown-body\">";
				$notification_body .= "<div class=\"list-group list-group-divided custom-scrollbar\">";
					foreach($notification as $key => $value){
					$notification_body .= "<a class=\"list-group-item\" href=\"".PROJECTPATH."/notification/update_notification?id=".$value['id']."\">";
						$notification_body .= "<div class=\"notification\">";
							$notification_body .= "<div class=\"notification-media\">";
								//<!--span class="icon icon-exclamation-triangle bg-warning rounded sq-40"></span-->
							$notification_body .= "</div>";
							$notification_body .= "<div class=\"notification-content\">";
								$notification_body .= "<small class=\"notification-timestamp\">".$this->center_function->mydate2date($value['notification_datetime'],1)."</small>";
								$notification_body .= "<h5 class=\"notification-heading\">".$value['notification_title']."</h5>";
								$notification_body .= "<p class=\"notification-text\">";
									$notification_body .= "<small class=\"truncate\">".$value['notification_text']."</small>";
								$notification_body .= "</p>";
							$notification_body .= "</div>";
						$notification_body .= "</div>";
					$notification_body .= "</a>";
					}
				$notification_body .= "</div>";
			$notification_body .= "</div>";
			}
		}else{
			$notification_body = '';
			$notification = array();
		}
		$arr_data['notification_body'] = $notification_body; 
		$arr_data['notification_count'] = count($notification)>0?count($notification):''; 
		echo json_encode($arr_data);
		//$this->load->view('notification/call_notification',$arr_data);
		
	}
}
