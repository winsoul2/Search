<?php
/**
 * Created by PhpStorm.
 * User: macmini2
 * Date: 2019-11-06
 * Time: 21:16
 */

class Text_file extends CI_Controller{

    /**
     * Text_file constructor.
     */
    public function __construct(){
        parent::__construct();
    }

    public function index(){
        $arr_data = array();
        $this->libraries->template('text_file/index',$arr_data);
    }

    public function check_empty(){
        $data = $this->input->post();
        $result = array();
        if($data['type'] == "DEPOSIT"){
            if($this->text_files->get_rows($data)){
                $result['data_type'] = 'DEPOSIT';
                $result['has_data'] = 1;
            }else{
                $result['data_type'] = 'DEPOSIT';
                $result['has_data'] = 0;
            }
        }else if($data['type'] == "LOAN"){
            if(sizeof($this->text_files->get_rows($data))){
                $result['data_type'] = 'LOAN';
                $result['has_data'] = 1;
            }else{
                $result['data_type'] = 'LOAN';
                $result['has_data'] = 0;
            }
        }else if($data['type'] == "SHARE"){
            if(sizeof($this->text_files->get_rows($data))){
                $result['data_type'] = 'SHARE';
                $result['has_data'] = 1;
            }else{
                $result['data_type'] = 'SHARE';
                $result['has_data'] = 0;
            }
        }else{
            $result['data_type'] = 'ERR';
            $result['has_data'] = 0;
        }
        header('content-type: application/json; charset:utf-8;');
        echo json_encode($result);
    }

    public function coper(){
        if(isset($_GET['loan_mem_type']) && isset($_GET['loan_month']) && isset($_GET['loan_year'])) {
            $input = array();
            $input['month'] = $_GET['loan_month'];
            $input['year'] = $_GET['loan_year'];
            $input['mem_type_id'] = $_GET['loan_mem_type'];
            $data = $this->text_files->get_loan_contract($input);
            $txt = "";
            if (sizeof($data)) {
                foreach ($data as $key => $item) {
                    $txt .= $this->text_files->loan_text_file($item);
                }
            }

            if($_GET['display'] === "show"){
                echo "<pre>";
                echo $txt;
                echo "<pre>";
                exit;
            }

            if($_GET['loan_mem_type'] == '1') {
                $this->text_files->create_file("COPER", $txt);
            }else{
                $this->text_files->create_file("COPER2", $txt);
            }
        }
    }

    public function hun(){
        if(isset($_GET['hun_mem_type']) && isset($_GET['hun_month']) && isset($_GET['hun_year'])) {
            $input = array();
            $input['month'] = $_GET['hun_month'];
            $input['year'] = $_GET['hun_year'];
            $input['mem_type_id'] = $_GET['hun_mem_type'];
            $data = $this->text_files->get_share_month($input);
            $txt = "";
            if (sizeof($data)) {
                foreach ($data as $key => $item) {
                    $txt .= $this->text_files->share_text_file($item);
                }
            }

            if($_GET['display'] === "show"){
                echo "<pre>";
                echo $txt;
                echo "<pre>";
                exit;
            }

            if($_GET['hun_mem_type'] == '1') {
                $this->text_files->create_file("HUN", $txt);
            }else{
                $this->text_files->create_file("HUN2", $txt);
            }
        }
    }

    public function sav(){
        if(isset($_GET['sav_mem_type']) && isset($_GET['sav_month']) && isset($_GET['sav_year'])) {
            $input = array();
            $input['month'] = $_GET['sav_month'];
            $input['year'] = $_GET['sav_year'];
            $input['mem_type_id'] = $_GET['sav_mem_type'];
            $data = $this->text_files->get_deposit_month($input);
            $txt = "";
            if (sizeof($data)) {
                foreach ($data as $key => $item) {
                    $txt .= $this->text_files->deposit_text_file($item);
                }
            }

            if($_GET['display'] === "show"){
                echo "<pre>";
                echo $txt;
                echo "<pre>";
                exit;
            }

            if ($_GET['sav_mem_type'] == '1') {
                $this->text_files->create_file("SAV", $txt);
            } else {
                $this->text_files->create_file("SAV2", $txt);
            }
        }
    }
	
	public function sumary_share(){
		$sumary = 0;
		$arr_data = array();
        if(isset($_POST['mem_type_id']) && isset($_POST['month']) && isset($_POST['year'])) {
            $input = array();
            $input['month'] = $_POST['month'];
            $input['year'] = $_POST['year'];
            $input['mem_type_id'] = $_POST['mem_type_id'];
            $data = $this->text_files->get_share_month($input);           
            if (sizeof($data)) {
                foreach ($data as $key => $item) {
                    $sumary+=$item['pay_amount'];
                }
            }			
        }
		$arr_data['sumary'] = number_format($sumary,2);
		echo json_encode($arr_data);
		exit;
    }
	
	public function sumary_loan(){
		$sumary = 0;
		$arr_data = array();
        if(isset($_POST['mem_type_id']) && isset($_POST['month']) && isset($_POST['year'])) {
            $input = array();
            $input['month'] = $_POST['month'];
            $input['year'] = $_POST['year'];
            $input['mem_type_id'] = $_POST['mem_type_id'];
            $data = $this->text_files->get_loan_contract($input);
			if (sizeof($data)) {
                foreach ($data as $key => $item) {
                    $sumary+=$item['pay_amount'];
                }
            }
        }
		$arr_data['sumary'] = number_format($sumary,2);
		echo json_encode($arr_data);
		exit;
    }
	
	public function sumary_deposit(){
		$sumary = 0;
		$arr_data = array();
        if(isset($_POST['mem_type_id']) && isset($_POST['month']) && isset($_POST['year'])) {
            $input = array();
            $input['month'] = $_POST['month'];
            $input['year'] = $_POST['year'];
            $input['mem_type_id'] = $_POST['mem_type_id'];
            $data = $this->text_files->get_deposit_month($input);
            if (sizeof($data)) {
                foreach ($data as $key => $item) {
                    $sumary+=$item['pay_amount'];
                }
            }
        }
		$arr_data['sumary'] = number_format($sumary,2);
		echo json_encode($arr_data);
		exit;
    }

}
