<?php
if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Loan_online_transfer extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('html', 'url'));
    }
	//อัพเดตสถานะ การโอนเงิน ระบบกู้เงินออนไลน์
    public function update_confirm_transfer($loan_application_id){		
		define("HOSTNAME","localhost") ;
		define("DBNAEM","spkt_com") ;
		define("USERNAME","spkt_com") ;
		define("PASSWORD","ZLB2RZ2z") ;
		
		$mysqli = new mysqli( HOSTNAME , USERNAME , PASSWORD );
		$mysqli->select_db(DBNAEM);
		$mysqli->query("SET NAMES utf8");
		
		$sql_update = "UPDATE loan_application SET 
				confirm_transfer = '1' ,
				admin_confirm_transfer = '".$_SESSION['USER_NAME']."' ,
				date_transfer = NOW() 
			WHERE loan_id = '".$loan_application_id."'";
		if($mysqli->query($sql_update)){
			$result_transfer = true;
		}else{
			$result_transfer =false;
		}
		return $result_transfer;
		
    }

}