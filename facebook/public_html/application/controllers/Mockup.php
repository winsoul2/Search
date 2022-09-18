<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mockup extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function mock_view(){
        $arr_data = array();

        $this->libraries->template('mockup/report',$arr_data);
    }

    public function cheque_non_clearing(){
        $arr_data = array();

        $arr_data['title'] = "รายงานการเคลื่อนไหวเช็คไม่ทันเคลียร์ริ่ง";
        $this->libraries->template('mockup/report_cheque',$arr_data);
    }

    public function cheque_clearing(){
        $arr_data = array();
        $arr_data['title'] = "รายงานเช็คผ่านเคลียร์ริ่ง";
        $this->libraries->template('mockup/report_cheque',$arr_data);
    }

    public function daily_cheque_summary(){
        $arr_data = array();
        $arr_data['title'] = "รายงานสรุปการนำส่งเช็คประจำวัน";
        $this->libraries->template('mockup/report_cheque_summary',$arr_data);
    }

    public function withhold_tax_summary(){
        $arr_data = array();
        $arr_data['title'] = "รายงานสรุปภาษีหัก ณ ที่จ่าย";
        $this->libraries->template('mockup/report_cheque_summary',$arr_data);
    }

    public function tax2(){
        $arr_data = array();

        if($_GET['member_id']) {
            $this->db->select(array('t1.*',
                't2.mem_group_name AS department_name',
                't3.mem_group_name AS faction_name',
                't4.mem_group_name AS level_name',
                't5.prename_short'));
            $this->db->from('coop_mem_apply as t1');
            $this->db->join("coop_mem_group AS t2", "t1.department = t2.id", "left");
            $this->db->join("coop_mem_group AS t3", "t1.faction = t3.id", "left");
            $this->db->join("coop_mem_group AS t4", "t1.level = t4.id", "left");
            $this->db->join("coop_prename AS t5", "t1.prename_id = t5.prename_id", "left");
            $this->db->where("t1.member_id = '" . $_GET['member_id'] . "'");
            $rs = $this->db->get()->result_array();
            $row = @$rs[0];
            $arr_data['fullname'] = $row['prename_full'].$row['firstname_th']." ".$row['lastname_th'];
            $arr_data['member_id'] = $row['member_id'];
        }
        if($_GET['year']){
            $arr_data['year'] = $_GET['year'];
        }
        $arr_data['title'] = "รายงาน ภงด.2";
        $this->libraries->template('mockup/report_combo_year',$arr_data);
    }

    public function tax(){
        $arr_data = array();
        if($_GET['member_id']) {
            $this->db->select(array('t1.*',
                't2.mem_group_name AS department_name',
                't3.mem_group_name AS faction_name',
                't4.mem_group_name AS level_name',
                't5.prename_short'));
            $this->db->from('coop_mem_apply as t1');
            $this->db->join("coop_mem_group AS t2", "t1.department = t2.id", "left");
            $this->db->join("coop_mem_group AS t3", "t1.faction = t3.id", "left");
            $this->db->join("coop_mem_group AS t4", "t1.level = t4.id", "left");
            $this->db->join("coop_prename AS t5", "t1.prename_id = t5.prename_id", "left");
            $this->db->where("t1.member_id = '" . $_GET['member_id'] . "'");
            $rs = $this->db->get()->result_array();
            $row = @$rs[0];
            $arr_data['fullname'] = $row['prename_full'].$row['firstname_th']." ".$row['lastname_th'];
            $arr_data['member_id'] = $row['member_id'];
        }
        if($_GET['year']){
            $arr_data['year'] = $_GET['year'];
        }
        $arr_data['title'] = "หนังสือรับรองการหักภาษี ณ ที่จ่ายประจำปี";
        $this->libraries->template('mockup/report_combo_year',$arr_data);
    }

    public function member(){
        //$member_id = sprintf("%06d", @$_POST['member_id']);
        $member_id = $this->center_function->complete_member_id(@$_POST['member_id']);
        $arr_data = array();
        $this->db->select('*');
        $this->db->from('coop_mem_apply t1');
        $this->db->join('coop_prename t2', 't1.prename_id=t2.prename_id', 'inner');
        $this->db->where("member_id LIKE '%".$member_id."%'");
        $this->db->limit(1);
        $rs_member = $this->db->get()->result_array();
        //echo $this->db->last_query();exit;
        if(sizeof($rs_member[0])) {
            $arr_data = $rs_member[0];
        }
        header('content-type: application/json; charset:utf8;');
        echo json_encode($arr_data);
        exit;
    }








}
