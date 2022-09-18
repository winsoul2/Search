<?php
/**
 * Created by PhpStorm.
 * User: Win10x64Bit
 * Date: 11/3/2562
 * Time: 13:33
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class setting_account_tranction extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }
    public function index_setting_account_tranction(){

        $arr_data = array();
        $id = @$_GET['id'];
        if(@$id){
            $this->db->select(array('*'));
            $this->db->from('setting_account_tranction');
            $this->db->where("setting_id  = '{$id}' ");
            $rs = $this->db->get()->result_array();
            $arr_data['row'] = @$rs[0];
        }else{
            $this->db->select('COUNT(setting_id) as _c');
            $this->db->from('setting_account_tranction');
            $count = $this->db->get()->result_array();

            $x=0;
            $join_arr = array();

            $this->paginater_all->type(DB_TYPE);
            $this->paginater_all->select('*');
            $this->paginater_all->main_table('setting_account_tranction');
            $this->paginater_all->where("");
            $this->paginater_all->page_now(@$_GET["page"]);
            $this->paginater_all->per_page(20);
            $this->paginater_all->page_link_limit(20);
            $this->paginater_all->order_by('setting_id ASC');
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
        }
        $this->libraries->template('/setting_account_tranction/index_setting_account_tranction',$arr_data);
    }

    public function coop_account_setting_save(){
        //echo"<pre>";print_r($_POST);exit;
        $data_insert = array();
        $data_insert['setting_name_list']	= @$_POST["setting_name_list"];
        $data_insert['description']  = @$_POST["description"];
        $data_insert['process']  = @$_POST["process"];
        $data_insert['ref_type']  = @$_POST["ref_type"];
        $data_insert['match_type']  = @$_POST["match_type"];
        $data_insert['create_time']  =  date('Y-m-d');

        $id_edit = @$_POST["old_account_chart_id"] ;

        $table = "setting_account_tranction";

        if(@$_POST['old_account_chart_id']!=''){
            // edit
            $this->db->where('setting_id', $id_edit);
            $this->db->update($table, $data_insert);
            $this->center_function->toast("แก้ไขข้อมูลเรียบร้อยแล้ว");
            // edit
        }else{
            // add
            $this->db->insert($table, $data_insert);
            $this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
            // add

        }
        echo"<script> document.location.href='/setting_account_tranction/index_setting_account_tranction' </script>";
    }
    public function sub_coop_account_setting_save(){
        //echo"<pre>";print_r($_POST);exit;
        $data_insert = array();
        $data_insert['match_type']	= @$_POST["match_type"];
        $data_insert['match_id']  = @$_POST["match_id"];
        $data_insert['account_chart_id']  = @$_POST["account_chart_id"];
        $data_insert['match_id_description']  = @$_POST["match_id_description"];
        $data_insert['bankcharge']  = @$_POST["bankcharge"];

        $data_insert['create_time']  =  date('Y-m-d');

        $id_edit = @$_POST["old_account_match_id"] ;

        $table = "coop_account_match";

        if(@$_POST['old_account_match_id']!=''){
            // edit
            $this->db->where('account_match_id', $id_edit);
            $this->db->update($table, $data_insert);
            $this->center_function->toast("แก้ไขข้อมูลเรียบร้อยแล้ว");
            // edit
        }else{
            // add
            $this->db->insert($table, $data_insert);
            $this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
            // add

        }
        echo"<script> document.location.href='/setting_account_tranction/sub_index_setting_account_match' </script>";
    }
    function  del_setting_account_tranction(){

        $table = @$_POST['table'];
        $table_sub = @$_POST['table_sub'];
        $id = @$_POST['id'];
        $field = @$_POST['field'];


        if (!empty($table_sub)) {
            $this->db->where($field, $id );
            $this->db->delete($table_sub);
        }

        $this->db->where($field, $id );
        $this->db->delete($table);
        $this->center_function->toast("ลบเรียบร้อยแล้ว");
        echo true;


    }
    function  sub_del_setting_account_tranction(){

        $table = @$_POST['table'];
        $table_sub = @$_POST['table_sub'];
        $id = @$_POST['id'];
        $field = @$_POST['field'];


        if (!empty($table_sub)) {
            $this->db->where($field, $id );
            $this->db->delete($table_sub);
        }

        $this->db->where($field, $id );
        $this->db->delete($table);
        $this->center_function->toast("ลบเรียบร้อยแล้ว");
        echo true;


    }
    public function sub_index_setting_account_match(){
        $arr_data = array();

        $loan_type = array();
        $this->db->select('*');
        $this->db->from("setting_match_type");
        $rs_type = $this->db->get()->result_array();
        foreach($rs_type as $key => $row_type){
            $loan_type[$row_type['match_type']] = $row_type['description'];

        }

        $arr_data['loan_type'] = $loan_type;

        $account_chart = array();
        $this->db->select('*');
        $this->db->from("coop_account_chart");
        $rs_type = $this->db->get()->result_array();
        foreach($rs_type as $key => $row_type){
            $account_chart[$row_type['account_chart_id']] = $row_type['account_chart'];

        }
        $arr_data['account_chart'] = $account_chart;

        $id = @$_GET['id'];
        if(@$id){
            $this->db->select(array('*'));
            $this->db->from('coop_account_match ');
            $this->db->where("account_match_id  = '{$id}' ");
            $rs = $this->db->get()->result_array();
            $arr_data['row'] = @$rs[0];
        }else{
            $this->db->select('COUNT(account_match_id) as _c');
            $this->db->from('coop_account_match');
            $count = $this->db->get()->result_array();

            $x=0;
            $join_arr = array();

            $this->paginater_all->type(DB_TYPE);
            $this->paginater_all->select('*');
            $this->paginater_all->main_table('coop_account_match');
            $this->paginater_all->where("");
            $this->paginater_all->page_now(@$_GET["page"]);
            $this->paginater_all->per_page(20);
            $this->paginater_all->page_link_limit(20);
            $this->paginater_all->order_by('account_match_id ASC');
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
        }
        $this->libraries->template('/setting_account_tranction/sub_index_setting_account_match',$arr_data);
    }

    function change_loan_type(){
            if( $_POST['type_id'] == 'loan'){
                $this->db->select('*');
                $this->db->from('coop_loan_type');
                $row = $this->db->get()->result_array();

                $text_return = "<option value=''>เลือกทั้งหมด</option>";
                foreach($row as $key => $value){
                    $text_return .= "<option value='".$value['id']."'>".$value['loan_type']."</option>";
                }
            }else if( $_POST['type_id'] == 'account_list' ){
                $this->db->select('*');
                $this->db->from('coop_account_list');
                $row = $this->db->get()->result_array();

                $text_return = "<option value=''>เลือกทั้งหมด</option>";
                foreach($row as $key => $value){
                    $text_return .= "<option value='".$value['account_id']."'>".$value['account_list']."</option>";
                }
            }else if( $_POST['type_id'] == 'save_transaction' ){
                $this->db->select('*');
                $this->db->from('coop_deposit_type_setting');
                $row = $this->db->get()->result_array();

                $text_return = "<option value=''>เลือกทั้งหมด</option>";
                foreach($row as $key => $value){
                    $text_return .= "<option value='".$value['type_id']."'>".$value['type_name']."</option>";
                }
            }else if( $_POST['type_id'] == 'share' ){
                $this->db->select('*');
                $this->db->from('coop_mem_share_code');
                $row = $this->db->get()->result_array();

                $text_return = "<option value=''>เลือกทั้งหมด</option>";
                foreach($row as $key => $value){
                $text_return .= "<option value='".$value['id']."'>".$value['share_description']."</option>";
                }
            }else if( $_POST['type_id'] == 'bank' ){

                $text_return  = "<option value='0'>เลือกทั้งหมด</option>";
                $this->db->select('*');
                $this->db->from('coop_bank');
                $row = $this->db->get()->result_array();
                foreach($row as $key => $value){
                    $text_return .= "<option value='{$value['bank_id']}'>".$value['bank_name']."</option>";
                }

            }else if( $_POST['type_id'] == 'main' ){

                $text_return  = "<option value='0'>เลือกทั้งหมด</option>";
                $this->db->select('*');
                $this->db->from('coop_main_account_tranction_setting');
                $row = $this->db->get()->result_array();
                foreach($row as $key => $value){
                    $text_return .= "<option value='".$value['main_account_id']."'>".$value['main_account_mane']."</option>";
                }

            }else{
                $text_return = '';
            }

        echo $text_return;
        exit;
    }
}
?>
