<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting_report_payment_interest extends CI_Controller {
    function __construct()
    {
        parent::__construct();
    }
    //แสดงรายการ
    public function setting_report_payment_interest(){
        $arr_data = array();
        $arr_data['items'] = $this->db->get("coop_report_payment_interest_style")->result_array();
        $this->libraries->template('setting_report_payment_interest/coop_report_payment_interest_style',$arr_data);
    }
    //แสดงขนาด
    public function get_style(){
        $style_id = $_POST['style_id'];
        $this->db->where("style_id", $style_id);
        $style = $this->db->get_where("coop_report_payment_interest_style")->row_array();
        header('Content-Type: application/json');
        echo json_encode(array("result" => $style));
    }

  
//กำหนดขนาด
    public function update_coop_report_payment_interest_style(){
        $data = $_POST;
        if($data){
            $style_id = $data['style_id'];
            $this->db->where("style_id", $style_id);
            $this->db->update("coop_report_payment_interest_style", $data);
        }else{
            die();
        }

        header("location: ".base_url('setting_report_payment_interest/setting_report_payment_interest'));
    }

//แก้ไขรายละเอียด
    public function coop_report_payment_interest_style_setting(){
        $arr_data = array();
        $style_id = @$_GET['style_id'];
        if($style_id){
            $arr_style = $this->db->select("style_name")->from('coop_report_payment_interest_style')->where("style_id = '{$style_id}'")->get()->row_array();            
            $arr_data['style_name'] = @$arr_style['style_name'];

            $this->db->where("style_id", $style_id);
            $arr_data['items'] = $this->db->get("coop_report_payment_interest_style_setting")->result_array();
        }
        $this->libraries->template('setting_report_payment_interest/coop_report_payment_interest_style_setting',$arr_data);
    }
//บันทึกแก้ไขรายละเอียด
    public function save_coop_report_payment_interest_style_setting(){
        $data = $_POST;
        $style_id = @$_GET['style_id'];
        $this->db->where("style_id", $style_id);    
        $this->db->delete("coop_report_payment_interest_style_setting");
        foreach ($data['style_value'] as $key => $value) {
            if($value!=""){
                $row = array(
                    "style_id"          =>     $style_id,
                    "style_value"       =>     $data['style_value'][$key],
                    "x"                 =>     $data['x'][$key],
                    "y"                 =>     $data['y'][$key],
                    "font_size"         =>     $data['font_size'][$key],
                    "width"             =>     $data['width'][$key],
                    "align"             =>     $data['align'][$key]
                );
                $this->db->insert("coop_report_payment_interest_style_setting", $row);
            }
        }
        header("location: ".base_url('setting_report_payment_interest/coop_report_payment_interest_style_setting?style_id='.$style_id));
    }

}
