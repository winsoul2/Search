<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_summary_and_purpose_model extends CI_Model {
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Condition_loan_model", "condition_model");
    }

    public function get_loan_summary_and_purpose(){
        $arr_data = array();
        $row = $this->db->get("coop_loan_reason")->result_array();
        foreach ($row as $key => $value) {
            $reason_list[$value['loan_reason_id']] = $value;
        }
        $start_date = $this->center_function->ConvertToSQLDate($_GET['start_date']);
        $end_date = $this->center_function->ConvertToSQLDate($_GET['end_date']);

        $where = '';
        if(!empty($_GET['loan_type'])){
            $where = " AND t2.loan_type_id = '".$_GET['loan_type']."'";
        }

        $this->db->select(array('t1.loan_amount', 't1.loan_reason', 'count(t1.id) as num_loan'));
        $this->db->from('coop_loan as t1');
        $this->db->join('coop_loan_name as t2', 't1.loan_type = t2.loan_name_id', 'left');
        $this->db->where("t1.approve_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000' AND loan_status NOT IN ('0','3','5')".$where);
        $this->db->group_by('t1.loan_amount');
        $this->db->group_by('t1.loan_reason');
        $data = $this->db->get()->result_array();
        $new_data = array();
        $loan_reason = array();
        foreach ($data as $key => $value) {
            $new_data[$value['loan_amount']][$value['loan_reason']]['num_loan'] = $value['num_loan'];

            if(!empty($loan_reason[$value['loan_reason']])){
                $loan_reason[$value['loan_reason']]['num_loan_total'] += $value['num_loan'];
                $loan_reason[$value['loan_reason']]['loan_amount_total'] += $value['num_loan']*$value['loan_amount'];
            }else{
                $loan_reason[$value['loan_reason']]['num_loan_total'] = $value['num_loan'];
                $loan_reason[$value['loan_reason']]['loan_amount_total'] = $value['num_loan']*$value['loan_amount'];
                $loan_reason[$value['loan_reason']]['loan_reason'] = $reason_list[$value['loan_reason']]['loan_reason'];
            }
        }
        ksort($loan_reason);

        $arr_data['loan_reason'] = $loan_reason;
        $arr_data['data'] = $new_data;
        return $arr_data;
    }
}
