<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_new_loan_request_model extends CI_Model {
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Condition_loan_model", "condition_model");
    }

	public function get_loan_request_loan_amount(){
        $start_date = $this->center_function->ConvertToSQLDate($_GET['start_date']);
        $end_date = $this->center_function->ConvertToSQLDate($_GET['end_date']);

        $where = '';
        if(!empty($_GET['loan_type'])){
            $where = " AND t2.loan_type_id = '".$_GET['loan_type']."'";
        }

        $this->db->select(array('count(t1.id) as num_loan_amount','t1.loan_amount','sum(t1.loan_amount) as sum_loan_amount'));
        $this->db->from('coop_loan as t1');
        $this->db->join('coop_loan_name as t2', 't1.loan_type = t2.loan_name_id', 'left');
        $this->db->where("t1.createdatetime BETWEEN '".$start_date."' AND '".$end_date."' AND loan_status NOT IN ('0','3','5')".$where);
//        $this->db->where("t1.createdatetime BETWEEN '".$start_date."' AND '".$end_date."' AND loan_status = '0'".$where);
        $this->db->group_by('t1.loan_amount');
        $data = $this->db->get()->result_array();
//        echo '<pre>'; print_r($data);
//        exit;
        return $data;
	}

    public function get_loan_request_objective(){

        $row = $this->db->get("coop_loan_reason")->result_array();
        foreach ($row as $key => $value) {
            $reason_list[$value['loan_reason_id']] = $value;
        }
//        echo '<pre>';print_r($reason_list);exit;
        $start_date = $this->center_function->ConvertToSQLDate($_GET['start_date']);
        $end_date = $this->center_function->ConvertToSQLDate($_GET['end_date']);

        $where = '';
        if(!empty($_GET['loan_type'])){
            $where = " AND t2.loan_type_id = '".$_GET['loan_type']."'";
        }

        $this->db->select(array('t1.loan_amount', 't1.loan_reason', 't1.createdatetime'));
        $this->db->from('coop_loan as t1');
        $this->db->join('coop_loan_name as t2', 't1.loan_type = t2.loan_name_id', 'left');
        $this->db->where("t1.createdatetime BETWEEN '".$start_date."' AND '".$end_date."' AND loan_status NOT IN ('0','3','5')".$where);
//        $this->db->where("t1.createdatetime BETWEEN '".$start_date."' AND '".$end_date."' AND loan_status = '0'".$where);
//        $this->db->group_by('t1.loan_amount');
        $data = $this->db->get()->result_array();
        $new_data = array();
        foreach ($data as $key => $value) {
            $createdatetime = substr($value['createdatetime'], 0, 10);
            if(!empty($new_data[$createdatetime][$value['loan_reason']])){
                $new_data[$createdatetime][$value['loan_reason']]['loan_amount'] += $value['loan_amount'];
                $new_data[$createdatetime][$value['loan_reason']]['num'] += 1;
            }else{
                $new_data[$createdatetime][$value['loan_reason']]['loan_amount'] = $value['loan_amount'];
                $new_data[$createdatetime][$value['loan_reason']]['num'] = 1;
                $new_data[$createdatetime][$value['loan_reason']]['loan_reason_name'] = $reason_list[$value['loan_reason']]['loan_reason'];
            }
        }
        ksort($new_data); // เรียงลำดับข้อมูลจากน้อยไปมาก
        foreach ($new_data as $key => $value){
            ksort($new_data[$key]);
        }
        return $new_data;
    }
}
