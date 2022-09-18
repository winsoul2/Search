<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Return_model extends ci_model
{
    public function __construct(){
        parent::__construct();
        $this->load->model("Finance_libraries", "Finance_libraries");
    }
    public function getLoanList($member_id, $begin, $end){
        return $this->db->where("member_id='{$member_id}' and payment_date between '{$begin}' and '{$end}'")
            ->get("coop_finance_transaction")->result_array();
    }
}
